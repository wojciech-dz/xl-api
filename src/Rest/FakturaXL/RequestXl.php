<?php

namespace App\Rest\FakturaXL;

use App\Rest\Request;
use Redis;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;


class RequestXl implements Request
{
    private const MAX_TIMEOUT_SECONDS = 30;
    private const WAIT_TIME = 6;
    private $apiUrl;
    private $cache;

    public function __construct(string $apiUrl)
    {
        $this->apiUrl = $apiUrl;
        $this->cache = new Redis;
        $this->cache->connect("localhost",6379);

    }

    public function get(string $endpoint, $time = self::WAIT_TIME, ?array $params = [], ?array $headers = []): array
    {
        while ($this->checkBusyApi()) {
            sleep(1);
        }

        $ch = curl_init();

        $this->setCommonOptions($ch, $this->apiUrl . $endpoint . '?' . http_build_query($params), $headers);
        $resultXml = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        $this->setBusyApi($time);

        return ['xml' => $resultXml];
    }

    public function post(string $endpoint, $time = self::WAIT_TIME, ?array $params = [], ?array $headers = []): array
    {
        while ($this->checkBusyApi()) {
            sleep(1);
        }

        $ch = curl_init();

        $this->setCommonOptions($ch, $this->apiUrl . $endpoint, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->makeXmlBody($params));

        $resultXml = curl_exec($ch);
        curl_close($ch);

        $this->setBusyApi($time);

        return $this->deXml($resultXml);
    }

    public function makeXmlBody(array $params)
    {
        $encoders = [new XmlEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);      
        return $serializer->serialize($params, 'xml');
    }

    public function deXml($xml)
    {
        $junkPattern = '-<br><br> (\d+).(\d+)-';
        $xml = preg_replace($junkPattern, '', $xml);
        
        return json_decode(json_encode(simplexml_load_string($xml)), true);
    }

    private function setCommonOptions($ch, string $url, ?array $headers = null): void
    {
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::MAX_TIMEOUT_SECONDS);
    }

    public function getCode($responseXml): ?int
    {
        $response = $this->deXml($responseXml);

        return $response['kod'] ?? null;
    }

    private function setBusyApi($time): void
    {
        $this->cache->setex('api_is_busy', $time, true);
    }

    private function checkBusyApi(): ?bool
    {
        return $this->cache->get('api_is_busy');
    }

    public function getResponseStatus(int $code): ?string
    {
        $statusList = [
            1 => "Dokument został poprawnie dodany",
            2 => "Przekroczono ilość zapytań na sekundę, prosimy spróbować za 5 sekund",
            3 => "Nie istnieje taki api_token",
            4 => "Nie istnieje dokument o takim ID",
            5 => "Dokument został poprawnie odczytany",
            6 => "Błędny typ dokumentu",
            7 => "Faktura o takim numerze już istnieje",
            8 => "Nie istnieje taki id_dzialy_firmy",
            9 => "Nazwa Nabywcy nie może być pusta",
            10 => "Nieprawidłowy NIP",
            11 => "Błędny kraj",
            12 => "Nazwa produktu nie może być pusta",
            13 => "Data musi mieć postać yyyy-mm-dd",
            14 => "Błędny status",
            15 => "Błędna waluta",
            16 => "Brak kursu dla tej daty, nie mozna wystawić faktury",
            17 => "Błędny język",
            18 => "Błędny rodzaj_platnosci",
            19 => "Limit bezpłatnych faktur w darmowym pakiecie został osiągnięty - prosimy o zakup Pakietu Pełnego",
            20 => "Dokument został poprawnie skasowany",
            21 => "Miesiąc został zamknięty nie można w nim dodawać ani wprowadzać nowych faktur",
            22 => "Wysłanie faktury na email nie powiodło się z powodu braku adresu email nabywcy na fakturze",
            23 => "Poprawne wysłanie dokumentu emailem",
            24 => "Nieprawidłowe pole magazyn_id",
            210 => "Dokument magazynowy nie został automatycznie zaktualizowany ponieważ ilość nie może być mniejsza od 0",
            211 => "Dokument magazynowy nie został automatycznie dodany ponieważ ilość nie może być mniejsza od 0",
            25 => "Przekroczono maksymalny rozmiar przesyłanego pliku",
            26 => "Nie istnieje produkt o takiej nazwie",
            27 => "Dział o takim ID nie istnieje",
            28 => "Maksymalny zakres dat do 31 dni",
            29 => "Brak numeru dokumentu",
            30 => "Dokument główny posiada już relację z innym dokumentem",
            31 => "Dokument podłączany posiada już relację z innym dokumentem",
            33 => "Dokument został poprawnie zapisany",
            34 => "Nie została wprowadzona żadna zmiana w dokumencie ",
            35 => "Kwota wpłaty musi być większa od 0",
            36 => "Za duża ilość wpłat",
            37 => "Dokument został już wcześniej opłacony",
            38 => "Imię nie może być puste",
            39 => "Nazwisko nie może być puste",
            900 => "Trwają prace konserwacyjne, zapraszamy za kilka minut."
        ];

        return $statusList[$code];
    }
}

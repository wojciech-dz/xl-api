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
            1 => "Dokument zosta?? poprawnie dodany",
            2 => "Przekroczono ilo???? zapyta?? na sekund??, prosimy spr??bowa?? za 5 sekund",
            3 => "Nie istnieje taki api_token",
            4 => "Nie istnieje dokument o takim ID",
            5 => "Dokument zosta?? poprawnie odczytany",
            6 => "B????dny typ dokumentu",
            7 => "Faktura o takim numerze ju?? istnieje",
            8 => "Nie istnieje taki id_dzialy_firmy",
            9 => "Nazwa Nabywcy nie mo??e by?? pusta",
            10 => "Nieprawid??owy NIP",
            11 => "B????dny kraj",
            12 => "Nazwa produktu nie mo??e by?? pusta",
            13 => "Data musi mie?? posta?? yyyy-mm-dd",
            14 => "B????dny status",
            15 => "B????dna waluta",
            16 => "Brak kursu dla tej daty, nie mozna wystawi?? faktury",
            17 => "B????dny j??zyk",
            18 => "B????dny rodzaj_platnosci",
            19 => "Limit bezp??atnych faktur w darmowym pakiecie zosta?? osi??gni??ty - prosimy o zakup Pakietu Pe??nego",
            20 => "Dokument zosta?? poprawnie skasowany",
            21 => "Miesi??c zosta?? zamkni??ty nie mo??na w nim dodawa?? ani wprowadza?? nowych faktur",
            22 => "Wys??anie faktury na email nie powiod??o si?? z powodu braku adresu email nabywcy na fakturze",
            23 => "Poprawne wys??anie dokumentu emailem",
            24 => "Nieprawid??owe pole magazyn_id",
            210 => "Dokument magazynowy nie zosta?? automatycznie zaktualizowany poniewa?? ilo???? nie mo??e by?? mniejsza od 0",
            211 => "Dokument magazynowy nie zosta?? automatycznie dodany poniewa?? ilo???? nie mo??e by?? mniejsza od 0",
            25 => "Przekroczono maksymalny rozmiar przesy??anego pliku",
            26 => "Nie istnieje produkt o takiej nazwie",
            27 => "Dzia?? o takim ID nie istnieje",
            28 => "Maksymalny zakres dat do 31 dni",
            29 => "Brak numeru dokumentu",
            30 => "Dokument g????wny posiada ju?? relacj?? z innym dokumentem",
            31 => "Dokument pod????czany posiada ju?? relacj?? z innym dokumentem",
            33 => "Dokument zosta?? poprawnie zapisany",
            34 => "Nie zosta??a wprowadzona ??adna zmiana w dokumencie ",
            35 => "Kwota wp??aty musi by?? wi??ksza od 0",
            36 => "Za du??a ilo???? wp??at",
            37 => "Dokument zosta?? ju?? wcze??niej op??acony",
            38 => "Imi?? nie mo??e by?? puste",
            39 => "Nazwisko nie mo??e by?? puste",
            900 => "Trwaj?? prace konserwacyjne, zapraszamy za kilka minut."
        ];

        return $statusList[$code];
    }
}

<?php

namespace App\Services\Invoices\Providers;

use App\Entity\Invoices\Invoice;
use App\Rest\FakturaXL\RequestXl;
use App\Rest\Request;
use App\Services\Invoices\InvoicesDownloadInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use splFileObject;

class FakturaXLProvider implements \App\Services\Invoices\InvoicesDownloadInterface
{
    private $apiUrl;
    private $token;
    private $downloadDir;
    private $sc;
    private $request;

    public function __construct(ContainerInterface $sc)
    {
        $this->sc = $sc;
        $this->apiUrl = $this->sc->getParameter('invoice.url');
        $this->token = $this->sc->getParameter('invoice.token');
        $this->downloadDir = $this->sc->getParameter('download.dir');
        $this->request = new RequestXl($this->apiUrl);
    }

    /**
     * @inheritDoc
     */
    public function downloadInvoicesList(array $parameters): ?array
    {
        $delay = 5;
        $parameters = array_merge($parameters, ['api_token' => $this->token]);

        return $this->request->post('api/lista_dokumentow.php', $delay, $parameters);
    }

    /**
     * @inheritDoc
     */
    public function downloadOneInvoice(int $id): ?array
    {
        $delay = 1;
        $parameters = [
            'dokument_id' => $id,
            'api_token' => $this->token,
        ];
        $document = $this->request->post('api/dokument_odczytaj.php', $delay, $parameters);
        $payload = [];
        $payload['invoice'] = $this->standarizeInvoice($id, $document);
        $payload['items'] = $this->standarizeItems($document);
        $payload['issuer'] = $this->standarizeIssuer($document);

        return $payload;
    }

    public function downloadAsPdf(Invoice $invoice): void
    {
        $delay = 1;
        $parameters = [
            'api' => $this->token,
            'dokument_id' => $invoice->getExternalId(),
            'pdf' => 1,
        ];
        $result = $this->request->get('dokument_export.php', $delay, $parameters);
        var_dump($result);

        $file = new \SplFileObject($this->downloadDir . $parameters['dokument_id'] . '.pdf', 'w');
        $file->fwrite($result['xml']);
    }

    public function standarizeInvoice($id, $document): ?array
    {
        $payload = [];
        $payload['externalid'] = $id;
        $payload['type'] = $this->checkEmpty($document['typ_faktury']);
        $payload['subtype'] = $this->checkEmpty($document['kod']);
        $payload['number'] = $this->checkEmpty($document['numer_faktury']);
        $payload['issuedate'] = $this->checkEmpty($document['data_wystawienia']);
        $payload['saledate'] = $this->checkEmpty($document['data_sprzedazy']);
        $payload['duedate'] = $this->checkEmpty($document['termin_platnosci_data']);
        $payload['paymentdate'] = $this->checkEmpty($document['data_oplacenia']);
        $payload['paymentamount'] = $this->checkEmpty($document['kwota_oplacona']);
        $payload['remarks'] = $this->checkEmpty($document['uwagi']);
        $payload['currency'] = $this->checkEmpty($document['waluta']);
        $payload['language'] = $this->checkEmpty($document['jezyk']);
        $payload['issuername'] = $this->checkEmpty($document['sprzedawca']['nip']);
        $payload['receivername'] = $this->checkEmpty($document['nabywca']['nip']);
        $payload['additionalremarks'] = $this->checkEmpty($document['dodatkowe_uwagi']);
        $payload['netvalue'] = $this->checkEmpty($document['wartosc_netto']);
        $payload['vatvalue'] = $this->checkEmpty($document['wartosc_vat']);
        $payload['grossvalue'] = $this->checkEmpty($document['wartosc_brutto']);

        return $payload;
    }

    public function standarizeItems($document): ?array
    {
        $position = 1;
        $payload =[];
        if (array_key_exists('nazwa', $document['faktura_pozycje'])) {
            $item = $document['faktura_pozycje'];
            $payload[] = $this->getPayload($position, $item);
        } else {
            foreach ($document['faktura_pozycje'] as $item) {
                $payload[] = $this->getPayload($position++, $item);            }
        }

        return $payload;
    }

    public function standarizeIssuer($document): ?array
    {
        $issuer = $document['nabywca'];
        $payload = [];
        $payload['name'] = $this->checkEmpty($issuer['nazwa']);
        $payload['forename'] = $this->checkEmpty($issuer['imie']);
        $payload['surname'] = $this->checkEmpty($issuer['nazwisko']);
        $payload['nip'] = $this->checkEmpty($issuer['nip']);
        $payload['street'] = $this->checkEmpty($issuer['ulica_i_numer']);
        $payload['postcode'] = $this->checkEmpty($issuer['kod_pocztowy']);
        $payload['town'] = $this->checkEmpty($issuer['miejscowosc']);
        $payload['country'] = $this->checkEmpty($issuer['kraj_id']);
        $payload['email'] = $this->checkEmpty($issuer['email']);
        $payload['phpne'] = $this->checkEmpty($issuer['telefon']);
        $payload['fax'] = $this->checkEmpty($issuer['fax']);
        $payload['www'] = $this->checkEmpty($issuer['www']);
        $payload['bankaccount'] = $this->checkEmpty($issuer['nr_konta_bankowego']);

        return $payload;
    }

    private function getPayload(int $position, $item): array
    {
        $payloadItem = [];
        $payloadItem['position'] = $position;
        $payloadItem['name'] = $this->checkEmpty($item['nazwa']);
        $payloadItem['productcode'] = $this->checkEmpty($item['kod_produktu']);
        $payloadItem['productid'] = $this->checkEmpty($item['produkt_id']);
        $payloadItem['pkwiu'] = $this->checkEmpty($item['pkwiu']);
        $payloadItem['amount'] = $this->checkEmpty($item['ilosc']);
        $payloadItem['unit'] = $this->checkEmpty($item['jm']);
        $payloadItem['net'] = $this->checkEmpty($item['netto']);
        $payloadItem['gross'] = $this->checkEmpty($item['brutto']);
        $payloadItem['vat'] = $this->checkEmpty($item['vat']);
        $payloadItem['netvalue'] = $this->checkEmpty($item['wartosc_netto']);
        $payloadItem['grossvalue'] = $this->checkEmpty($item['wartosc_brutto']);

        return $payloadItem;
    }

    private function checkEmpty($element)
    {
        return $element === [] ? null : $element;
    }
}
<?php

namespace App\Services\Invoices;

use App\Entity\Invoices\Invoice;
use App\Entity\Invoices\InvoiceItems;
use App\Entity\Issuers\Issuer;
use App\Repository\Invoices\InvoiceItemsRepository;
use App\Repository\Invoices\InvoiceRepository;
use App\Repository\Issuers\IssuerRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use App\Services\Invoices\Providers\FakturaXLProvider;
use Psr\Container\ContainerInterface;

class InvoicesService
{
    private InvoicesDownloadInterface $provider;
    private $invoiceProvider;
    private $sc;
    private $entityManager;
    private $invoiceRepository;
    private $invoiceItemsRepository;
    private $issuerRepository;

    private const PROVIDERS = [
        'FakturaXL' => FakturaXLProvider::class,
    ];

    public function __construct(
        $invoiceProvider,
        ContainerInterface $sc,
        EntityManagerInterface $entityManager,
        InvoiceRepository $invoiceRepository,
        InvoiceItemsRepository $invoiceItemsRepository,
        IssuerRepository $issuerRepository
    ) {
        $this->invoiceProvider = $invoiceProvider;
        $this->sc = $sc;
        $this->entityManager = $entityManager;
        $this->invoiceRepository = $invoiceRepository;
        $this->invoiceItemsRepository = $invoiceItemsRepository;
        $this->issuerRepository = $issuerRepository;
    }

    public function downloadOneInvoice(int $id): ?array
    {
        $provider = self::PROVIDERS[$this->invoiceProvider];
        $this->provider = new $provider($this->sc);

        return $this->provider->downloadOneInvoice($id);
    }

    public function downloadAsPdf(Invoice $id): void
    {
        $provider = self::PROVIDERS[$this->invoiceProvider];
        $this->provider = new $provider($this->sc);
        $this->provider->downloadAsPdf($id);
    }

    public function downloadInvoicesList($dateFrom = '2022-06-01', $dateTo = '2022-06-30'): ?array
    {
        $parameters = [
            'data_od' => $dateFrom->format('Y-m-d'),
            'data_do' => $dateTo->format('Y-m-d'),
        ];
        $provider = self::PROVIDERS[$this->invoiceProvider];
        $this->provider = new $provider($this->sc);

        $results = $this->provider->downloadInvoicesList($parameters);
        $list = [];
        foreach ($results['dokument'] as $k => $v) {
            $list[] = [
                'id' => $v['id'],
                'numer_faktury' => $v['numer_faktury'],
                'nazwa_skrocona_odbiorcy' => $v['nazwa_skrocona_odbiorcy'],
                'data_wystawienia' => $v['data_wystawienia'],
                'wartosc_brutto' => $v['wartosc_brutto'],
            ];
        }

        return $list;
    }

    public function downloadAllInvoices(string $firstMonth): ?array
    {
        $invoicesList = [];
        $now = new DateTime();
        $now->modify('last day of this month');
        $dateTo = new DateTime($firstMonth);
        while ($dateTo <= $now) {
            $dateTo->modify('last day of this month');
            $dateFrom = clone($dateTo);
            $dateFrom->modify('first day of this month');
            $invoicesList = array_merge($invoicesList, $this->downloadInvoicesList($dateFrom, $dateTo));
            $dateTo->modify('+1 day');
        }

        return $invoicesList;
    }

    public function loadData($id, $payload): void
    {
        $invoice = $this->loadInvoice($payload['invoice']);
        foreach ($payload['items'] as $item) {
            $this->loadItem($invoice, $item);
        }
        $this->loadIssuer($payload['issuer']);
    }

    public function loadInvoice($payload): Invoice
    {
        if ($invoice = $this->invoiceRepository->getByExternal($payload['externalid'])) {
            return $invoice;
        }

        $invoice = new Invoice();

        $invoice->setExternalId($payload['externalid']);
        $invoice->setType($payload['type']);
        $invoice->setSubtype($payload['subtype']);
        $invoice->setNumber($payload['number']);
        $invoice->setIssueDate(new DateTime($payload['issuedate']));
        $invoice->setSaleDate(new DateTime($payload['saledate']));
        $invoice->setDueDate(new DateTime($payload['duedate']));
        $invoice->setPaymentDate(new DateTime($payload['paymentdate']));
        $invoice->setPaymentAmount($payload['paymentamount']);
        $invoice->setRemarks($payload['remarks']);
        $invoice->setCurrency($payload['currency']);
        $invoice->setLanguage($payload['language']);
        $invoice->setIssuerName($payload['issuername']);
        $invoice->setReceiverName($payload['receivername']);
        $invoice->setAdditionalRemarks($payload['additionalremarks']);
        $invoice->setNetValue($payload['netvalue']);
        $invoice->setVat($payload['vatvalue']);
        $invoice->setGrossValue($payload['grossvalue']);

        $this->entityManager->persist($invoice);
        $this->entityManager->flush();

        return $invoice;
    }

    public function loadItem($invoice, $payload): void
    {
        if ($this->invoiceItemsRepository->getByInvoiceAndPosition($invoice, $payload['position'])) {
            return;
        }

        $invoiceItem = new InvoiceItems();

        $invoiceItem->setInvoiceId($invoice->getExternalId());
        $invoiceItem->setInvoice($invoice);
        $invoiceItem->setPosition($payload['position']);
        $invoiceItem->setName($payload['name']);
        $invoiceItem->setProductCode($payload['productcode']);
        $invoiceItem->setProductId($payload['productid']);
        $invoiceItem->setPkwiu($payload['pkwiu']);
        $invoiceItem->setAmount($payload['amount']);
        $invoiceItem->setUnit($payload['unit']);
        $invoiceItem->setNetValue($payload['netvalue']);
        $invoiceItem->setVat($payload['vat']);
        $invoiceItem->setGrossValue($payload['grossvalue']);
        $invoiceItem->setNet($payload['net']);
        $invoiceItem->setGross($payload['gross']);

        $this->entityManager->persist($invoiceItem);
        $this->entityManager->flush();
    }

    public function loadIssuer($payload): void
    {
        if ($this->issuerRepository->getByNip($payload['nip'])) {
            return;
        }

        $issuer = new Issuer();

        $issuer->setName($payload['name']);
        $issuer->setForename($payload['forename']);
        $issuer->setSurname($payload['surname']);
        $issuer->setNip($payload['nip']);
        $issuer->setStreet($payload['street']);
        $issuer->setPostcode($payload['postcode']);
        $issuer->setTown($payload['town']);
        $issuer->setCountry($payload['country']);
        $issuer->setEmail($payload['email']);
        $issuer->setPhone($payload['phpne']);
        $issuer->setFax($payload['fax']);
        $issuer->setWww($payload['www']);
        $issuer->setBankAccount($payload['bankaccount']);

        $this->entityManager->persist($issuer);
        $this->entityManager->flush();
    }

    public function getInvoiceItems(Invoice $invoice): ?array
    {
        return $this->invoiceItemsRepository->getByInvoice($invoice);
    }
}
<?php

namespace App\Services\Invoices;

use App\Entity\Invoices\Invoice;

interface InvoicesDownloadInterface
{
    public function downloadInvoicesList(array $parameters): ?array;

    public function downloadOneInvoice(int $id): ?array;

    public function downloadAsPdf(Invoice $id): void;
}
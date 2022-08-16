<?php

namespace App\Command\Invoices;

use App\Services\Invoices\InvoicesService;
use DateTime;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GetFreshCommand extends Command
{
    protected static $defaultName = 'Invoices:get-fresh';
    protected static $defaultDescription = 'Downloads documents from API';
    private $invoicesService;

    public function __construct(InvoicesService $invoicesService)
    {
        $this->invoicesService = $invoicesService;
        parent::__construct(static::$defaultName);
    }

    protected function configure(): void
    {
        $this
            ->addArgument('day', InputArgument::OPTIONAL, 'Pierwszy miesiąc pobierania faktur.
             Oczekuje daty w formacie "yyyy-mm-dd". Jeśli nie zostanie podana, bierze faktury z bieżącego miesiąca.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $day = $input->getArgument('day');

        if ($day) {
            $startDay = new DateTime($day);
            $io->note(sprintf('Pobierasz dokumenty od: %s', $day));
        } else {
            $startDay = new DateTime;
            $io->note(sprintf('Nie podałeś argumentu, pobieram dane od (%s)', $startDay->format('Y-m')));
        }

        $invoices = $this->invoicesService->downloadAllInvoices($startDay->format('Y-m-d'));
        foreach ($invoices as $invoice) {
            $payload = $this->invoicesService->downloadOneInvoice($invoice['id']);
            $this->invoicesService->loadData($invoice['id'], $payload);
        }

        $io->success('Dokumenty zostały pobrane.');

        return Command::SUCCESS;
    }
}

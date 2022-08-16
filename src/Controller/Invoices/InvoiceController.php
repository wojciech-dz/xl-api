<?php

namespace App\Controller\Invoices;

use App\Entity\Invoices\Invoice;
use App\Form\Invoices\InvoiceType;
use App\Repository\Invoices\InvoiceRepository;
use App\Repository\Invoices\InvoiceItemsRepository;
use App\Services\Invoices\InvoicesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/invoices")
 */
class InvoiceController extends AbstractController
{
    private InvoicesService $invoicesService;

    public function __construct(InvoicesService $invoicesService)
    {
        $this->invoicesService = $invoicesService;
    }

    /**
     * @Route("/", name="app_invoices_invoice_index", methods={"GET"})
     */
    public function index(InvoiceRepository $invoiceRepository, Request $request): ?Response
    {
        $invoices = $invoiceRepository->findAll();
        return $this->render('invoices/invoice/index.html.twig', [
            'invoices' => $invoices,
            'limit' => 7,
        ]);
    }

    /**
     * @Route("/scroll", name="app_invoices_invoice_dose", methods={"GET"})
     */
    public function scrollIndex(InvoiceRepository $invoiceRepository, Request $request): Response
    {
        $page = $request->get('page') ?? 1;
        $limit = $request->get('limit') ?? 7;
        [$maxPages, $items] = $invoiceRepository->getOneDose($page, $limit);
        $invoices = [];
        foreach ($items as $item) {
            $invoices[] = [
                'id' => $item['id'],
                'number' => $item['number'],
                'externalId' => $item['externalId'],
                'issuerName' => $item['issuerName'],
                'issueDate' => $item['issueDate']->format('Y-m-d'),
                'grossValue' => $item['grossValue']
            ];
        }
        return $this->render('invoices/invoice/scroll_index.html.twig', [
            'invoices' => $invoices,
            'thisPage' => $page,
            'maxPages' => $maxPages,
            'limit' => $limit,
        ]);
    }

    /**
     * @Route("/new", name="app_invoices_invoice_new", methods={"GET", "POST"})
     */
    public function new(Request $request, InvoiceRepository $invoiceRepository): Response
    {
        $invoice = new Invoice();
        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $invoiceRepository->add($invoice, true);

            return $this->redirectToRoute('app_invoices_invoice_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('invoices/invoice/new.html.twig', [
            'invoice' => $invoice,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_invoices_invoice_show", methods={"GET"})
     */
    public function show(Invoice $invoice): Response
    {
        return $this->render('invoices/invoice/show.html.twig', [
            'invoice' => $invoice,
        ]);
    }

    /**
     * @Route("/{id}/pdf", name="app_invoices_invoice_pdf", methods={"GET"})
     */
    public function getPdf(Invoice $invoice): RedirectResponse
    {
        $this->invoicesService->downloadAsPdf($invoice);
        return new RedirectResponse($this->generateUrl('app_invoices'));
    }

    /**
     * @Route("/{id}/items", name="app_invoices_invoice_items_filtered", methods={"GET"})
     */
    public function getItemsForInvoice(InvoiceItemsRepository $invoiceItemsRepository, Invoice $invoice): Response
    {
        $items = $this->invoicesService->getInvoiceItems($invoice);
        return $this->render('invoices/invoice/just_items.html.twig', [
            'invoice_items' => $items,
        ]);
    }

    /**
     * @Route("/{id}/full-details", name="app_invoices_invoice_full_details_filtered", methods={"GET"})
     */
    public function getFullDetailsForInvoice(InvoiceItemsRepository $invoiceItemsRepository, Invoice $invoice): Response
    {
        $items = $this->invoicesService->getInvoiceItems($invoice);
        return $this->render('invoices/invoice/just_items.html.twig', [
            'invoice_items' => $items,
            'invoice' => $invoice,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_invoices_invoice_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Invoice $invoice, InvoiceRepository $invoiceRepository): Response
    {
        $form = $this->createForm(InvoiceType::class, $invoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $invoiceRepository->add($invoice, true);

            return $this->redirectToRoute('app_invoices_invoice_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('invoices/invoice/edit.html.twig', [
            'invoice' => $invoice,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_invoices_invoice_delete", methods={"POST"})
     */
    public function delete(Request $request, Invoice $invoice, InvoiceRepository $invoiceRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$invoice->getId(), $request->request->get('_token'))) {
            $invoiceRepository->remove($invoice, true);
        }

        return $this->redirectToRoute('app_invoices_invoice_index', [], Response::HTTP_SEE_OTHER);
    }
}

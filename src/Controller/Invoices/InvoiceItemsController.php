<?php

namespace App\Controller\Invoices;

use App\Entity\Invoices\InvoiceItems;
use App\Form\Invoices\InvoiceItemsType;
use App\Repository\Invoices\InvoiceItemsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/invoice-items")
 */
class InvoiceItemsController extends AbstractController
{
    /**
     * @Route("/", name="app_invoices_invoice_items_index", methods={"GET"})
     */
    public function index(InvoiceItemsRepository $invoiceItemsRepository): Response
    {
        return $this->render('invoices/invoice_items/index.html.twig', [
            'invoice_items' => $invoiceItemsRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_invoices_invoice_items_new", methods={"GET", "POST"})
     */
    public function new(Request $request, InvoiceItemsRepository $invoiceItemsRepository): Response
    {
        $invoiceItem = new InvoiceItems();
        $form = $this->createForm(InvoiceItemsType::class, $invoiceItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $invoiceItemsRepository->add($invoiceItem, true);

            return $this->redirectToRoute('app_invoices_invoice_items_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('invoices/invoice_items/new.html.twig', [
            'invoice_item' => $invoiceItem,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_invoices_invoice_items_show", methods={"GET"})
     */
    public function show(InvoiceItems $invoiceItem): Response
    {
        return $this->render('invoices/invoice_items/show.html.twig', [
            'invoice_item' => $invoiceItem,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_invoices_invoice_items_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, InvoiceItems $invoiceItem, InvoiceItemsRepository $invoiceItemsRepository): Response
    {
        $form = $this->createForm(InvoiceItemsType::class, $invoiceItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $invoiceItemsRepository->add($invoiceItem, true);

            return $this->redirectToRoute('app_invoices_invoice_items_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('invoices/invoice_items/edit.html.twig', [
            'invoice_item' => $invoiceItem,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_invoices_invoice_items_delete", methods={"POST"})
     */
    public function delete(Request $request, InvoiceItems $invoiceItem, InvoiceItemsRepository $invoiceItemsRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$invoiceItem->getId(), $request->request->get('_token'))) {
            $invoiceItemsRepository->remove($invoiceItem, true);
        }

        return $this->redirectToRoute('app_invoices_invoice_items_index', [], Response::HTTP_SEE_OTHER);
    }
}

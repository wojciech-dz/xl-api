<?php

namespace App\Controller\Issuers;

use App\Entity\Issuers\Issuer;
use App\Form\Issuers\IssuerType;
use App\Repository\Issuers\IssuerRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/issuers/issuer")
 */
class IssuerController extends AbstractController
{
    /**
     * @Route("/", name="app_issuers_issuer_index", methods={"GET"})
     */
    public function index(IssuerRepository $issuerRepository): Response
    {
        return $this->render('issuers/issuer/index.html.twig', [
            'issuers' => $issuerRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_issuers_issuer_new", methods={"GET", "POST"})
     */
    public function new(Request $request, IssuerRepository $issuerRepository): Response
    {
        $issuer = new Issuer();
        $form = $this->createForm(IssuerType::class, $issuer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $issuerRepository->add($issuer, true);

            return $this->redirectToRoute('app_issuers_issuer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('issuers/issuer/new.html.twig', [
            'issuer' => $issuer,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_issuers_issuer_show", methods={"GET"})
     */
    public function show(Issuer $issuer): Response
    {
        return $this->render('issuers/issuer/show.html.twig', [
            'issuer' => $issuer,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_issuers_issuer_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Issuer $issuer, IssuerRepository $issuerRepository): Response
    {
        $form = $this->createForm(IssuerType::class, $issuer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $issuerRepository->add($issuer, true);

            return $this->redirectToRoute('app_issuers_issuer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('issuers/issuer/edit.html.twig', [
            'issuer' => $issuer,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_issuers_issuer_delete", methods={"POST"})
     */
    public function delete(Request $request, Issuer $issuer, IssuerRepository $issuerRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$issuer->getId(), $request->request->get('_token'))) {
            $issuerRepository->remove($issuer, true);
        }

        return $this->redirectToRoute('app_issuers_issuer_index', [], Response::HTTP_SEE_OTHER);
    }
}

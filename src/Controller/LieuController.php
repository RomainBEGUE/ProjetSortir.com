<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Ville;
use App\Form\LieuType;
use App\Repository\LieuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/lieu")
 */
class LieuController extends AbstractController
{
    /**
     * @Route("/", name="lieu_index", methods={"GET"})
     */
    public function index(LieuRepository $lieuRepository): Response
    {
        return $this->render('lieu/index.html.twig', [
            'lieus' => $lieuRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="lieu_new", methods={"GET","POST"})
     */
    public function new(Request $request, LieuRepository $lieuRepository): Response
    {
        $lieu = new Lieu();
        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$lieuRepository->findOneBySomeField($form->get('rue')->getData(),$form->get('ville')->getData()->getNom()))
                {
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($lieu);
                    $entityManager->flush();
                    $this->addFlash('success', 'Lieu ajouté');
                    return $this->redirectToRoute('main_create', [], Response::HTTP_SEE_OTHER);
                }
            else
                {
                    $this->addFlash('fail', 'Ce lieu existe');
                    return $this->render('lieu/new.html.twig', [
                        'lieu' => $lieu,
                        'form' => $form->createView(),
                    ]);
                }
        }

        return $this->render('lieu/new.html.twig', [
            'lieu' => $lieu,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="lieu_show", methods={"GET"})
     */
    public function show(Lieu $lieu): Response
    {
        return $this->render('lieu/show.html.twig', [
            'lieu' => $lieu,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="lieu_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Lieu $lieu, LieuRepository $lieuRepository): Response
    {
        $form = $this->createForm(LieuType::class, $lieu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (!$lieuRepository->findOneBySomeField($form->get('rue')->getData(),$form->get('ville')->getData()->getNom()))
                {
                    $this->getDoctrine()->getManager()->flush();
                    $this->addFlash('success', 'Le lieu a bien été mis à jour');
                    return $this->redirectToRoute('lieu_index', [], Response::HTTP_SEE_OTHER);
                }
            else
                {
                    $this->addFlash('fail', 'Ce lieu existe déjà');
                    return $this->render('lieu/edit.html.twig', [
                        'lieu' => $lieu,
                        'form' => $form->createView(),
                    ]);
                }
        }

        return $this->render('lieu/edit.html.twig', [
            'lieu' => $lieu,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="lieu_delete", methods={"POST"})
     */
    public function delete(Request $request, Lieu $lieu): Response
    {
        if ($this->isCsrfTokenValid('delete'.$lieu->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($lieu);
            $entityManager->flush();
        }

        return $this->redirectToRoute('lieu_index', [], Response::HTTP_SEE_OTHER);
    }
}

<?php

namespace App\Controller;

use App\Entity\FicheFrais;
use App\Form\MoisFicheType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/fichefrais')]
final class FicheFraisController extends AbstractController
{
    #[Route(name: 'app_fiche_frais', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser(); // Assuming the user is logged in
        $fichesFrais = $entityManager->getRepository(FicheFrais::class)->findBy(['user' => $user]);

        $form = $this->createForm(MoisFicheType::class, $fichesFrais);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $fiche = $form->get('fiches')->getData();
        }


        return $this->render('fiche_frais/index.html.twig', [
            'form' => $form,
            'fiche' => $fiche
        ]);
    }

    /*#[Route('/new', name: 'app_fiche_frais_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ficheFrais = new FicheFrais();
        $form = $this->createForm(FicheFraisType::class, $ficheFrais);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ficheFrais);
            $entityManager->flush();

            return $this->redirectToRoute('app_fiche_frais', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('fiche_frais/new.html.twig', [
            'ficheFrais' => $ficheFrais,
            'form' => $form,
        ]);
    }*/
}

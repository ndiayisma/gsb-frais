<?php

namespace App\Controller;

use App\Entity\FicheFrais;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FicheFraisController extends AbstractController
{
    #[Route('/fichefrais', name: 'app_fiche_frais')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser(); // Assuming the user is logged in
        $fichesFrais = $entityManager->getRepository(FicheFrais::class)->findBy(['user' => $user]);

        return $this->render('fiche_frais/index.html.twig', [
            'fichesFrais' => $fichesFrais,
        ]);
    }
}

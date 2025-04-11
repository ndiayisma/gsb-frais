<?php

namespace App\Controller;

use App\Entity\FicheFrais;
use App\Entity\FraisForfait;
use App\Entity\LigneFraisForfait;
use App\Entity\Etat;
use App\Form\MoisFicheType;
use Doctrine\ORM\EntityManagerInterface;
use http\Env\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ComptableController extends AbstractController
{
    #[Route('/comptable', name: 'app_comptable')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MoisFicheType::class);

        return $this->render('comptable/index.html.twig', [


            'controller_name' => 'ComptableController',
        ]);
    }
}

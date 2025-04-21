<?php

namespace App\Controller;

use App\Entity\FicheFrais;
use App\Entity\FraisForfait;
use App\Entity\LigneFraisForfait;
use App\Entity\Etat;
use App\Form\MoisFicheType;
use App\Form\SaisieFicheFraisType;
use App\Form\MoisFicheComptableType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ComptableController extends AbstractController
{
    #[IsGranted('ROLE_COMPTABLE')]
    #[Route('/comptable', name: 'app_comptable')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $selectedUser = null;
        $fiches = [];
        $selectedFiche = null;

        // Initial form creation
        $form = $this->createForm(MoisFicheComptableType::class, null, [
            'allow_extra_fields' => true, // // Désactive la validation des champs supplémentaires
            'fiches' => $fiches,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $selectedUser = $form->get('user')->getData();

            if ($selectedUser) {
                // Fetch fiches for the selected user
                $fiches = $entityManager->getRepository(FicheFrais::class)
                    ->findBy(['user' => $selectedUser]);

                // Rebuild the form with updated fiches
                $form = $this->createForm(MoisFicheComptableType::class, null, [
                    'fiches' => $fiches,
                ]);

                // Handle the request again to avoid extra fields error
                $form->handleRequest($request);

                // Optionally, retrieve the selected fiche
                $selectedFiche = $form->get('fiches')->getData();
            }
        }

        return $this->render('comptable/index.html.twig', [
            'form' => $form->createView(),
            'selectedUser' => $selectedUser,
            'selectedFiche' => $selectedFiche,
        ]);
    }
}

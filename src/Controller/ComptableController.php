<?php

namespace App\Controller;

use App\Entity\FicheFrais;
use App\Entity\FraisForfait;
use App\Entity\LigneFraisForfait;
use App\Entity\Etat;
use App\Form\MoisFicheType;
use App\Form\SaisieFicheFraisType;
use App\Form\LigneFraisHorsForfaitType;
use App\Entity\LigneFraisHorsForfait;
use App\Form\MoisFicheComptableType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_COMPTABLE')]
#[Route('/comptable')]
final class ComptableController extends AbstractController
{

    #[Route(name: 'app_comptable')]
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

    #[Route('/fiche/{id}', name: 'app_comptable_fiche', methods: ['GET'])]
    public function show(FicheFrais $ficheFrais): Response
    {
        return $this->render('comptable/show.html.twig', [
            'ficheFrais' => $ficheFrais,
        ]);
    }

    #[Route('fiche/update/{id}', name: 'app_comptable_fiche_update', methods: ['POST'])]
    public function updateAValider(Request $request, LigneFraisHorsForfait $ligneFraisHorsForfait, EntityManagerInterface $entityManager): Response
    {
        $ligneFraisHorsForfait->setAValider(!$ligneFraisHorsForfait->getAValider()); // Merci Zitoune

        $libelle = $ligneFraisHorsForfait->getLibelle();

        if (str_starts_with($libelle, 'REFUSÉ : ')) {
            // Si déjà refusé, on enlève le préfixe pour le réaccepter
            $libelle = substr($libelle, strlen('REFUSÉ : '));
        } else {
            // Sinon on ajoute le préfixe pour le refuser
            $libelle = 'REFUSÉ : ' . $libelle;
        }

        $ligneFraisHorsForfait->setLibelle($libelle);

        $entityManager->flush();

        return $this->redirectToRoute('app_comptable_fiche', [
            'id' => $ligneFraisHorsForfait->getFicheFrais()->getId()
        ]);
    }

    #[Route('/comptable/fiche/{id}/valider', name: 'comptable_valider_fiche', methods: ['POST'])]
    public function validerFiche(FicheFrais $ficheFrais, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'état "Validée" (par exemple, avec l'ID correspondant)
        $etatValide = $entityManager->getRepository(Etat::class)->find(2); // VA = Validée
        $montantTotal = 0;

        foreach($ficheFrais->getLigneFraisForfaits() as $ligneFraisForfait) {
            $montantTotal += $ligneFraisForfait->getTotalAmount();
        }

        if ($etatValide) {
            $ficheFrais->setEtat($etatValide); // Mettre à jour l'état de la fiche
            $ficheFrais->setDateModif(new \DateTime()); // Mettre à jour la date de modification
            $ficheFrais->setMontantValid($montantTotal); // Mettre à jour le montant validé

            $entityManager->flush();

            $this->addFlash('success', 'La fiche de frais a été validée avec succès.');
        } else {
            $this->addFlash('danger', 'Impossible de valider la fiche : état non trouvé.');
        }

        return $this->redirectToRoute('app_comptable'); // Redirige vers la liste des fiches
    }


}

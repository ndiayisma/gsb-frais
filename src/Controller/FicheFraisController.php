<?php

namespace App\Controller;

use App\Entity\FicheFrais;
use App\Entity\FraisForfait;
use App\Entity\LigneFraisForfait;
use App\Entity\Etat;
use App\Form\MoisFicheType;
use App\Form\SaisieFicheFraisType;
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
        $fiche = null; // Initialize the variable
        if ($form->isSubmitted() && $form->isValid()) {

            $fiche = $form->get('fiches')->getData();

        }


        return $this->render('fiche_frais/index.html.twig', [
            'form' => $form,
            'fiche' => $fiche
        ]);
    }

    #[Route('/new', name: 'app_fiche_frais_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser(); // Assuming the user is logged in
        $date = new \DateTime();
        $date->modify('first day of this month');
        $mois = $date->format('Y-m');


        // Check if the fiche frais already exists for the user and the given month
        $existingFicheFrais = $entityManager->getRepository(FicheFrais::class)->findOneBy([
            'user' => $user,
            'mois' => \DateTime::createFromFormat('Y-m', $mois),
        ]);
        $ficheFrais = new FicheFrais();
        if (!$existingFicheFrais) {
            // Si la fiche existe, alors il n'y a pas besoin de le créer, il faudra donc le modifier

            $ficheFrais->setUser($user);
            $ficheFrais->setMois(\DateTime::createFromFormat('Y-m', $mois));
            $ficheFrais->setMontantValid(0);
            $ficheFrais->setNbJustifications(0);
            $ficheFrais->setDateModif($date);
            $ficheFrais->setEtat($entityManager->getRepository(Etat::class)->find(4));
            $ligneKm = new LigneFraisForfait();
            $ligneKm->setFicheFrais($ficheFrais);
            $ligneKm->setFraisForfait($entityManager->getRepository(FraisForfait::class)->find(2));
            $ligneKm->setQuantite(0);
            $ligneEtape = new LigneFraisForfait();
            $ligneEtape->setFicheFrais($ficheFrais);
            $ligneEtape->setFraisForfait($entityManager->getRepository(FraisForfait::class)->find(1));
            $ligneEtape->setQuantite(0);
            $ligneNuitee = new LigneFraisForfait();
            $ligneNuitee->setFicheFrais($ficheFrais);
            $ligneNuitee->setFraisForfait($entityManager->getRepository(FraisForfait::class)->find(3));
            $ligneNuitee->setQuantite(0);
            $ligneRepas = new LigneFraisForfait();
            $ligneRepas->setFicheFrais($ficheFrais);
            $ligneRepas->setFraisForfait($entityManager->getRepository(FraisForfait::class)->find(4));
            $ligneRepas->setQuantite(0);
            $ficheFrais->addLigneFraisForfait($ligneKm);
            $ficheFrais->addLigneFraisForfait($ligneEtape);
            $ficheFrais->addLigneFraisForfait($ligneNuitee);
            $ficheFrais->addLigneFraisForfait($ligneRepas);
            $entityManager->persist($ficheFrais);
            $entityManager->flush();

        }


        // If the fiche frais does not exist, create a new one
        $form = $this->createForm(SaisieFicheFraisType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {



            return $this->redirectToRoute('app_fiche_frais', ['id' => $ficheFrais->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('fiche_frais/new.html.twig', [
            'ficheFrais' => $ficheFrais,
            'form' => $form,
        ]);
    }
}

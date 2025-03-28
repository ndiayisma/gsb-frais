<?php

namespace App\Controller;

use App\Entity\FicheFrais;
use App\Entity\FraisForfait;
use App\Entity\LigneFraisForfait;
use App\Entity\Etat;
use App\Entity\LigneFraisHorsForfait;
use App\Form\LigneFraisHorsForfaitType;
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

            //Pour enregistrer les modifications de la fiche du mois en cours dans la BDD
            $entityManager->persist($fiche);
            $entityManager->flush();

            /*// Redirect to avoid form resubmission
            return $this->redirectToRoute('app_fiche_frais');*/

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
        $mois = new \DateTime();
        $mois->modify('first day of this month');



        // Check if the fiche frais already exists for the user and the given month
        $ficheFrais = $entityManager->getRepository(FicheFrais::class)->findOneBy([
            'user' => $user,
            'mois' => $mois,
        ]);

        if (!$ficheFrais) {
            // If the fiche frais does not exist, create a new one
            $ficheFrais = new FicheFrais();
            $ficheFrais->setUser($user);
            $ficheFrais->setMois($mois);
            $ficheFrais->setMontantValid(0);
            $ficheFrais->setNbJustifications(0);
            $ficheFrais->setDateModif(new \DateTime());
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


        // Si la fiche existe, alors il n'y a pas besoin de le créer, il faudra donc le modifier
        $formSaisie = $this->createForm(SaisieFicheFraisType::class,null, [
            'km' => $ficheFrais->getLigneFraisForfaits()->get(0)->getQuantite(),
            'etape' => $ficheFrais->getLigneFraisForfaits()->get(1)->getQuantite(),
            'nuit' => $ficheFrais->getLigneFraisForfaits()->get(2)->getQuantite(),
            'resto' => $ficheFrais->getLigneFraisForfaits()->get(3)->getQuantite(),
        ]);
        $formSaisie->handleRequest($request);
        $ligneFraisHorsForfait = new LigneFraisHorsForfait();
        $formHF = $this->createForm(LigneFraisHorsForfaitType::class, $ligneFraisHorsForfait);
        $formHF->handleRequest($request);


        if ($formSaisie->isSubmitted() && $formSaisie->isValid()) {
            $data = $formSaisie->getData();

            // Mise à jour des quantités des lignes de frais forfait
            $ficheFrais->getLigneFraisForfaits()->get(0)->setQuantite($data['km']);
            $ficheFrais->getLigneFraisForfaits()->get(1)->setQuantite($data['etape']);
            $ficheFrais->getLigneFraisForfaits()->get(2)->setQuantite($data['nuit']);
            $ficheFrais->getLigneFraisForfaits()->get(3)->setQuantite($data['resto']);

            $entityManager->persist($ficheFrais);
            $entityManager->flush();
        }//else{
            //$formSaisie->get('km')->setData($ficheFrais->getLigneFraisForfaits()->get(0)->getQuantite());
        //}

        if ($formHF->isSubmitted() && $formHF->isValid()) {
            //dd($ligneFraisHorsForfait);
            $dataHF = $formHF->getData();

            $ligneFraisHorsForfait = new LigneFraisHorsForfait();
            $ligneFraisHorsForfait->setLibelle($dataHF->getLibelle());
            $ligneFraisHorsForfait->setMontant($dataHF->getMontant());
            $ligneFraisHorsForfait->setDate($mois);
            $ligneFraisHorsForfait->setFicheFrais($ficheFrais);

            $ficheFrais->addLigneFraisHorsForfait($ligneFraisHorsForfait);
            $entityManager->persist($ligneFraisHorsForfait);
            $entityManager->flush();


        }


        return $this->render('fiche_frais/new.html.twig', [
            'ficheFrais' => $ficheFrais,
            'form' => $formSaisie,
            'formHF' => $formHF
        ]);
    }
}

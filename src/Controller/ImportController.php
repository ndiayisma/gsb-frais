<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\FicheFrais;
use App\Entity\FraisForfait;
use App\Entity\LigneFraisForfait;
use App\Entity\LigneFraisHorsForfait;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ImportController extends AbstractController
{
    private UserPasswordHasherInterface $passwordHasher;
    private EntityManagerInterface $entityManager;


    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager)
    {
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;

    }

    #[Route('/import/user', name: 'app_importUser')]
    public function importUser(): Response
    {
        $jsonfile = $this->getParameter('kernel.project_dir') . '/public/visiteur.json';
        $jsondata = file_get_contents($jsonfile);
        $data = json_decode($jsondata);

        foreach ($data as $visitor) {
            $user = new User();
            $user->setLogin($visitor->login);
            $hashedPassword = $this->passwordHasher->hashPassword($user, $visitor->mdp);
            $user->setPassword($hashedPassword);

            // Check if roles property exists
            if (property_exists($visitor, 'roles')) {
                $user->setRoles($visitor->roles);
            } else {
                $user->setRoles(['ROLE_USER']); // Set a default role if not present
            }
            $user->setEmail($visitor->nom . $visitor->prenom . '@gsb.fr');
            $user->setNom($visitor->nom);
            $user->setPrenom($visitor->prenom);
            $user->setAdresse($visitor->adresse);
            $user->setCp($visitor->cp);
            $user->setVille($visitor->ville);
            $user->setDateEmbauche(new \DateTime($visitor->dateEmbauche));
            $user->setOldId($visitor->id);

            $this->entityManager->persist($user);


        }

        $this->entityManager->flush();
        return $this->render('import/index.html.twig', [
            'controller_name' => 'ImportController',
            'data' => $data,
        ]);
    }
    #[Route('/import/fichefrais', name: 'app_importFicheFrais')]
    public function importFicheFrais(): Response
    {
        $jsonfile = $this->getParameter('kernel.project_dir') . '/public/fichefrais.json';
        $jsondata = file_get_contents($jsonfile);
        $data = json_decode($jsondata);

        foreach ($data as $fichefraisImport) {
            $ficheFrais = new FicheFrais();
            $month = \DateTime::createFromFormat('Ym', $fichefraisImport->mois);
            $month->modify('first day of this month');
            $ficheFrais->setMois($month);
            $ficheFrais->setMontantValid($fichefraisImport->montantValide);
            $ficheFrais->setNbJustifications($fichefraisImport->nbJustificatifs);
            $ficheFrais->setDateModif(new \DateTime($fichefraisImport->dateModif)); //Enregistrer la date au premier du mois

            $user = $this->entityManager->getRepository(User::class)->findOneBy(['oldId' => $fichefraisImport->idVisiteur]);

            switch ($fichefraisImport->idEtat) {
                case 'RB':
                    $idEtat = 1; // Remboursé
                    break;
                case 'VA':
                    $idEtat = 2; // Validé
                    break;
                case 'CL':
                    $idEtat = 3; // Clôturé
                    break;
                case 'CR':
                    $idEtat = 4; // Créé
                    break;

            }


            $etat = $this->entityManager->getRepository(Etat::class)->find(['id' => $idEtat]);
            $ficheFrais->setUser($user);


            $ficheFrais->setEtat($etat);
            $this->entityManager->persist($ficheFrais);

        }

        $this->entityManager->flush();
        return $this->render('import/index.html.twig', [
            'controller_name' => 'ImportController',
            'data' => $data,
        ]);
    }

    #[Route('/import/lignefraisforfait', name: 'app_importLigneFraisForfait')]
    public function importLigneFraisForfait(): Response
    {
        $jsonfile = $this->getParameter('kernel.project_dir') . '/public/lignefraisforfait.json';
        $jsondata = file_get_contents($jsonfile);
        $data = json_decode($jsondata);

        foreach ($data as $ligneFraisForfaitImport) {
            $ligneFraisForfait = new LigneFraisForfait();
            $ligneFraisForfait->setQuantite($ligneFraisForfaitImport->quantite);
            $month = \DateTime::createFromFormat('Ym', $ligneFraisForfaitImport->mois);
            $month->modify('first day of this month');
            switch ($ligneFraisForfaitImport->idFraisForfait) {
                case 'ETP':
                    $idFraisForfait = 1; // Forfait Etape
                    break;
                case 'KM':
                    $idFraisForfait = 2; // Frais Kilométrique
                    break;
                case 'NUI':
                    $idFraisForfait = 3; // Nuitée Hôtel
                    break;
                case 'REP':
                    $idFraisForfait = 4; // Repas Restaurant
                    break;

            }
            $user = $this->entityManager->getRepository(User::class)->findOneBy(['oldId' => $ligneFraisForfaitImport->idVisiteur]);
            $ficheFrais = $this->entityManager->getRepository(FicheFrais::class)->findOneBy(['mois' => $month, 'user' => $user]);
            $forfait = $this->entityManager->getRepository(FraisForfait::class)->find(['id' => $idFraisForfait]);



            $ligneFraisForfait->setFraisForfait($forfait);
            $ligneFraisForfait->setFicheFrais($ficheFrais);
            $this->entityManager->persist($ligneFraisForfait);

        }

        $this->entityManager->flush();
        return $this->render('import/index.html.twig', [
            'controller_name' => 'ImportController',
            'data' => $data,
        ]);
    }




    #[Route('/import/lignefraishorsforfait', name: 'app_importLigneFraisHorsForfait')]
    public function importLigneFraisHorsForfait(): Response
    {
        $jsonfile = $this->getParameter('kernel.project_dir') . '/public/lignefraishorsforfait.json';
        $jsondata = file_get_contents($jsonfile);
        $data = json_decode($jsondata);

        foreach ($data as $ligneFraisHorsForfaitImport) {
            $ligneFraisHorsForfait = new LigneFraisHorsForfait();
            $ligneFraisHorsForfait->setLibelle($ligneFraisHorsForfaitImport->libelle);
            $ligneFraisHorsForfait->setMontant($ligneFraisHorsForfaitImport->montant);
            $ligneFraisHorsForfait->setDate(new \DateTime($ligneFraisHorsForfaitImport->date));
            $ligneFraisHorsForfait->setAValider(true);
            $month = \DateTime::createFromFormat('Ym', $ligneFraisHorsForfaitImport->mois);
            $month->modify('first day of this month');

            $user = $this->entityManager->getRepository(User::class)->findOneBy(['oldId' => $ligneFraisHorsForfaitImport->idVisiteur]);
            $ficheFrais = $this->entityManager->getRepository(FicheFrais::class)->findOneBy(['mois' => $month, 'user' => $user]);
            $ligneFraisHorsForfait->setFicheFrais($ficheFrais);
            $this->entityManager->persist($ligneFraisHorsForfait);

        }

        $this->entityManager->flush();
        return $this->render('import/index.html.twig', [
            'controller_name' => 'ImportController',
            'data' => $data,
        ]);
    }
}


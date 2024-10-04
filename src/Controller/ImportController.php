<?php

namespace App\Controller;

use App\Entity\FicheFrais;
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
            $ficheFrais->setMois($month);
            $ficheFrais->setMontantValid($fichefraisImport->montantValide);
            $ficheFrais->setNbJustifications($fichefraisImport->nbJustificatifs);
            $ficheFrais->setDateModif(new \DateTime($fichefraisImport->dateModif));
            $ficheFrais->setEtat($fichefraisImport->idEtat);


            $this->entityManager->persist($ficheFrais);


        }



        $this->entityManager->flush();
        return $this->render('import/index.html.twig', [
            'controller_name' => 'ImportController',
            'data' => $data,
        ]);
    }
}


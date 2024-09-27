<?php

namespace App\Controller;

use App\Entity\User;
use phpDocumentor\Reflection\Types\Void_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ImportController extends AbstractController
{
    #[Route('/import', name: 'app_import')]
    public function index(): Response
    {
        $jsonfile = $this->getParameter('kernel.project_dir') . '/public/visiteur.json';
        $jsondata = file_get_contents($jsonfile);
        $data = json_decode($jsondata);

        foreach($data as $visitor) {
            $user = new User();
            $user->setLogin($visitor->login);
            $user->setPassword($visitor->mdp);
            $user->setRoles($visitor->roles);
            $user->setEmail($visitor->email);
            $user->setNom($visitor->nom);
            $user->setPrenom($visitor->prenom);
            $user->setAdresse($visitor->adresse);
            $user->setCp($visitor->cp);
            $user->setVille($visitor->ville);
            $user->setDateEmbauche(new \DateTime($visitor->dateEmbauche));


        }

        return $this->render('import/index.html.twig', [
            'controller_name' => 'ImportController',
            'data' => $data,
        ]);
    }
}

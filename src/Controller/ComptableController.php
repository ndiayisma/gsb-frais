<?php

namespace App\Controller;

use App\Entity\FicheFrais;
use App\Entity\FraisForfait;
use App\Entity\LigneFraisForfait;
use App\Entity\Etat;
use App\Form\MoisFicheType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class ComptableController extends AbstractController
{
    #[Route('/comptable', name: 'app_comptable', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager, ManagerRegistry $doctrine): Response
    {
        $moisList = [
            '01' => 'Janvier',
            '02' => 'Février',
            '03' => 'Mars',
            '04' => 'Avril',
            '05' => 'Mai',
            '06' => 'Juin',
            '07' => 'Juillet',
            '08' => 'Août',
            '09' => 'Septembre',
            '10' => 'Octobre',
            '11' => 'Novembre',
            '12' => 'Décembre',
        ];



        if($request->isMethod('POST')){
            $selectedMonth = '2024' .$request->request->get('mois');
            $selectedDate = \DateTime::createFromFormat('Ym', $selectedMonth);


            if ($selectedDate === false) {
                throw new \Exception('Invalid date format');
            }
            $selectedDate->modify('first day of this month');
            $fichesFrais = $doctrine->getRepository(FicheFrais::class)->findBy(['mois' => $selectedDate]);
            usort($fichesFrais, function($a, $b){
                return $b->getMontantValid() <=> $a->getMontantValid();
            });

            //keep the top 3
            $fichesFrais = array_slice($fichesFrais, 0, 3);
        }

        return $this->render('comptable/index.html.twig', [

            'fichesFrais' => $fichesFrais ?? [],
            'moisList' => $moisList,
            'selectedMonth' => $selectedMonth ?? null
        ]);
    }
}

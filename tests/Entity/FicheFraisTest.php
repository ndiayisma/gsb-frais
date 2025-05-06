<?php

namespace App\Tests\Entity;


use App\Entity\FraisForfait;
use App\Entity\LigneFraisForfait;
use App\Entity\FicheFrais;
use App\Entity\LigneFraisHorsForfait;
use App\Entity\Categorie;
use PHPUnit\Framework\TestCase;

class FicheFraisTest extends \PHPUnit\Framework\TestCase
{
    public function testGetTotalFraisForfaitises()
    {
        $ficheFrais = new FicheFrais();

        // Création des frais forfaitisés
        $frais1 = new FraisForfait();
        $frais1->setMontant(10.0);

        $ligne1 = new LigneFraisForfait();
        $ligne1->setFraisForfait($frais1);
        $ligne1->setQuantite(2); // 10 * 2 = 20

        $frais2 = new FraisForfait();
        $frais2->setMontant(15.0);

        $ligne2 = new LigneFraisForfait();
        $ligne2->setFraisForfait($frais2);
        $ligne2->setQuantite(3); // 15 * 3 = 45

        // Ajout des lignes à la fiche de frais
        $ficheFrais->addLigneFraisForfait($ligne1);
        $ficheFrais->addLigneFraisForfait($ligne2);

        // Calcul attendu : 20 + 45 = 65
        $this->assertEquals(65.0, $ficheFrais->getTotalFraisForfaitises());
    }

    public function testGetTotalLFHF(): void
    {
        $ficheFrais = new FicheFrais();

        // Création des lignes de frais hors forfait
        $ligne1 = new LigneFraisHorsForfait();
        $ligne1->setLibelle('Repas professionnel');
        $ligne1->setMontant(50.0);

        $ligne2 = new LigneFraisHorsForfait();
        $ligne2->setLibelle('Hôtel');
        $ligne2->setMontant(100.0);

        $ligne3 = new LigneFraisHorsForfait();
        $ligne3->setLibelle('REFUSÉ - Déplacement');
        $ligne3->setMontant(30.0);

        // Ajout des lignes à la fiche de frais
        $ficheFrais->addLigneFraisHorsForfait($ligne1);
        $ficheFrais->addLigneFraisHorsForfait($ligne2);
        $ficheFrais->addLigneFraisHorsForfait($ligne3);

        // Calcul attendu : 50 + 100 = 150 (ligne3 exclue)
        $this->assertEquals(150.0, $ficheFrais->getTotalLFHF());
    }

}
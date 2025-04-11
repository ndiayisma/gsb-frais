<?php

namespace App\Tests\Entity;

use App\Entity\FraisForfait;
use App\Entity\LigneFraisForfait;
use PHPUnit\Framework\TestCase;

class LigneFraisForfaitTest extends TestCase
{
    public function testGetTotalAmount()
    {
        $fraisForfait = new FraisForfait();
        $fraisForfait->setMontant('10.00');

        $ligneFraisForfait = new LigneFraisForfait();
        $ligneFraisForfait->setQuantite(5);
        $ligneFraisForfait->setFraisForfait($fraisForfait);

        $this->assertEquals('50.00', $ligneFraisForfait->getTotalAmount());
    }

}

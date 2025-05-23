<?php

namespace App\Entity;

use App\Repository\LigneFraisForfaitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LigneFraisForfaitRepository::class)]
class LigneFraisForfait
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $quantite = null;

    #[ORM\ManyToOne(inversedBy: 'ligneFraisForfaits')]
    private ?FicheFrais $ficheFrais = null;

    #[ORM\ManyToOne(inversedBy: 'ligneFraisForfaits')]
    private ?FraisForfait $fraisForfait = null;

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(?int $quantite): static
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getFicheFrais(): ?FicheFrais
    {
        return $this->ficheFrais;
    }

    public function setFicheFrais(?FicheFrais $ficheFrais): static
    {
        $this->ficheFrais = $ficheFrais;

        return $this;
    }

    public function getFraisForfait(): ?FraisForfait
    {
        return $this->fraisForfait;
    }

    public function setFraisForfait(?FraisForfait $fraisForfait): static
    {
        $this->fraisForfait = $fraisForfait;

        return $this;
    }

    public function getTotalAmount(): float
    {
        return $this->getFraisForfait()->getMontant() * $this->getQuantite();
    }

}

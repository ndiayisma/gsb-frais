<?php

namespace App\Entity;

use App\Repository\EtatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtatRepository::class)]
class Etat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    /**
     * @var Collection<int, FicheFrais>
     */
    #[ORM\OneToMany(targetEntity: FicheFrais::class, mappedBy: 'etat')]
    private Collection $fichesDeFrais;

    public function __construct()
    {
        $this->fichesDeFrais = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection<int, FicheFrais>
     */
    public function getFichesDeFrais(): Collection
    {
        return $this->fichesDeFrais;
    }

    public function addFichesDeFrai(FicheFrais $fichesDeFrai): static
    {
        if (!$this->fichesDeFrais->contains($fichesDeFrai)) {
            $this->fichesDeFrais->add($fichesDeFrai);
            $fichesDeFrai->setEtat($this);
        }

        return $this;
    }

    public function removeFichesDeFrai(FicheFrais $fichesDeFrai): static
    {
        if ($this->fichesDeFrais->removeElement($fichesDeFrai)) {
            // set the owning side to null (unless already changed)
            if ($fichesDeFrai->getEtat() === $this) {
                $fichesDeFrai->setEtat(null);
            }
        }

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\FicheFraisRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FicheFraisRepository::class)]
class FicheFrais
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $mois = null;

    #[ORM\Column(nullable: true)]
    private ?int $nbJustifications = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $montantValid = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateModif = null;

    #[ORM\ManyToOne(inversedBy: 'fichesDeFrais')]
    private ?User $user = null;

    /**
     * @var Collection<int, LigneFraisHorsForfait>
     */
    #[ORM\OneToMany(targetEntity: LigneFraisHorsForfait::class, mappedBy: 'ficheFrais')]
    private Collection $ligneFraisHorsForfaits;

    /**
     * @var Collection<int, LigneFraisForfait>
     */
    #[ORM\OneToMany(targetEntity: LigneFraisForfait::class, mappedBy: 'ficheFrais')]
    private Collection $ligneFraisForfaits;

    #[ORM\ManyToOne(inversedBy: 'fichesDeFrais')]
    private ?Etat $etat = null;

    public function __construct()
    {
        $this->ligneFraisHorsForfaits = new ArrayCollection();
        $this->ligneFraisForfaits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMois(): ?\DateTimeInterface
    {
        return $this->mois;
    }

    public function setMois(\DateTimeInterface $mois): static
    {
        $this->mois = $mois;

        return $this;
    }

    public function getNbJustifications(): ?int
    {
        return $this->nbJustifications;
    }

    public function setNbJustifications(?int $nbJustifications): static
    {
        $this->nbJustifications = $nbJustifications;

        return $this;
    }

    public function getMontantValid(): ?string
    {
        return $this->montantValid;
    }

    public function setMontantValid(string $montantValid): static
    {
        $this->montantValid = $montantValid;

        return $this;
    }

    public function getDateModif(): ?\DateTimeInterface
    {
        return $this->dateModif;
    }

    public function setDateModif(\DateTimeInterface $dateModif): static
    {
        $this->dateModif = $dateModif;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, LigneFraisHorsForfait>
     */
    public function getLigneFraisHorsForfaits(): Collection
    {
        return $this->ligneFraisHorsForfaits;
    }

    public function addLigneFraisHorsForfait(LigneFraisHorsForfait $ligneFraisHorsForfait): static
    {
        if (!$this->ligneFraisHorsForfaits->contains($ligneFraisHorsForfait)) {
            $this->ligneFraisHorsForfaits->add($ligneFraisHorsForfait);
            $ligneFraisHorsForfait->setFicheFrais($this);
        }

        return $this;
    }

    public function removeLigneFraisHorsForfait(LigneFraisHorsForfait $ligneFraisHorsForfait): static
    {
        if ($this->ligneFraisHorsForfaits->removeElement($ligneFraisHorsForfait)) {
            // set the owning side to null (unless already changed)
            if ($ligneFraisHorsForfait->getFicheFrais() === $this) {
                $ligneFraisHorsForfait->setFicheFrais(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, LigneFraisForfait>
     */
    public function getLigneFraisForfaits(): Collection
    {
        return $this->ligneFraisForfaits;
    }

    public function addLigneFraisForfait(LigneFraisForfait $ligneFraisForfait): static
    {
        if (!$this->ligneFraisForfaits->contains($ligneFraisForfait)) {
            $this->ligneFraisForfaits->add($ligneFraisForfait);
            $ligneFraisForfait->setFicheFrais($this);
        }

        return $this;
    }

    public function removeLigneFraisForfait(LigneFraisForfait $ligneFraisForfait): static
    {
        if ($this->ligneFraisForfaits->removeElement($ligneFraisForfait)) {
            // set the owning side to null (unless already changed)
            if ($ligneFraisForfait->getFicheFrais() === $this) {
                $ligneFraisForfait->setFicheFrais(null);
            }
        }

        return $this;
    }

    public function getEtat(): ?Etat
    {
        return $this->etat;
    }

    public function setEtat(?Etat $etat): static
    {
        $this->etat = $etat;

        return $this;
    }
}

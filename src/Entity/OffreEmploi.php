<?php

namespace App\Entity;

use App\Repository\OffreEmploiRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OffreEmploiRepository::class)
 */
class OffreEmploi
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="date")
     */
    private $date_Offre;

    /**
     * @ORM\ManyToMany(targetEntity=Candidat::class, inversedBy="offreEmplois")
     */
    private $Candidat;

    /**
     * @ORM\ManyToOne(targetEntity=Recruteur::class, inversedBy="OffreEmploi")
     * @ORM\JoinColumn(nullable=false)
     */
    private $recruteur;

    public function __construct()
    {
        $this->Candidat = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDateOffre(): ?\DateTimeInterface
    {
        return $this->date_Offre;
    }

    public function setDateOffre(\DateTimeInterface $date_Offre): self
    {
        $this->date_Offre = $date_Offre;

        return $this;
    }

    /**
     * @return Collection<int, Candidat>
     */
    public function getCandidat(): Collection
    {
        return $this->Candidat;
    }

    public function addCandidat(Candidat $candidat): self
    {
        if (!$this->Candidat->contains($candidat)) {
            $this->Candidat[] = $candidat;
        }

        return $this;
    }

    public function removeCandidat(Candidat $candidat): self
    {
        $this->Candidat->removeElement($candidat);

        return $this;
    }

    public function getRecruteur(): ?Recruteur
    {
        return $this->recruteur;
    }

    public function setRecruteur(?Recruteur $recruteur): self
    {
        $this->recruteur = $recruteur;

        return $this;
    }
}

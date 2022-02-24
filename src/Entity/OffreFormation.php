<?php

namespace App\Entity;

use App\Repository\OffreFormationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OffreFormationRepository::class)
 */
class OffreFormation
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
    private $lieu_formation;

    /**
     * @ORM\Column(type="integer")
     */
    private $duree_formation;

    /**
     * @ORM\ManyToMany(targetEntity=Candidat::class, inversedBy="offreFormations")
     */
    private $Candidat;

    /**
     * @ORM\ManyToOne(targetEntity=Recruteur::class, inversedBy="OffreFormation")
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

    public function getLieuFormation(): ?string
    {
        return $this->lieu_formation;
    }

    public function setLieuFormation(string $lieu_formation): self
    {
        $this->lieu_formation = $lieu_formation;

        return $this;
    }

    public function getDureeFormation(): ?int
    {
        return $this->duree_formation;
    }

    public function setDureeFormation(int $duree_formation): self
    {
        $this->duree_formation = $duree_formation;

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
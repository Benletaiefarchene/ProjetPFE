<?php

namespace App\Entity;

use App\Repository\CandidatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CandidatRepository::class)
 */
class Candidat extends User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=CV::class, cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $CV;

    /**
     * @ORM\ManyToMany(targetEntity=OffreEmploi::class, mappedBy="Candidat")
     */
    private $offreEmplois;

    /**
     * @ORM\ManyToMany(targetEntity=OffreFormation::class, mappedBy="Candidat")
     */
    private $offreFormations;

    /**
     * @ORM\ManyToMany(targetEntity=Forum::class, mappedBy="Candidat")
     */
    private $forums;

    public function __construct()
    {
        $this->offreEmplois = new ArrayCollection();
        $this->offreFormations = new ArrayCollection();
        $this->forums = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCV(): ?CV
    {
        return $this->CV;
    }

    public function setCV(CV $CV): self
    {
        $this->CV = $CV;

        return $this;
    }

    /**
     * @return Collection<int, OffreEmploi>
     */
    public function getOffreEmplois(): Collection
    {
        return $this->offreEmplois;
    }

    public function addOffreEmploi(OffreEmploi $offreEmploi): self
    {
        if (!$this->offreEmplois->contains($offreEmploi)) {
            $this->offreEmplois[] = $offreEmploi;
            $offreEmploi->addCandidat($this);
        }

        return $this;
    }

    public function removeOffreEmploi(OffreEmploi $offreEmploi): self
    {
        if ($this->offreEmplois->removeElement($offreEmploi)) {
            $offreEmploi->removeCandidat($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, OffreFormation>
     */
    public function getOffreFormations(): Collection
    {
        return $this->offreFormations;
    }

    public function addOffreFormation(OffreFormation $offreFormation): self
    {
        if (!$this->offreFormations->contains($offreFormation)) {
            $this->offreFormations[] = $offreFormation;
            $offreFormation->addCandidat($this);
        }

        return $this;
    }

    public function removeOffreFormation(OffreFormation $offreFormation): self
    {
        if ($this->offreFormations->removeElement($offreFormation)) {
            $offreFormation->removeCandidat($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Forum>
     */
    public function getForums(): Collection
    {
        return $this->forums;
    }

    public function addForum(Forum $forum): self
    {
        if (!$this->forums->contains($forum)) {
            $this->forums[] = $forum;
            $forum->addCandidat($this);
        }

        return $this;
    }

    public function removeForum(Forum $forum): self
    {
        if ($this->forums->removeElement($forum)) {
            $forum->removeCandidat($this);
        }

        return $this;
    }
}

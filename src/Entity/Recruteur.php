<?php

namespace App\Entity;

use App\Repository\RecruteurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RecruteurRepository::class)
 */
class Recruteur extends User
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
    private $eamil;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pays;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $photo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ville;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $diplome;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $langue_preferee;

    /**
     * @ORM\OneToMany(targetEntity=OffreEmploi::class, mappedBy="recruteur")
     */
    private $OffreEmploi;

    /**
     * @ORM\OneToMany(targetEntity=OffreFormation::class, mappedBy="recruteur")
     */
    private $OffreFormation;

    /**
     * @ORM\OneToMany(targetEntity=Forum::class, mappedBy="recruteur")
     */
    private $Forum;

    public function __construct()
    {
        $this->OffreEmploi = new ArrayCollection();
        $this->OffreFormation = new ArrayCollection();
        $this->Forum = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEamil(): ?string
    {
        return $this->eamil;
    }

    public function setEamil(string $eamil): self
    {
        $this->eamil = $eamil;

        return $this;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(string $pays): self
    {
        $this->pays = $pays;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getDiplome(): ?string
    {
        return $this->diplome;
    }

    public function setDiplome(string $diplome): self
    {
        $this->diplome = $diplome;

        return $this;
    }

    public function getLanguePreferee(): ?string
    {
        return $this->langue_preferee;
    }

    public function setLanguePreferee(string $langue_preferee): self
    {
        $this->langue_preferee = $langue_preferee;

        return $this;
    }

    /**
     * @return Collection<int, OffreEmploi>
     */
    public function getOffreEmploi(): Collection
    {
        return $this->OffreEmploi;
    }

    public function addOffreEmploi(OffreEmploi $offreEmploi): self
    {
        if (!$this->OffreEmploi->contains($offreEmploi)) {
            $this->OffreEmploi[] = $offreEmploi;
            $offreEmploi->setRecruteur($this);
        }

        return $this;
    }

    public function removeOffreEmploi(OffreEmploi $offreEmploi): self
    {
        if ($this->OffreEmploi->removeElement($offreEmploi)) {
            // set the owning side to null (unless already changed)
            if ($offreEmploi->getRecruteur() === $this) {
                $offreEmploi->setRecruteur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, OffreFormation>
     */
    public function getOffreFormation(): Collection
    {
        return $this->OffreFormation;
    }

    public function addOffreFormation(OffreFormation $offreFormation): self
    {
        if (!$this->OffreFormation->contains($offreFormation)) {
            $this->OffreFormation[] = $offreFormation;
            $offreFormation->setRecruteur($this);
        }

        return $this;
    }

    public function removeOffreFormation(OffreFormation $offreFormation): self
    {
        if ($this->OffreFormation->removeElement($offreFormation)) {
            // set the owning side to null (unless already changed)
            if ($offreFormation->getRecruteur() === $this) {
                $offreFormation->setRecruteur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Forum>
     */
    public function getForum(): Collection
    {
        return $this->Forum;
    }

    public function addForum(Forum $forum): self
    {
        if (!$this->Forum->contains($forum)) {
            $this->Forum[] = $forum;
            $forum->setRecruteur($this);
        }

        return $this;
    }

    public function removeForum(Forum $forum): self
    {
        if ($this->Forum->removeElement($forum)) {
            // set the owning side to null (unless already changed)
            if ($forum->getRecruteur() === $this) {
                $forum->setRecruteur(null);
            }
        }

        return $this;
    }
}

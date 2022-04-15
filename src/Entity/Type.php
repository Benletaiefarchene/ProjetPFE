<?php

namespace App\Entity;

use App\Repository\TypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TypeRepository::class)
 */
class Type
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
    private $type ;

    /**
     * @ORM\OneToMany(targetEntity=OffreEmploi::class, mappedBy="type")
     */
    private $OffreEmploi;

    public function __construct()
    {
        $this->OffreEmploi = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

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
            $offreEmploi->setType($this);
        }

        return $this;
    }

    public function removeOffreEmploi(OffreEmploi $offreEmploi): self
    {
        if ($this->OffreEmploi->removeElement($offreEmploi)) {
            // set the owning side to null (unless already changed)
            if ($offreEmploi->getType() === $this) {
                $offreEmploi->setType(null);
            }
        }

        return $this;
    }
    public function __toString(): string
    {
        return  $this->type;
    }
}

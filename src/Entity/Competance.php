<?php

namespace App\Entity;

use App\Repository\CompetanceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CompetanceRepository::class)
 */
class Competance
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
    private $Competance;

    /**
     * @ORM\JoinColumn(onDelete="CASCADE") 
     * @ORM\ManyToOne(targetEntity=CV::class, inversedBy="competances")
     * @ORM\JoinColumn(nullable=false)
     */
    private $CV;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompetance(): ?string
    {
        return $this->Competance;
    }

    public function setCompetance(string $Competance): self
    {
        $this->Competance = $Competance;

        return $this;
    }

    public function getCV(): ?CV
    {
        return $this->CV;
    }

    public function setCV(?CV $CV): self
    {
        $this->CV = $CV;

        return $this;
    }
}

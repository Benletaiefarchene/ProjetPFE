<?php

namespace App\Entity;

use App\Repository\OffreFormationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
    private $titre;

 

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

   /**
     * @ORM\Column(name="folder", type="string", length=500)
     * @Assert\File(mimeTypes={"application/zip", "application/rar"})
     */
    private $Folder;

    /**
     * @ORM\Column(type="boolean")
     */
    private $Role;

  

    // public function __construct()
    // {
    //     $this->Candidat = new ArrayCollection();
    // }

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
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

    public function getFolder(){
        return $this->Folder;
    }

    public function setFolder( $Folder)
    {
        $this->Folder = $Folder;

        return $this;
    }

    public function getRole(): ?bool
    {
        return $this->Role;
    }

    public function setRole(bool $Role): self
    {
        $this->Role = $Role;

        return $this;
    }

}

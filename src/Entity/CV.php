<?php

namespace App\Entity;

use App\Repository\CVRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CVRepository::class)
 */
class CV
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
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sexe;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $pays;

    /**
     * @ORM\Column(type="date")
     */
    private $datenaissance;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ville;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $languepreferee;

  /**
     * @ORM\Column(name="photo", type="string", length=500)
     * @Assert\File(mimeTypes={"image/jpeg", "image/jpg", "image/png", "image/GIF"})
     */
    private $photo;

/**
     * @ORM\Column(name="video", type="string", length=500)
     * @Assert\File(mimeTypes = {"video/mpeg", "video/mp4", "video/quicktime", "video/x-ms-wmv", "video/x-msvideo", "video/x-flv"})
     */
    private $video;
    


    /**
     * @ORM\OneToMany(targetEntity=Competance::class, mappedBy="CV" , cascade={"persist"} )
     */
    private $competances;

    /**
     * @ORM\OneToMany(targetEntity=Experience::class, mappedBy="CV", cascade={"persist"})
     */
    private $experiences;

    public function __construct()
    {
        $this->competances = new ArrayCollection();
        $this->experiences = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
    public function setId( $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(string $sexe): self
    {
        $this->sexe = $sexe;

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

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->datenaissance;
    }

    public function setDateNaissance(\DateTimeInterface $datenaissance): self
    {
        $this->datenaissance = $datenaissance;

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

    public function getLanguePreferee(): ?string
    {
        return $this->languepreferee;
    }

    public function setLanguePreferee(string $languepreferee): self
    {
        $this->languepreferee = $languepreferee;

        return $this;
    }

    public function getPhoto()
    {
        return $this->photo;
    }

    public function setPhoto($photo)
    {
        $this->photo = $photo;

        return $this;
    }
    public function getVideo()
    {
        return $this->video;
    }

    public function setVideo($video)
    {
        $this->video = $video;

        return $this;
    }

    /**
     * @return Collection<int, Competance>
     */
    public function getCompetances(): Collection
    {
        return $this->competances;
    }

    public function addCompetance(Competance $competance): self
    {
        if (!$this->competances->contains($competance)) {
            $this->competances[] = $competance;
            $competance->setCV($this);
        }

        return $this;
    }

    public function removeCompetance(Competance $competance): self
    {
        if ($this->competances->removeElement($competance)) {
            // set the owning side to null (unless already changed)
            if ($competance->getCV() === $this) {
                $competance->setCV(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Experience>
     */
    public function getExperiences(): Collection
    {
        return $this->experiences;
    }

    public function addExperience(Experience $experience): self
    {
        if (!$this->experiences->contains($experience)) {
            $this->experiences[] = $experience;
            $experience->setCV($this);
        }

        return $this;
    }

    public function removeExperience(Experience $experience): self
    {
        if ($this->experiences->removeElement($experience)) {
            // set the owning side to null (unless already changed)
            if ($experience->getCV() === $this) {
                $experience->setCV(null);
            }
             
        }

        return $this;
    }
    public function __toString() 
{ 
   return (string)$this->id; 
}
  
}

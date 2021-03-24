<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CompteRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CompteRepository::class)
 * @ApiResource(
 *          collectionOperations={
 *           "getCompteByAgence"={
 *               "method"="GET",
 *                   "path"="compte/{idAgence}/agence",
 *                   "normalization_context"={"groups"={"getCompteByAgence:read"}},
 *                   "security_message"="Vous n'avez pas access à cette Ressource"
 *          }
 * }
 *  )
 */
class Compte
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"depotCaissier:read","postc_un:read","getc_un","delc_un:read","annuleDepotByCaissier:read","getCompteByAgence","getUserById:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"depotCaissier:read","getc_un","delc_un:read","getCompteByAgence","getUserById:read"})
     */
    private $numCompte;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"depotCaissier:read","getc_un","getCompteByAgence","getUserById:read"})
     */
    private $solde;

    /**
     * @ORM\OneToMany(targetEntity=Depot::class, mappedBy="compte")
     */
    private $depots;

    /**
     * @ORM\ManyToOne(targetEntity=Agence::class, inversedBy="compte")
     * @Groups({"getCompteByAgence"})
     */
    private $agence;

    

    /**
     * @ORM\Column(type="boolean")
     */
    private $archivage;

    /**
     * @ORM\OneToMany(targetEntity=TRansaction::class, mappedBy="compteEnvoi")
     */
    private $tRansactions;

    public function __construct()
    {
        $this->depots = new ArrayCollection();
        $this->tRansactions = new ArrayCollection();
        
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumCompte(): ?int
    {
        return $this->numCompte;
    }

    public function setNumCompte(int $numCompte): self
    {
        $this->numCompte = $numCompte;

        return $this;
    }

    public function getSolde(): ?int
    {
        return $this->solde;
    }

    public function setSolde(int $solde): self
    {
        $this->solde = $solde;

        return $this;
    }

    /**
     * @return Collection|Depot[]
     */
    public function getDepots(): Collection
    {
        return $this->depots;
    }

    public function addDepot(Depot $depot): self
    {
        if (!$this->depots->contains($depot)) {
            $this->depots[] = $depot;
            $depot->setCompte($this);
        }

        return $this;
    }

    public function removeDepot(Depot $depot): self
    {
        if ($this->depots->removeElement($depot)) {
            // set the owning side to null (unless already changed)
            if ($depot->getCompte() === $this) {
                $depot->setCompte(null);
            }
        }

        return $this;
    }

    public function getAgence(): ?Agence
    {
        return $this->agence;
    }

    public function setAgence(?Agence $agence): self
    {
        $this->agence = $agence;

        return $this;
    }


    public function getArchivage(): ?bool
    {
        return $this->archivage;
    }

    public function setArchivage(bool $archivage): self
    {
        $this->archivage = $archivage;

        return $this;
    }

    /**
     * @return Collection|TRansaction[]
     */
    public function getTRansactions(): Collection
    {
        return $this->tRansactions;
    }

    public function addTRansaction(TRansaction $tRansaction): self
    {
        if (!$this->tRansactions->contains($tRansaction)) {
            $this->tRansactions[] = $tRansaction;
            $tRansaction->setCompteEnvoi($this);
        }

        return $this;
    }

    public function removeTRansaction(TRansaction $tRansaction): self
    {
        if ($this->tRansactions->removeElement($tRansaction)) {
            // set the owning side to null (unless already changed)
            if ($tRansaction->getCompteEnvoi() === $this) {
                $tRansaction->setCompteEnvoi(null);
            }
        }

        return $this;
    }
}

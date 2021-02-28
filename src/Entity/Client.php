<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=ClientRepository::class)
 */
class Client
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
    private $nomComplet;

    /**
     * @ORM\Column(type="integer")
     */
    private $phoneClient;

    /**
     * @ORM\Column(type="integer")
     */
    private $cni;


    /**
     * @ORM\Column(type="integer")
     */
    private $solde;

    /**
     * @ORM\OneToMany(targetEntity=TRansaction::class, mappedBy="clientDepot")
     */
    private $tRansactions;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $action;

    /**
     * @ORM\Column(type="integer")
     */
    private $codeTransaction;

    public function __construct()
    {
        $this->transaction = new ArrayCollection();
        $this->tRansactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomComplet(): ?string
    {
        return $this->nomComplet;
    }

    public function setNomComplet(string $nomComplet): self
    {
        $this->nomComplet = $nomComplet;

        return $this;
    }

    public function getPhoneClient(): ?int
    {
        return $this->phoneClient;
    }

    public function setPhoneClient(int $phoneClient): self
    {
        $this->phoneClient = $phoneClient;

        return $this;
    }

    public function getCni(): ?int
    {
        return $this->cni;
    }

    public function setCni(int $cni): self
    {
        $this->cni = $cni;

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
            $tRansaction->setClientDepot($this);
        }

        return $this;
    }

    public function removeTRansaction(TRansaction $tRansaction): self
    {
        if ($this->tRansactions->removeElement($tRansaction)) {
            // set the owning side to null (unless already changed)
            if ($tRansaction->getClientDepot() === $this) {
                $tRansaction->setClientDepot(null);
            }
        }

        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(string $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function getCodeTransaction(): ?int
    {
        return $this->codeTransaction;
    }

    public function setCodeTransaction(int $codeTransaction): self
    {
        $this->codeTransaction = $codeTransaction;

        return $this;
    }
}

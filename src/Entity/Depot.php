<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\DepotRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=DepotRepository::class)
 * @ApiResource(
 *      collectionOperations={
 *           "depotCaissier"={
 *               "method"="POST",
 *                   "path"="caissier/depot/compte",
 *                   "denormailzation_context"={"groups"={"depotCaissier:read"}},
 *                   "security"="is_granted('ROLE_CAISSIER')",
 *                   "security_message"="Vous n'avez pas access à cette Ressource"
 *          }
 *
 *}
 *  )
 * 
 */
class Depot
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"depotCaissier:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"depotCaissier:read"})
     * @var \DateTime
     */
    private $dateDepot;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"depotCaissier:read"})
     */
    private $montantDEpot;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="depot")
     * @Groups({"depotCaissier:read"})
     * @ApiSubresource()
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Compte::class, inversedBy="depots")
     * @Groups({"depotCaissier:read"})
     * @ApiSubresource()
     */
    private $compte;

    public  function __construct(){
        $this->dateDepot = new \DateTime();
        $this->Archivage = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDepot(): ?\DateTimeInterface
    {
        return $this->dateDepot;
    }

    public function setDateDepot(\DateTimeInterface $dateDepot): self
    {
        $this->dateDepot = $dateDepot;

        return $this;
    }

    public function getMontantDEpot(): ?int
    {
        return $this->montantDEpot;
    }

    public function setMontantDEpot(int $montantDEpot): self
    {
        $this->montantDEpot = $montantDEpot;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCompte(): ?Compte
    {
        return $this->compte;
    }

    public function setCompte(?Compte $compte): self
    {
        $this->compte = $compte;

        return $this;
    }
}

<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProfilRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ProfilRepository::class)
 * @ApiResource(routePrefix= "/admin",
 *      collectionOperations={"get",
 *          "get_trois"={
 *               "method"="GET",
 *                   "path"="/profils",
 *                   "normalization_context"={"groups"={"get_trois"}},
 *                   "security"="is_granted('ROLE_ADMINSYSTEM')",
 *                   "security_message"="Vous n'avez pas access à cette Ressource"
 *          },
 *          "post_trois"={
 *               "method"="POST",
 *                   "path"="/profils",
 *                   "denormalization_context"={"groups"={"post_trois"}},
 *                   "security"="is_granted('ROLE_ADMINSYSTEM')",
 *                   "security_message"="Vous n'avez pas access à cette Ressource"
 *          }
 * }
 * )
 */
class Profil
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"get_trois","post_un"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"get_trois","post_trois","post_un"})
     */
    private $libelle;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"get_trois","post_trois"})
     */
    private $archivage;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="profil")
     */
    private $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

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
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setProfil($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getProfil() === $this) {
                $user->setProfil(null);
            }
        }

        return $this;
    }
}

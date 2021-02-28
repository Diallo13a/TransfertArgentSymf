<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"ADMINSYSTEM"="AdminSystem","CAISSIER"="Caissier", "ADMINAGENCE"="AdminAgence" , "UTILISATEURAGENCE"="UtilisateurAgence" ,"user"="User"})
 * @ApiResource(routePrefix= "/admin",
 *      collectionOperations={
 *           "get_un"={
 *               "method"="GET",
 *                   "path"="/users",
 *                   "normalization_context"={"groups"={"get_un_ad:read"}},
 *                   "security"="is_granted('ROLE_ADMINSYSTEM')",
 *                   "security_message"="Vous n'avez pas access à cette Ressource"
 *          },
 *          "post_un"={
 *               "method"="POST",
 *                   "path"="/users",
 *                   "denormalization_context"={"groups"={"post_un"}},
 *                   "security"="is_granted('ROLE_ADMINSYSTEM')",
 *                   "security_message"="Vous n'avez pas access à cette Ressource"
 *          }
 * }
 *  )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"get_un_ad:read","depotCaissier:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"get_un_ad:read","post_un"})
     */
    private $email;

    
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string", nullable=true)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"get_un_ad:read","post_un","depotCaissier:read"})
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"get_un_ad:read","post_un","depotCaissier:read"})
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"get_un_ad:read","post_un","depotCaissier:read"})
     */
    private $prenom;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"get_un_ad:read","post_un"})
     */
    private $phone;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"get_un_ad:read","post_un"})
     */
    private $cni;

    /**
     * @ORM\Column(type="blob", nullable=true)
     */
    private $avatar;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get_un_ad:read","post_un"})
     */
    private $addresse;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"get_un_ad:read","post_un"})
     */
    private $archivage=0;

    /**
     * @ORM\ManyToOne(targetEntity=Profil::class, inversedBy="users")
     */
    private $profil;

    /**
     * @ORM\OneToMany(targetEntity=Depot::class, mappedBy="user")
     * @Groups({"depotCaissier:read"})
     */
    private $depot;

    /**
     * @ORM\ManyToOne(targetEntity=Agence::class, inversedBy="users")
     */
    private $agence;

    /**
     * @ORM\OneToMany(targetEntity=TRansaction::class, mappedBy="user")
     */
    private $transaction;

    public function __construct()
    {
        $this->depot = new ArrayCollection();
        $this->transaction = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_'.$this->profil->getLibelle();

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getPhone(): ?int
    {
        return $this->phone;
    }

    public function setPhone(int $phone): self
    {
        $this->phone = $phone;

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

    public function getAvatar()
    {
        return $this->avatar;
    }

    public function setAvatar($avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getAddresse(): ?string
    {
        return $this->addresse;
    }

    public function setAddresse(string $addresse): self
    {
        $this->addresse = $addresse;

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

    public function getProfil(): ?Profil
    {
        return $this->profil;
    }

    public function setProfil(?Profil $profil): self
    {
        $this->profil = $profil;

        return $this;
    }

    /**
     * @return Collection|Depot[]
     */
    public function getDepot(): Collection
    {
        return $this->depot;
    }

    public function addDepot(Depot $depot): self
    {
        if (!$this->depot->contains($depot)) {
            $this->depot[] = $depot;
            $depot->setUser($this);
        }

        return $this;
    }

    public function removeDepot(Depot $depot): self
    {
        if ($this->depot->removeElement($depot)) {
            // set the owning side to null (unless already changed)
            if ($depot->getUser() === $this) {
                $depot->setUser(null);
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

    /**
     * @return Collection|TRansaction[]
     */
    public function getTransaction(): Collection
    {
        return $this->transaction;
    }

    public function addTransaction(TRansaction $transaction): self
    {
        if (!$this->transaction->contains($transaction)) {
            $this->transaction[] = $transaction;
            $transaction->setUser($this);
        }

        return $this;
    }

    public function removeTransaction(TRansaction $transaction): self
    {
        if ($this->transaction->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getUser() === $this) {
                $transaction->setUser(null);
            }
        }

        return $this;
    }
}

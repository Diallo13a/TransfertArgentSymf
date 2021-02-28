<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\TarifRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=TarifRepository::class)
 */
class Tarif
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $borneInf;

    /**
     * @ORM\Column(type="integer")
     */
    private $borneSup;

    /**
     * @ORM\Column(type="integer")
     */
    private $fraisEnvoi;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBorneInf(): ?int
    {
        return $this->borneInf;
    }

    public function setBorneInf(int $borneInf): self
    {
        $this->borneInf = $borneInf;

        return $this;
    }

    public function getBorneSup(): ?int
    {
        return $this->borneSup;
    }

    public function setBorneSup(int $borneSup): self
    {
        $this->borneSup = $borneSup;

        return $this;
    }

    public function getFraisEnvoi(): ?int
    {
        return $this->fraisEnvoi;
    }

    public function setFraisEnvoi(int $fraisEnvoi): self
    {
        $this->fraisEnvoi = $fraisEnvoi;

        return $this;
    }
}

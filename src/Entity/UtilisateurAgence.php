<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\UtilisateurAgenceRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=UtilisateurAgenceRepository::class)
 */
class UtilisateurAgence extends User
{
    
    private $id;

    public function getId(): ?int
    {
        return $this->id;
    }
}

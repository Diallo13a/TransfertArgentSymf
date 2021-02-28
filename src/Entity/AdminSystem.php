<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\AdminSystemRepository;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=AdminSystemRepository::class)
 */
class AdminSystem extends User
{
    

    public function getId(): ?int
    {
        return $this->id;
    }
}

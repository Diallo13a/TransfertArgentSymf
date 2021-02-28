<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CaissierRepository;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ORM\Entity(repositoryClass=CaissierRepository::class)
 * @ApiResource(
 *   
 *)
 */
class Caissier extends User
{

   
}

<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CaissierRepository;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ORM\Entity(repositoryClass=CaissierRepository::class)
 * @ApiResource(routePrefix= "/admin",
 *      collectionOperations={
 *           "getcai_un"={
 *               "method"="GET",
 *                   "path"="/caissiers",
 *                   "normalization_context"={"groups"={"getcai_un:read"}},
 *                   "security"="is_granted('ROLE_ADMINSYSTEM')",
 *                   "security_message"="Vous n'avez pas access à cette Ressource"
 *          }
 *          
 * }
 *  
 *      
 *)
 */
class Caissier extends User
{

   
}

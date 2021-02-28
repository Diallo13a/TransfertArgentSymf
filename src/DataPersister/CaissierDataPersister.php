<?php
namespace App\DataPersister;


use App\Entity\Caissier;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;

final class CaissierDataPersister implements ContextAwareDataPersisterInterface
{

    public function __construct(
        EntityManagerInterface $entityManager
        
    ) 
    {
        $this->_entityManager = $entityManager;
       
    }
    public function supports($data, array $context = []): bool
    {
        return $data instanceof Caissier;
    }

    public function persist($data, array $context = [])
    {
      // call your persistence layer to save $data
      return $data;
    }

    public function remove($data, array $context = [])
    {
      // call your persistence layer to delete $data
    }
}
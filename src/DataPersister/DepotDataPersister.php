<?php
namespace App\DataPersister;


use App\Entity\Depot;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;

final class DepotDataPersister implements ContextAwareDataPersisterInterface
{

    public function __construct(
        EntityManagerInterface $entityManager
        
    ) 
    {
        $this->_entityManager = $entityManager;
       
    }
    public function supports($data, array $context = []): bool
    {
        return $data instanceof Depot;
    }

    public function persist($data, array $context = [])
    {
      // cumul sum compte after depot
      $data->getCompte()->setSolde($data->getCompte()->getSolde() + $data->getMontantDEpot());
      $this->_entityManager->persist($data);
      $this->_entityManager->flush();
    }

    public function remove($data, array $context = [])
    {
      // call your persistence layer to delete $data
    }
}
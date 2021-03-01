<?php


namespace App\DataPersister;


use App\Entity\Compte;
use App\Repository\UserRepository;
use App\Repository\CompteRepository;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;

final class ProfilDataPersister implements ContextAwareDataPersisterInterface
{

    /**
     * @var EntityManagerInterface
     */
    private $entitymanager;
    /**
     * @var UserRepository
     */
    private $userrepository;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository, CompteRepository $compteRepository){
        $this->entitymanager=$entityManager;
        $this->userrepository=$userRepository;
        $this->compterepository=$compteRepository;
        
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof Compte;
        // TODO: Implement supports() method.
    }

    public function persist($data, array $context = [])
    {
       
       $data->setNumCompte($data->getNumCompte());
       $data->setArchivage(true);
       $this->entitymanager->persist($data);
       $this->entitymanager->flush();

        return $data;
    }

    public function remove($data, array $context = [])
    {
        // TODO: Implement remove() method.
        $id = $data->getId();
        $comptes=$this->compterepository->findBy(['compte'=>$id]);

        $data->setArchivage(1);
        $persist=$this->entitymanager->persist($data);
        $this->entitymanager->flush($persist);

        foreach ($comptes as $value){
            $value->setArchivage(1);
            $compte=$this->entitymanager->persist($value);
            $this->entitymanager->flush($compte);
        }
    }
}
<?php


namespace App\DataPersister;


use App\Entity\Profil;
use App\Repository\UserRepository;
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

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository){
        $this->entitymanager=$entityManager;
        $this->userrepository=$userRepository;
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof Profil;
        // TODO: Implement supports() method.
    }

    public function persist($data, array $context = [])
    {
       
       $data->setLibelle($data->getLibelle());
       $data->setArchivage(true);
       $this->entitymanager->persist($data);
       $this->entitymanager->flush();

        return $data;
    }

    public function remove($data, array $context = [])
    {
        // TODO: Implement remove() method.
        $id = $data->getId();
        $users=$this->userrepository->findBy(['profil'=>$id]);

        $data->setArchivage(1);
        $persist=$this->entitymanager->persist($data);
        $this->entitymanager->flush($persist);

        foreach ($users as $value){
            $value->setArchivage(1);
            $user=$this->entitymanager->persist($value);
            $this->entitymanager->flush($user);
        }
    }
}
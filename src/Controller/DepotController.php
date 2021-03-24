<?php

namespace App\Controller;

use DateTime;
use App\Entity\Depot;
use App\Repository\UserRepository;
use App\Repository\DepotRepository;
use App\Repository\CompteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DepotController extends AbstractController
{

    public function __construct(SerializerInterface $serializerInterface, EntityManagerInterface $entityManagerInterface,
     DepotRepository $depotRepository, CompteRepository $compteRepository, UserRepository $userRepository)
     {
         $this->serializer = $serializerInterface;
         $this->entitymanagerinterface = $entityManagerInterface;
         $this->depotrepository = $depotRepository;
         $this->compterepository = $compteRepository;
         $this->userrepository = $userRepository;
    }
    /**
     * @Route(
     *      name="annuleDepotByCaissier" ,
     *      path="/api/caissier/annule/depot",
     *      methods={"PUT"},
     *      defaults={
     *         "__api_resource_class"=Compte::class ,
     *         "__api_collection_operation_name"="annuleDepotByCaissier"
     *     }
     *)
    */

    public function findLasDepotCompte(Request $request, Security $security)
    {
        
        
        //Objet de la table depot
        $LastDepot = $this->depotrepository->findOneBy([],['id'=>'desc']);
        // dd($LastDepot); // //id=24


        // Compte last depot cad compte_id
        $CompteLastDepot= $LastDepot->getCompte();
        // dd($CompteLastDepot);
        $CompteLastDepot->setSolde($CompteLastDepot->getSolde() - $LastDepot->getMontantDEpot());
        // dd($CompteLastDepot->getSolde());
        // dd($LastDepot->getMontantDEpot());
        
          

        $this->entitymanagerinterface->remove($LastDepot );
        
        
        $this->entitymanagerinterface->flush();

        return $this->json("success",201);

    }
}

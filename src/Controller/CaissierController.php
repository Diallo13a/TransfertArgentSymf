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

class CaissierController extends AbstractController   // Tout cet travail devrait etre dans DepotController pour faciliter la comprehension
{
    /**
     * @Route("/caissier", name="caissier")
     */
    public function index(): Response
    {
        return $this->render('caissier/index.html.twig', [
            'controller_name' => 'CaissierController',
        ]);
    }

    private $serializer;
    
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
     *      name="depotCaissier" ,
     *      path="/api/caissier/depot/compte",
     *      methods={"POST"},
     *      defaults={
     *         "__api_resource_class"=Depot::class ,
     *         "__api_collection_operation_name"="depotCaissier"
     *     }
     *)
    */

    public function depotByCaissier(Request $request, Security $security){
        
        // recup tous les donnees
        // $dataPostman = json_decode($request->getContent(), true);
        $dataPostman =  json_decode($request->getContent());
        //  dd($dataPostman);
        // denormalize 
        // $depot = $this->serializer->denormalize($dataPostman, Depot::class);
        $montant = $dataPostman->montantDEpot ; //get montant
        // dd($montant);
        $utilisateur = $dataPostman->user ; //get id utilisateur
        // dd($utilisateur);

         // Validate negatif number 
         if($montant < 0) {
            // return new JsonResponse("Can be negative number!" ,400) ; 
             return $this->json("le montant ne peut pas être négatif!",400);
         } 
        
          // // Instancier Depot
        $newDepot = new Depot();
        $newDepot->setDateDepot(new DateTime());    
        $newDepot->setmontantDEpot($dataPostman->montantDEpot);
        $newDepot->setUser($security->getUser());
         //get id agence of utilisateur cad id de l'utilisateur on cherche son correspondance de son agence
         $idAgence = $this->userrepository->findOneBy(['id'=>(int)$utilisateur])->getAgence()->getId();
        //  dd($idAgence);
        // Id de l'agence ci_dessus on cherche son compte
        $focusCompte = $this->compterepository->findBy(['agence'=>$idAgence]); //reper account
        dd($focusCompte);
        $newDepot->setCompte($focusCompte[0]);
        // dd($newDepot);
        $this->entitymanagerinterface->persist($newDepot);
        $focusCompte[0]->setSolde($focusCompte[0]->getSolde() + $montant);
        // dd($focusCompte);
        $this->entitymanagerinterface->persist($focusCompte[0]);
        $this->entitymanagerinterface->flush();

        return $this->json("Votre dépôt a réussi avec success!",201);


        
        // $depot->setDateDepot(new DateTime())
        //       ->setUser($security->getUser())
        //       ->getCompte()->setSolde($depot->getMontantDEpot());
           
        // dd(($depot->getCompte()));

         
        // $this->entitymanagerinterface->persist($depot);
        // $this->entitymanagerinterface->flush();

        // return $this->json("success",201);

        
        // // get numCompte
        // $numCompte = $dataPostman->numCompte ;
        // // dd($numCompte);   

        // // get idCaissier
        // $idCaissier = $dataPostman->idCaissier ;
        // // dd($idCaissier);

        // // Instancier Depot
        // $newDepot = new Depot();

        // $newDepot->setMontantDEpot($dataPostman->montantDEpot);
        // // dd($newDepot);
        // $newDepot->setUser($this->userrepository->findOneBy(['id'=>(int)$idCaissier]));
        // // dd($newDepot);
        // $newDepot->setCompte($this->compterepository->findOneBy(['id'=>(int)$numCompte]));
        // // dd($newDepot);
        // $numCompte = $this->compterepository->findOneBy(['id'=>(int)$numCompte]); 
        // // $numCompte = $dataPostman->numCompte ;
        // // dd($numCompte);
        // $numCompte->setSolde($numCompte->getSolde() + $dataPostman->montantDEpot);
        



    }
}

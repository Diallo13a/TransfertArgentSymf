<?php

namespace App\Controller;

use DateTime;
use App\Entity\Client;
use App\Entity\TRansaction;
use App\Repository\UserRepository;
use App\Repository\TarifRepository;
use App\Entity\SummarizeTransaction;
use App\Repository\ClientRepository;
use App\Repository\CompteRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TRansactionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TransactionController extends AbstractController
{
    /**
     * @Route("/transaction", name="transaction")
     */
    public function index(): Response
    {
        return $this->render('transaction/index.html.twig', [
            'controller_name' => 'TransactionController',
        ]);
    }

    private $serializer;
    public function __construct(SerializerInterface $serializerInterface, EntityManagerInterface $entityManagerInterface,
     TRansactionRepository $tRansactionRepository, ClientRepository $clientRepository, UserRepository $userRepository,CompteRepository $compterepository,
     TarifRepository $tarifRepository)
     {
         $this->serializer = $serializerInterface;
         $this->entitymanagerinterface = $entityManagerInterface;
         $this->transactionrepository = $tRansactionRepository;
         $this->clientrepository = $clientRepository;
         $this->userrepository = $userRepository;
         $this->compterepository = $compterepository;
         $this->tarifRepository = $tarifRepository;
    }

    public function getTarifs($montant) {
        $allTarifs = $this->tarifRepository->findAll();
       
        foreach($allTarifs as $value) {      
           if($value->getBorneInf() < $montant && $value->getBorneSup() >= $montant) {
               return $value->getFraisEnvoi() ;
           }
        }
   }

    /**
     * @Route(
     *      name="depotUserAgence" ,
     *      path="/api/useragence/depot/client",
     *      methods={"POST"},
     *      defaults={
     *         "__api_resource_class"=TRansaction::class ,
     *         "__api_collection_operation_name"="depotUserAgence"
     *     }
     *)
    */

    public function depotUserAgence(Request $request, Security $security){

        // $typeTransaction = array('envoi','retrait');
        // recup tous les donnees
        $dataPostman = json_decode($request->getContent(), true);
        // dd($dataPostman);
        // denormalize 
        $depot = $this->serializer->denormalize($dataPostman, TRansaction::class);
        // dd($depot);
        // dd($dataPostman["montant"]);
        

        $frais = $this->getTarifs($dataPostman["montant"]);
        // dd($frais);
        // Montant deposé
        //  dd($dataPostman["montant"]);
         $montantReelEnvoye  = $dataPostman["montant"] - $frais;
        //  dd($montantReelEnvoye);
         // frais etat
         $etat = $frais*0.4;
         // frais system
         $system = $frais*0.3;
         // frais depot
         $fraisDepot = $frais*0.1;
        //  dd($fraisDepot);
         // frais retrait
         $fraisRetrait = $frais*0.2;

         //Ce que j'ai reellement envoyé
        //  $compteFocus->setSolde(($compteFocus->getSolde() - $montantToSended) + $fraisEnvoie);
        $clientEnv = new Client();
        // dd($dataPostman["nomComplet_envoyeur"]);    


        $codeTransaction = rand(100,999)*243;
        $clientEnv->setSolde($montantReelEnvoye)
                  ->setnomComplet($dataPostman["nomComplet_envoyeur"])
                  ->setPhoneClient($dataPostman["phone_client_envoyeur"])
                  ->setCodeTransaction($codeTransaction)
                  ->setAction("depot")
                  ->setcni($dataPostman["cni_envoyeur"]);

                //   dd($clientEnv);

        $clientRecv = new Client();
        // dd($dataPostman["nomComplet_envoyeur"]);    

        $clientRecv->setSolde($montantReelEnvoye)
                  ->setnomComplet($dataPostman["nomComplet_receveur"])
                  ->setPhoneClient($dataPostman["phone_client_receveur"])
                  ->setCodeTransaction($codeTransaction)
                  ->setAction("transaction en cours...")
                  ->setcni($dataPostman["cni_receveur"]);

               //   dd($clientRecv);
                
         
        // On recupere le montant
        // $montant = $this->compterepository->find($dataPostman["montant"]);
        // dd($montant);

        // On recupere l'id compte
        $cpte = $this->compterepository->find($dataPostman["comptes"]);
        // dd($cpte);
        // Deduction du compte cad lors d'un depot cad actualisation 
        $dept = $cpte->setSolde(($cpte->getSolde() - ($dataPostman["montant"])) + $fraisDepot) ;


        // dd($dept);
       
       
        //     // Ajout du compte cad lors d'unretrait
        // $dept = $cpte->setSolde($cpte->getSolde() + $depot->getMontant());
       
        
       
        //($cpte->setSolde($cpte->getSolde() - $depot->getMontant()));

          // genere code transaction
          $numBeetween = rand(1, 10);  // choose number beetween 100-1000
          $date = new \DateTime('now');
          
          

        $depot->setDateDepot(new DateTime())
              ->setdateRetrait(new DateTime())
              ->setdateAnnulation(new DateTime())
              ->setUser($security->getUser())
              ->setTtc(100)
              ->setfraisEtat($etat)
              ->setfraisSystem($system)
              ->setfraisEnvoi($fraisDepot)
              ->setfraisRetrait($fraisRetrait)
              ->setCodeTransaction($codeTransaction)
              ->setClientDepot($clientEnv)
              ->setClientRetrait($clientRecv)
              ->setTypeTransaction("EnCours");

            //   ->getCompte()->setSolde($cpte->getSolde() - $depot->getMontant()); 
            //    + $depot->solde
            
        //    dd(($depot->getCompte()));
        

         
        $this->entitymanagerinterface->persist($depot);
        $this->entitymanagerinterface->persist($dept);
        $this->entitymanagerinterface->persist($clientEnv);
        $this->entitymanagerinterface->persist($clientRecv);
        $this->entitymanagerinterface->flush();

        return $this->json("success",201);

    }















    // /**
    //  * @Route(
    //  *      name="retraitUserAgence" ,
    //  *      path="/api/useragence/retrait/client",
    //  *      methods={"POST"},
    //  *      defaults={
    //  *         "__api_resource_class"=TRansaction::class ,
    //  *         "__api_collection_operation_name"="retraitUserAgence"
    //  *     }
    //  *)
    // */

    // public function retraitUserAgence(Request $request, Security $security){

    //     // $typeTransaction = array('envoi','retrait');
    //     // recup tous les donnees
    //     $dataPostman = json_decode($request->getContent(), true);
    //     // dd($dataPostman);
    //     // denormalize 
    //     $depot = $this->serializer->denormalize($dataPostman, TRansaction::class);
    //     // dd($dataPostman["comptes"]);

    //     // On recupere l'id compte
    //     $cpte = $this->compterepository->find($dataPostman["comptes"]);
       
    //     //     // Deduction du compte cad lors d'un depot
    //     // $dept = $cpte->setSolde($cpte->getSolde() - $depot->getMontant());
       
    //         // Ajout du compte cad lors d'unretrait
    //     $dept = $cpte->setSolde($cpte->getSolde() + $depot->getMontant());
       
        
       
    //     //($cpte->setSolde($cpte->getSolde() - $depot->getMontant()));

    //       // genere code transaction
    //       $numBeetween = rand(1, 100);  // choose number beetween 100-1000
    //       $date = new \DateTime('now');
    //       $genereCodeTransaction = ($numBeetween.date_format($date, 'YmdHi'));

    //     $depot->setDateDepot(new DateTime())
    //           ->setdateRetrait(new DateTime())
    //           ->setdateAnnulation(new DateTime())
    //           ->setUser($security->getUser())
    //           ->setTtc(100)
    //           ->setfraisEtat(40)
    //           ->setfraisSystem(30)
    //           ->setfraisEnvoi(10)
    //           ->setfraisRetrait(20)
    //           ->setCodeTransaction(1)
    //           ->setTypeTransaction("retrait");

    //         //   ->getCompte()->setSolde($cpte->getSolde() - $depot->getMontant()); 
    //         //    + $depot->solde
            
    //     //    dd(($depot->getCompte()));
        

         
    //     $this->entitymanagerinterface->persist($depot);
    //     $this->entitymanagerinterface->persist($dept);
    //     $this->entitymanagerinterface->flush();

    //     return $this->json("success",201);

    // }




    
    


    /**
     * @Route(
     *      name="retraitUserAgence" ,
     *      path="/api/useragence/retrait/client/{code}",
     *      methods={"PUT"},
     *      defaults={
     *         "__api_resource_class"=TRansaction::class ,
     *         "__api_collection_operation_name"="retraitUserAgence"
     *     }
     *)
    */
    public function retraitUserAgence(Request $request, SerializerInterface $serializer, $code)
    {
        $transactionDo =  $this->transactionrepository->findTransactionByCode($code) ;
         
        if($transactionDo) {
            
            if($transactionDo->getTypeTransaction() == "Reussie") {
                 return $this->json("Cette transaction est déjà retirée ", 400);  
            } else if($transactionDo->getTypeTransaction() == "Annulee"){
                 return $this->json("Cette transaction a étè annulée ", 400);  
            } else {
                // data given on postman
                $dataPostman =  json_decode($request->getContent());
                $idCompteCaissierGiven = $dataPostman->comptes;

                $time = new \DateTime();
                $transactionDo->setDateRetrait($time);
                $transactionDo->setTypeTransaction("Reussie");
                $transactionDo->setCompteRetrait($this->compterepository->findOneBy(['id'=>(int)$idCompteCaissierGiven]));
                $this->entitymanagerinterface->persist($transactionDo);
                // dd($transactionDo);
                
                $compteFocus =  $this->compterepository->findOneBy(['id'=>(int)$idCompteCaissierGiven]);
                $compteFocus->setSolde($compteFocus->getSolde() +$transactionDo->getMontant() + $transactionDo->getFraisRetrait());
                $this->entitymanagerinterface->persist($compteFocus);
                //  dd($compteFocus);
                
                //update client received  
                $clientReceiver = $this->clientrepository->find($transactionDo->getClientDepot()->getId());
                $clientReceiver->setSolde($transactionDo->getMontant());
                $clientReceiver->setAction("retrait");
                $this->entitymanagerinterface->persist($clientReceiver);

                // summarize transaction
                $summarizeTransaction = new SummarizeTransaction();
                $summarizeTransaction->setMontant($transactionDo->getMontant());
                $summarizeTransaction->setCompte($idCompteCaissierGiven);
                $summarizeTransaction->setType("retrait");
                $this->entitymanagerinterface->persist($summarizeTransaction);

                 $this->entitymanagerinterface->flush();
                 return $this->json("retrait reussit", 201);
            }
           
        } else {
            return $this->json("Ce code n'est pas valide", 400);  
        }
      
    }


}

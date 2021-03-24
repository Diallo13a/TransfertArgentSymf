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
        // Dans cette partie on a recupéré l'utilisateur qui se trouve dans une agence et l'id qui correspond à cette agence
        $userconnect = $this->getUser()->getId();
        //dd($userconnect);
        // On a le solde
        // $solde=$this->getUser()->getAgence()->getCompte()[1]->getSolde();
    
        // Id compte
        $idcompte =$this->getUser()->getAgence()->getCompte()[1]->getId();
        // dd($idcompte);
        // dd($solde);
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

        // On recupere le compte (l'id compte)
        // $cpte = $this->compterepository->find($dataPostman["comptes"]);
        // dd($cpte);
       
       
       
        //     // Ajout du compte cad lors d'unretrait
        // $dept = $cpte->setSolde($cpte->getSolde() + $depot->getMontant());
       
        
       
        //($cpte->setSolde($cpte->getSolde() - $depot->getMontant()));

          // genere code transaction
          $numBeetween = rand(1, 10);  // choose number beetween 100-1000
          $date = new \DateTime('now');
          
          

        $depot->setDateDepot(new DateTime())
              ->setdateRetrait(new DateTime())
              ->setdateAnnulation(new DateTime())
              ->setTtc(100)
              ->setfraisEtat($etat)
              ->setfraisSystem($system)
              ->setfraisEnvoi($fraisDepot)
              ->setfraisRetrait($fraisRetrait)
              ->setCodeTransaction($codeTransaction)
              ->setClientDepot($clientEnv)
              ->setClientRetrait($clientRecv)
              ->setTypeTransaction("EnCours");

               // Les infos du compte
        $newsolde= $this->compterepository->find((int)$idcompte);
        // dd($newsolde);
        // Deduction du compte cad lors d'un depot cad actualisation 
        $newsolde->setSolde(($newsolde->getSolde() - ($dataPostman["montant"])) + $fraisDepot) ;
        // $newsolde->addTRansaction($depot);
        $user = $this->userrepository->find((int)$userconnect);
        // dd($user);
        $depot->setCompteRetrait($newsolde);

        $depot->setUser($user);
        // dd($depot);

            //   ->getCompte()->setSolde($cpte->getSolde() - $depot->getMontant()); 
            //    + $depot->solde
            
        //    dd(($depot->getCompte()));
        

         
        $this->entitymanagerinterface->persist($depot);
        $this->entitymanagerinterface->persist($newsolde);
        $this->entitymanagerinterface->persist($clientEnv);
        $this->entitymanagerinterface->persist($clientRecv);
        $this->entitymanagerinterface->flush();

        return $this->json("success",201);

    }











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
                $clientReceiver = $this->clientrepository->find($transactionDo->getClientRetrait()->getId());
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



    /**
     * @Route(
     *      name="getTransactionByCode" ,
     *      path="/api/transaction/{code}" ,
     *      methods={"GET"} ,
     *      defaults={
     *         "__controller"="App\Controller\TransactionController::getTransactionByCode",
     *         "_api_resource_class"=TRansaction::class ,
     *         "_api_collection_operation_name"="getTransactionByCode"
     *     }
     *)
     */
    public function getTransactionByCode(Request $request, SerializerInterface $serializer, $code)
    {
        $data = array();
        
        $transaction =  $this->transactionrepository->findTransactionByCode($code) ;

        if($transaction) {
           
            $recuperator = $this->clientrepository->findById($transaction->getClientRetrait()->getId());
            // if($recuperator) {
                $envoyer = $this->clientrepository->findById($transaction->getClientDepot()->getId());
                
                foreach($envoyer as $env ) {
                    foreach($recuperator as $recup) {
                        array_push($data, $transaction, $env, $recup );
                    }
                }
                return $this->json($data , 200);
            // } 
            // else {
            //     $deposer = $this->clientRepository->findById($transaction->getCompteEnvoi()->getId());
            //     $retrait = $this->clientRepository->findById($transaction->getCompteRetrait()->getId());
                
            //     foreach($deposer as $dep) {
            //         foreach($retrait as $ret) {
            //             array_push($data, $transaction, $dep, $ret);
            //         }
            //     }
            //     return $this->json($data , 200);
            // }

        } 
        else {
            return $this->json("Ce code n'est pas valide", 400);  
        }

    }


    

    /**
     * @Route(
     *      name="recupTransaction" ,
     *      path="/api/recupTransaction/{code}" ,
     *     methods={"GET"} ,
     *     defaults={
     *         "__controller"="App\Controller\TransactionController::recupTransaction",
     *         "_api_resource_class"=TRansaction::class ,
     *         "_api_collection_operation_name"="recupTransaction"
     *     }
     *)
     */
    public function recupTransaction(Request $request, SerializerInterface $serializer, $code)
    {
        $transactionDo =  $this->transactionrepository->findTransactionByCode($code) ;
             
        if($transactionDo) {
            
            if($transactionDo->getTypeTransaction() == "Reussie") {
                 return $this->json("Cette transaction est déjà retirée ", 400);  
            } else if($transactionDo->getTypeTransaction() == "Annulée"){
                 return $this->json("Cette transaction a étè annulée ", 400);  
            } else {

                $userConnected = $this->getUser();  //for recup token's user
                  //get id agence of utilisateur
                $idAgence = $this->userrepository->findOneBy(['id'=>$userConnected->getId()])->getAgence()->getId();
                // dd($idAgence);
                $focusCompte = $this->compterepository->findBy(['agence'=>$idAgence])[0]; //reper account
                // dd($focusCompte);

                $time = new \DateTime();
                $dateFormatted = date_format($time,"d/m/Y H:i");
                $transactionDo->setDateRetrait($time);
                $transactionDo->setTypeTransaction("Reussie");
                $transactionDo->setCompteRetrait($focusCompte);
                // $transactionDo->setClientRetrait($userConnected);
                $this->entitymanagerinterface->persist($transactionDo);
                 //  dd($transactionDo);
                
                $compteFocus =  $this->compterepository->findOneBy(['id'=>(int)$focusCompte->getId()]);
                $compteFocus->setSolde($compteFocus->getSolde() +$transactionDo->getMontant() + $transactionDo->getFraisRetrait());
                // $compteFocus->setMiseajour($dateFormatted);
                $this->entitymanagerinterface->persist($compteFocus);
               // dd($compteFocus->getMiseajour());
                
                //update client received  
                $clientReceiver = $this->clientrepository->find($transactionDo->getClientRetrait()->getId());
                $clientReceiver->setSolde($transactionDo->getMontant());
                $clientReceiver->setAction("retrait");
                $this->entitymanagerinterface->persist($clientReceiver);

             
                // summarize transaction
                $summarizeTransaction = new SummarizeTransaction();
                $summarizeTransaction->setMontant($transactionDo->getMontant());
                $summarizeTransaction->setCompte($focusCompte->getId());
                $summarizeTransaction->setType("retrait");
                // $summarizeTransaction->setUser($userConnected->getId());
                // $summarizeTransaction->setDate(date_format($time,"d/m/Y"));
                // $summarizeTransaction->setFrais(0);
                $this->entitymanagerinterface->persist($summarizeTransaction);


                
                 $this->entitymanagerinterface->flush();
                 // return $this->json("Vous avez retiré ".$transactionDo->getMontant()." par le distributeur N°".$focusCompte->getIdentifiantCompte()."."."\n"."Date de retrait: ".$focusCompte->getMiseajour()."", 200);
                 return $this->json("retrait reussit", 201);
            }
           
        } else {
            return $this->json("Ce code n'est pas valide", 400);  
        }
      
    }

    /*  ******************* End Recup Transaction ******************** */

    /**
     * @Route(
     *  path="/api/transaction", 
     *  name="getAllTransaction", 
     *  methods={"GET"}
     * )
     */
    public function getAllTransaction(Request $request){
        
        $transaction = $this->transactionrepository->findAll();
        return $this->json($transaction, Response::HTTP_OK, [], );
    }

}




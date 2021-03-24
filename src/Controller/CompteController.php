<?php

namespace App\Controller;

use App\Repository\CompteRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CompteController extends AbstractController
{
   


    /**
     * @Route(
     *      name="getCompteByAgence" ,
     *      path="/api/compte/{idAgence}/agence" ,
     *      methods={"GET"} 
     *)
    */
    public function getCompteByAgence( Request $request, CompteRepository $compteRepository, $idAgence) {
        $compte = $compteRepository->findCompteByidAgence($idAgence);
        //dd($compte);
        return $this->json($compte, 200);
   }
}

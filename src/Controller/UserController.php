<?php

namespace App\Controller;

use App\Entity\User;

use App\Entity\AdminSystem;
use App\Services\PutService;
use App\Repository\UserRepository;
use App\Repository\ProfilRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use ApiPlatform\Core\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userpasswordEncoder;

    /**
     * UserController constructor.
     */
    public function __construct(SerializerInterface $serializer, ValidatorInterface $validator,UserPasswordEncoderInterface $userPasswordEncoder, EntityManagerInterface $manager)
    {
        $this->serialize = $serializer;
        $this->validator = $validator;
        $this->userpasswordEncoder = $userPasswordEncoder;
        $this->manager = $manager;
    }
    /**
    *  @Route(
    *      name="addUser" ,
    *      path="/api/admin/users" ,
    *      methods={"POST"} ,
    *     defaults={
    *         "__controller"="App\Controller\UserController::addUser",
    *         "_api_resource_class"=User::class,
    *         "_api_collection_operation_name"="adding"
    *         }
    *     )
    *  */

    // public function addUser(Request $request, UserRepository $repository){
    //     //all data
    //     $user = $request->request->all();
    //     $user["phone"]=intval($user["phone"]);
    //     $user["cni"]=intval($user["cni"]);
    //     // dd($user);
        
    //    // $profil=$request->request->all()->avatar;
    //     //Recuperation de l'image
    //     $avatar = $request->files->get('avatar');


    //     $profil="App\\Entity\\".trim($user["profil"]);
    //     //dd(class_exists($profil)) ;
    //     if (class_exists($profil)){
    //         $user = $this->serialize->denormalize($user,$profil,true);
    //         if($avatar){
    //            // return new JsonResponse("Veuillez ajouter votre image",Response::HTTP_BAD_REQUEST,[],true);
    //            $avatarBlob = fopen($avatar->getRealPath(),"rb");

    //         $user->setAvatar($avatarBlob);
               
    //         }
            


    //         $errors = $this->validator->validate($user);

    //         /* if (count($errors)){
    //              $errors = $this->serializer->serialize($errors,'json');
    //              return new JsonResponse($errors,Response::HTTP_BAD_REQUEST,[],true);
    //          }*/
    //         $password =  $user->getPassword();
    //         $user->setPassword($this->userpasswordEncoder->encodePassword($user,$password));
    //         $user->setArchivage(false);

    //         $em = $this->getDoctrine()->getManager();
    //         $em->persist($user);

    //         // dd($user);
    //         $em->flush();

    //         return $this->json("success",201);
    //     }

    // }


public function addUser( Request $request,ProfilRepository $repo,UserPasswordEncoderInterface $encode) {

    //all data
    $user = $request->request->all() ;
    // unset($user["phone"]);
    $user["phone"]=intval($user["phone"]);
    $user["cni"]=intval($user["cni"]);
    // dd($user);

    //get profil
    $profil = $repo->findByLibelle($user["profil"])[0] ;
    unset($user["profil"]);
     if($profil->getLibelle() == "ADMINSYSTEM") {
         $user = $this->serialize->denormalize($user, AdminSystem::class);
    } elseif ($profil->getLibelle() =="CAISSIER") {
         $user = $this->serialize->denormalize($user, "App\Entity\Caissier");
    } elseif ($profil->getLibelle() =="ADMINAGENCE") {
         $user = $this->serialize->denormalize($user, "App\Entity\AdminAgence");
    }elseif ($profil->getLibelle() =="UTILISATEURAGENCE") {
         $user = $this->serialize->denormalize($user, "App\Entity\UtilisateurAgence");
    }
    $user->setProfil($profil);
    //recupération de l'image
    $avatar = $request->files->get("avatar");
    //is not obliged
    if($avatar)
    {
        //  return new JsonResponse("veuillez mettre une images",Response::HTTP_BAD_REQUEST,[],true);
        //$base64 = base64_decode($imagedata);
        $avatarBlob = fopen($avatar->getRealPath(),"r+");
        $user->setavatar($avatarBlob);
    }


    $errors = $this->validator->validate($user);
     if ($errors){
         $errors = $this->serialize->serialize($errors,"json");
         return new JsonResponse($errors,Response::HTTP_BAD_REQUEST,[],true);
     }
    // dd($user);
     $password =  $user->getPassword();
    $user->setPassword($encode->encodePassword($user,$password));
    $em = $this->getDoctrine()->getManager();
    $em->persist($user);
    $em->flush();

    return $this->json("success",201);

}

    // /**
    //  * @Route(
    //  *      name="updated" ,
    //  *      path="/api/admin/users/{id}" ,
    //  *       methods={"PUT"}
    //  *),
    //  * @Route(
    //  *      name="UpdatedApprenant" ,
    //  *      path="/api/apprenants/{id}" ,
    //  *      methods={"PUT"}
    //  *)
    //  */
    // public function cool(Request $request , PutService $putService,$id) {

    //     return  $putService->putData($request, $id) ;

    // }



    /**
     * @Route(
     *      name="updated" ,
     *      path="/api/admin/users/{id}" ,
     *       methods={"PUT"}
     *)
     */
    public function putUser(Request $request, PutService $postService, 
    EntityManagerInterface $manager,SerializerInterface $serializer,UserRepository $u, $id) {
        $userForm= $postService->UpdateUser($request, 'avatar');
        //dd($userForm);
         $user = $u->find($id);
         foreach ($userForm as $key => $value) {
             if($key === 'profils'){
                 $value = $serializer->denormalize($value, Profil::class);
             }
             $setter = 'set'.ucfirst(trim(strtolower($key)));
             //dd($setter);
             if(method_exists(User::class, $setter)) {
                 $user->$setter($value);
                 //dd($user);
             }
         }
         $manager->flush();

         return new JsonResponse("success",200) ;
        //  return new JsonResponse("success",200,[],true);
 
     }

}

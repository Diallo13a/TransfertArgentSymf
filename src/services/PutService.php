<?php


namespace App\services;


use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use ApiPlatform\Core\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PutService
{
    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var EntityManagerInterface
     */
    private $entitymanager;
    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $userpasswordencoder;
    /**
     * @var UserRepository
     */
    private $userrepository;

    public function __construct(SerializerInterface $serializer, EntityManagerInterface $entityManager,
                                ValidatorInterface $validator, UserPasswordEncoderInterface $userPasswordEncoder,
                                UserRepository $userRepository){
        $this->serializer=$serializer;
        $this->entitymanager=$entityManager;
        $this->validator=$validator;
        $this->userpasswordencoder=$userPasswordEncoder;
        $this->userrepository=$userRepository;
    }
    public function putData($request,$id){

        $dataId=$this->userrepository->find($id);
        $data=$request->request->all();
        foreach ($data as $key=>$value){
            if ($key!=="_method" || !$value){
                $dataId->{"set".ucfirst($key)}($value);
            }
        }
        $avatar=$request->files->get("avatar");
        $avatarBlob = fopen($avatar->getRealpath(),"rb");
        if ($avatar){
            $dataId->setAvatar($avatarBlob);
        }
        $errors=$this->validator->validate($dataId);
        if ($errors){
            $errors=$this->serializer->serialize($errors,"json");
            return new JsonResponse($errors,Response::HTTP_BAD_REQUEST,[],true);
        }
        $this->entitymanager->persist($dataId);
        $this->entitymanager->flush();
        return new JsonResponse("success",201);
    }




    // Update User
    public function UpdateUser(Request $request, string $filename = null)
    {
        $row = $request->getContent();
        $delimitor = "multipart/form-data; boundary=";
        // dd($delimitor);
        $boundary = "--".explode($delimitor, $request->headers->get("content-type"))[1];
        // dd($boundary);
        $elements = str_replace([$boundary,'Content-Disposition: form-data;',"name="],"",$row);
        //dd($elements);
        $tabElements = explode("\r\n\r\n", $elements);
        //dd($tabElements);
        $data = [];

        for ($i = 0; isset($tabElements[$i+1]); $i++) {

            $key = str_replace(["\r\n",'"','"'],'',$tabElements[$i]);
            //dd($key);
            if (strchr($key, $filename)) {
                $file = fopen('php://memory', 'r+');
                fwrite($file, $tabElements[$i+1]);
                rewind($file);
                $data[$filename] = $file;
            } else {
                $val = str_replace(["\r\n",'--'], '', $tabElements[$i+1]);
                $data[$key] = $val;
            }
        }
        //dd($data);
        return $data;
    }

}
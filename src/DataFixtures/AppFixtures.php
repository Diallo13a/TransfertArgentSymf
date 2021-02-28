<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Profil;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $repo;
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder){
        $this->encoder=$encoder;

    }
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        $faker = Factory::create('fr_FR');

        $profils =["ADMINSYSTEM" ,"CAISSIER" ,"ADMINAGENCE" ,"UTILISATEURAGENCE"];
        foreach ($profils as $key => $libelle) {
            $profil =new Profil() ;
            $profil ->setLibelle ($libelle );
            $manager ->persist ($profil );
            $manager ->flush();
            for ($i=1; $i <=2 ; $i++) {
                $user = new User() ;
                $user ->setProfil ($profil );
                // $user ->setLogin (strtolower ($libelle ).$i);
                // $user->setPhone($faker->phoneNumber);
                $user->setUsername($faker->userName);
                $user->setAddresse($faker->address);
                $user->setEmail ($faker->email);
                $user->setArchivage(0);
                //Génération des Users
                $password = $this ->encoder ->encodePassword ($user, 'pass_1234' );
                $user ->setPassword ($password );
                $manager ->persist ($user);
            }
            $manager ->flush();
        }
}
}
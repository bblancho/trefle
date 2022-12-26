<?php

namespace App\DataFixtures;

use App\Entity\Ingredient;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');
        
        for ($i=1; $i < 50 ; $i++) { 

            $ingredient = new Ingredient();
            $ingredient
                ->setNom("Ingredient #".$i)
                ->setPrix( mt_rand(0, 100) )
            ;

            $manager->persist($ingredient);
        }

        $manager->flush();
    }
}

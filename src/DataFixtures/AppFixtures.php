<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Faker\Generator;
use App\Entity\Recette;
use App\Entity\Ingredient;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    /**
     *
     * @var Generator
     */
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR') ;

    }
    
    public function load(ObjectManager $manager): void
    {
        // User
        $users = [] ;

        for ($i=1; $i < 10 ; $i++) { 

            $user = new User();

            $user
                ->setNom( $this->faker->lastName() )
                ->setPrenom( $this->faker->firstName() )
                ->setPseudo( mt_rand(0, 1) == 1 ? $this->faker->firstName() : null )
                ->setEmail( $this->faker->email() )
                ->setRoles(['ROLE_USER'])
                ->setPlainPassword('Test2023') // rattaché à mon eventListener (userListener)
            ;

            $users[] = $user;

            $manager->persist($user); // déclenche l'eventListener (userListener)
        }

        // Ingrédients
        $ingredients = [] ;
        
        for ($i=1; $i < 50 ; $i++) { 

            $ingredient = new Ingredient();
            $ingredient
                ->setNom( $this->faker->word(3) )
                ->setPrix( mt_rand(0, 100) )
                ->setUser( $users[mt_rand( 0 , count($users) - 1 )] )
            ;

            $ingredients[] =  $ingredient;

            $manager->persist($ingredient);
        }

        //recettes
        for ($i=1; $i < 25 ; $i++) { 

            $recette = new Recette();
            $recette
                ->setNom( $this->faker->word() )
                ->setTime( mt_rand(0, 1) == 1 ? mt_rand(1, 1441) : null)
                ->setnbPersonne( mt_rand(0, 1) == 1 ? mt_rand(1, 50) : null)
                ->setDifficulte( mt_rand(0, 1) == 1 ? mt_rand(1, 5) : null)
                ->setDescription( $this->faker->text(150) )
                ->setPrix( mt_rand(0, 1) == 1 ? mt_rand(1, 1000) : null)
                ->setIsFavori( mt_rand(0, 1) == 1 ? true : false)
                ->setUser( $users[mt_rand( 0 , count($users) - 1 )] )
            ;

            for ($j=0; $j < mt_rand(5, 15); $j++) { 
                $recette->addIngredient( $ingredients[ mt_rand(0, count($ingredients) -1)]);
            }

            $manager->persist($recette);
        }



        $manager->flush();
    }
}

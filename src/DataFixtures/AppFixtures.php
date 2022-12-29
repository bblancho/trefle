<?php

namespace App\DataFixtures;

use Faker\Factory;
use Faker\Generator;
use App\Entity\Recette;
use App\Entity\Ingredient;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;


class AppFixtures extends Fixture
{
    /**
     *
     * @var Generator
     */
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }
    
    public function load(ObjectManager $manager): void
    {

        // Ingrédients
        $ingredients = [] ;
        
        for ($i=1; $i < 50 ; $i++) { 

            $ingredient = new Ingredient();
            $ingredient
                ->setNom( $this->faker->word(3) )
                ->setPrix( mt_rand(0, 100) )
            ;

            $ingredients[] =  $ingredient;

            $manager->persist($ingredient);
        }

        // recettes
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
            ;

            for ($j=0; $j < mt_rand(5, 15); $j++) { 
                $recette->addIngredient( $ingredients[ mt_rand(0, count($ingredients) -1)]);
            }

            $manager->persist($recette);
        }

        $manager->flush();
    }
}

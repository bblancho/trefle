<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IngredientController extends AbstractController
{
    private $manager;
    private $repoIngredient;
    /**
     * Undocumented function
     * @param IngredientRepository $repoIngredient
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager, IngredientRepository $repoIngredient)
    {
        $this->manager = $manager;
        $this->repoIngredient = $repoIngredient;
    }

    /**
     * This function display all ingredeints
     * 
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/ingredient', name: 'ingredient.index', methods: ['GET'])]
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $ingredients = $paginator->paginate(
            $this->repoIngredient->findAll(), // Requête contenant les données à paginer (ici nos ingrédients)
            $request->query->getInt('page', 1), // Numéro page en cours, passé dans l'URL, 1 si aucune page
            10 // Nombre de résultats par page
        );

        return $this->render('ingredient/index.html.twig', [
            'ingredients' => $ingredients
        ]);
    }

    /**
     * This function create Formbuilder for ingredient
     *
     * @return Response
     */
    #[Route('/ingredient/nouveau', name: 'ingredient.new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $ingredient = $form->getData();

            $this->manager->persist($ingredient);
            $this->manager->flush();

            $this->addFlash('success', 'Votre ingrédient a bien été créé avec succès!');

            return $this->redirectToRoute("ingredient.index") ;
        }

        return $this->render('ingredient/new.html.twig',[
            'form' => $form->createView()
        ]);
    }


}

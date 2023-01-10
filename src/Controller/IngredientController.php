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
    private $em;
    private $repoIngredient;
    /**
     * Undocumented function
     * @param IngredientRepository $repoIngredient
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $em, IngredientRepository $repoIngredient)
    {
        $this->em = $em;
        $this->repoIngredient = $repoIngredient;
    }

    /**
     * This function display all ingredients
     * 
     * @param PaginatorInterface $paginator
     * @param Request $request
     * 
     * @return Response
     */
    #[Route('/ingredient', name: 'ingredient_index', methods: ['GET'])]
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $ingredients = $paginator->paginate(
            $this->repoIngredient->findByLastIngredients(), // Requête contenant les données à paginer (ici nos ingrédients)
            $request->query->getInt('page', 1), // Numéro page en cours, passé dans l'URL, 1 si aucune page
            10 // Nombre de résultats par page
        );

        return $this->render('ingredient/index.html.twig', [
            'ingredients' => $ingredients
        ]);
    }

    /**
     * This function show a ingredient
     * 
     * @param int $id
     * 
     * @return Response
     */
    #[Route('/ingredient/{id}', name: 'ingredient_show', requirements: ['id' => '\d+'] , methods: ['GET']) ]
    public function show(int $id): Response
    {
        
        $ingredient = $this->repoIngredient->find($id) ;

        if (!$ingredient) {
            throw $this->createNotFoundException(
                'Aucun ingrédient trouvé pour l\'id: ' . $id
            );
        }

        return $this->render('ingredient/show.html.twig', ['ingredient' => $ingredient]);
    }

    /**
     * This function create a new ingredient
     *
     * @return Response
     */
    #[Route('/ingredient/nouveau', name: 'ingredient_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $ingredient = new Ingredient();

        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $ingredient = $form->getData();
            $ingredient->setUser( $this->getUser() ) ;

            $this->em->persist($ingredient);
            $this->em->flush();

            $this->addFlash('success', 'Votre ingrédient a bien été créé avec succès!');

            return $this->redirectToRoute("ingredient_index") ;
        }

        return $this->render('ingredient/new.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * This function edit a ingredient
     *
     * @param Request $request
     * @param integer $id
     * 
     * @return Response
     */
    #[Route('/ingredient/editer/{id}', name: 'ingredient_edit', methods: ['GET', 'POST']) ]
    public function edit(Request $request, Ingredient $ingredient): Response
    {
        if ( !$ingredient ) {
            throw $this->createNotFoundException('Aucun ingrédient trouvé');
        }

        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $ingredient = $form->getData();

            $this->em->flush();

            $this->addFlash('success', 'Votre ingrédient a bien été modifié avec succès!');

            return $this->redirectToRoute("ingredient_show", ['id' => $ingredient->getId()]) ;
        }

        return $this->render('ingredient/edite.html.twig', [
            'ingredient' => $ingredient,
            'form' => $form->createView(),
        ]);
    }

    /**
     * This function delete a ingredient
     * 
     * @param ingredient $id
     * 
     * @return Response
     */
    #[Route('/ingredient/supprimer/{id}', name: 'ingredient_delete', requirements: ['id' => '\d+'] , methods: ['GET'])]
    public function delete(int $id): Response
    {   
        $ingredient = $this->repoIngredient->find($id) ;

        if ( !$ingredient ) {
            $this->addFlash('warning', 'Aucun ingrédient a trouvé !') ;

            return $this->redirectToRoute('ingredient_index') ;
        }

        $this->em->remove($ingredient) ;
        $this->em->flush() ;

        $this->addFlash('success', 'Votre ingrédient a bien été supprimé !') ;

        return $this->redirectToRoute('ingredient_index') ;
    }

    
}
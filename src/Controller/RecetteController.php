<?php

namespace App\Controller;

use App\Entity\Recette;
use App\Form\RecetteType;
use App\Repository\RecetteRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class RecetteController extends AbstractController
{
    private $repoRecette;

    /**
     * 
     * @param RecetteRepository $repoRecette
     */
    public function __construct( RecetteRepository $repoRecette)
    {
        $this->repoRecette = $repoRecette;
    }

    /**
     * This function display all recettes
     * 
     * @param PaginatorInterface $paginator
     * @param Request $request
     * 
     * @return Response
     */
    #[Route('/recette', name: 'recette_index', methods: ["GET"])]
    #[IsGranted('ROLE_USER')]
    public function index(PaginatorInterface $paginator, Request $request): Response
    {
        $recettes = $paginator->paginate(
            $this->repoRecette->findByLastRecette(), // Requête contenant les données à paginer (ici nos recettes)
            $request->query->getInt('page', 1), // Numéro page en cours, passé dans l'URL, 1 si aucune page
            10 // Nombre de résultats par page
        );

        return $this->render('recette/index.html.twig', compact(
            'recettes',
        ));
    }
     /**
     * This function display all recettes public
     * 
     * @param PaginatorInterface $paginator
     * @param Request $request
     * 
     * @return Response
     */
    #[Route('/recette/publique', name: 'recette_publique', methods: ["GET"])]
    public function indexPublic(PaginatorInterface $paginator, Request $request): Response
    {
        $recettes = $paginator->paginate(
            $this->repoRecette->findByRecettePublique(11), // Requête contenant les données à paginer (ici nos recettes)
            $request->query->getInt('page', 1), // Numéro page en cours, passé dans l'URL, 1 si aucune page
            10 // Nombre de résultats par page
        );

        return $this->render('recette/indexPublic.html.twig', compact(
            'recettes',
        ));
    }

    /**
     *  This function create a new recette
     *
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * 
     * @return Response
     */
    #[IsGranted('ROLE_USER')]
    #[Route("/recette/creation", name: "recette_new", methods: ['GET', 'POST'])]
    public function new(Request $request, ManagerRegistry $doctrine): Response
    {
        $recette = new Recette();

        $form = $this->createForm(RecetteType::class, $recette);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $recette = $form->getData();
            $recette->setUser( $this->getUser() ) ;

            $em =  $doctrine->getManager();

            $em->persist($recette);

            $em->flush();

            $this->addFlash('success', 'Votre recette a été créée avec succès !');

            return $this->redirectToRoute("recette_index") ;
        }

        return $this->render('recette/new.html.twig',[
            'form' => $form->createView(),
        ]);
    }
    
    /**
     * This function show a recette
     * 
     * @param int $id
     * 
     * @return Response
     */
    #[Security("is_granted('ROLE_USER') and recette.getIsPublique() === true")]
    #[Route('/recette/{id}', name: "recette_show", methods: ['GET'])]
    public function show(Recette $recette): Response
    {
        if (!$recette) {
            throw $this->createNotFoundException(
                'Aucune recette trouvé pour l\'id: ' . $recette.getId() 
            );
        }

        return $this->render('recette/show.html.twig', ['recette' => $recette]);
    }

    /**
     * This function edit a recette
     *
     * @param Request $request
     * @param integer $id
     * 
     * @return Response
     */
    #[Security("is_granted('ROLE_USER') and user === recette.getUser()")]
    #[Route('/recette/editer/{id}', name: 'recette_edit', methods: ['GET', 'POST']) ]
    public function edit(Request $request, ManagerRegistry $doctrine, Recette $recette ): Response
    {
        if ( !$recette ) {
            throw $this->createNotFoundException('Aucune recette trouvée.');
        }

        $form = $this->createForm(RecetteType::class, $recette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $recette = $form->getData();

            $em =  $doctrine->getManager();

            $em->flush();

            $this->addFlash('success', 'Votre recette a bien été modifiée!');

            return $this->redirectToRoute("recette_show", ['id' => $recette->getId()]) ;
        }

        return $this->render('recette/edite.html.twig', [
            'recette' => $recette,
            'form' => $form->createView(),
            'value_button_edite' => 1,
        ]);
    }

    /**
     * 
     * This function delete a recette
     * 
     * @param recette $id
     * 
     * @return Response
     */
    #[Security("is_granted('ROLE_USER') and user === recette.getUser()")]
    #[Route( '/recette/supprimer/{id}', name: 'recette_delete', requirements: ['id' => '\d+'] , methods: ['GET'])]
    public function delete(int $id, ManagerRegistry $doctrine): Response
    {
        $recette = $this->repoRecette->find($id) ;

        if ( !$recette ) {
            $this->addFlash('warning', 'Aucune recette a trouvée !') ;

            return $this->redirectToRoute('recette_index') ;
        }

        $em =  $doctrine->getManager() ;

        $em->remove($recette) ;

        $em->flush() ;

        $this->addFlash('success', 'Votre recette a bien été supprimée !') ;

        return $this->redirectToRoute('recette_index') ;
    }


}

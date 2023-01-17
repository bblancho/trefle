<?php

namespace App\Controller;

use App\Repository\RecetteRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    /**
     * 
     * @param RecetteRepository $repoRecette
     */
    public function __construct( RecetteRepository $repoRecette)
    {
        $this->repoRecette = $repoRecette;
    }
    
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $recettes = $this->repoRecette->findByRecettePublique(3) ;

        return $this->render('home/index.html.twig', [
            'recettes' => $recettes,
        ]);
    }
}

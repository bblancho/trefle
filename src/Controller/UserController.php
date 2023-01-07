<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{

    /**
     * This function edit a user
     *
     * @param Request $request
     * @param User $user
     * @param ManagerRegistry $doctrine
     * 
     * @return Response
     */
    #[Route('/utilisateur/edition/{id}', name: 'user_edit')]
    public function edit(Request $request, ManagerRegistry $doctrine, User $user): Response
    {
        //  dd( $user, $this->getUser() ) ;

        // On teste si le user est connecté
        if( !$this->getUser() ) {
            return $this->redirectToRoute('security_login');
        }

        // On teste si on a bien le user courant connecté
        if( $this->getUser() !== $user) {
            return $this->redirectToRoute('recette_index');
        }

        if ( !$user ) {
            throw $this->createNotFoundException('Aucun utilisateur trouvé.') ;
        }

        $form = $this->createForm(UserType::class, $user) ;
        $form->handleRequest($request) ;

        if( $form->isSubmitted() && $form->isValid() ){

            $user = $form->getData();

            $em =  $doctrine->getManager();

            $em->flush();

            $this->addFlash('success', 'Votre compte a bien été modifié!');

            //return $this->redirectToRoute("user_show", ['id' => $user->getId()]) ;
            return $this->redirectToRoute('recette_index');
        }

        return $this->render('user/edite.html.twig', [
            'form' => $form->createView() ,
            'user' => $user
        ]);

    }


}

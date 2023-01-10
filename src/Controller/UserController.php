<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserPasswordType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{

    /**
     * This function edit a user
     *
     * @param Request $request
     * @param User $currentUser
     * @param ManagerRegistry $doctrine
     * 
     * @return Response
     */
    #[Security("is_granted('ROLE_USER') and user === currentUser ")]
    #[Route('/utilisateur/edition/{id}', name: 'user_edit')]
    public function edit(Request $request, ManagerRegistry $doctrine, User $currentUser): Response
    {
        //  dd( $user, $this->getUser() ) ;

        if ( !$currentUser ) {
            throw $this->createNotFoundException('Aucun utilisateur trouvé.') ;
        }

        $form = $this->createForm(UserType::class, $currentUser) ;
        $form->handleRequest($request) ;

        if( $form->isSubmitted() && $form->isValid() ){
            
            $currentUser = $form->getData();

            $em =  $doctrine->getManager();

            $em->persist($currentUser);
            $em->flush();

            $this->addFlash('success', 'Votre compte a bien été modifié!');

            //return $this->redirectToRoute("user_show", ['id' => $user->getId()]) ;
            return $this->redirectToRoute('recette_index');
        }

        return $this->render('user/edite.html.twig', [
            'form' => $form->createView() ,
        ]);

    }

    /**
     * This function edit a user
     *
     * @param Request $request
     * @param User $currentUser
     * @param ManagerRegistry $doctrine
     * @param UserPasswordHasherInterface $hasher
     * 
     * @return Response
     */
    #[Security("is_granted('ROLE_USER') and user === currentUser ")]
    #[Route('/utilisateur/edition-mot-de-passe/{id}', name: 'user_edit_password')]
    public function editPassword(Request $request, ManagerRegistry $doctrine, User $currentUser, UserPasswordHasherInterface $hasher ): Response
    {
        // dd( $this->getUser() ) ;

        if ( !$currentUser ) {
            throw $this->createNotFoundException('Aucun utilisateur trouvé.') ;
        }

        $form = $this->createForm(UserPasswordType::class, $currentUser) ;
        $form->handleRequest($request) ;

        if( $form->isSubmitted() && $form->isValid() ){
            
            $currentUser = $form->getData();
            $oldPassword = $form->get('oldPassword')->getData() ; 

            if( $hasher->isPasswordValid( $currentUser, $oldPassword ) ){

                $currentUser->setPassword( $form->getData()->getPlainPassword() ) ;
                $currentUser->setCreatedAt( new \DateTimeImmutable() ) ;

                $em =  $doctrine->getManager();

                $em->persist($currentUser);
                $em->flush();
    
                $this->addFlash('success', 'Votre mot de passe a bien été modifié !');
    
                //return $this->redirectToRoute("user_show", ['id' => $user->getId()]) ;
                return $this->redirectToRoute('recette_index');

            } else {
                $this->addFlash('error', "Erreur sur l'ancien mot de passe.");
            }

        }

        return $this->render('user/edite_password.html.twig', [
            'form' => $form->createView() ,
        ]);

    }


}

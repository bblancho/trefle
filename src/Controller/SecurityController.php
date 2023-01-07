<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private $em;

    /**
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Formulaire de connexion
     *
     * @param AuthenticationUtils $authenticationUtils
     * 
     * @return Response
     */
    #[Route('/connexion', name: 'security_login', methods: ['GET','POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ( $this->getUser() ) {
            return $this->redirectToRoute('ingredient_index');
        }

        // retrouver une erreur d'authentification s'il y en a une
        $error = $authenticationUtils->getLastAuthenticationError();

        // retrouver le dernier identifiant de connexion utilisé
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'controller_name' => 'SecurityController',
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * Formulaire de déconnexion
     *
     * @return void
     */
    #[Route( '/deconnexion', name: 'security_logout', methods: ['GET'] )]
    public function logout(): void
    {
        throw new \Exception('This should never be reached!');
    }

    /**
     * Formulaire d'inscription
     *
     * @param Request $request
     * 
     * @return Response
     */
    #[Route( "/inscription" , name: "security_register", methods: ['GET', 'POST'] )]
    public function registration(Request $request): Response
    {
        $user = new User();
        $user->setRoles(['ROLE_USER']) ;

        $form = $this->createForm(RegistrationType::class, $user) ;

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid() ) {

            $user = $form->getData();

            $this->em->persist( $user ) ;

            $this->em->flush();

            $this->addFlash('success', 'Votre inscription a bien été prise en compte!');

            return $this->redirectToRoute("security_login") ;
        }

        return $this->render('security/inscription.html.twig', [
            'form' => $form->createView() ,
        ]);
    }



}

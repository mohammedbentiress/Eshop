<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{

     /**
      * register new user
      *
      * @Route("/register", name="app_register")
      *
      * @param Request $request
      * @param UserPasswordEncoderInterface $passwordEncoder
      * @param GuardAuthenticatorHandler $guardHandler
      * @param LoginFormAuthenticator $authenticator
      * @return Response
      */
    public function register(
        Request $request, 
        UserPasswordEncoderInterface $passwordEncoder, 
        GuardAuthenticatorHandler $guardHandler, 
        LoginFormAuthenticator $authenticator
    ): Response {
        $request = $this->get('request_stack')->getMasterRequest();

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user,['action'=>'/register']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            
            $user->setRoles(['ROLE_USER']);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        } else {
            return $this->forward('App\Controller\SecurityController::login', [
                'registrationForm'  => $form
                ]);
        }
    }
}

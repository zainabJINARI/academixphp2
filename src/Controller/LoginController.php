<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            
            $roles = $this->getUser()->getRoles();

            // Redirect the user based on their role
            foreach ($roles as $role) {
                if ($role === 'ROLE_STUDENT') {
                    return $this->redirectToRoute('dashboard');
                } elseif ($role === 'ROLE_TUTOR') {
                    return $this->redirectToRoute('all_courses');
                } elseif ($role === 'ROLE_ADMIN') {
                    return $this->redirectToRoute('app_admin');
                }
            }
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}

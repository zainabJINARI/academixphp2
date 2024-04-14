<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/signup', name: 'signup')]
    public function index(): Response
    {
        return $this->render('user/signup.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/login', name: 'login')]
    public function login(): Response
    {
        return $this->render('user/login.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}

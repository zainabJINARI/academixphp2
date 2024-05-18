<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TutorController extends AbstractController
{
    #[Route('/dashboard', name: 'app_tutor')]
    public function index(): Response
    {
        return $this->render('tutor/index.html.twig', [
            'controller_name' => 'TutorController',
        ]);
    }
}

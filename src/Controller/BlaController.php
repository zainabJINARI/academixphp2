<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlaController extends AbstractController
{
    #[Route('/bla', name: 'app_bla')]
    public function index(): Response
    {
        return $this->render('bla/index.html.twig', [
            'controller_name' => 'BlaController',
        ]);
    }
}

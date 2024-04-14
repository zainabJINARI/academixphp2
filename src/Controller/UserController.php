<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface ;
use App\Repository\ProductRepository ;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User; 
use Symfony\Component\Security\Core\Exception\BadCredentialsException;


class UserController extends AbstractController
{
    #[Route('/signup', name: 'signup' ,  methods: ['GET' , 'POST']) ]
    public function index(Request $request , EntityManagerInterface $entityManager): Response
    {
        if($request->getMethod() === 'POST') {

            $fullname = $request->request->get('fullname');
            $email = $request->request->get('email');
            $password = $request->request->get('password');
            $role = $request->request->get('role');
        
            $user = new User();
            $user->setFullname($fullname);
            $user->setEmail($email);
            $user->setPassword($password);
            $user->setRole($role);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('signup');
    
            // return $this->redirectToRoute('courses');
        }
        return $this->render('user/signup.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/login', name: 'login' , methods:['GET' , 'POST'])]
    public function login(Request $request , EntityManagerInterface $entityManager): Response
    {

        if($request->getMethod() === 'POST' ) {
           $email = $request->request ->get('email');
           $password = $request->request ->get('password');
           $user = $entityManager->getRepository(User::class)->find($email);

           if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé avec l\'email : ' . $email);
           }

           if (!password_verify($password, $user->getPassword())) {
                    throw new BadCredentialsException('Mot de passe incorrect');
           }

            return $this->redirectToRoute('dashboard');

        
        
        }

        return $this->render('user/login.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }
}

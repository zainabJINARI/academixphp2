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
use Symfony\Component\HttpFoundation\RedirectResponse;


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
            if($entityManager->getRepository(User::class)->find($email)) {
                throw $this->createNotFoundException('Utilisateur  avec  cet email est deja existe');
            }
            $user = new User();
            $user->setFullname($fullname);
            $user->setEmail($email);
            $user->setPassword($password);
            $user->setRole($role);
            
            $entityManager->persist($user);
            $entityManager->flush();

            return new RedirectResponse($this->generateUrl('all_courses'));

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
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
 
            if (!$user) {
             throw $this->createNotFoundException('Utilisateur non trouvé avec l\'email : ' . $email);
            }
 
            if (!$password == $user->getPassword()) {
                     throw new BadCredentialsException('Mot de passe incorrect');
            }
            return new RedirectResponse($this->generateUrl('all_courses'));
         }
 
         return $this->render('user/login.html.twig', [
             'controller_name' => 'UserController',
         ]);

    }
    #[Route('/dashboard', name: 'dashboard' , methods:['GET'])]
    public function dashboard(Request $request , EntityManagerInterface $entityManager): Response
    {
        
 
         return $this->render('user/dashboard.html.twig', [
             'controller_name' => 'UserController',
         ]);
    }
}

?>

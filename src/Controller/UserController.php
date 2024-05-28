<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface ;
use App\Repository\ProductRepository ;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User; 
use App\Entity\Enrollment;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\HttpFoundation\RedirectResponse;


class UserController extends AbstractController
{
    

    #[Route('/dashboard', name: 'dashboard' , methods:['GET'])]
    public function dashboard(Request $request , EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user || !in_array('ROLE_STUDENT', $user->getRoles())) {
          
            return new Response('Unauthorized access', Response::HTTP_UNAUTHORIZED);
        }

       
        $enrollmentRepository = $entityManager->getRepository(Enrollment::class);
        $studentEnrollments = $enrollmentRepository->findCoursesByStudent($user->getUserIdentifier());

        
        return $this->render('user/dashboard.html.twig', [
            'studentEnrollments' => $studentEnrollments,
        ]);
    }





    #[Route('/user/update-profile', name: 'update_user_profile', methods: ['POST'])]
    public function updateUserProfile(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Get the current user
        $currentUser = $this->getUser();

        // Get the current user's email
        $email = $currentUser->getUserIdentifier();

        // Fetch the user entity from the database based on the email
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        // Retrieve form data from the request
        $username = $request->request->get('username');
        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $profileImage = $request->request->get('profileImage');

        // Update user properties
        $user->setUsername($username);
        $user->setEmail($email);

        // Check if a new password is provided and update it
        if (!empty($password)) {
            // Hash the password (replace this with your actual password hashing logic)
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $user->setPassword($hashedPassword);
        }
        if (!empty($profileImage)) {
            $user->setProfileImage($profileImage);
        }

        // Handle profile picture upload (if applicable)

        // Persist changes to the database
        $entityManager->flush();

        // Redirect back to the admin page or wherever you want
        return $this->redirectToRoute('/dashboard');
    }


    #[Route('/user/delete-user', name: 'delete_user')]
    public function deleteUser(EntityManagerInterface $entityManager): Response
    {
        $currentUser = $this->getUser();
        // Get the current user's email
        $email = $currentUser->getUserIdentifier();
        // Fetch the user entity from the database based on the email
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        // Get the associated courses using DQL

        // Remove the tutor entity
        $entityManager->remove($user);
        $entityManager->flush();
        // Return a JSON response indicating success
        return $this->redirectToRoute('app_home');
    }
}

?>

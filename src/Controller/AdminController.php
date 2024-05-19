<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\CourseRepository;
use App\Repository\UserRepository;
use App\Repository\RequestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface\UserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, UserRepository $userRepository, CourseRepository $courseRepository , RequestRepository $requestRepository ,  ): Response
    {

        $tutors = $userRepository->createQueryBuilder('u')
            ->andWhere('u.roles LIKE :role')
            ->setParameter('role', '%"ROLE_TUTOR"%')
            ->getQuery()
            ->getResult();
        $tutorsData = [];
        foreach ($tutors as $tutor) {
            $tutorsData[] = [
                'id' => $tutor->getId(),
                'username' => $tutor->getUsername(),
                'bio' => $tutor->getBio(),
                'profile' => $tutor->getProfileImage(),

            ];
        }
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        $courses = $courseRepository->createQueryBuilder('c')
            ->select('c', 'u.username as tutor_name',)
            ->leftJoin('c.tutor', 'u')
            ->getQuery()
            ->getResult();
            
        $coursesData = [];
    
        foreach ($courses as $course) {
            $coursesData[] = [
                'id' => $course[0]->getId(),
                'title' => $course[0]->getTitle(),

                'nbrLessons' => $course[0]->getNbrLessons(),
                'thumbnail' => $course[0]->getThumbnail(),

                'tutor_name' => $course['tutor_name'],

            ];
        }

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setRoles(['ROLE_TUTOR']);

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_admin');
        }
        

        //Recuperer les requests 
        $requests = $requestRepository->findAll();
        $requestsData = [];

        foreach ($requests as $requestt) {
            $requestData = [];

            $requestData['id'] = $requestt->getId();
            $requestData['time'] = $requestt->getTime()->format('Y-m-d H:i:s');
            $requestData['course'] = $requestt->getCourse();
            $tutorId = $requestt->getIdtutor();

            if ($tutorId !== null) {
                $tutor = $userRepository->find($tutorId);
                if ($tutor !== null) {
                    $tutorName = $tutor->getUsername();
                    $requestData['tutor'] = $tutorName;
                } else {
                    $requestData['tutor'] = 'Unknown Tutor';
                }
            } else {
                $requestData['tutor'] = 'No Tutor Assigned';
            }

            $requestsData[] = $requestData;
        }

        


        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'registrationForm' => $form->createView(),
            'tutors' => $tutorsData,
            'courses'=>$coursesData ,
            'requests'=> $requestsData
        ]);
    }


    #[Route('/admin/delete-tutor/{id}', name: 'delete_tutor')]
    public function deleteTutor(EntityManagerInterface $entityManager, $id): Response
    {
        $userId = $id;
        $tutor = $entityManager->getRepository(User::class)->findOneBy(['id' => $userId]);

        // Get the associated courses using DQL
        $courses = $entityManager->createQuery(
            'SELECT c FROM App\Entity\Course c WHERE c.tutor = :userId'
        )->setParameter('userId', $userId)->getResult();

        // Remove each course associated with the tutor
        foreach ($courses as $course) {
            $entityManager->remove($course);
        }

        // Remove the tutor entity
        $entityManager->remove($tutor);
        $entityManager->flush();

        // Return a JSON response indicating success
        return $this->redirectToRoute('app_admin');
    }


    #[Route('/admin/update-profile', name: 'update_admin_profile', methods: ['POST'])]
    public function updateAdminProfile(Request $request, EntityManagerInterface $entityManager): Response
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
        return $this->redirectToRoute('app_admin');
    }

    #[Route('/admin/delete-admin', name: 'delete_tutor')]
    public function deleteAdmin(EntityManagerInterface $entityManager): Response
    {
        $currentUser = $this->getUser();

        // Get the current user's email
        $email = $currentUser->getUserIdentifier();

        // Fetch the user entity from the database based on the email
        $admin = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        // Get the associated courses using DQL

        // Remove the tutor entity
        $entityManager->remove($admin);
        $entityManager->flush();

        // Return a JSON response indicating success
        return $this->redirectToRoute('app_home');
    }
    #[Route('/admin/courses/create-course', name: 'create-course')]
    public function createCourse(EntityManagerInterface $entityManagerInterface,Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $title = $request->request->get('title');
            $tutorid = $request->request->get('tutorid');

            $category = $request->request->get('category');
            $tutor = $entityManagerInterface->getRepository(User::class)->find($tutorid);
            if (!$tutor) {
                
            }
    
            $course = new Course();
            $course->setTitle($title);
            $course->setTutor($tutor);
            $course->setcategory($category);
            $course->setNbrHours(0);
            $course->setLevel('Beginner');
            $entityManagerInterface->persist($course);
            $entityManagerInterface->flush();
            return new RedirectResponse($this->generateUrl('app_admin'));
        }
        return new Response("dh");
    }
    
}

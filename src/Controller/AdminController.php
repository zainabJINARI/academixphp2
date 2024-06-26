<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\RequestAccount;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\CourseRepository;
use App\Repository\RequestAccountRepository;
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
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, UserRepository $userRepository, CourseRepository $courseRepository , RequestRepository $requestRepository ,  RequestAccountRepository $requestAccountRepository ): Response
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
                'email' =>$tutor->getEmail(),
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
        $requests = $requestRepository->findBy(['status'=>'pending']);
        $requestsDataCreate= [];
        $requestsDataDelete = [];


        foreach ($requests as $requestt) {
            
            $requestsData= [];
            $courseName= $courseRepository->find($requestt->getCourseId())->getTitle();
            
            $requestData['id'] = $requestt->getId();
            $requestData['time'] = $requestt->getTime()->format('Y-m-d H:i:s');
            $requestData['category'] = $courseRepository->find($requestt->getCourseId())->getCategory();
            $requestData['description'] = $requestt->getDescription();
            $requestData['course'] = $courseName;
            $requestData['idCourse'] = $requestt->getCourseId();
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
           if($requestt->getType()=='Create'){
             $requestsDataCreate[] = $requestData;
           }else if($requestt->getType()=='Delete'){
            $requestsDataDelete[]=$requestData;
           }
        
        }



        // Récupérer les requêtes
        $requestsAccounts = $requestAccountRepository->findBy(['status' => 'pending']);
        $requestsDataAccount = [];

        foreach ($requestsAccounts as $requestac) {
            $requestsAcData = [];
            $requestsAcData['id'] = $requestac->getId();
            $requestsAcData['time'] = $requestac->getTime()->format('Y-m-d H:i:s');

            $owner = $userRepository->find($requestac->getOwner());
            if ($owner) {
                $requestsAcData['tutorName'] = $owner->getUsername();
            } else {
                $requestsAcData['tutorName'] = 'Unknown';
            }

            $requestsDataAccount[] = $requestsAcData;
        }




        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'registrationForm' => $form->createView(),
            'tutors' => $tutorsData,
            'courses'=>$coursesData ,
            'requests'=> $requestsDataCreate,
            'requestsDelete'=>$requestsDataDelete ,
            'requestsAccounts'=>$requestsDataAccount
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

    #[Route('/admin/delete-admin', name: 'delete_admin')]
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


    #[Route('/admin/update-tutor/{id}', name: 'update_tutor')]
    public function updateTutor(int $id, EntityManagerInterface $entityManager, Request $request,  UserPasswordHasherInterface $passwordHasher): Response
    {
        $tutor = $entityManager->getRepository(User::class)->find($id);
        
        if (!$tutor) {
            throw $this->createNotFoundException('Tutor not found');
        }

        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $plainPassword = $request->request->get('password');

            if ($tutor->getEmail() !== $email) {
                $tutor->setEmail($email);
            }

            if (!empty($plainPassword)) {
                $hashedPassword = $passwordHasher->hashPassword($tutor, $plainPassword);
                $tutor->setPassword($hashedPassword);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_admin');
        }

        return $this->render('admin/index.html.twig', [
            'tutor' => $tutor,
        ]);

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



    #[Route('/edit-course-status/{id}', name: 'edit-course-status')]    public function EditStatusCourse(EntityManagerInterface $entityManagerInterface, CourseRepository $courseRepository  ,  Request $request , RequestRepository $requestRepository): Response
    {
        
        $id = $request->attributes->get('id'); 
        $reqst = $requestRepository->find($id);

        if ($reqst) { 
            $courseId = $reqst->getCourseId(); 
            $course = $courseRepository->find($courseId);
            if ($course) { 
                $course->setActive(true); 
                $reqst->setStatus('accepted');
                $entityManagerInterface->flush();
                return new Response($reqst->getStatus());
            } else {
                return new Response("Course not found");
            }
        } else {
            return new Response("Request not found");
        }
    }


    #[Route('/edit-request-status/{id}', name: 'edit-request-status')]    
    public function EditStatusRequest(EntityManagerInterface $entityManagerInterface, RequestRepository $requestRepository  ,  Request $request): Response
    {
        $id = $request->attributes->get('id');
        $reqst = $requestRepository->find($id);
        if ($reqst) {

            $reqst->setStatus('rejected');
            $entityManagerInterface->flush();

            return new Response("OK");
        } else {
                return new Response("Course not found");
        }
    }

    #[Route('/admin/course/delete/{id}', name: 'delete-course')]
    public function DeleteCourse(EntityManagerInterface $entityManagerInterface, RequestRepository $requestRepository, CourseRepository $courseRepository, Request $request, $id): Response
    {
        // $requestt = $requestRepository->findOneBy(['courseid' => $id, 'type' => 'Delete']);
        $requestt = $requestRepository->find($id);
        if ($requestt && $requestt->getType() == 'Delete') {
            $requestt->setStatus('accepted');
            
            $idcour = $requestt->getCourseId() ;
            $course = $courseRepository->find($idcour);
            if ($course) {
                $course->setActive(false);
                $entityManagerInterface->flush();
                return $this->redirectToRoute('app_admin');
            } else {
                return new Response('Course not found', 404);
            }
        } else {
            return new Response('Request not found', 404);
        }
    }



    #[Route('/admin/course/rejected/{id}', name: 'delete-course')]
    public function RejectedCourse(EntityManagerInterface $entityManagerInterface, RequestRepository $requestRepository, CourseRepository $courseRepository, Request $request, $id): Response
    {
        $requestt = $requestRepository->find($id);
        if ($requestt && $requestt->getType() == 'Delete') {
            
            $requestt->setStatus('rejected');
            $entityManagerInterface->flush();
            return $this->redirectToRoute('app_admin');
            
        } else {
            return new Response('Request not found', 404);
        }
    }


    //admin/account/delete/


    #[Route('/admin/account/rejected/{id}', name: 'reject-account')]
    public function RejectedDeleteAccount(EntityManagerInterface $entityManagerInterface, RequestAccountRepository $requestAccountRepository ,  Request $request, $id): Response
    {
     
        $requestAccount = $requestAccountRepository->find($id);
        if ($requestAccount) {
            $requestAccount->setStatus('rejected');
            $entityManagerInterface->persist($requestAccount);
            $entityManagerInterface->flush();
            
            return new Response("Request rejected successfully.");
        } else {
            return new Response("This request does not exist.", Response::HTTP_NOT_FOUND);
        }
    }




    #[Route('/admin/account/delete/{id}', name: 'delete-account')]
public function AcceptDeleteAccount(
    EntityManagerInterface $entityManager, 
    RequestAccountRepository $requestAccountRepository, 
    UserRepository $userRepository, 
    int $id
): Response {
    $requestAccount = $requestAccountRepository->find($id);

    if ($requestAccount) {
        $requestAccount->setStatus('accepted');
        $user = $userRepository->find($requestAccount->getOwner());

        if ($user) {
            $entityManager->remove($user);
            $entityManager->persist($requestAccount);
            $entityManager->flush();

            $this->addFlash('success', 'User deleted successfully.');
            return $this->redirectToRoute('app_admin'); 
        } else {
            return new Response("This User does not exist.", Response::HTTP_NOT_FOUND);
        }
    } else {
        return new Response("This request does not exist.", Response::HTTP_NOT_FOUND);
    }
}


    
}

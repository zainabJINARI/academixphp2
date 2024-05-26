<?php

namespace App\Controller;

use App\Form\CourseType;
use App\Entity\Lesson ;
use App\Form\LessonFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
 
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Course ;
use App\Entity\User ;
use App\Repository\UserRepository ;
use App\Repository\CourseRepository ;
use App\Repository\RequestRepository ;
use App\Entity\RequestCourse ;
use Symfony\Component\Security\Csrf\CsrfToken;
use Cocur\Slugify\Slugify;



use App\Form\ModuleType;

use App\Entity\Module;



class TutorController extends AbstractController
{
    private $csrfTokenManager;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->csrfTokenManager = $csrfTokenManager;
    }

    
    #[Route('/app_tutor', name: 'app_tutor')]
    public function index(Security $security, EntityManagerInterface $entityManager): Response
    {

        $currentUser = $security->getUser();

        // Directly query the database for courses assigned to the current tutor and where active is true
        $courses = $entityManager->createQueryBuilder()
            ->select('c')
            ->from(Course::class, 'c')
            ->where('c.tutor = :tutor')
            ->andWhere('c.active = :active')
            ->setParameter('tutor', $currentUser)
            ->setParameter('active', true)
            ->getQuery()
            ->getResult();

        return $this->render('tutor/index.html.twig', [
            'controller_name' => 'TutorController',
            'courses' => $courses
        ]);
    }

    #[Route('/create-new-course', name: 'create-new-course')]
    public function newCourse(EntityManagerInterface $entityManager, Request $request, UserRepository $userRepository): Response
    {
        $courseName = $request->request->get('course-name');
        $category = $request->request->get('category-name');
        $description = $request->request->get('course-description');
        $tutorId = intval($request->request->get('tutor-id'));
        $tutor = $userRepository->find($tutorId);

        $course = new Course();
        $course->setTitle($courseName);
        $course->setCategory($category);
        $course->setTutor($tutor);


        $request = new RequestCourse();
        $request->setStatus('pending');
        $request->setType('Create');
        $currentDateTime = new \DateTime();
        $request->setTime($currentDateTime);
        $request->setIdtutor($tutorId);
        $request->setDescription($description);
        


        $entityManager->persist($course);
        $entityManager->flush();
        $entityManager->persist($request);
        $request->setCourseId(intval($course->getId()));
        $entityManager->flush();

        return $this->redirectToRoute('requests_tutor');

       

    }


    
    #[Route('/course/edit/{id}', name: 'detail_course_tutor')]
    public function edit(Request $request, Course $course, EntityManagerInterface $entityManager, $id): Response

    {
        $course = $entityManager->getRepository(Course::class)->findOneBy(['id' => $id]);
        // Handle the case where the course is not found
        if (!$course) {
            throw $this->createNotFoundException('No course found for id ' . $id);
        }


        $modules = $entityManager->getRepository(Module::class)->findBy(['idCourse' => $id]);
        $modulesDataAll = [];
        // Pour chaque module, récupérer les leçons associées
        foreach ($modules as $module) {
            $moduleData = [];
            $moduleData['id'] = $module->getId();
            $moduleData['name'] = $module->getName();
            $moduleData['nbrLessons'] = $module->getNbrLessons();
            $moduleData['nbrHours'] = $module->getNbrHours();
            $moduleData['order'] = $module->getOrder();

            // Récupérer les leçons associées au module
            $lessons = $entityManager->getRepository(Lesson::class)->findBy(['idModule' => $module->getId()]);
            $lessonsDataAll = [];

            foreach ($lessons as $lesson) {
                $lessonData = [];
                // $lessonData['id'] = $lesson->getId();
                $lessonData['name'] = $lesson->getName();
                // $lessonData['duration'] = $lesson->getDuration();
                $lessonData['order'] = $lesson->getOrder();
                // $lessonData['urlVideo'] = $lesson->getUrlvideo();

                $lessonsDataAll[] = $lessonData;
            }

            $moduleData['lessons'] = $lessonsDataAll;

            $modulesDataAll[] = $moduleData;
        }


        
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            if ($user instanceof UserInterface) {
                $course->setTutor($user);

                // Handle the thumbnail file upload if a new file is provided
                $thumbnailFile = $form->get('thumbnail')->getData();
                if ($thumbnailFile) {
                    $newFilename = uniqid() . '.' . $thumbnailFile->guessExtension();
                    $thumbnailFile->move(
                        $this->getParameter('thumbnails_directory'),
                        $newFilename
                    );
                    $course->setThumbnail($newFilename);
                }

                $entityManager->persist($course);
                $entityManager->flush();

                return $this->redirectToRoute('app_tutor');
            } else {
                $this->addFlash('error', 'User not found');
            }
        }

        $course = $entityManager->getRepository(Course::class)->find($id);
        if (!$course) {
            throw $this->createNotFoundException('No course found for id ' . $id);
        }

        $module = new Module();
        $formModule = $this->createForm(ModuleType::class, $module, ['course_id' => $id]);
        $formModule->handleRequest($request);

        if ($formModule->isSubmitted() && $formModule->isValid()) {
        $module->setIdCourse($id);

        $position = $formModule->get('position')->getData();
        if ($position === 'beginning') {
            $entityManager->getRepository(Module::class)->incrementOrderForCourse($id);
            $module->setOrder(1);
        } elseif ($position === 'end') {
            $maxOrder = $entityManager->getRepository(Module::class)->getMaxOrderForCourse($id);
            $module->setOrder($maxOrder + 1);
        } elseif ($position === 'after_module') {
            $afterModuleId = $formModule->get('afterModule')->getData()->getId();
            $afterModuleOrder = $entityManager->getRepository(Module::class)->findOneBy(['id' => $afterModuleId])->getOrder();

            $entityManager->getRepository(Module::class)->incrementOrderAfterModule($id, $afterModuleOrder);
            $module->setOrder($afterModuleOrder + 1);
        }

        $entityManager->persist($module);
        $entityManager->flush();

        return $this->redirectToRoute('app_tutor');
    }



        return $this->render('tutor/detail_course_edit.html.twig', [
            'course' => $course,
            'form' => $form->createView(),
            'modules' => $modulesDataAll,
            'formModule' => $formModule->createView(),

        ]);
    }




    #[Route('/course/delete/{id}', name: 'delete-request')]
    public function deleteRequest(EntityManagerInterface $entityManager, Security $security, $id , RequestRepository $requestRepository , Request $request): Response
    {

        $currentUser = $security->getUser();
        if (!$currentUser || !$currentUser->getUserIdentifier()) {
            return new JsonResponse(['error' => 'User not found or not authenticated'], 400);
        }

        $tutor = $entityManager->getRepository(User::class)->findOneBy(['email' => $currentUser->getUserIdentifier()]);        
        if (!$tutor) {
            return new JsonResponse(['error' => 'Tutor not found'], 404);
        }

        $requestCourse = new RequestCourse();

        $isRequested = $requestRepository->findOneBy([
            'courseid' => $id,
            'type' => 'Delete'
        ]);

        $description = $request->request->get('description-content');

        if(!$isRequested){
            $requestCourse->setStatus('pending');
            $requestCourse->setType('Delete');
            $currentDateTime = new \DateTime();
            $requestCourse->setTime($currentDateTime);
            $requestCourse->setIdtutor($tutor->getId());
            $requestCourse->setCourseId($id);
            $requestCourse->setDescription($description);
            $entityManager->persist($requestCourse);
            $entityManager->flush();
        }
        else{
            return new Response("Already required to delete");
        }
        return $this->redirectToRoute('requests_tutor');
    }


    
    #[Route('/tutor/all_requests', name: 'requests_tutor')]
    public function detail_course_tutor(EntityManagerInterface $entityManager, CourseRepository $courseRepository,UserRepository $userRepository,Security $security): Response
    {
        $currentUser = $security->getUser();
        $tutor = $entityManager->getRepository(User::class)->findOneBy(['email' => $currentUser->getUserIdentifier()]);

        

        $requests = $entityManager->getRepository(RequestCourse::class)->findBy(['idtutor' => $tutor->getId()]);

        if (!$requests) {
            return new JsonResponse(['error' => 'No requests found for this tutor'], 404);
        }

        $requestsArrayDelete=[];
        $requestsArrayCreate=[];
        foreach ($requests as $requestt) {
            
            $requestsData= [];
            $courseName= $courseRepository->find($requestt->getCourseId())->getTitle();
            
            $requestData['id'] = $requestt->getId();
            $requestData['time'] = $requestt->getTime()->format('Y-m-d H:i:s');
            $requestData['course'] = $courseName;
            $requestData['id'] = $requestt->getCourseId();
            $requestData['status'] = $requestt->getStatus();
            

            
           if($requestt->getType()=='Create'){
             $requestsArrayCreate[] = $requestData;
           }else if($requestt->getType()=='Delete'){
            $requestsArrayDelete[]=$requestData;
           }
        

            
        }
        return $this->render('tutor/all_requests.html.twig', [
            'controller_name' => 'detail_course',
            'delete_request'=>$requestsArrayDelete,
            'create_request'=>$requestsArrayCreate,
           


        ]);
    }



   

    #[Route('/module/delete/{id}', name: 'delete_module', methods: ['DELETE'])]
    public function deleteModule(int $id, EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $token = $request->headers->get('X-CSRF-TOKEN');
        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken('delete' . $id, $token))) {
            return new JsonResponse(['error' => 'Invalid CSRF token'], 403);
        }

        $moduleRepository = $entityManager->getRepository(Module::class);
        $module = $moduleRepository->find($id);

        if (!$module) {
            return new JsonResponse(['error' => 'Module not found'], 404);
        }

        $courseId = $module->getIdCourse();
        $order = $module->getOrder();

        $entityManager->remove($module);
        $entityManager->flush();

        $moduleRepository->decrementOrderAfterModule($courseId, $order);

        return new JsonResponse(['success' => 'Module deleted successfully']);
    }

    #[Route('/course/add/lesson/{id}', name: 'add_lesson')]
    public function addNewLesson(Request $request, Module $module, EntityManagerInterface $entityManager, $id): Response {
        
        $lesson = new Lesson();
        $formLesson = $this->createForm(LessonFormType::class, $lesson, ['module_id' => $id]);
        $formLesson->handleRequest($request);

        if ($formLesson->isSubmitted() && $formLesson->isValid()) {

            $name =$formLesson->get('name')->getData();
            $duration =$formLesson->get('duration')->getData();
           
            $position = $formLesson->get('position')->getData();
            if ($position === 'beginning') {
                $entityManager->getRepository(Lesson::class)->incrementOrderForModule($id);
                $lesson->setOrder(1);
            } elseif ($position === 'end') {
                $maxOrder = $entityManager->getRepository(Lesson::class)->getMaxOrderForModule($id);
                $lesson->setOrder($maxOrder + 1);
            } elseif ($position === 'after_lesson') {
                $afterLessonId = $formLesson->get('afterLesson')->getData()->getId();
                $afterLessonOrder = $entityManager->getRepository(Lesson::class)->findOneBy(['id' => $afterLessonId])->getOrder();

                $entityManager->getRepository(Lesson::class)->incrementOrderAfterLesson($id, $afterLessonOrder);
                $lesson->setOrder($afterLessonOrder + 1);
            }
            $videoFile = $formLesson->get('video')->getData();

            if ($videoFile) {
                $originalFilename = pathinfo($videoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $slugify = new Slugify();

                $safeFilename = $slugify->slugify($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$videoFile->guessExtension();
                $videoFile->move(
                        $this->getParameter('video_directory'),
                        $newFilename
                );
                
                $lesson->setUrlvideo($newFilename);
                $lesson->setName($name);
                $lesson->setDuration($duration);
                $lesson->setIdModule($id);

                $entityManager->persist($lesson);
                $entityManager->flush();
                return new JsonResponse(['success' => true]);
                
            }  
        }
        $formHtml = $this->renderView('tutor/add_lesson.html.twig', [
            'formLesson' => $formLesson->createView(),
            'idmodule' => $id ,
        ]);
    
        return new JsonResponse(['formHtml' => $formHtml]);
        
     
    }




}









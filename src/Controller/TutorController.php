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
 
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Course ;
use App\Entity\User ;
use App\Repository\UserRepository ;
use App\Repository\CourseRepository ;
use App\Repository\RequestRepository ;
use App\Entity\RequestCourse ;
use Cocur\Slugify\Slugify;



use App\Form\ModuleType;

use App\Entity\Module;



class TutorController extends AbstractController
{
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
    // #[Route('/tutor/course/{id}', name: 'detail_course_tutor')]
    // public function detail_course_tutor($id,EntityManagerInterface $entityManager): Response
    // {

    //     $course = $entityManager->getRepository(Course::class)->findOneBy(['id' => $id]);

    //     return $this->render('tutor/detail_course_edit.html.twig', [
    //         'controller_name' => 'detail_course',
    //         'course'=>$course


    //     ]);
    // }
    #[Route('/create-new-course', name: 'create-new-course')]
    public function newCourse(EntityManagerInterface $entityManager, Request $request, UserRepository $userRepository): JsonResponse
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

        return new JSONResponse($course->getId());

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

        foreach ($modules as $module) {
            $modulesData = [];
            $modulesData['id'] = $module->getId();
            $modulesData['name'] = $module->getName();
            $modulesData['nbrLessons'] = $module->getNbrLessons();
            $modulesData['nbrHours'] = $module->getNbrHours();
            $modulesData['order'] = $module->getOrder();
            $modulesDataAll[] = $modulesData;
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
    public function deleteRequest(EntityManagerInterface $entityManager, Security $security, $id , RequestRepository $requestRepository): Response
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

        $isRequested = $requestRepository->findBy(['courseid' => $id]);
        if(!$isRequested || ($requestCourse  &&  $requestCourse->getType() != 'Delete')){
            $requestCourse->setStatus('pending');
            $requestCourse->setType('Delete');
            $currentDateTime = new \DateTime();
            $requestCourse->setTime($currentDateTime);
            $requestCourse->setIdtutor($tutor->getId());
            $requestCourse->setCourseId($id);
            $requestCourse->setDescription('Delete this course');
            $entityManager->persist($requestCourse);
            $entityManager->flush();
        }
        else{
            return new Response("Deja required a Delete ");
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



    #[Route('/lesson/add', name: 'add_lesson')]
    public function addLesson(Request $request, EntityManagerInterface $entityManager): Response
    {

        $lesson = new Lesson();
        $form = $this->createForm(LessonFormType::class, $lesson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $name =$form->get('name')->getData();;
            $duration =$form->get('duration')->getData();;
            $videoFile = $form->get('video')->getData();


            if ($videoFile) {
                $originalFilename = pathinfo($videoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $slugify = new Slugify();

                // Generate a slug from the original filename
                $safeFilename = $slugify->slugify($originalFilename);
                // $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$videoFile->guessExtension();
                $videoFile->move(
                        $this->getParameter('video_directory'),
                        $newFilename
                );
                

                $lesson->setUrlvideo($newFilename);
                $lesson->setName($name);
                $lesson->setDuration($duration);
                $entityManager->persist($lesson);
                $entityManager->flush();

                return $this->redirectToRoute('add_lesson');
            }
        }

        return $this->render('tutor/add_lesson.html.twig', [
            'form' => $form->createView(),
        ]);
    }


}









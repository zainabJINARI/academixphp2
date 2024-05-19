<?php

namespace App\Controller;

use App\Form\CourseType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
 
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Course ;
use App\Repository\UserRepository ;
use App\Entity\RequestCourse ;

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
        $request->setStatus(false);
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

        return $this->render('tutor/detail_course_edit.html.twig', [
            'course' => $course,
            'form' => $form->createView(),

        ]);
    }
}

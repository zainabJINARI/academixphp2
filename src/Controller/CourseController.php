<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CourseRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Entity\Course;
use App\Entity\Module;

use App\Entity\Enrollment;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;


class CourseController extends AbstractController
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/allcourses', name: 'all_courses')]
    public function index(Request $request,
    CourseRepository $courseRepository): Response
    {
        $topic = $request->query->get('topic');
        $coursesData = [];
        if ($topic === 'popular') {
            $courses = $courseRepository->createQueryBuilder('c')
                ->select('c', 'u.username as tutor_name', 'u.profileImage as tutor_photo')
                ->leftJoin('c.tutor', 'u')
                ->getQuery()
                ->getResult();
            foreach ($courses as $course) {
                    $coursesData[] = [
                        'id' => $course[0]->getId(),
                        'title' => $course[0]->getTitle(),
                        'nbrHours' => $course[0]->getNbrHours(),
                        'nbrLessons' => $course[0]->getNbrLessons(),
                        'thumbnail' => $course[0]->getThumbnail(),
                        'level' => $course[0]->getLevel(),
                        'tutor_name' => $course['tutor_name'],
                        'tutor_photo' => $course['tutor_photo'],
                    ];
            }    
        } else {
            $courses = $courseRepository->findBy(['category' => $topic]);
            foreach ($courses as $course) {
                $coursesData[] = [
                    'id' => $course->getId(), 
                    'title' => $course->getTitle(), 
                    'nbrHours' => $course->getNbrHours(),
                    'nbrLessons' => $course->getNbrLessons(),
                    'thumbnail' => $course->getThumbnail(),
                    'level' => $course->getLevel(),
                    'tutor_name' => $course->getTutor() ? $course->getTutor()->getUsername() : null,
                    'tutor_photo' => $course->getTutor() ? $course->getTutor()->getProfileImage() : null,
                ];
            }
        }
        return $this->render('course/index.html.twig', [
            'controller_name' => 'CourseController',
            'courses' => $coursesData
        ]);
    }



    #[Route('/filter', name: 'all_filters')]
    public function filter(Request $request , CourseRepository $courseRepository)
    {
        $tut = $request->query->get('tut');
        $dur = $request->query->get('dur');
        $level = $request->query->get('level');

        $queryBuilder = $courseRepository->createQueryBuilder('c')
            ->select('c', 'u.username as tutor_name', 'u.profileImage as tutor_photo')
            ->leftJoin('c.tutor', 'u');

            if ($tut) {
                $queryBuilder->andWhere('u.username = :tut')
                    ->setParameter('tut', $tut);
            }

            if ($dur === 'short') {
                $queryBuilder->andWhere('c.nbrHours < :nbrHours')
                    ->setParameter('nbrHours', 1);
            } elseif ($dur === 'medium') {
                $queryBuilder->andWhere('c.nbrHours >= :minHours AND c.nbrHours <= :maxHours')
                    ->setParameter('minHours', 1)
                    ->setParameter('maxHours', 3);
            } elseif ($dur === 'long') {
                $queryBuilder->andWhere('c.nbrHours > :nbrHours')
                    ->setParameter('nbrHours', 3);
            }

            if ($level) {
                $queryBuilder->andWhere('c.level = :level')
                    ->setParameter('level', $level);
            }

            
             $courses = $queryBuilder->getQuery()->getResult();

                $coursesData = [];
                if(count($courses) > 0) {
                    foreach ($courses as $course) {
                        $coursesData[] = [
                            'id' => $course[0]->getId(),
                            'title' => $course[0]->getTitle(),
                            'nbrHours' => $course[0]->getNbrHours(),
                            'nbrLessons' => $course[0]->getNbrLessons(),
                            'thumbnail' => $course[0]->getThumbnail(),
                            'level' => $course[0]->getLevel(),
                            'tutor_name' => $course['tutor_name'],
                            'tutor_photo' => $course['tutor_photo'],
                        ];
                }   
                }
              
            return $this->render('course/index.html.twig', [

                'courses' => $coursesData
            ]);
    }
    #[Route('/enroll/{id}', name: 'enroll_course')]
    public function enrollCourse(int $id, EntityManagerInterface $entityManager): Response
    {
        
        $user = $this->getUser();
        $course = $entityManager->getRepository(Course::class)->find($id);
        if (!$user || !in_array('ROLE_STUDENT', $user->getRoles()) || !$course) {
            return new Response('Error: Unable to enroll in the course', Response::HTTP_BAD_REQUEST);
        }
        $enrollment = new Enrollment();
        $enrollment->setStudent($user);
        $enrollment->setCourse($course);
        $entityManager->persist($enrollment);
        $entityManager->flush();
        return $this->redirectToRoute('course_details', ['id' => $id]);
    }

    #[Route('/courses/{id}', name: 'course_details')]
    public function details(int $id, EntityManagerInterface $entityManager): Response
    {
        $course = $entityManager->getRepository(Course::class)->find($id);

        if (!$course) {
            throw $this->createNotFoundException('The course does not exist');
        }

        $tutor = $course->getTutor();
        $modules = $entityManager->getRepository(Module::class)->findBy(['idCourse' => $id], ['order' => 'ASC']);
        $enrollmentRepository = $entityManager->getRepository(Enrollment::class);
        $enrollmentCount = $enrollmentRepository->countStudentsByCourse($id);

        // Check if the current user is a student and fetch their enrollments
        $studentEnrollments = [];
        $user = $this->security->getUser();
        if ($user && in_array('ROLE_STUDENT', $user->getRoles())) {
            $studentEnrollments = $enrollmentRepository->findCoursesByStudent($user->getUserIdentifier());
        }

        return $this->render('course/course_details.html.twig', [
            'course' => $course,
            'tutor' => $tutor,
            'modules' => $modules,
            'enrollmentCount' => $enrollmentCount,
            'studentEnrollments' => $studentEnrollments,
        ]);
    }

}





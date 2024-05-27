<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CourseRepository;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Course;
use App\Entity\User;
use App\Entity\Module;
use App\Entity\CourseProgress;
use App\Entity\ModuleProgress;
use App\Entity\LessonProgress;

use App\Entity\Enrollment;
use App\Entity\Lesson;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Security;


class CourseController extends AbstractController
{


    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/allcourses', name: 'all_courses')]
    public function index(
        Request $request,
        CourseRepository $courseRepository
    ): Response {
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
    public function filter(Request $request, CourseRepository $courseRepository)
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
        if (count($courses) > 0) {
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
        // Check if the student is already enrolled
        $existingEnrollment = $entityManager->getRepository(Enrollment::class)
            ->findOneBy(['course' => $course, 'student' => $user]);

        if ($existingEnrollment) {
            return new Response('Error: You are already enrolled in this course', Response::HTTP_CONFLICT);
        }
        // create enrollement instance 
        $enrollment = new Enrollment();
        $enrollment->setStudent($user);
        $enrollment->setCourse($course);


        // start progress tracking
        $courseProgress = new CourseProgress();
        $courseProgress->setCourse($course);
        $courseProgress->setStudent($user);
        $modules = $entityManager->getRepository(Module::class)->getModules($course->getId());
        $courseProgress->setTotalModules(count($modules));
        $courseProgress->setCompletedModules(0);


        $enrollment->setCourseProgress($courseProgress);
        $entityManager->persist($courseProgress);
        $entityManager->persist($enrollment);
        $entityManager->flush();

        foreach ($modules as $module) {
            $moduleProgress = new ModuleProgress();
            $moduleProgress->setModule($module);
            $moduleProgress->setStudent($user);
            $lessons = $entityManager->getRepository(Lesson::class)->getLessons($module->getId());
            $moduleProgress->setTotalLessons(count($lessons));
            $moduleProgress->setCompletedLessons(0);
            $moduleProgress->setCourseProgress($courseProgress);
            $entityManager->persist($moduleProgress);

            foreach ($lessons as $lesson) {
                $lessonProgress = new LessonProgress();
                $lessonProgress->setLesson($lesson);
                $lessonProgress->setStudent($user);
                $lessonProgress->setCompleted(false);
                $lessonProgress->setModuleProgress($moduleProgress);
                $entityManager->persist($lessonProgress);
            }
        }

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
        $lessonsByModule = [];
        foreach ($modules as $module) {
            $lessons = $entityManager->getRepository(Lesson::class)->findBy(['idModule' => $module->getId()]);
            $lessonsByModule[$module->getId()] = $lessons;
        }

        // Check if the current user is a student and fetch their enrollments
        $studentEnrollment = null;
        $user = $this->security->getUser();

        if ($user && in_array('ROLE_STUDENT', $user->getRoles())) {
            $student = $entityManager->getRepository(User::class)->findOneBy(['email' => $user->getUserIdentifier()]);
            $studentEnrollment = $enrollmentRepository->findEnrollmentByUserAndCourse($student->getId(), $course->getId());
        }
        $courseProgress = null;
        $moduleProgresses = [];
        $lessonProgressesByModule = [];
        $isCourseCompleted = false;
        $progressPercentage = 0;
        $moduleProgressData = [];
        $lessonCompletionStatus = [];

        if ($studentEnrollment) {
            $courseProgress = $studentEnrollment->getCourseProgress();
            $queryBuilder = $entityManager->createQueryBuilder();
            $moduleProgresses = $queryBuilder
                ->select('mp')
                ->from(ModuleProgress::class, 'mp')
                ->where('mp.courseProgress = :courseProgressId')
                ->setParameter('courseProgressId', $courseProgress->getId())
                ->getQuery()
                ->getResult();

            foreach ($modules as $module) {
                $lessons = $entityManager->getRepository(Lesson::class)->findBy(['idModule' => $module->getId()]);
                $lessonsByModule[$module->getId()] = $lessons;

                // Initialize progress data
                $moduleProgressData[$module->getId()] = [
                    'completedLessons' => 0,
                    'totalLessons' => count($lessons),
                    'progress' => 0
                ];

                foreach ($moduleProgresses as $moduleProgress) {
                    if ($moduleProgress->getModule()->getId() === $module->getId()) {
                        $completedLessons = $moduleProgress->getCompletedLessons();
                        $totalLessons = $moduleProgressData[$module->getId()]['totalLessons'];
                        $progress = $totalLessons > 0 ? ($completedLessons / $totalLessons) * 100 : 0;

                        $moduleProgressData[$module->getId()] = [
                            'completedLessons' => $completedLessons,
                            'totalLessons' => $totalLessons,
                            'progress' => $progress
                        ];
                    }
                }


                $lessonCompletionStatus = [];

                foreach ($modules as $module) {
                    $lessons = $entityManager->getRepository(Lesson::class)->findBy(['idModule' => $module->getId()]);
                    $lessonCompletionStatus[$module->getId()] = [];

                    foreach ($lessons as $lesson) {
                        $courseProgress = $studentEnrollment->getCourseProgress();

                        // Fetch the LessonProgress entity for the given lesson and course progress
                        $lessonProgress = $entityManager->getRepository(LessonProgress::class)->findOneBy([
                            'lesson' => $lesson,
                            
                        ]);

                        // Check if the lesson has been completed
                        $isCompleted = $lessonProgress && $lessonProgress->isCompleted();

                        $lessonCompletionStatus[$module->getId()][$lesson->getId()] = $isCompleted;
                    }
                }
            }


            // Calculate progress percentage
            if ($courseProgress->getTotalModules() > 0) {
                $progressPercentage = ($courseProgress->getCompletedModules() / $courseProgress->getTotalModules()) * 100;
            }

            // Check if the course is completed
            $isCourseCompleted = $courseProgress->isCompleted();
        }

        return $this->render('course/course_details.html.twig', [
            'course' => $course,
            'tutor' => $tutor,
            'modules' => $modules,
            'enrollmentCount' => $enrollmentCount,
            'studentEnrollment' => $studentEnrollment,
            'progressPercentage' => $progressPercentage,
            'isCourseCompleted' => $isCourseCompleted,
            'lessonsByModule' => $lessonsByModule,
            'moduleProgressData' => $moduleProgressData,
            'lessonCompletionStatus' => $lessonCompletionStatus

        ]);
    }

    // #[Route('/courses/{id}', name: 'course_details')]
    // public function details(int $id, EntityManagerInterface $entityManager, Security $security): Response
    // {
    //     $course = $entityManager->getRepository(Course::class)->find($id);

    //     if (!$course) {
    //         throw $this->createNotFoundException('The course does not exist');
    //     }

    //     $tutor = $course->getTutor();
    //     $modules = $entityManager->getRepository(Module::class)->findBy(['idCourse' => $id], ['order' => 'ASC']);
    //     $enrollmentRepository = $entityManager->getRepository(Enrollment::class);
    //     $enrollmentCount = $enrollmentRepository->countStudentsByCourse($id);

    //     $user = $security->getUser();
    //     $studentEnrollments = [];
    //     $courseProgress = null;
    //     $moduleProgresses = [];
    //     $lessonProgresses = [];

    //     if ($user && in_array('ROLE_STUDENT', $user->getRoles())) {
    //         $studentEnrollments = $enrollmentRepository->findCoursesByStudent($user->getUserIdentifier());
    //         $enrolled = false;
    //         foreach ($studentEnrollments as $enrollment) {
    //             if ($enrollment->getCourse() === $course) {
    //                 $enrolled = true;
    //                 break;
    //             }
    //         }

    //         if ($enrolled) {

    //             $courseProgress = $entityManager->getRepository(CourseProgress::class)
    //                 ->findOneBy(['course' => $course, 'student' => $user]);

    //             foreach ($courseProgress->getModuleProgresses() as $moduleProgress) {
    //                 $moduleProgresses[] = $moduleProgress;

    //                 foreach ($moduleProgress->getLessonProgresses() as $lessonProgress) {
    //                     $lessonProgresses[] = $lessonProgress;
    //                 }
    //             }
    //         }

    //     }

    //     return $this->render('course/course_details.html.twig', [
    //         'course' => $course,
    //         'tutor' => $tutor,
    //         'modules' => $modules,
    //         'enrollmentCount' => $enrollmentCount,
    //         'studentEnrollments' => $studentEnrollments,
    //         'courseProgress' => $courseProgress,
    //         'moduleProgresses' => $moduleProgresses,
    //         'lessonProgresses' => $lessonProgresses,
    //     ]);

    // }

}

<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CourseRepository;
use Symfony\Component\HttpFoundation\JsonResponse;



class CourseController extends AbstractController
{
    #[Route('/allcourses', name: 'all_courses')]
    public function index(EntityManagerInterface $entityManager,
    CourseRepository $courseRepository): Response
    {

        $courses = $courseRepository->createQueryBuilder('c')
        ->select('c', 'u.fullname as tutor_name', 'u.profilePhoto as tutor_photo')
        ->leftJoin('c.tutor', 'u')
        ->getQuery()
        ->getResult();

       
        $coursesData = [];
        foreach ($courses as $course) {
           
            
            $coursesData[] = [
                'id' => $course[0]->getId(), 
                'title' => $course[0]->getTitle(), 
                'nbrHours'=>$course[0]->getNbrHours(),
                'nbrLessons'=>$course[0]->getNbrLessons(),
                'thumbnail'=>$course[0]->getThumbnail(),
                'level'=>$course[0]->getLevel(),
                'tutor_name' => $course['tutor_name'],
                'tutor_photo' => $course['tutor_photo'],
            ];
        }

        // Return a JSON response containing the courses data
         return $this->render('course/index.html.twig', [
            'controller_name' => 'CourseController',
            'courses'=>$coursesData
        ]);
    }
}

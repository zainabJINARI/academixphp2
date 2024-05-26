<?php

namespace App\Form;

use App\Entity\Lesson;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Repository\LessonRepository;





class LessonFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('duration', NumberType::class, [
                'constraints' => [
                    new Positive(['message' => 'The duration must be a positive number.']),
                ],
            ])
            ->add('video', FileType::class, [
                'label' => 'Video (MP4 file)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024M',
                        'mimeTypes' => [
                            'video/mp4',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid MP4 video',
                    ])
                ],
            ])
            ->add('position', ChoiceType::class, [
                'choices' => [
                    'Add at the beginning' => 'beginning',
                    'Add at the end' => 'end',
                    'Add after a specific lesson' => 'after_lesson',
                ],
                'expanded' => true,
                'mapped' => false, // This field is not mapped to an entity property
                'required' => true,
            ])
            ->add('afterLesson', EntityType::class, [
                'class' => Lesson::class,
                'choice_label' => 'name',
                'label' => 'Select a lesson to add after:',
                'required' => false,
                'placeholder' => 'Select a lesson',
                'mapped' => false, // This field is not mapped to an entity property
                'query_builder' => function (LessonRepository $repository) use ($options) {
                    return $repository->createQueryBuilder('m')
                        ->andWhere('m.id = :moduleId')
                        ->setParameter('moduleId', $options['module_id']);
                },
            ]);
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lesson::class,
        ]);

        $resolver->setRequired(['module_id']);
    }
}
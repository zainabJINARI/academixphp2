<?php
namespace App\Form;

use App\Entity\Module;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ModuleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;

class ModuleType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class)
            ->add('position', ChoiceType::class, [
                'choices' => [
                    'Add at the beginning' => 'beginning',
                    'Add at the end' => 'end',
                    'Add after a specific module' => 'after_module',
                ],
                'expanded' => true,
                'mapped' => false, // This field is not mapped to an entity property
                'required' => true,
            ])
            ->add('afterModule', EntityType::class, [
                'class' => Module::class,
                'choice_label' => 'name',
                'label' => 'Select a module to add after:',
                'required' => false,
                'placeholder' => 'Select a module',
                'mapped' => false, // This field is not mapped to an entity property
                'query_builder' => function (ModuleRepository $repository) use ($options) {
                    return $repository->createQueryBuilder('m')
                        ->andWhere('m.idCourse = :courseId')
                        ->setParameter('courseId', $options['course_id']);
                },
            ]);
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Module::class,
        ]);
        $resolver->setRequired(['course_id']);
    }
}

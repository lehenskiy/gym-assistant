<?php

declare(strict_types=1);

namespace App\Exercise\Add;

use App\Shared\Enum\Muscle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddExerciseForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('description', TextareaType::class)
            ->add('executionTechnique', TextareaType::class)
            ->add('executionTips', TextareaType::class)
            ->add('difficulty', IntegerType::class, ['attr' => ['min' => 1, 'max' => 5]])
            ->add('targetMuscles', ChoiceType::class, [
                'expanded' => true,
                'placeholder' => 'Choose target muscles',
                'multiple' => true,
                'choices' => [
                    'Upper body muscles' => [
                        Muscle::RearDelt->fullName() => Muscle::RearDelt,
                        Muscle::MiddleDelt->fullName() => Muscle::MiddleDelt,
                        Muscle::FrontDelt->fullName() => Muscle::FrontDelt,
                        Muscle::Trapezius->fullName() => Muscle::Trapezius,
                        Muscle::Latissimus->fullName() => Muscle::Latissimus,
                        Muscle::ExtensorSpine->fullName() => Muscle::ExtensorSpine,
                        Muscle::PectoralUpper->fullName() => Muscle::PectoralUpper,
                        Muscle::PectoralLower->fullName() => Muscle::PectoralLower,
                        Muscle::Biceps->fullName() => Muscle::Biceps,
                        Muscle::Triceps->fullName() => Muscle::Triceps,
                        Muscle::ObliqueAbs->fullName() => Muscle::ObliqueAbs,
                        Muscle::RectusAbs->fullName() => Muscle::RectusAbs,
                    ],
                    'Leg muscles' =>
                        [
                            Muscle::Gluteal->fullName() => Muscle::Gluteal,
                            Muscle::Quadriceps->fullName() => Muscle::Quadriceps,
                            Muscle::Hamstring->fullName() => Muscle::Hamstring,
                            Muscle::Calf->fullName() => Muscle::Calf,
                        ]
                ],
            ])
            ->add('image', FileType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AddExerciseDTO::class,
            'required' => false,
            'empty_data' => new AddExerciseDTO(),
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}

<?php

declare(strict_types=1);

namespace App\Exercise\Search;

use App\Shared\Enum\Muscle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titleToSearch', SearchType::class)
            ->add('authorToSearch', SearchType::class)
            ->add('difficultiesForFilter', ChoiceType::class, [
                'expanded' => true,
                'multiple' => true,
                'choices' => [
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5
                ]
            ])
            ->add('containsImage', ChoiceType::class, [
                'choices' => [
                    'Any' => null,
                    'Yes' => true,
                    'No' => false
                ],
            ])
            ->add('targetMusclesForFilter', ChoiceType::class, [
                'expanded' => true,
                'placeholder' => 'Choose target muscles',
                'multiple' => true,
                'choices' => [
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
                    Muscle::Gluteal->fullName() => Muscle::Gluteal,
                    Muscle::Quadriceps->fullName() => Muscle::Quadriceps,
                    Muscle::Hamstring->fullName() => Muscle::Hamstring,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SearchExerciseParamsDTO::class,
            'required' => false,
            'empty_data' => new SearchExerciseParamsDTO(),
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}

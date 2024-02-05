<?php

declare(strict_types=1);

namespace App\UserProfile\Edit;

use App\Shared\Enum\UserGender;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditProfileForm extends AbstractType
{
    private const WEIGHT_MULTIPLIER = 10;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('public', CheckboxType::class)
            ->add('gender', ChoiceType::class, [
                'choices' => [
                    'Not selected' => null,
                    UserGender::Male->name => UserGender::Male,
                    UserGender::Female->name => UserGender::Female
                ]
            ])
            ->add('birthDate', DateType::class, [
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
            ])
            ->add('height', IntegerType::class)
            ->add('currentWeight', NumberType::class, [
                'scale' => 1,
                'setter' => function (EditProfileDTO $editDTO, ?float $currentWeight = null): void {
                    if ($currentWeight !== null) {
                        $editDTO->currentWeight = (int)($currentWeight * self::WEIGHT_MULTIPLIER);
                    } else {
                        $editDTO->currentWeight = null;
                    }
                }
            ])
            ->add('goalWeight', NumberType::class, [
                'scale' => 1,
                'setter' => function (EditProfileDTO $editDTO, ?float $goalWeight = null): void {
                    if ($goalWeight !== null) {
                        $editDTO->goalWeight = (int)($goalWeight * self::WEIGHT_MULTIPLIER);
                    } else {
                        $editDTO->goalWeight = null;
                    }
                },
                'getter' => function (EditProfileDTO $editDTO): ?float {
                    if ($editDTO->goalWeight === null) {
                        return null;
                    }

                    return (float)($editDTO->goalWeight / self::WEIGHT_MULTIPLIER);
                }
            ])
            ->add('email', EmailType::class)
            ->add('currentPassword', PasswordType::class)
            ->add('newPassword', PasswordType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EditProfileDTO::class,
            'required' => false,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}

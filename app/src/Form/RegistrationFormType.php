<?php

namespace App\Form;

use App\UseCase\UserRegistration\RegisterUserDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Email is required']),
                    new Assert\Email(['message' => 'Incorrect email address']),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Password is required']),
                    new Assert\Length([
                        'min' => 6,
                        'minMessage' => 'The password must be {{ limit }} characters or less',
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'I agree to the terms of service',
                'mapped' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RegisterUserDto::class,
        ]);
    }
}

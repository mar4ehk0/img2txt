<?php

namespace App\UseCase\UserRegistration;

use Symfony\Component\Validator\Constraints as Assert;

readonly class RegisterUserDto
{
    #[Assert\NotBlank(message: 'Email is required')]
    #[Assert\Email(message: 'Incorrect email address')]
    public string $email;

    #[Assert\NotBlank(message: 'Password is required')]
    #[Assert\Length(
        min: 6,
        minMessage: 'The password must be {{ limit }} characters or less',
        max: 4096
    )]
    public string $plainPassword;

    #[Assert\IsTrue(message: 'You must agree to the terms and conditions.')]
    public bool $agreeTerms;
}

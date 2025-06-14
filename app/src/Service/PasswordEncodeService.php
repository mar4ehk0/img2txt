<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

readonly class PasswordEncodeService
{
    public function __construct(
        private PasswordHasherFactoryInterface $factory,
    ) {
    }

    public function hash(string $plainPassword): string
    {
        $hasher = $this->factory->getPasswordHasher(User::class);

        return $hasher->hash($plainPassword);
    }
}

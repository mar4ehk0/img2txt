<?php

namespace App\UseCase\UserRegistration;

use App\Entity\User;
use App\Service\PasswordEncodeService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Factory\UlidFactory;

class RegistrationHandler
{
    public function __construct(
        private EntityManagerInterface $em,
        private PasswordEncodeService $passwordEncoder,
        private UlidFactory $ulidFactory,
    ) {
    }

    public function handle(RegisterUserDto $data): void
    {
        $now = new \DateTimeImmutable();
        $ulid = $this->ulidFactory->create();
        $hashedPassword = $this->passwordEncoder->hash($data->plainPassword);

        $user = new User(
            id: $ulid,
            email: $data->email,
            roles: [],
            password: $hashedPassword,
            images: new ArrayCollection([]),
            isVerified: false,
            createdAt: $now,
            updatedAt: $now
        );

        $this->em->persist($user);
        $this->em->flush();
    }
}

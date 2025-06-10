<?php

namespace App\Service;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class JwtTokenValidator
{
    public function __construct(
        private JWTTokenManagerInterface $jwtManager,
        private UserProviderInterface $userProvider,
    ) {
    }

    public function validateToken(?string $authHeader): ?UserInterface
    {
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return null;
        }

        $token = substr($authHeader, 7);
        try {
            $data = $this->jwtManager->parse($token);

            $username = $data['email'] ?? null;
            if (!$username) {
                return null;
            }

            return $this->userProvider->loadUserByIdentifier($username);
        } catch (\Exception) {
            return null;
        }
    }
}

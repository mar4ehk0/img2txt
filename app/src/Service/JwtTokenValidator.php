<?php

namespace App\Service;

use App\Exception\InvalidTokenException;
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

    public function validateToken(string $authHeader): UserInterface
    {
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            throw new InvalidTokenException('Missing Bearer token');
        }

        $token = substr($authHeader, 7);

        try {
            $data = $this->jwtManager->parse($token);
            $username = $data['email'] ?? null;

            if (!$username) {
                throw new InvalidTokenException('Email not found in token');
            }

            return $this->userProvider->loadUserByIdentifier($username);
        } catch (\Throwable $e) {
            throw new InvalidTokenException('Token is invalid or expired');
        }
    }
}

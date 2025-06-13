<?php

namespace App\Controller;

use App\Service\JwtTokenValidator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LoginController extends BaseController
{
    public function __construct(
        private JwtTokenValidator $jwtTokenValidator,
    ) {
    }

    #[Route('/login', name: 'app_login', methods: ['GET'])]
    public function login(): Response
    {
        return $this->render('login/login.html.twig');
    }

    #[Route('/check-token', name: 'app_check_token', methods: ['GET'])]
    public function checkToken(Request $request): JsonResponse
    {
        $authHeader = $request->headers->get('Authorization');
        $user = $this->jwtTokenValidator->validateToken($authHeader);

        return new JsonResponse([
            'message' => 'Login Successful',
        ]);
    }
}

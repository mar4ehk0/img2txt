<?php

namespace App\Controller;

use App\Service\JwtTokenValidator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LoginController extends BaseController
{
    #[Route('/login', name: 'app_login')]
    public function login(): Response
    {
        return $this->render('login/login.html.twig');
    }

    #[Route('/check-token', name: 'app_check_token')]
    public function checkToken(Request $request, JwtTokenValidator $validator): JsonResponse
    {
        $authHeader = $request->headers->get('Authorization');
        $user = $validator->validateToken($authHeader);
        if (!$user) {
            return $this->redirectToRoute('app_register');
        }

        return new JsonResponse('Login success');
    }
}

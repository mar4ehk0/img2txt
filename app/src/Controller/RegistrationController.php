<?php

namespace App\Controller;

use App\Form\RegistrationFormType;
use App\UseCase\UserRegistration\RegisterUserDto;
use App\UseCase\UserRegistration\RegistrationHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends BaseController
{
    public function __construct(
        private RegistrationHandler $handler,
        private Request $request,
    ) {
    }

    #[Route('/register', name: 'app_register', methods: ['GET', 'POST'])]
    public function register(): Response
    {
        $formData = new RegisterUserDto();
        $form = $this->createForm(RegistrationFormType::class, $formData);
        $form->handleRequest($this->request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handler->handle($formData);

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}

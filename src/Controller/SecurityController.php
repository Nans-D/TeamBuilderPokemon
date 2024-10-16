<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(CsrfTokenManagerInterface $csrfTokenManager, AuthenticationUtils $authenticationUtils, Request $request): Response
    {

        $csrfToken = $csrfTokenManager->getToken('authenticate')->getValue();

        if ($this->getUser()) {
            return $this->redirectToRoute('app_index');
        }

        // get the login error if there is one
        $errorResponse = null;
        if ($error = $authenticationUtils->getLastAuthenticationError()) {
            $errorResponse = $error->getMessage();
        }

        return $this->render('security/login.html.twig', [
            'csrf_token' => $csrfToken,
            'error' => $errorResponse,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}

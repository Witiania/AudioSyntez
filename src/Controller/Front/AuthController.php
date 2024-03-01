<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AuthController extends AbstractController
{
    #[Route('/register', name: 'register_page', methods: 'GET')]
    public function registerPage(): Response
    {
        return $this->render('auth/register.html.twig');
    }

    #[Route('/verify', name: 'verify_page', methods: 'GET')]
    public function verifyPage(): Response
    {
        return $this->render('auth/verify.html.twig');
    }

    #[Route('/login', name: 'login_page', methods: 'GET')]
    public function loginPage(): Response
    {
        return $this->render('auth/login.html.twig');
    }

    #[Route('/reset_password', name: 'reset_password', methods: 'GET')]
    public function resetPage(): Response
    {
        return $this->render('auth/reset_password.html.twig');
    }

    #[Route('/reset_code', name: 'reset_code', methods: 'GET')]
    public function resetCodePage(): Response
    {
        return $this->render('auth/reset_code.html.twig');
    }

    #[ Route('/home_page', name: 'home_page', methods: 'GET')]
    public function homePage(): Response
    {
        return $this->render('auth/home_page.html.twig');
    }
}

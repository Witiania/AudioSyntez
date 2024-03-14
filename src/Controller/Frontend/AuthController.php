<?php

namespace App\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AuthController extends AbstractController
{
    #[Route('/register', name: 'register_page', methods: 'GET')]
    public function registerPage(): Response
    {
        return $this->render('register.html.twig');
    }

    #[Route('/verify', name: 'verify_page', methods: 'GET')]
    public function verifyPage(): Response
    {
        return $this->render('verify.html.twig');
    }

    #[Route('/login', name: 'login_page', methods: 'GET')]
    public function loginPage(): Response
    {
        return $this->render('login.html.twig');
    }

    #[Route('/reset_password', name: 'reset_password', methods: 'GET')]
    public function resetPage(): Response
    {
        return $this->render('reset_password.html.twig');
    }

    #[Route('/reset_code', name: 'reset_code', methods: 'GET')]
    public function resetCodePage(): Response
    {
        return $this->render('reset_code.html.twig');
    }

    #[Route('/home_page', name: 'home_page', methods: 'GET')]
    public function homePage(): Response
    {
        return $this->render('home_page.html.twig');
    }
}

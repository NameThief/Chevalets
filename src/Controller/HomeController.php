<?php

namespace App\Controller;

use Twig\Environment;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[route('/', name: 'home')]
    public function home(Environment $twig)
    {
        $html = $twig->render('home/home.html.twig');
        return new Response($html);
    }
}

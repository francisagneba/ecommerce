<?php

namespace App\Controller;

use App\Taxes\Calculator;
use App\Taxes\Detector;
use Cocur\Slugify\Slugify;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class HelloController
{

    /**
     * @Route("/hello/{prenom?Word}", name="hello")
     */
    public function hello(string $prenom, Environment $twig): Response
    {
        $html = $twig->render('hello.html.twig', [
            'prenom' => $prenom,
            'ages' => [
                12,
                18,
                29,
                15
            ]

        ]);
        return new Response($html);
    }
}
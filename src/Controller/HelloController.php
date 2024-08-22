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
    public function hello(string $prenom, LoggerInterface $logger, Calculator $calculator, Slugify $slugify, Environment $twig, Detector $detector): Response
    {
        dump($detector->detect(101));
        dump($detector->detect(10));

        dump($twig);

        dump($slugify->slugify("Hello World"));

        $logger->error("Mon message de log !");

        $tva = $calculator->calcul(100);

        // Pour déboguer
        dump($tva); // Utiliser dump() pour voir le résultat dans la barre de débogage

        return new Response("Hello $prenom, TVA: $tva");
    }
}

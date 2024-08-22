<?php

namespace App\Controller;

use App\Taxes\Calculator;
use Cocur\Slugify\Slugify;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController
{

    /**
     * @Route("/hello/{prenom?Word}", name="hello")
     */
    public function hello(string $prenom, LoggerInterface $logger, Calculator $calculator, Slugify $slugify): Response
    {


        dump($slugify->slugify("Hello World"));

        $logger->error("Mon message de log !");

        $tva = $calculator->calcul(100);

        // Pour déboguer
        dump($tva); // Utiliser dump() pour voir le résultat dans la barre de débogage

        return new Response("Hello $prenom, TVA: $tva");
    }
}

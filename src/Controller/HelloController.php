<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;


class HelloController extends AbstractController
{

    /**
     * @Route("/hello/{prenom?Word}", name="hello")
     */
    public function hello(string $prenom)
    {
        return $this->render('hello.html.twig', [
            'prenom' => $prenom,

        ]);
    }
}
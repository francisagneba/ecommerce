<?php

namespace App\Controller\Purchase;

use App\Entity\User;
use Twig\Environment;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PurchasesListController extends AbstractController
{
    // protected $security;
    // protected $router;
    // protected $twig;

    // public function __construct(Security $security, RouterInterface $router, Environment $twig)
    // {
    //     $this->security = $security;
    //     $this->router = $router;
    //     $this->twig = $twig;
    // }

    #[Route('/purchases', name: 'purchase_index')]
    public function index()
    {
        if (!$this->isGranted('ROLE_USER')) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à vos commandes.');
        }
        //1- Nous voulons nous assurer que la personne est connecté et nous 
        //avons besoin du service Security

        /** @var User */
        //$user = $this->security->getUser();
        $user = $this->getUser();

        //2- Nous voulons passer l'utilisateur conncté à twig afin d'afficher ses commandes
        // $html = $this->twig->render('purchase/index.html.twig', [
        //     'purchases' => $user->getPurchases()
        // ]);
        // return new Response($html);

        return $this->render('purchase/index.html.twig', [
            'purchases' => $user->getPurchases()
        ]);
    }
}
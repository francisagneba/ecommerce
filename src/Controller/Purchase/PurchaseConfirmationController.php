<?php

namespace App\Controller\Purchase;

use App\Cart\CartService;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Form\CartConfirmationType;
use App\Purchase\PurchasePersister;
use DateTime;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

class PurchaseConfirmationController extends AbstractController
{

    protected $security;
    protected $cartService;
    protected $em;
    protected $purchasePersister;

    public function __construct(
        Security $security,
        CartService $cartService,
        EntityManagerInterface $em,
        PurchasePersister $purchasePersister
    ) {
        $this->security = $security;
        $this->cartService = $cartService;
        $this->em = $em;
        $this->purchasePersister = $purchasePersister;
    }

    #[Route('/purchase/confirm', name: 'purchase_confirm')]
    public function confirm(Request $request)
    {

        //1. Nous voulons lire les données du formulaires
        //$form = $this->formFactory->create(CartConfirmationType::class);
        $form = $this->createForm(CartConfirmationType::class);

        //Analyse de la requete 
        $form->handleRequest($request);

        //2. Si le formulaire n'a pas été soumis : dégager
        if (!$form->isSubmitted()) {
            //Message flash puis redirection (FlashBagInterface)
            //$flahBag->add('warning', 'Vous devez remplir le formulaire de confirmation');

            $this->addFlash('warning', 'Vous devez remplir le formulaire de confirmation');

            //return new RedirectResponse($this->router->generate('cart_show'));
            return $this->redirectToRoute('cart_show');
        }

        //3. Si je ne suis pas connecté: dégager (Security)
        //$user = $this->security->getUser();
        $user = $this->getUser();

        if (!$user) {
            throw new AccessDeniedException("Vous devez être connecté pour confirmer une comande");
        }

        //4. S'il n'y a pas de produits dans le panier : dégage (cartService)
        $cartItems = $this->cartService->getDetailedCartItems();

        if (count($cartItems) === 0) {
            $this->addFlash('warning', 'Vous ne pouvez confirmer une commande avec un panier vide');
            //return new RedirectResponse($this->router->generate('cart_show'));
            return $this->redirectToRoute('cart_show');
        }

        //5. Nous allons créer une purchase
        /** @var Purchase */
        $purchase = $form->getData();

        //6. nous allons la lier avec l'utilisateur actuellemnt connecté (securité)
        $this->purchasePersister->storePerchase($purchase);
        //8. Nous allons enregistrer la commande (EntityManagerInterface)
        $this->em->flush();

        //9. Après l'enregistrement de notre commande dans la BD on va demander
        //à notre panier de se vider
        //$this->cartService->empty();

        //$this->addFlash('success', 'La commande a bien été enregistrée');
        //return new RedirectResponse($this->router->generate('purchase_index'));
        return $this->redirectToRoute('purchase_payment_form', [
            'id' => $purchase->getId()
        ]);
    }
}

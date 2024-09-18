<?php

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Repository\PurchaseRepository;
use App\Stripe\StripeService;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasePaymentController extends AbstractController
{
    #[Route('/purchase/pay/{id}', name: 'purchase_payment_form')]
    public function showCardForm($id, PurchaseRepository $purchaseRepository, StripeService $stripeService)
    {
        if (!$this->isGranted('ROLE_USER')) {
            //throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à vos commandes.');
        }

        $purchase = $purchaseRepository->find($id);

        if (
            !$purchase ||
            ($purchase && $purchase->getUser() !== $this->getUser()) ||
            ($purchase && $purchase->getStatus() === Purchase::STATUS_PAID)
        ) {
            return $this->redirectToRoute('cart_show');
        }

        // $stripe = new \Stripe\StripeClient("sk_test_51MMxtSCCiqfrJkmLqIPSCdSB8BsSxZYaV7x1Q10PgrXZbK8wRwJeFj0FlyMSGF0wlDHpf0qTtTGuRIhVeDcYDygZ00TMg4oj7e");

        // // Création du PaymentIntent
        // $paymentIntent = $stripe->paymentIntents->create([
        //     'amount' => $purchase->getTotal() * 100, // Montant en centimes
        //     'currency' => 'eur',
        //     'payment_method_types' => ['card'], // Type de paiement autorisé
        //     'description' => 'Commande #' . $purchase->getId(), // Description de la commande
        // ]);

        // Récupérer le client_secret
        $clientSecret = $stripeService->getPaymentIntent($purchase)->client_secret;

        // Afficher le client_secret pour le débogage
        // dd($clientSecret);

        return $this->render('purchase/payment.html.twig', [
            'clientSecret' => $clientSecret,
            'purchase' => $purchase,
            'stripePublicKey' => $stripeService->getPublicKey()
        ]);
    }
}

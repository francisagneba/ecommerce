<?php

namespace App\Stripe;

use App\Entity\Purchase;

class StripeService
{
    protected $secretKey;
    protected $publicKey;

    public function __construct(string $secretKey, string $publicKey)
    {
        //dd($secretKey, $publicKey);
        $this->secretKey = $secretKey;
        $this->publicKey = $publicKey;
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    public function getPaymentIntent(Purchase $purchase)
    {
        $stripe = new \Stripe\StripeClient($this->secretKey);

        // Création du PaymentIntent
        return $stripe->paymentIntents->create([
            'amount' => $purchase->getTotal() * 100, // Montant en centimes
            'currency' => 'eur',
            'payment_method_types' => ['card'], // Type de paiement autorisé
            'description' => 'Commande #' . $purchase->getId(), // Description de la commande
        ]);
    }
}

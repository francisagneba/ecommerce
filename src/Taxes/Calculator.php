<?php

namespace App\Taxes;

use Psr\Log\LoggerInterface;

class Calculator
{
    protected $logger;
    protected $tva;

    public function __construct(LoggerInterface $logger, float $tva)
    {
        $this->logger = $logger;
        $this->tva = $tva;
    }

    public function calcul(float $prix): float
    {
        $this->logger->info("Un calcul a lieu : $prix");
        return $prix * (20 / 100);
    }
}

class Detector
{
    protected $prixfix;

    public function __construct(float $prixfix)
    {
        $this->prixfix = $prixfix;
    }

    public function detect(float $prix): bool
    {
        if ($prix > $this->prixfix) {
            return True;
        } else {
            return false;
        }
    }
}

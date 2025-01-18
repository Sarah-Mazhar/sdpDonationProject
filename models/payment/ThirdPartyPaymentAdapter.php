<?php

require_once __DIR__ . '/PaymentStrategy.php';
require_once __DIR__ . '/ThirdPartyPaymentGateway.php';

class ThirdPartyPaymentAdapter implements PaymentStrategy {
    private $thirdPartyGateway;

    public function __construct(ThirdPartyPaymentGateway $thirdPartyGateway) {
        $this->thirdPartyGateway = $thirdPartyGateway;
    }

    public function pay($amount) {
        return $this->thirdPartyGateway->processPayment($amount);
    }
}
?>
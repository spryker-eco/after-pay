<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Authorize;

interface PaymentAuthorizeWriterInterface
{
    /**
     * @param string $orderReference
     * @param string $idReservation
     * @param string $idCheckout
     *
     * @return void
     */
    public function save($orderReference, $idReservation, $idCheckout);
}

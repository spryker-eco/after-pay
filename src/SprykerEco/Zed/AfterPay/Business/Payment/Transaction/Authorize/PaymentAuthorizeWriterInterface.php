<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Authorize;

interface PaymentAuthorizeWriterInterface
{
    /**
     * @param string $orderReference
     * @param string $idReservation
     * @param string $idCheckout
     *
     * @return void
     */
    public function save(string $orderReference, string $idReservation = null, string $idCheckout = null): void;
}

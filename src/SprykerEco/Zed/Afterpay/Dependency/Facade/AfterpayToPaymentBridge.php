<?php
/**
 * Copyright © 2017-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Dependency\Facade;

use Generated\Shared\Transfer\SalesPaymentTransfer;

class AfterpayToPaymentBridge implements AfterpayToPaymentInterface
{

    /**
     * @var \Spryker\Zed\Payment\Business\PaymentFacadeInterface
     */
     protected $paymentFacade;

    /**
     * @param \Spryker\Zed\Payment\Business\PaymentFacadeInterface $paymentFacade
     */
    public function __construct($paymentFacade)
    {
        $this->paymentFacade = $paymentFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\SalesPaymentTransfer $salesPaymentTransfer
     *
     * @return int
     */
    public function getPaymentMethodPriceToPay(SalesPaymentTransfer $salesPaymentTransfer)
    {
        return $this->paymentFacade->getPaymentMethodPriceToPay($salesPaymentTransfer);
    }


}

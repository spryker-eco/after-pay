<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Payment\Transaction;

use Generated\Shared\Transfer\AfterPayCallTransfer;
use Generated\Shared\Transfer\SalesPaymentTransfer;
use SprykerEco\Shared\AfterPay\AfterPayConfig;
use SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToPaymentFacadeInterface;

class PriceToPayProvider implements PriceToPayProviderInterface
{
    /**
     * @var \SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToPaymentFacadeInterface
     */
    protected $paymentFacade;

    /**
     * @param \SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToPaymentFacadeInterface $paymentFacade
     */
    public function __construct(AfterPayToPaymentFacadeInterface $paymentFacade)
    {
        $this->paymentFacade = $paymentFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     *
     * @return int
     */
    public function getPriceToPayForOrder(AfterPayCallTransfer $afterPayCallTransfer): int
    {
        $salesPaymentTransfer = $this->createSalesPaymentTransfer($afterPayCallTransfer);

        return $this->paymentFacade->getPaymentMethodPriceToPay($salesPaymentTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     *
     * @return \Generated\Shared\Transfer\SalesPaymentTransfer
     */
    protected function createSalesPaymentTransfer(AfterPayCallTransfer $afterPayCallTransfer): SalesPaymentTransfer
    {
        return (new SalesPaymentTransfer())
            ->setPaymentProvider(AfterPayConfig::PROVIDER_NAME)
            ->setPaymentMethod($afterPayCallTransfer->getPaymentMethod())
            ->setFkSalesOrder($afterPayCallTransfer->getIdSalesOrder());
    }
}

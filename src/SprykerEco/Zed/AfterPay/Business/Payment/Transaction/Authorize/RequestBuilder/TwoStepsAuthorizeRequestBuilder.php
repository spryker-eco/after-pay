<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Authorize\RequestBuilder;

use Generated\Shared\Transfer\AfterPayAuthorizeRequestTransfer;
use Generated\Shared\Transfer\AfterPayCallTransfer;
use Generated\Shared\Transfer\AfterPayRequestOrderTransfer;
use Generated\Shared\Transfer\AfterPayRequestPaymentTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use SprykerEco\Shared\AfterPay\AfterPayConfig;
use SprykerEco\Zed\AfterPay\Business\Payment\Mapper\OrderToRequestTransferInterface;

class TwoStepsAuthorizeRequestBuilder implements AuthorizeRequestBuilderInterface
{
    /**
     * @var array
     */
    protected static $paymentMethods = [
        AfterPayConfig::PAYMENT_METHOD_INVOICE => AfterPayConfig::PAYMENT_TYPE_INVOICE,
    ];

    /**
     * @var \SprykerEco\Zed\AfterPay\Business\Payment\Mapper\OrderToRequestTransferInterface
     */
    protected $orderToRequestMapper;

    /**
     * @param \SprykerEco\Zed\AfterPay\Business\Payment\Mapper\OrderToRequestTransferInterface $orderToRequestMapper
     */
    public function __construct(OrderToRequestTransferInterface $orderToRequestMapper)
    {
        $this->orderToRequestMapper = $orderToRequestMapper;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $orderWithPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayAuthorizeRequestTransfer
     */
    public function buildAuthorizeRequest(AfterPayCallTransfer $orderWithPaymentTransfer): AfterPayAuthorizeRequestTransfer
    {
        $authorizeRequestTransfer = $this
            ->orderToRequestMapper
            ->orderToAuthorizeRequest($orderWithPaymentTransfer);

        $authorizeRequestTransfer
            ->setIdSalesOrder(
                $orderWithPaymentTransfer->getIdSalesOrder()
            );

        $this->addCheckoutId($authorizeRequestTransfer, $orderWithPaymentTransfer);

        return $authorizeRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayAuthorizeRequestTransfer $authorizeRequestTransfer
     * @param \Generated\Shared\Transfer\OrderTransfer $orderWithPaymentTransfer
     *
     * @return void
     */
    protected function addOrderNumber(
        AfterPayAuthorizeRequestTransfer $authorizeRequestTransfer,
        OrderTransfer $orderWithPaymentTransfer
    ): void {
        $requestOrderTransfer = new AfterPayRequestOrderTransfer();
        $requestOrderTransfer->setNumber($orderWithPaymentTransfer->getOrderReference());

        $authorizeRequestTransfer->setOrder($requestOrderTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayAuthorizeRequestTransfer $authorizeRequestTransfer
     * @param \Generated\Shared\Transfer\OrderTransfer $orderWithPaymentTransfer
     *
     * @return void
     */
    protected function addCheckoutId(
        AfterPayAuthorizeRequestTransfer $authorizeRequestTransfer,
        OrderTransfer $orderWithPaymentTransfer
    ): void {
        $checkoutId = $orderWithPaymentTransfer->getAfterPayPayment()->getIdCheckout();
        $authorizeRequestTransfer->setCheckoutId($checkoutId);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayAuthorizeRequestTransfer $authorizeRequestTransfer
     * @param \Generated\Shared\Transfer\OrderTransfer $orderWithPaymentTransfer
     *
     * @return void
     */
    protected function addPaymentDetails(
        AfterPayAuthorizeRequestTransfer $authorizeRequestTransfer,
        OrderTransfer $orderWithPaymentTransfer
    ): void {
        $paymentMethod = $orderWithPaymentTransfer->getAfterPayPayment()->getPaymentMethod();

        $requestPaymentTransfer = new AfterPayRequestPaymentTransfer();
        $requestPaymentTransfer->setType(static::$paymentMethods[$paymentMethod]);

        $authorizeRequestTransfer->setPayment($requestPaymentTransfer);
    }
}

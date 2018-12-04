<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Authorize\RequestBuilder;

use Generated\Shared\Transfer\AfterpayAuthorizeRequestTransfer;
use Generated\Shared\Transfer\AfterpayCallTransfer;
use Generated\Shared\Transfer\AfterpayRequestOrderTransfer;
use Generated\Shared\Transfer\AfterpayRequestPaymentTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use SprykerEco\Shared\Afterpay\AfterpayConfig;
use SprykerEco\Shared\Afterpay\AfterpayConstants;
use SprykerEco\Zed\Afterpay\Business\Payment\Mapper\OrderToRequestTransferInterface;

class TwoStepsAuthorizeRequestBuilder implements AuthorizeRequestBuilderInterface
{
    /**
     * @var array
     */
    protected static $paymentMethods = [
        AfterpayConfig::PAYMENT_METHOD_INVOICE => AfterpayConfig::PAYMENT_TYPE_INVOICE,
    ];

    /**
     * @var \SprykerEco\Zed\Afterpay\Business\Payment\Mapper\OrderToRequestTransferInterface
     */
    private $orderToRequestMapper;

    /**
     * @param \SprykerEco\Zed\Afterpay\Business\Payment\Mapper\OrderToRequestTransferInterface $orderToRequestMapper
     */
    public function __construct(OrderToRequestTransferInterface $orderToRequestMapper)
    {
        $this->orderToRequestMapper = $orderToRequestMapper;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayCallTransfer $orderWithPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayAuthorizeRequestTransfer
     */
    public function buildAuthorizeRequest(AfterpayCallTransfer $orderWithPaymentTransfer): AfterpayAuthorizeRequestTransfer
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
     * @param \Generated\Shared\Transfer\AfterpayAuthorizeRequestTransfer $authorizeRequestTransfer
     * @param \Generated\Shared\Transfer\OrderTransfer $orderWithPaymentTransfer
     *
     * @return void
     */
    protected function addOrderNumber(
        AfterpayAuthorizeRequestTransfer $authorizeRequestTransfer,
        OrderTransfer $orderWithPaymentTransfer
    ): void {
        $requestOrderTransfer = new AfterpayRequestOrderTransfer();
        $requestOrderTransfer->setNumber($orderWithPaymentTransfer->getOrderReference());

        $authorizeRequestTransfer->setOrder($requestOrderTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayAuthorizeRequestTransfer $authorizeRequestTransfer
     * @param \Generated\Shared\Transfer\OrderTransfer $orderWithPaymentTransfer
     *
     * @return void
     */
    protected function addCheckoutId(
        AfterpayAuthorizeRequestTransfer $authorizeRequestTransfer,
        OrderTransfer $orderWithPaymentTransfer
    ): void {
        $checkoutId = $orderWithPaymentTransfer->getAfterpayPayment()->getIdCheckout();
        $authorizeRequestTransfer->setCheckoutId($checkoutId);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayAuthorizeRequestTransfer $authorizeRequestTransfer
     * @param \Generated\Shared\Transfer\OrderTransfer $orderWithPaymentTransfer
     *
     * @return void
     */
    protected function addPaymentDetails(
        AfterpayAuthorizeRequestTransfer $authorizeRequestTransfer,
        OrderTransfer $orderWithPaymentTransfer
    ): void {
        $paymentMethod = $orderWithPaymentTransfer->getAfterpayPayment()->getPaymentMethod();

        $requestPaymentTransfer = new AfterpayRequestPaymentTransfer();
        $requestPaymentTransfer->setType(static::$paymentMethods[$paymentMethod]);

        $authorizeRequestTransfer->setPayment($requestPaymentTransfer);
    }
}

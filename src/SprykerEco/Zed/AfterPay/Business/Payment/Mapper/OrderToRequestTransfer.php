<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Payment\Mapper;

use Generated\Shared\Transfer\AfterPayAuthorizeRequestTransfer;
use Generated\Shared\Transfer\AfterPayCallTransfer;
use Generated\Shared\Transfer\AfterPayCancelRequestTransfer;
use Generated\Shared\Transfer\AfterPayCaptureRequestTransfer;
use Generated\Shared\Transfer\AfterPayRefundRequestTransfer;
use Generated\Shared\Transfer\AfterPayRequestAddressTransfer;
use Generated\Shared\Transfer\AfterPayRequestCustomerTransfer;
use Generated\Shared\Transfer\AfterPayRequestOrderItemTransfer;
use Generated\Shared\Transfer\AfterPayRequestOrderTransfer;
use Generated\Shared\Transfer\AfterPayRequestPaymentTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use SprykerEco\Shared\AfterPay\AfterPayConfig;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\PriceToPayProviderInterface;
use SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToMoneyFacadeInterface;
use SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToStoreFacadeInterface;

class OrderToRequestTransfer implements OrderToRequestTransferInterface
{
    public const NEGATIVE_MULTIPLIER = -1;
    public const GIFT_CARD_PROVIDER = 'GiftCard';

    /**
     * @var \SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToMoneyFacadeInterface
     */
    protected $moneyFacade;

    /**
     * @var \SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToStoreFacadeInterface
     */
    protected $storeFacade;

    /**
     * @var array
     */
    protected static $paymentMethods = [
        AfterPayConfig::PAYMENT_METHOD_INVOICE => AfterPayConfig::PAYMENT_TYPE_INVOICE,
    ];

    /**
     * @var \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\PriceToPayProviderInterface
     */
    protected $priceToPayProvider;

    /**
     * @param \SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToMoneyFacadeInterface $moneyFacade
     * @param \SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToStoreFacadeInterface $storeFacade
     * @param \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\PriceToPayProviderInterface $priceToPayProvider
     */
    public function __construct(
        AfterPayToMoneyFacadeInterface $moneyFacade,
        AfterPayToStoreFacadeInterface $storeFacade,
        PriceToPayProviderInterface $priceToPayProvider
    ) {
        $this->moneyFacade = $moneyFacade;
        $this->storeFacade = $storeFacade;
        $this->priceToPayProvider = $priceToPayProvider;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayAuthorizeRequestTransfer
     */
    public function orderToAuthorizeRequest(AfterPayCallTransfer $afterPayCallTransfer): AfterPayAuthorizeRequestTransfer
    {
        $requestTransfer = new AfterPayAuthorizeRequestTransfer();

        $requestTransfer
            ->setPayment(
                $this->buildPaymentRequestTransfer($afterPayCallTransfer)
            )
            ->setCustomer(
                $this->buildCustomerRequestTransfer($afterPayCallTransfer)
            )
            ->setOrder(
                $this->buildOrderWithItemsRequestTransfer($afterPayCallTransfer)
            );

        return $requestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayCaptureRequestTransfer
     */
    public function orderToBaseCaptureRequest(AfterPayCallTransfer $afterPayCallTransfer): AfterPayCaptureRequestTransfer
    {
        $requestTransfer = new AfterPayCaptureRequestTransfer();

        $requestTransfer
            ->setOrderDetails(
                $this->buildOrderRequestTransfer($afterPayCallTransfer)
                    ->setTotalGrossAmount(0)
                    ->setTotalNetAmount(0)
            );

        return $requestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayCancelRequestTransfer
     */
    public function orderToBaseCancelRequest(AfterPayCallTransfer $afterPayCallTransfer): AfterPayCancelRequestTransfer
    {
        $requestTransfer = new AfterPayCancelRequestTransfer();

        $requestTransfer
            ->setCancellationDetails(
                $this->buildOrderRequestTransfer($afterPayCallTransfer)
                    ->setTotalGrossAmount(0)
                    ->setTotalNetAmount(0)
            );

        return $requestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayRefundRequestTransfer
     */
    public function orderToBaseRefundRequest(OrderTransfer $orderTransfer): AfterPayRefundRequestTransfer
    {
        $refundRequestTransfer = new AfterPayRefundRequestTransfer();

        $refundRequestTransfer
            ->setIdSalesOrder($orderTransfer->getIdSalesOrder())
            ->setOrderNumber($orderTransfer->getOrderReference());

        return $refundRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayRequestOrderItemTransfer
     */
    public function orderItemToAfterPayItemRequest(ItemTransfer $itemTransfer): AfterPayRequestOrderItemTransfer
    {
        return $this->buildOrderItemRequestTransfer($itemTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayRequestCustomerTransfer
     */
    protected function buildCustomerRequestTransfer(AfterPayCallTransfer $afterPayCallTransfer): AfterPayRequestCustomerTransfer
    {
        $billingAddressTransfer = $afterPayCallTransfer->getBillingAddress();
        $customerRequestTransfer = new AfterPayRequestCustomerTransfer();

        $customerRequestTransfer
            ->setFirstName($billingAddressTransfer->getFirstName())
            ->setLastName($billingAddressTransfer->getLastName())
            ->setConversationalLanguage($this->getStoreCountryIso2())
            ->setCustomerCategory(AfterPayConfig::API_CUSTOMER_CATEGORY_PERSON)
            ->setSalutation($billingAddressTransfer->getSalutation())
            ->setEmail($afterPayCallTransfer->getEmail());

        $customerRequestTransfer->setAddress(
            $this->buildCustomerBillingAddressRequestTransfer($afterPayCallTransfer)
        );

        return $customerRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayRequestOrderTransfer
     */
    protected function buildOrderWithItemsRequestTransfer(AfterPayCallTransfer $afterPayCallTransfer): AfterPayRequestOrderTransfer
    {
        $orderRequestTransfer = $this->buildOrderRequestTransfer($afterPayCallTransfer);

        foreach ($afterPayCallTransfer->getItems() as $itemTransfer) {
            $orderRequestTransfer->addItem(
                $this->buildOrderItemRequestTransfer($itemTransfer)
            );
        }

        return $orderRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayRequestOrderTransfer
     */
    protected function buildOrderRequestTransfer(AfterPayCallTransfer $afterPayCallTransfer): AfterPayRequestOrderTransfer
    {
        $orderRequestTransfer = new AfterPayRequestOrderTransfer();
        $orderRequestTransfer
            ->setNumber($afterPayCallTransfer->getOrderReference())
            ->setTotalGrossAmount($this->getStringDecimalOrderGrossTotal($afterPayCallTransfer))
            ->setTotalNetAmount($this->getStringDecimalOrderNetTotal($afterPayCallTransfer));

        return $orderRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayRequestPaymentTransfer
     */
    protected function buildPaymentRequestTransfer(AfterPayCallTransfer $afterPayCallTransfer): AfterPayRequestPaymentTransfer
    {
        $paymentMethod = $afterPayCallTransfer->getPaymentMethod();

        $requestPaymentTransfer = new AfterPayRequestPaymentTransfer();
        $requestPaymentTransfer->setType(static::$paymentMethods[$paymentMethod]);

        return $requestPaymentTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayRequestOrderItemTransfer
     */
    protected function buildOrderItemRequestTransfer(ItemTransfer $itemTransfer): AfterPayRequestOrderItemTransfer
    {
        $orderItemRequestTransfer = new AfterPayRequestOrderItemTransfer();

        $orderItemRequestTransfer
            ->setProductId($itemTransfer->getSku())
            ->setDescription($itemTransfer->getName())
            ->setGrossUnitPrice($this->getStringDecimalItemGrossUnitPrice($itemTransfer))
            ->setNetUnitPrice($this->getStringDecimalItemNetUnitPrice($itemTransfer))
            ->setQuantity($itemTransfer->getQuantity());

        return $orderItemRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayRequestAddressTransfer
     */
    protected function buildCustomerBillingAddressRequestTransfer(AfterPayCallTransfer $afterPayCallTransfer): AfterPayRequestAddressTransfer
    {
        $customerAddressTransfer = $afterPayCallTransfer->getBillingAddress();
        $customerAddressRequestTransfer = new AfterPayRequestAddressTransfer();

        $customerAddressRequestTransfer
            ->setCountryCode($customerAddressTransfer->getIso2Code())
            ->setStreet($customerAddressTransfer->getAddress1())
            ->setStreetNumber($customerAddressTransfer->getAddress2())
            ->setPostalCode($customerAddressTransfer->getZipCode())
            ->setPostalPlace($customerAddressTransfer->getCity());

        return $customerAddressRequestTransfer;
    }

    /**
     * @return string
     */
    protected function getStoreCountryIso2(): string
    {
        return $this->storeFacade->getCurrentStore()->getName();
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     *
     * @return string
     */
    protected function getStringDecimalOrderGrossTotal(AfterPayCallTransfer $afterPayCallTransfer): string
    {
        $orderGrossTotal = (int)$afterPayCallTransfer->getTotals()->getGrandTotal();

        return (string)$this->moneyFacade->convertIntegerToDecimal($orderGrossTotal);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     *
     * @return string
     */
    protected function getStringDecimalOrderNetTotal(AfterPayCallTransfer $afterPayCallTransfer): string
    {
        $orderGrossTotal = (int)$afterPayCallTransfer->getTotals()->getGrandTotal();
        $orderTaxTotal = (int)$afterPayCallTransfer->getTotals()->getTaxTotal()->getAmount();
        $orderNetTotal = (int)$orderGrossTotal - $orderTaxTotal;

        return (string)$this->moneyFacade->convertIntegerToDecimal($orderNetTotal);
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return string
     */
    protected function getStringDecimalItemGrossUnitPrice(ItemTransfer $itemTransfer): string
    {
        $itemUnitGrossPrice = (int)$itemTransfer->getUnitPriceToPayAggregation();

        return (string)$this->moneyFacade->convertIntegerToDecimal($itemUnitGrossPrice);
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return string
     */
    protected function getStringDecimalItemNetUnitPrice(ItemTransfer $itemTransfer): string
    {
        $itemUnitGrossPriceAmount = (int)$itemTransfer->getUnitPriceToPayAggregation();
        $itemUnitTaxAmount = (int)$itemTransfer->getUnitTaxAmountFullAggregation();
        $itemUnitNetAmount = $itemUnitGrossPriceAmount - $itemUnitTaxAmount;

        return (string)$this->moneyFacade->convertIntegerToDecimal($itemUnitNetAmount);
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderWithPaymentTransfer
     * @param \Generated\Shared\Transfer\AfterPayRequestOrderTransfer $orderRequestTransfer
     *
     * @return void
     */
    protected function addGiftcardItems(
        OrderTransfer $orderWithPaymentTransfer,
        AfterPayRequestOrderTransfer $orderRequestTransfer
    ): void {
        foreach ($this->getGiftcards($orderWithPaymentTransfer) as $index => $paymentTransfer) {
            $orderItemRequestTransfer = new AfterPayRequestOrderItemTransfer();
            $amount = (string)$this->moneyFacade->convertIntegerToDecimal(static::NEGATIVE_MULTIPLIER * $paymentTransfer->getAmount());

            $orderItemRequestTransfer
                ->setProductId(static::GIFT_CARD_PROVIDER . $index)
                ->setDescription(static::GIFT_CARD_PROVIDER . $index)
                ->setGrossUnitPrice($amount)
                ->setQuantity(1);

            $orderRequestTransfer->addItem($orderItemRequestTransfer);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderWithPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentTransfer[]
     */
    protected function getGiftcards(OrderTransfer $orderWithPaymentTransfer): array
    {
        $giftCardPayments = [];
        foreach ($orderWithPaymentTransfer->getPayments() as $paymentTransfer) {
            if ($paymentTransfer->getPaymentMethod() !== static::GIFT_CARD_PROVIDER) {
                continue;
            }

            $giftCardPayments[] = $paymentTransfer;
        }

        return $giftCardPayments;
    }
}

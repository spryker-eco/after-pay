<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment\Mapper;

use Generated\Shared\Transfer\AfterpayAuthorizeRequestTransfer;
use Generated\Shared\Transfer\AfterpayCallTransfer;
use Generated\Shared\Transfer\AfterpayCancelRequestTransfer;
use Generated\Shared\Transfer\AfterpayCaptureRequestTransfer;
use Generated\Shared\Transfer\AfterpayRefundRequestTransfer;
use Generated\Shared\Transfer\AfterpayRequestAddressTransfer;
use Generated\Shared\Transfer\AfterpayRequestCustomerTransfer;
use Generated\Shared\Transfer\AfterpayRequestOrderItemTransfer;
use Generated\Shared\Transfer\AfterpayRequestOrderTransfer;
use Generated\Shared\Transfer\AfterpayRequestPaymentTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use SprykerEco\Shared\Afterpay\AfterpayConfig;
use SprykerEco\Shared\Afterpay\AfterpayConstants;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\PriceToPayProviderInterface;
use SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToMoneyInterface;
use SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToStoreInterface;

class OrderToRequestTransfer implements OrderToRequestTransferInterface
{
    public const NEGATIVE_MULTIPLIER = -1;
    public const GIFT_CARD_PROVIDER = 'GiftCard';

    /**
     * @var \SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToMoneyInterface
     */
    protected $moneyFacade;

    /**
     * @var \SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToStoreInterface
     */
    protected $storeFacade;

    /**
     * @var array
     */
    protected static $paymentMethods = [
        AfterpayConfig::PAYMENT_METHOD_INVOICE => AfterpayConfig::PAYMENT_TYPE_INVOICE,
    ];

    /**
     * @var \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\PriceToPayProviderInterface
     */
    protected $priceToPayProvider;

    /**
     * @param \SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToMoneyInterface $moneyFacade
     * @param \SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToStoreInterface $storeFacade
     * @param \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\PriceToPayProviderInterface $priceToPayProvider
     */
    public function __construct(
        AfterpayToMoneyInterface $moneyFacade,
        AfterpayToStoreInterface $storeFacade,
        PriceToPayProviderInterface $priceToPayProvider
    ) {
        $this->moneyFacade = $moneyFacade;
        $this->storeFacade = $storeFacade;
        $this->priceToPayProvider = $priceToPayProvider;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayCallTransfer $afterpayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayAuthorizeRequestTransfer
     */
    public function orderToAuthorizeRequest(AfterpayCallTransfer $afterpayCallTransfer): AfterpayAuthorizeRequestTransfer
    {
        $requestTransfer = new AfterpayAuthorizeRequestTransfer();

        $requestTransfer
            ->setPayment(
                $this->buildPaymentRequestTransfer($afterpayCallTransfer)
            )
            ->setCustomer(
                $this->buildCustomerRequestTransfer($afterpayCallTransfer)
            )
            ->setOrder(
                $this->buildOrderWithItemsRequestTransfer($afterpayCallTransfer)
            );

        return $requestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayCallTransfer $afterpayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayCaptureRequestTransfer
     */
    public function orderToBaseCaptureRequest(AfterpayCallTransfer $afterpayCallTransfer): AfterpayCaptureRequestTransfer
    {
        $requestTransfer = new AfterpayCaptureRequestTransfer();

        $requestTransfer
            ->setOrderDetails(
                $this->buildOrderRequestTransfer($afterpayCallTransfer)
                    ->setTotalGrossAmount(0)
                    ->setTotalNetAmount(0)
            );

        return $requestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayCallTransfer $afterpayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayCancelRequestTransfer
     */
    public function orderToBaseCancelRequest(AfterpayCallTransfer $afterpayCallTransfer): AfterpayCancelRequestTransfer
    {
        $requestTransfer = new AfterpayCancelRequestTransfer();

        $requestTransfer
            ->setCancellationDetails(
                $this->buildOrderRequestTransfer($afterpayCallTransfer)
                    ->setTotalGrossAmount(0)
                    ->setTotalNetAmount(0)
            );

        return $requestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayRefundRequestTransfer
     */
    public function orderToBaseRefundRequest(OrderTransfer $orderTransfer): AfterpayRefundRequestTransfer
    {
        $refundRequestTransfer = new AfterpayRefundRequestTransfer();

        $refundRequestTransfer
            ->setIdSalesOrder($orderTransfer->getIdSalesOrder())
            ->setOrderNumber($orderTransfer->getOrderReference());

        return $refundRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayRequestOrderItemTransfer
     */
    public function orderItemToAfterpayItemRequest(ItemTransfer $itemTransfer): AfterpayRequestOrderItemTransfer
    {
        return $this->buildOrderItemRequestTransfer($itemTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayCallTransfer $afterpayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayRequestCustomerTransfer
     */
    protected function buildCustomerRequestTransfer(AfterpayCallTransfer $afterpayCallTransfer): AfterpayRequestCustomerTransfer
    {
        $billingAddressTransfer = $afterpayCallTransfer->getBillingAddress();
        $customerRequestTransfer = new AfterpayRequestCustomerTransfer();

        $customerRequestTransfer
            ->setFirstName($billingAddressTransfer->getFirstName())
            ->setLastName($billingAddressTransfer->getLastName())
            ->setConversationalLanguage($this->getStoreCountryIso2())
            ->setCustomerCategory(AfterpayConfig::API_CUSTOMER_CATEGORY_PERSON)
            ->setSalutation($billingAddressTransfer->getSalutation())
            ->setEmail($afterpayCallTransfer->getEmail());

        $customerRequestTransfer->setAddress(
            $this->buildCustomerBillingAddressRequestTransfer($afterpayCallTransfer)
        );

        return $customerRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayCallTransfer $afterpayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayRequestOrderTransfer
     */
    protected function buildOrderWithItemsRequestTransfer(AfterpayCallTransfer $afterpayCallTransfer): AfterpayRequestOrderTransfer
    {
        $orderRequestTransfer = $this->buildOrderRequestTransfer($afterpayCallTransfer);

        foreach ($afterpayCallTransfer->getItems() as $itemTransfer) {
            $orderRequestTransfer->addItem(
                $this->buildOrderItemRequestTransfer($itemTransfer)
            );
        }

        return $orderRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayCallTransfer $afterpayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayRequestOrderTransfer
     */
    protected function buildOrderRequestTransfer(AfterpayCallTransfer $afterpayCallTransfer): AfterpayRequestOrderTransfer
    {
        $orderRequestTransfer = new AfterpayRequestOrderTransfer();
        $orderRequestTransfer
            ->setNumber($afterpayCallTransfer->getOrderReference())
            ->setTotalGrossAmount($this->getStringDecimalOrderGrossTotal($afterpayCallTransfer))
            ->setTotalNetAmount($this->getStringDecimalOrderNetTotal($afterpayCallTransfer));

        return $orderRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayCallTransfer $afterpayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayRequestPaymentTransfer
     */
    protected function buildPaymentRequestTransfer(AfterpayCallTransfer $afterpayCallTransfer): AfterpayRequestPaymentTransfer
    {
        $paymentMethod = $afterpayCallTransfer->getPaymentMethod();

        $requestPaymentTransfer = new AfterpayRequestPaymentTransfer();
        $requestPaymentTransfer->setType(static::$paymentMethods[$paymentMethod]);

        return $requestPaymentTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayRequestOrderItemTransfer
     */
    protected function buildOrderItemRequestTransfer(ItemTransfer $itemTransfer): AfterpayRequestOrderItemTransfer
    {
        $orderItemRequestTransfer = new AfterpayRequestOrderItemTransfer();

        $orderItemRequestTransfer
            ->setProductId($itemTransfer->getSku())
            ->setDescription($itemTransfer->getName())
            ->setGrossUnitPrice($this->getStringDecimalItemGrossUnitPrice($itemTransfer))
            ->setNetUnitPrice($this->getStringDecimalItemNetUnitPrice($itemTransfer))
            ->setQuantity($itemTransfer->getQuantity());

        return $orderItemRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayCallTransfer $afterpayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayRequestAddressTransfer
     */
    protected function buildCustomerBillingAddressRequestTransfer(AfterpayCallTransfer $afterpayCallTransfer): AfterpayRequestAddressTransfer
    {
        $customerAddressTransfer = $afterpayCallTransfer->getBillingAddress();
        $customerAddressRequestTransfer = new AfterpayRequestAddressTransfer();

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
     * @param \Generated\Shared\Transfer\AfterpayCallTransfer $afterpayCallTransfer
     *
     * @return string
     */
    protected function getStringDecimalOrderGrossTotal(AfterpayCallTransfer $afterpayCallTransfer): string
    {
        $orderGrossTotal = (int)$afterpayCallTransfer->getTotals()->getGrandTotal();

        return (string)$this->moneyFacade->convertIntegerToDecimal($orderGrossTotal);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayCallTransfer $afterpayCallTransfer
     *
     * @return string
     */
    protected function getStringDecimalOrderNetTotal(AfterpayCallTransfer $afterpayCallTransfer): string
    {
        $orderGrossTotal = (int)$afterpayCallTransfer->getTotals()->getGrandTotal();
        $orderTaxTotal = (int)$afterpayCallTransfer->getTotals()->getTaxTotal()->getAmount();
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
     * @param \Generated\Shared\Transfer\AfterpayRequestOrderTransfer $orderRequestTransfer
     *
     * @return void
     */
    protected function addGiftcardItems(
        OrderTransfer $orderWithPaymentTransfer,
        AfterpayRequestOrderTransfer $orderRequestTransfer
    ): void {
        foreach ($this->getGiftcards($orderWithPaymentTransfer) as $index => $paymentTransfer) {
            $orderItemRequestTransfer = new AfterpayRequestOrderItemTransfer();
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

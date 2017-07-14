<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment\Mapper;

use Generated\Shared\Transfer\AfterpayAuthorizeRequestTransfer;
use Generated\Shared\Transfer\AfterpayCancelRequestTransfer;
use Generated\Shared\Transfer\AfterpayCaptureRequestTransfer;
use Generated\Shared\Transfer\AfterpayRequestAddressTransfer;
use Generated\Shared\Transfer\AfterpayRequestCustomerTransfer;
use Generated\Shared\Transfer\AfterpayRequestOrderItemTransfer;
use Generated\Shared\Transfer\AfterpayRequestOrderTransfer;
use Generated\Shared\Transfer\AfterpayRequestPaymentTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Spryker\Shared\Kernel\Store;
use SprykerEco\Shared\Afterpay\AfterpayConstants;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\PriceToPayProviderInterface;
use SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToMoneyInterface;

class OrderToRequestTransfer implements OrderToRequestTransferInterface
{
    const NEGATIVE_MULTIPLIER = -1;
    const GIFT_CARD_PROVIDER = 'GiftCard';

    /**
     * @var \SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToMoneyInterface
     */
    protected $money;

    /**
     * @var \Spryker\Shared\Kernel\Store
     */
    protected $store;

    /**
     * @var array
     */
    protected static $paymentMethods = [
        AfterpayConstants::PAYMENT_METHOD_INVOICE => AfterpayConstants::PAYMENT_TYPE_INVOICE,
    ];

    /**
     * @var \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\PriceToPayProviderInterface
     */
    protected $priceToPayProvider;

    /**
     * @param \SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToMoneyInterface $money
     * @param \Spryker\Shared\Kernel\Store $store
     * @param \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\PriceToPayProviderInterface $priceToPayProvider
     */
    public function __construct(
        AfterpayToMoneyInterface $money,
        Store $store,
        PriceToPayProviderInterface $priceToPayProvider
    )
    {
        $this->money = $money;
        $this->store = $store;
        $this->priceToPayProvider = $priceToPayProvider;
    }

    /**
     * @todo consider to split this class into separate one-s, like orderToAuthorizeRequest, orderToCaptureRequest, etc.
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderWithPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayAuthorizeRequestTransfer
     */
    public function orderToAuthorizeRequest(OrderTransfer $orderWithPaymentTransfer)
    {
        $requestTransfer = new AfterpayAuthorizeRequestTransfer();

        $requestTransfer
            ->setIdSalesOrder(
                $orderWithPaymentTransfer->getIdSalesOrder()
            )
            ->setPayment(
                $this->buildPaymentRequestTransfer($orderWithPaymentTransfer)
            )
            ->setCustomer(
                $this->buildCustomerRequestTransfer($orderWithPaymentTransfer)
            )
            ->setOrder(
                $this->buildOrderWithItemsRequestTransfer($orderWithPaymentTransfer)
            );

        return $requestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayCaptureRequestTransfer
     */
    public function orderToBaseCaptureRequest(OrderTransfer $orderTransfer)
    {
        $requestTransfer = new AfterpayCaptureRequestTransfer();

        $requestTransfer
            ->setIdSalesOrder($orderTransfer->getIdSalesOrder())
            ->setOrderDetails(
                $this->buildOrderRequestTransfer($orderTransfer)
                    ->setTotalGrossAmount(0)
                    ->setTotalNetAmount(0)
            );

        return $requestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayCancelRequestTransfer
     */
    public function orderToBaseCancelRequest(OrderTransfer $orderTransfer)
    {
        $requestTransfer = new AfterpayCancelRequestTransfer();

        $requestTransfer
            ->setIdSalesOrder($orderTransfer->getIdSalesOrder())
            ->setCancellationDetails(
                $this->buildOrderRequestTransfer($orderTransfer)
                    ->setTotalGrossAmount(0)
                    ->setTotalNetAmount(0)
            );

        return $requestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayRequestOrderItemTransfer
     */
    public function orderItemToAfterpayItemRequest(ItemTransfer $itemTransfer)
    {
        return $this->buildOrderItemRequestTransfer($itemTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderWithPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayRequestCustomerTransfer
     */
    protected function buildCustomerRequestTransfer(OrderTransfer $orderWithPaymentTransfer)
    {
        $billingAddressTransfer = $orderWithPaymentTransfer->getBillingAddress();
        $customerRequestTransfer = new AfterpayRequestCustomerTransfer();

        $customerRequestTransfer
            ->setFirstName($billingAddressTransfer->getFirstName())
            ->setLastName($billingAddressTransfer->getLastName())
            ->setConversationalLanguage($this->getStoreCountryIso2())
            ->setCustomerCategory(AfterpayConstants::API_CUSTOMER_CATEGORY_PERSON)
            ->setSalutation($billingAddressTransfer->getSalutation())
            ->setEmail($orderWithPaymentTransfer->getEmail());

        $customerRequestTransfer->setAddress(
            $this->buildCustomerBillingAddressRequestTransfer($orderWithPaymentTransfer)
        );

        return $customerRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderWithPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayRequestOrderTransfer
     */
    protected function buildOrderWithItemsRequestTransfer(OrderTransfer $orderWithPaymentTransfer)
    {
        $orderRequestTransfer = $this->buildOrderRequestTransfer($orderWithPaymentTransfer);

        foreach ($orderWithPaymentTransfer->getItems() as $itemTransfer) {
            $orderRequestTransfer->addItem(
                $this->buildOrderItemRequestTransfer($itemTransfer)
            );
        }

        $this->addGiftcardItems($orderWithPaymentTransfer, $orderRequestTransfer);

        return $orderRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderWithPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayRequestOrderTransfer
     */
    protected function buildOrderRequestTransfer(OrderTransfer $orderWithPaymentTransfer)
    {
        $priceToPay = $this->priceToPayProvider->getPriceToPayForOrder($orderWithPaymentTransfer);

        $orderRequestTransfer = new AfterpayRequestOrderTransfer();
        $orderRequestTransfer
            ->setNumber($orderWithPaymentTransfer->getOrderReference())
            ->setTotalGrossAmount($this->getStringDecimalOrderGrossTotal($priceToPay))
            ->setTotalNetAmount($this->getStringDecimalOrderNetTotal($orderWithPaymentTransfer, $priceToPay));

        return $orderRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderWithPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayRequestPaymentTransfer
     */
    protected function buildPaymentRequestTransfer(OrderTransfer $orderWithPaymentTransfer)
    {
        $paymentMethod = $orderWithPaymentTransfer->getAfterpayPayment()->getPaymentMethod();

        $requestPaymentTransfer = new AfterpayRequestPaymentTransfer();
        $requestPaymentTransfer->setType(static::$paymentMethods[$paymentMethod]);

        return $requestPaymentTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayRequestOrderItemTransfer
     */
    protected function buildOrderItemRequestTransfer(ItemTransfer $itemTransfer)
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
     * @param \Generated\Shared\Transfer\OrderTransfer $orderWithPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayRequestAddressTransfer
     */
    protected function buildCustomerBillingAddressRequestTransfer(OrderTransfer $orderWithPaymentTransfer)
    {
        $customerAddressTransfer = $orderWithPaymentTransfer->getBillingAddress();
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
    protected function getStoreCountryIso2()
    {
        return $this->store->getCurrentCountry();
    }

    /**
     * @todo think about moving such int-to-decimal and back operations to the Api layer. Do all the operations
     * in integers, on the business side, and translate ints to decimal-strings right before building json requests.
     * ! Make sure to pass the right request payload to the transaction logs (with floats, not ints) !
     * To do this, it may be necessary to duplicate all request transfer objects:
     * "business" one-s will contain totals as ints
     * "api" one-s will contain totals as strings
     * Like this it will be easier to see, what's happening with the data.
     *
     * @param int $priceToPay
     *
     * @return string
     */
    protected function getStringDecimalOrderGrossTotal($priceToPay)
    {
        return (string)$this->money->convertIntegerToDecimal($priceToPay);
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderWithPaymentTransfer
     * @param int $priceToPay
     *
     * @return float
     */
    protected function getStringDecimalOrderNetTotal(OrderTransfer $orderWithPaymentTransfer, $priceToPay)
    {
        $orderTaxTotal = $orderWithPaymentTransfer->getTotals()->getTaxTotal()->getAmount();
        $orderNetTotal = $priceToPay - $orderTaxTotal;

        return (string)$this->money->convertIntegerToDecimal($orderNetTotal);
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return float
     */
    protected function getStringDecimalItemGrossUnitPrice(ItemTransfer $itemTransfer)
    {
        $itemUnitGrossPrice = $itemTransfer->getUnitPriceToPayAggregation();

        return (string)$this->money->convertIntegerToDecimal($itemUnitGrossPrice);
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return float
     */
    protected function getStringDecimalItemNetUnitPrice(ItemTransfer $itemTransfer)
    {
        $itemUnitGrossPriceAmount = $itemTransfer->getUnitPriceToPayAggregation();
        $itemUnitTaxAmount = $itemTransfer->getUnitTaxAmountFullAggregation();
        $itemUnitNetAmount = $itemUnitGrossPriceAmount - $itemUnitTaxAmount;

        return (string)$this->money->convertIntegerToDecimal($itemUnitNetAmount);
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderWithPaymentTransfer
     * @param  \Generated\Shared\Transfer\AfterpayRequestOrderTransfer $orderRequestTransfer
     *
     * @return void
     */
    protected function addGiftcardItems(OrderTransfer $orderWithPaymentTransfer, AfterpayRequestOrderTransfer $orderRequestTransfer)
    {
        foreach ($this->getGiftcards($orderWithPaymentTransfer) as $index => $paymentTransfer) {

            $orderItemRequestTransfer = new AfterpayRequestOrderItemTransfer();
            $amount = (string)$this->money->convertIntegerToDecimal($paymentTransfer->getAmount());

            $orderItemRequestTransfer
                ->setProductId(static::GIFT_CARD_PROVIDER . $index)
                ->setDescription(static::GIFT_CARD_PROVIDER . $index)
                ->setGrossUnitPrice(static::NEGATIVE_MULTIPLIER * $amount)
                ->setQuantity(1);

            $orderRequestTransfer->addItem($orderItemRequestTransfer);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderWithPaymentTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentTransfer[]
     */
    protected function getGiftcards(OrderTransfer $orderWithPaymentTransfer)
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

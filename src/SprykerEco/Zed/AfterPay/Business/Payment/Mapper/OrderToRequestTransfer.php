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
use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use SprykerEco\Shared\AfterPay\AfterPayConfig as AfterPayConfigShared;
use SprykerEco\Zed\AfterPay\AfterPayConfig as AfterPayConfigZed;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\PriceToPayProviderInterface;
use SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToMoneyFacadeInterface;
use SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToStoreFacadeInterface;

class OrderToRequestTransfer implements OrderToRequestTransferInterface
{
    public const NEGATIVE_MULTIPLIER = -1;

    /**
     * @var string
     */
    public const GIFT_CARD_PROVIDER = 'GiftCard';

    /**
     * @var string
     */
    protected const ZERO_AMOUNT = '0';

    /**
     * @var int
     */
    protected const ORDER_ITEM_QUANTITY = 1;

    /**
     * @var string
     */
    protected const SHIPPING_FEE_PROVIDER = 'ShippingFee';

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
        AfterPayConfigShared::PAYMENT_METHOD_INVOICE => AfterPayConfigShared::PAYMENT_TYPE_INVOICE,
    ];

    /**
     * @var \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\PriceToPayProviderInterface
     */
    protected $priceToPayProvider;

    /**
     * @var \SprykerEco\Zed\AfterPay\AfterPayConfig
     */
    protected $config;

    /**
     * @param \SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToMoneyFacadeInterface $moneyFacade
     * @param \SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToStoreFacadeInterface $storeFacade
     * @param \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\PriceToPayProviderInterface $priceToPayProvider
     * @param \SprykerEco\Zed\AfterPay\AfterPayConfig $config
     */
    public function __construct(
        AfterPayToMoneyFacadeInterface $moneyFacade,
        AfterPayToStoreFacadeInterface $storeFacade,
        PriceToPayProviderInterface $priceToPayProvider,
        AfterPayConfigZed $config
    ) {
        $this->moneyFacade = $moneyFacade;
        $this->storeFacade = $storeFacade;
        $this->priceToPayProvider = $priceToPayProvider;
        $this->config = $config;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayAuthorizeRequestTransfer
     */
    public function orderToAuthorizeRequest(AfterPayCallTransfer $afterPayCallTransfer): AfterPayAuthorizeRequestTransfer
    {
        return (new AfterPayAuthorizeRequestTransfer())
            ->setPayment($this->buildPaymentRequestTransfer($afterPayCallTransfer))
            ->setCustomer($this->buildCustomerRequestTransfer($afterPayCallTransfer))
            ->setOrder($this->buildOrderWithItemsRequestTransfer($afterPayCallTransfer));
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayCaptureRequestTransfer
     */
    public function orderToBaseCaptureRequest(AfterPayCallTransfer $afterPayCallTransfer): AfterPayCaptureRequestTransfer
    {
        $orderRequestTransfer = $this->buildOrderRequestTransfer($afterPayCallTransfer)
            ->setTotalGrossAmount(static::ZERO_AMOUNT)
            ->setTotalNetAmount(static::ZERO_AMOUNT);

        return (new AfterPayCaptureRequestTransfer())
            ->setOrderDetails($orderRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayCancelRequestTransfer
     */
    public function orderToBaseCancelRequest(AfterPayCallTransfer $afterPayCallTransfer): AfterPayCancelRequestTransfer
    {
        $orderRequestTransfer = $this->buildOrderRequestTransfer($afterPayCallTransfer)
            ->setTotalGrossAmount(static::ZERO_AMOUNT)
            ->setTotalNetAmount(static::ZERO_AMOUNT);

        return (new AfterPayCancelRequestTransfer())
            ->setCancellationDetails($orderRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayRefundRequestTransfer
     */
    public function orderToBaseRefundRequest(OrderTransfer $orderTransfer): AfterPayRefundRequestTransfer
    {
        return (new AfterPayRefundRequestTransfer())
            ->setIdSalesOrder($orderTransfer->getIdSalesOrder())
            ->setOrderNumber($orderTransfer->getOrderReference());
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

        return (new AfterPayRequestCustomerTransfer())
            ->setFirstName($billingAddressTransfer->getFirstName())
            ->setLastName($billingAddressTransfer->getLastName())
            ->setConversationalLanguage($this->getStoreCountryIso2())
            ->setCustomerCategory(AfterPayConfigShared::API_CUSTOMER_CATEGORY_PERSON)
            ->setSalutation($this->config->getSalutation($billingAddressTransfer->getSalutation()))
            ->setEmail($afterPayCallTransfer->getEmail())
            ->setAddress($this->buildCustomerBillingAddressRequestTransfer($afterPayCallTransfer));
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
                $this->buildOrderItemRequestTransfer($itemTransfer),
            );
        }

        $orderRequestTransfer = $this->addExpensesToOrderRequestTransfer($orderRequestTransfer, $afterPayCallTransfer);
        $orderRequestTransfer = $this->addGiftcardItems($orderRequestTransfer, $afterPayCallTransfer);

        return $orderRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayRequestOrderTransfer
     */
    protected function buildOrderRequestTransfer(AfterPayCallTransfer $afterPayCallTransfer): AfterPayRequestOrderTransfer
    {
        return (new AfterPayRequestOrderTransfer())
            ->setNumber($afterPayCallTransfer->getOrderReference())
            ->setTotalNetAmount($this->getStringDecimalOrderNetTotal($afterPayCallTransfer))
            ->setTotalGrossAmount($this->getStringDecimalOrderGrossTotal($afterPayCallTransfer))
            ->setCurrency($afterPayCallTransfer->getCurrency());
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayRequestPaymentTransfer
     */
    protected function buildPaymentRequestTransfer(AfterPayCallTransfer $afterPayCallTransfer): AfterPayRequestPaymentTransfer
    {
        return (new AfterPayRequestPaymentTransfer())
            ->setType(static::$paymentMethods[$afterPayCallTransfer->getPaymentMethod()] ?? null);
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayRequestOrderItemTransfer
     */
    protected function buildOrderItemRequestTransfer(ItemTransfer $itemTransfer): AfterPayRequestOrderItemTransfer
    {
        return (new AfterPayRequestOrderItemTransfer())
            ->setProductId($itemTransfer->getSku())
            ->setDescription($itemTransfer->getName())
            ->setGrossUnitPrice($this->getStringDecimalItemGrossUnitPrice($itemTransfer))
            ->setQuantity($itemTransfer->getQuantity())
            ->setNetUnitPrice($this->getStringDecimalItemNetUnitPrice($itemTransfer))
            ->setVatAmount($this->getStringDecimalItemVatAmountPrice($itemTransfer))
            ->setVatPercent($itemTransfer->getTaxRate())
            ->setGroupId($itemTransfer->getGroupKey());
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayRequestAddressTransfer
     */
    protected function buildCustomerBillingAddressRequestTransfer(AfterPayCallTransfer $afterPayCallTransfer): AfterPayRequestAddressTransfer
    {
        $customerAddressTransfer = $afterPayCallTransfer->getBillingAddress();

        return (new AfterPayRequestAddressTransfer())
            ->setCountryCode($customerAddressTransfer->getIso2Code())
            ->setStreet($customerAddressTransfer->getAddress1())
            ->setStreetNumber($customerAddressTransfer->getAddress2())
            ->setPostalCode($customerAddressTransfer->getZipCode())
            ->setPostalPlace($customerAddressTransfer->getCity());
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
        $orderGrossTotal = $afterPayCallTransfer->getTotals()->getGrandTotal();

        return (string)$this->moneyFacade->convertIntegerToDecimal($orderGrossTotal);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     *
     * @return string
     */
    protected function getStringDecimalOrderNetTotal(AfterPayCallTransfer $afterPayCallTransfer): string
    {
        $orderGrossTotal = $afterPayCallTransfer->getTotals()->getGrandTotal();
        $orderTaxTotal = $afterPayCallTransfer->getTotals()->getTaxTotal()->getAmount();
        $orderNetTotal = $orderGrossTotal - $orderTaxTotal;

        return (string)$this->moneyFacade->convertIntegerToDecimal($orderNetTotal);
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return string
     */
    protected function getStringDecimalItemGrossUnitPrice(ItemTransfer $itemTransfer): string
    {
        $itemUnitGrossPrice = $itemTransfer->getUnitPriceToPayAggregation();

        return (string)$this->moneyFacade->convertIntegerToDecimal($itemUnitGrossPrice);
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return string
     */
    protected function getStringDecimalItemNetUnitPrice(ItemTransfer $itemTransfer): string
    {
        $itemUnitGrossPriceAmount = $itemTransfer->getUnitPriceToPayAggregation();
        $itemUnitTaxAmount = $itemTransfer->getUnitTaxAmountFullAggregation();
        $itemUnitNetAmount = $itemUnitGrossPriceAmount - $itemUnitTaxAmount;

        return (string)$this->moneyFacade->convertIntegerToDecimal($itemUnitNetAmount);
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return string
     */
    protected function getStringDecimalItemVatAmountPrice(ItemTransfer $itemTransfer): string
    {
        $itemVatAmountPrice = $itemTransfer->getUnitTaxAmountFullAggregation();

        return (string)$this->moneyFacade->convertIntegerToDecimal($itemVatAmountPrice);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayRequestOrderTransfer $orderRequestTransfer
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayRequestOrderTransfer
     */
    protected function addGiftcardItems(
        AfterPayRequestOrderTransfer $orderRequestTransfer,
        AfterPayCallTransfer $afterPayCallTransfer
    ): AfterPayRequestOrderTransfer {
        foreach ($this->getGiftcards($afterPayCallTransfer) as $index => $paymentTransfer) {
            $amount = (string)$this->moneyFacade
                ->convertIntegerToDecimal(static::NEGATIVE_MULTIPLIER * $paymentTransfer->getAmount());

            $orderRequestTransfer->addItem(
                (new AfterPayRequestOrderItemTransfer())
                    ->setProductId(static::GIFT_CARD_PROVIDER . $index)
                    ->setDescription(static::GIFT_CARD_PROVIDER . $index)
                    ->setGrossUnitPrice($amount)
                    ->setQuantity(static::ORDER_ITEM_QUANTITY),
            );
        }

        return $orderRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     *
     * @return array<\Generated\Shared\Transfer\PaymentTransfer>
     */
    protected function getGiftcards(AfterPayCallTransfer $afterPayCallTransfer): array
    {
        $giftCardPayments = [];
        foreach ($afterPayCallTransfer->getPayments() as $paymentTransfer) {
            if ($paymentTransfer->getPaymentMethod() !== static::GIFT_CARD_PROVIDER) {
                continue;
            }

            $giftCardPayments[] = $paymentTransfer;
        }

        return $giftCardPayments;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayRequestOrderTransfer $orderRequestTransfer
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayRequestOrderTransfer
     */
    protected function addExpensesToOrderRequestTransfer(
        AfterPayRequestOrderTransfer $orderRequestTransfer,
        AfterPayCallTransfer $afterPayCallTransfer
    ): AfterPayRequestOrderTransfer {
        foreach ($afterPayCallTransfer->getExpenses() as $expenseTransfer) {
            if ($expenseTransfer->getSumPriceToPayAggregation() > 0) {
                $orderRequestTransfer->addItem(
                    $this->buildOrderExpenseRequestTransfer($expenseTransfer),
                );
            }
        }

        return $orderRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ExpenseTransfer $expenseTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayRequestOrderItemTransfer
     */
    protected function buildOrderExpenseRequestTransfer(ExpenseTransfer $expenseTransfer): AfterPayRequestOrderItemTransfer
    {
        $item = (new AfterPayRequestOrderItemTransfer())
            ->setProductId($expenseTransfer->getType())
            ->setDescription($expenseTransfer->getName())
            ->setGrossUnitPrice($this->getStringDecimalExpenseGrossUnitPrice($expenseTransfer))
            ->setNetUnitPrice($this->getStringDecimalExpenseNetUnitPrice($expenseTransfer))
            ->setVatAmount($this->getStringDecimalExpenseVatAmount($expenseTransfer))
            ->setVatPercent($expenseTransfer->getTaxRate())
            ->setQuantity($expenseTransfer->getQuantity());

        return $item;
    }

    /**
     * @param \Generated\Shared\Transfer\ExpenseTransfer $expenseTransfer
     *
     * @return string
     */
    protected function getStringDecimalExpenseGrossUnitPrice(ExpenseTransfer $expenseTransfer): string
    {
        $expenseUnitGrossPrice = $expenseTransfer->getUnitPriceToPayAggregation();

        return (string)$this->moneyFacade->convertIntegerToDecimal($expenseUnitGrossPrice);
    }

    /**
     * @param \Generated\Shared\Transfer\ExpenseTransfer $expenseTransfer
     *
     * @return string
     */
    protected function getStringDecimalExpenseNetUnitPrice(ExpenseTransfer $expenseTransfer): string
    {
        $expenseUnitGrossPriceAmount = $expenseTransfer->getUnitPriceToPayAggregation();
        $expenseUnitTaxAmount = $expenseTransfer->getUnitTaxAmount();
        $expenseUnitNetAmount = $expenseUnitGrossPriceAmount - $expenseUnitTaxAmount;

        return (string)$this->moneyFacade->convertIntegerToDecimal($expenseUnitNetAmount);
    }

    /**
     * @param \Generated\Shared\Transfer\ExpenseTransfer $expenseTransfer
     *
     * @return string
     */
    protected function getStringDecimalExpenseVatAmount(ExpenseTransfer $expenseTransfer): string
    {
        $expenseVatAmount = $expenseTransfer->getSumTaxAmount();

        return (string)$this->moneyFacade->convertIntegerToDecimal($expenseVatAmount);
    }
}

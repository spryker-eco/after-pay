<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Payment\Mapper;

use Generated\Shared\Transfer\AfterPayAvailablePaymentMethodsRequestTransfer;
use Generated\Shared\Transfer\AfterPayRequestAddressTransfer;
use Generated\Shared\Transfer\AfterPayRequestCustomerTransfer;
use Generated\Shared\Transfer\AfterPayRequestOrderItemTransfer;
use Generated\Shared\Transfer\AfterPayRequestOrderTransfer;
use Generated\Shared\Transfer\ExpenseTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Shared\AfterPay\AfterPayConfig;
use SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToMoneyFacadeInterface;
use SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToStoreFacadeInterface;

class QuoteToRequestTransfer implements QuoteToRequestTransferInterface
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
     * @param \SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToMoneyFacadeInterface $moneyFacade
     * @param \SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToStoreFacadeInterface $storeFacade
     */
    public function __construct(
        AfterPayToMoneyFacadeInterface $moneyFacade,
        AfterPayToStoreFacadeInterface $storeFacade
    ) {
        $this->moneyFacade = $moneyFacade;
        $this->storeFacade = $storeFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayAvailablePaymentMethodsRequestTransfer
     */
    public function quoteToAvailablePaymentMethods(QuoteTransfer $quoteTransfer): AfterPayAvailablePaymentMethodsRequestTransfer
    {
        return (new AfterPayAvailablePaymentMethodsRequestTransfer())
            ->setCustomer(
                $this->buildCustomerRequestTransfer($quoteTransfer)
            )
            ->setOrder(
                $this->buildOrderRequestTransfer($quoteTransfer)
            );
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayRequestCustomerTransfer
     */
    protected function buildCustomerRequestTransfer(QuoteTransfer $quoteTransfer): AfterPayRequestCustomerTransfer
    {
        $quoteBillingAddressTransfer = $quoteTransfer->getBillingAddress();

        return (new AfterPayRequestCustomerTransfer())
            ->setFirstName($quoteBillingAddressTransfer->getFirstName())
            ->setLastName($quoteBillingAddressTransfer->getLastName())
            ->setConversationalLanguage($this->getStoreCountryIso2())
            ->setCustomerCategory(AfterPayConfig::API_CUSTOMER_CATEGORY_PERSON)
            ->setSalutation($quoteBillingAddressTransfer->getSalutation())
            ->setEmail($quoteTransfer->getCustomer()->getEmail())
            ->setAddress(
                $this->buildCustomerBillingAddressRequestTransfer($quoteTransfer)
            );
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayRequestOrderTransfer
     */
    protected function buildOrderRequestTransfer(QuoteTransfer $quoteTransfer): AfterPayRequestOrderTransfer
    {
        $orderRequestTransfer = (new AfterPayRequestOrderTransfer())
            ->setTotalGrossAmount($this->getStringDecimalQuoteGrossTotal($quoteTransfer))
            ->setCurrency($quoteTransfer->getCurrency()->getCode());

        foreach ($quoteTransfer->getItems() as $itemTransfer) {
            $orderRequestTransfer->addItem(
                $this->buildOrderItemRequestTransfer($itemTransfer)
            );
        }

        $orderRequestTransfer = $this->addExpensesToOrderRequestTransfer($orderRequestTransfer, $quoteTransfer);

        $this->addGiftcardItems($quoteTransfer, $orderRequestTransfer);

        return $orderRequestTransfer;
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
            ->setVatAmount($this->getStringDecimalItemVatAmountPrice($itemTransfer))
            ->setVatPercent($itemTransfer->getTaxRate())
            ->setGroupId($itemTransfer->getGroupKey());
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayRequestAddressTransfer
     */
    protected function buildCustomerBillingAddressRequestTransfer(QuoteTransfer $quoteTransfer): AfterPayRequestAddressTransfer
    {
        $customerAddressTransfer = $quoteTransfer->getBillingAddress();

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
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return string
     */
    protected function getStringDecimalQuoteGrossTotal(QuoteTransfer $quoteTransfer): string
    {
        $quoteTotal = $quoteTransfer->getTotals()->getGrandTotal();
        if ($quoteTransfer->getTotals()->getPriceToPay()) {
            $quoteTotal = $quoteTransfer->getTotals()->getPriceToPay();
        }

        return (string)$this->moneyFacade->convertIntegerToDecimal($quoteTotal);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return string
     */
    protected function getStringDecimalQuoteNetTotal(QuoteTransfer $quoteTransfer): string
    {
        $quoteGrossTotal = $quoteTransfer->getTotals()->getGrandTotal();
        $quoteTaxTotal = $quoteTransfer->getTotals()->getTaxTotal()->getAmount();
        $quoteNetTotal = $quoteGrossTotal - $quoteTaxTotal;

        return (string)$this->moneyFacade->convertIntegerToDecimal($quoteNetTotal);
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
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\AfterPayRequestOrderTransfer $orderRequestTransfer
     *
     * @return void
     */
    protected function addGiftcardItems(
        QuoteTransfer $quoteTransfer,
        AfterPayRequestOrderTransfer $orderRequestTransfer
    ): void {
        foreach ($this->getGiftcards($quoteTransfer) as $index => $paymentTransfer) {
            $amount = (string)$this->moneyFacade->convertIntegerToDecimal(static::NEGATIVE_MULTIPLIER * $paymentTransfer->getAmount());

            $orderItemRequestTransfer = (new AfterPayRequestOrderItemTransfer())
                ->setProductId(static::GIFT_CARD_PROVIDER . $index)
                ->setDescription(static::GIFT_CARD_PROVIDER . $index)
                ->setGrossUnitPrice($amount)
                ->setQuantity(1);

            $orderRequestTransfer->addItem($orderItemRequestTransfer);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentTransfer[]
     */
    protected function getGiftcards(QuoteTransfer $quoteTransfer): array
    {
        $giftCardPayments = [];
        foreach ($quoteTransfer->getPayments() as $paymentTransfer) {
            if ($paymentTransfer->getPaymentMethod() !== static::GIFT_CARD_PROVIDER) {
                continue;
            }

            $giftCardPayments[] = $paymentTransfer;
        }

        return $giftCardPayments;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayRequestOrderTransfer $orderRequestTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayRequestOrderTransfer
     */
    protected function addExpensesToOrderRequestTransfer(
        AfterPayRequestOrderTransfer $orderRequestTransfer,
        QuoteTransfer $quoteTransfer
    ): AfterPayRequestOrderTransfer {
        foreach ($quoteTransfer->getExpenses() as $expenseTransfer) {
            if ($expenseTransfer->getSumPriceToPayAggregation() > 0) {
                $orderRequestTransfer->addItem(
                    $this->buildOrderExpenseRequestTransfer($expenseTransfer)
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
        return (new AfterPayRequestOrderItemTransfer())
            ->setProductId($expenseTransfer->getType())
            ->setDescription($expenseTransfer->getName())
            ->setGrossUnitPrice($this->getStringDecimalExpenseGrossUnitPrice($expenseTransfer))
            ->setNetUnitPrice($this->getStringDecimalExpenseNetUnitPrice($expenseTransfer))
            ->setQuantity($expenseTransfer->getQuantity());
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
}

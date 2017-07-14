<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment\Mapper;

use Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsRequestTransfer;
use Generated\Shared\Transfer\AfterpayRequestAddressTransfer;
use Generated\Shared\Transfer\AfterpayRequestCustomerTransfer;
use Generated\Shared\Transfer\AfterpayRequestOrderItemTransfer;
use Generated\Shared\Transfer\AfterpayRequestOrderTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Shared\Kernel\Store;
use SprykerEco\Shared\Afterpay\AfterpayConstants;
use SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToMoneyInterface;


//@todo extract shared behaviour between quote and order transfers if possible

class QuoteToRequestTransfer implements QuoteToRequestTransferInterface
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
     * @param \SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToMoneyInterface $money
     * @param \Spryker\Shared\Kernel\Store $store
     */
    public function __construct(AfterpayToMoneyInterface $money, Store $store)
    {
        $this->money = $money;
        $this->store = $store;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsRequestTransfer
     */
    public function quoteToAvailablePaymentMethods(QuoteTransfer $quoteTransfer)
    {
        $requestTransfer = new AfterpayAvailablePaymentMethodsRequestTransfer();

        $requestTransfer
            ->setCustomer(
                $this->buildCustomerRequestTransfer($quoteTransfer)
            )
            ->setOrder(
                $this->buildOrderRequestTransfer($quoteTransfer)
            );

        return $requestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayRequestCustomerTransfer
     */
    protected function buildCustomerRequestTransfer(QuoteTransfer $quoteTransfer)
    {
        $quoteBillingAddressTransfer = $quoteTransfer->getBillingAddress();
        $customerRequestTransfer = new AfterpayRequestCustomerTransfer();

        $customerRequestTransfer
            ->setFirstName($quoteBillingAddressTransfer->getFirstName())
            ->setLastName($quoteBillingAddressTransfer->getLastName())
            ->setConversationalLanguage($this->getStoreCountryIso2())
            ->setCustomerCategory(AfterpayConstants::API_CUSTOMER_CATEGORY_PERSON)
            ->setSalutation($quoteBillingAddressTransfer->getSalutation())
            ->setEmail($quoteTransfer->getCustomer()->getEmail());

        $customerRequestTransfer->setAddress(
            $this->buildCustomerBillingAddressRequestTransfer($quoteTransfer)
        );

        return $customerRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayRequestOrderTransfer
     */
    protected function buildOrderRequestTransfer(QuoteTransfer $quoteTransfer)
    {
        $orderRequestTransfer = new AfterpayRequestOrderTransfer();
        $orderRequestTransfer->setTotalGrossAmount($this->getStringDecimalQuoteGrossTotal($quoteTransfer));
        $orderRequestTransfer->setTotalNetAmount($this->getStringDecimalQuoteNetTotal($quoteTransfer));

        foreach ($quoteTransfer->getItems() as $itemTransfer) {
            $orderRequestTransfer->addItem(
                $this->buildOrderItemRequestTransfer($itemTransfer)
            );
        }

        $this->addGiftcardItems($quoteTransfer, $orderRequestTransfer);

        return $orderRequestTransfer;
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
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayRequestAddressTransfer
     */
    protected function buildCustomerBillingAddressRequestTransfer(QuoteTransfer $quoteTransfer)
    {
        $customerAddressTransfer = $quoteTransfer->getBillingAddress();
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
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return string
     */
    protected function getStringDecimalQuoteGrossTotal(QuoteTransfer $quoteTransfer)
    {
        $quoteTotal = $quoteTransfer->getTotals()->getGrandTotal();
        if ($quoteTransfer->getTotals()->getPriceToPay()) {
            $quoteTotal = $quoteTransfer->getTotals()->getPriceToPay();
        }

        return (string)$this->money->convertIntegerToDecimal($quoteTotal);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return string
     */
    protected function getStringDecimalQuoteNetTotal(QuoteTransfer $quoteTransfer)
    {
        $quoteGrossTotal = $quoteTransfer->getTotals()->getGrandTotal();
        $quoteTaxTotal = $quoteTransfer->getTotals()->getTaxTotal()->getAmount();
        $quoteNetTotal = $quoteGrossTotal - $quoteTaxTotal;

        return (string)$this->money->convertIntegerToDecimal($quoteNetTotal);
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return string
     */
    protected function getStringDecimalItemGrossUnitPrice(ItemTransfer $itemTransfer)
    {
        $itemUnitGrossPrice = $itemTransfer->getUnitPriceToPayAggregation();

        return (string)$this->money->convertIntegerToDecimal($itemUnitGrossPrice);
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return string
     */
    protected function getStringDecimalItemNetUnitPrice(ItemTransfer $itemTransfer)
    {
        $itemUnitGrossPriceAmount = $itemTransfer->getUnitPriceToPayAggregation();
        $itemUnitTaxAmount = $itemTransfer->getUnitTaxAmountFullAggregation();
        $itemUnitNetAmount = $itemUnitGrossPriceAmount - $itemUnitTaxAmount;

        return (string)$this->money->convertIntegerToDecimal($itemUnitNetAmount);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param  \Generated\Shared\Transfer\AfterpayRequestOrderTransfer $orderRequestTransfer
     *
     * @return void
     */
    protected function addGiftcardItems(QuoteTransfer $quoteTransfer, AfterpayRequestOrderTransfer $orderRequestTransfer)
    {
        foreach ($this->getGiftcards($quoteTransfer) as $index => $paymentTransfer) {

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
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentTransfer[]
     */
    protected function getGiftcards(QuoteTransfer $quoteTransfer)
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

}

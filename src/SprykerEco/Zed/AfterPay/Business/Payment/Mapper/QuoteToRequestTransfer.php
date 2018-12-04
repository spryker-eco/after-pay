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
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Shared\AfterPay\AfterPayConfig;
use SprykerEco\Shared\AfterPay\AfterPayConstants;
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
        $requestTransfer = new AfterPayAvailablePaymentMethodsRequestTransfer();

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
     * @return \Generated\Shared\Transfer\AfterPayRequestCustomerTransfer
     */
    protected function buildCustomerRequestTransfer(QuoteTransfer $quoteTransfer): AfterPayRequestCustomerTransfer
    {
        $quoteBillingAddressTransfer = $quoteTransfer->getBillingAddress();
        $customerRequestTransfer = new AfterPayRequestCustomerTransfer();

        $customerRequestTransfer
            ->setFirstName($quoteBillingAddressTransfer->getFirstName())
            ->setLastName($quoteBillingAddressTransfer->getLastName())
            ->setConversationalLanguage($this->getStoreCountryIso2())
            ->setCustomerCategory(AfterPayConfig::API_CUSTOMER_CATEGORY_PERSON)
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
     * @return \Generated\Shared\Transfer\AfterPayRequestOrderTransfer
     */
    protected function buildOrderRequestTransfer(QuoteTransfer $quoteTransfer): AfterPayRequestOrderTransfer
    {
        $orderRequestTransfer = new AfterPayRequestOrderTransfer();
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
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayRequestAddressTransfer
     */
    protected function buildCustomerBillingAddressRequestTransfer(QuoteTransfer $quoteTransfer): AfterPayRequestAddressTransfer
    {
        $customerAddressTransfer = $quoteTransfer->getBillingAddress();
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
}

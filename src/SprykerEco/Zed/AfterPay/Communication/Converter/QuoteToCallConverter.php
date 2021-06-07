<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Communication\Converter;

use Generated\Shared\Transfer\AfterPayCallTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

/**
 * @deprecated Use {@link \SprykerEco\Zed\AfterPay\Business\Mapper\AfterPayMapper} instead.
 */
class QuoteToCallConverter implements QuoteToCallConverterInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayCallTransfer
     */
    public function convert(QuoteTransfer $quoteTransfer): AfterPayCallTransfer
    {
        return (new AfterPayCallTransfer())
            ->fromArray($quoteTransfer->toArray(), true)
            ->setIdSalesOrder($quoteTransfer->getPayment()->getIdSalesOrder())
            ->setEmail($quoteTransfer->getCustomer()->getEmail())
            ->setPaymentMethod($quoteTransfer->getPayment()->getPaymentSelection());
    }
}

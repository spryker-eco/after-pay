<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment\Mapper;

use Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsRequestTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

interface QuoteToRequestTransferInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsRequestTransfer
     */
    public function quoteToAvailablePaymentMethods(QuoteTransfer $quoteTransfer): AfterpayAvailablePaymentMethodsRequestTransfer;
}

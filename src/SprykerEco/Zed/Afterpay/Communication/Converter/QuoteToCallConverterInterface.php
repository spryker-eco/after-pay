<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Communication\Converter;

use Generated\Shared\Transfer\AfterpayCallTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

interface QuoteToCallConverterInterface
{
    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayCallTransfer
     */
    public function convert(QuoteTransfer $quoteTransfer): AfterpayCallTransfer;
}

<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEcoTest\Zed\Afterpay\Business;

use Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Shared\Afterpay\AfterpayConstants;

class AfterpayFacadeAvailableMethodsTest extends AfterpayFacadeAbstractTest
{
    /**
     * @return void
     */
    public function testGetAvailablePaymentMethods()
    {
        $quote = $this->createQuoteTransfer();
        $output = $this->doFacadeCall($quote);
        $this->doTest($output);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsTransfer
     */
    protected function doFacadeCall(QuoteTransfer $quoteTransfer)
    {
        return $this->facade->getAvailablePaymentMethods($quoteTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsTransfer $output
     *
     * @return void
     */
    protected function doTest(AfterpayAvailablePaymentMethodsTransfer $output)
    {
        $methodNames = $output->getAvailablePaymentMethodNames();

        $this->assertEquals($methodNames, [AfterpayConstants::RISK_CHECK_METHOD_INVOICE]);
        $this->assertNotEmpty($output->getRiskCheckCode());
        $this->assertNotEmpty($output->getCheckoutId());
        $this->assertNotEmpty($output->getQuoteHash());
    }
}

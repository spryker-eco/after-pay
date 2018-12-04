<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\AfterPay\Business;

use Generated\Shared\Transfer\AfterPayAvailablePaymentMethodsTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Shared\AfterPay\AfterPayConfig;

class AfterPayFacadeAvailableMethodsTest extends AfterPayFacadeAbstractTest
{
    /**
     * @return void
     */
    public function testGetAvailablePaymentMethods(): void
    {
        $quote = $this->createQuoteTransfer();
        $output = $this->doFacadeCall($quote);
        $this->doTest($output);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayAvailablePaymentMethodsTransfer
     */
    protected function doFacadeCall(QuoteTransfer $quoteTransfer): AfterPayAvailablePaymentMethodsTransfer
    {
        return $this->facade->getAvailablePaymentMethods($quoteTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayAvailablePaymentMethodsTransfer $output
     *
     * @return void
     */
    protected function doTest(AfterPayAvailablePaymentMethodsTransfer $output): void
    {
        $methodNames = $output->getAvailablePaymentMethodNames();

        $this->assertEquals($methodNames, [AfterPayConfig::RISK_CHECK_METHOD_INVOICE]);
        $this->assertNotEmpty($output->getRiskCheckCode());
        $this->assertNotEmpty($output->getCheckoutId());
        $this->assertNotEmpty($output->getQuoteHash());
    }
}

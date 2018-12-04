<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\AfterPay\Business;

use Generated\Shared\Transfer\AfterPayApiResponseTransfer;
use Generated\Shared\Transfer\AfterPayCallTransfer;
use SprykerEco\Shared\AfterPay\AfterPayConfig;
use SprykerEco\Shared\AfterPay\AfterPayConstants;
use SprykerEcoTest\Zed\AfterPay\Mock\AfterPayFacadeMock;

class AfterPayFacadeAuthorizeTest extends AfterPayFacadeAbstractTest
{
    /**
     * @return void
     */
    public function testAuthorize()
    {
        $input = $this->createCallTransfer();
        $output = $this->doFacadeCall($input);
        $this->doTest($output);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $input
     *
     * @return \Generated\Shared\Transfer\AfterPayApiResponseTransfer
     */
    protected function doFacadeCall(AfterPayCallTransfer $input)
    {
        return (new AfterPayFacadeMock())->authorizePayment($input);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayApiResponseTransfer $output
     *
     * @return void
     */
    protected function doTest(AfterPayApiResponseTransfer $output)
    {
        $this->assertNotEmpty($output->getOutcome());
        $this->assertEquals($output->getOutcome(), AfterPayConfig::API_TRANSACTION_OUTCOME_ACCEPTED);
        $this->assertNotEmpty($output->getCheckoutId());
        $this->assertNotEmpty($output->getReservationId());
        $this->assertNotEmpty($output->getResponsePayload());
    }
}

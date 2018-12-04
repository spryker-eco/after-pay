<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\AfterPay\Business;

use Generated\Shared\Transfer\AfterPayApiResponseTransfer;
use Generated\Shared\Transfer\AfterPayCallTransfer;
use SprykerEco\Shared\AfterPay\AfterPayConfig;

class AfterPayFacadeAuthorizeTest extends AfterPayFacadeAbstractTest
{
    /**
     * @return void
     */
    public function testAuthorize(): void
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
    protected function doFacadeCall(AfterPayCallTransfer $input): AfterPayApiResponseTransfer
    {
        return $this->facade->authorizePayment($input);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayApiResponseTransfer $output
     *
     * @return void
     */
    protected function doTest(AfterPayApiResponseTransfer $output): void
    {
        $this->assertNotEmpty($output->getOutcome());
        $this->assertEquals($output->getOutcome(), AfterPayConfig::API_TRANSACTION_OUTCOME_ACCEPTED);
        $this->assertNotEmpty($output->getCheckoutId());
        $this->assertNotEmpty($output->getReservationId());
        $this->assertNotEmpty($output->getResponsePayload());
    }
}

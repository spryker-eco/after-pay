<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEcoTest\Zed\Afterpay\Business;

use Generated\Shared\Transfer\AfterpayApiResponseTransfer;
use Generated\Shared\Transfer\AfterpayCallTransfer;
use SprykerEco\Shared\Afterpay\AfterpayConstants;
use SprykerEcoTest\Zed\Afterpay\Mock\AfterpayFacadeMock;

class AfterpayFacadeAuthorizeTest extends AfterpayFacadeAbstractTest
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
     * @param \Generated\Shared\Transfer\AfterpayCallTransfer $input
     *
     * @return \Generated\Shared\Transfer\AfterpayApiResponseTransfer
     */
    protected function doFacadeCall(AfterpayCallTransfer $input)
    {
        return (new AfterpayFacadeMock())->authorizePayment($input);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayApiResponseTransfer $output
     *
     * @return void
     */
    protected function doTest(AfterpayApiResponseTransfer $output)
    {
        $this->assertNotEmpty($output->getOutcome());
        $this->assertEquals($output->getOutcome(), AfterpayConstants::API_TRANSACTION_OUTCOME_ACCEPTED);
        $this->assertNotEmpty($output->getCheckoutId());
        $this->assertNotEmpty($output->getReservationId());
        $this->assertNotEmpty($output->getResponsePayload());
    }
}

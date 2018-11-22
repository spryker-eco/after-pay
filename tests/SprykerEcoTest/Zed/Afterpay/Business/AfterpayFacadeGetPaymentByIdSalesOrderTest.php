<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEcoTest\Zed\Afterpay\Business;

use SprykerEco\Shared\Afterpay\AfterpayConstants;

class AfterpayFacadeGetPaymentByIdSalesOrderTest extends AfterpayFacadeAbstractTest
{
    /**
     * @return void
     */
    protected function testGetPaymentByIdSalesOrder()
    {
        $idSalesOrder = 45;
        $output = $this->doFacadeCall($idSalesOrder);
        $this->doTest($output);
    }

    /**
     * @param int $input
     *
     * @return \Generated\Shared\Transfer\AfterpayPaymentTransfer
     */
    protected function doFacadeCall($input)
    {
        return $this->facade->getPaymentByIdSalesOrder($input);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayPaymentTransfer $output
     *
     * @return void
     */
    protected function doTest($output)
    {
        $paymentMethod = $output->getPaymentMethod();
        $this->assertTrue(in_array($paymentMethod, [AfterpayConstants::RISK_CHECK_METHOD_INVOICE]));
    }
}

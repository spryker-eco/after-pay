<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\AfterPay\Business;

use SprykerEco\Shared\AfterPay\AfterPayConfig;

class AfterPayFacadeGetPaymentByIdSalesOrderTest extends AfterPayFacadeAbstractTest
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
     * @return \Generated\Shared\Transfer\AfterPayPaymentTransfer
     */
    protected function doFacadeCall($input)
    {
        return $this->facade->getPaymentByIdSalesOrder($input);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayPaymentTransfer $output
     *
     * @return void
     */
    protected function doTest($output)
    {
        $paymentMethod = $output->getPaymentMethod();
        $this->assertTrue(in_array($paymentMethod, [AfterPayConfig::RISK_CHECK_METHOD_INVOICE]));
    }
}

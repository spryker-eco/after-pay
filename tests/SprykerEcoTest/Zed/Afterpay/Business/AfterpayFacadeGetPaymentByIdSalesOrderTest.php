<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Afterpay\Business;

use SprykerEco\Shared\Afterpay\AfterpayConfig;
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
        $this->assertTrue(in_array($paymentMethod, [AfterpayConfig::RISK_CHECK_METHOD_INVOICE]));
    }
}

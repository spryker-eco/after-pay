<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\AfterPay\Business;

use Generated\Shared\Transfer\AfterPayPaymentTransfer;
use Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPay;
use Spryker\Shared\Oms\OmsConstants;
use SprykerEco\Shared\AfterPay\AfterPayConfig;

class AfterPayFacadeGetPaymentByIdSalesOrderTest extends AfterPayFacadeAbstractTest
{
    /**
     * @var \SprykerEcoTest\Zed\AfterPay\AfterPayZedTester
     */
    protected $tester;

    /**
     * @return void
     */
    public function testGetPaymentByIdSalesOrder(): void
    {
        $idSalesOrder = $this->prepareData();
        $output = $this->doFacadeCall($idSalesOrder);
        $this->doTest($output);
    }

    /**
     * @param int $input
     *
     * @return \Generated\Shared\Transfer\AfterPayPaymentTransfer
     */
    protected function doFacadeCall(int $input): AfterPayPaymentTransfer
    {
        return $this->facade->getPaymentByIdSalesOrder($input);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayPaymentTransfer $output
     *
     * @return void
     */
    protected function doTest(AfterPayPaymentTransfer $output): void
    {
        $paymentMethod = $output->getPaymentMethod();
        $this->assertTrue(in_array($paymentMethod, [AfterPayConfig::RISK_CHECK_METHOD_INVOICE]));
    }

    /**
     * @return int
     */
    protected function prepareData(): int
    {
        $processName = 'AfterPayInvoice01';
        $this->tester->setConfig(OmsConstants::ACTIVE_PROCESSES, [$processName]);
        $prices = [
            'unitPrice' => 100,
            'sumPrice' => 100,
        ];
        $saveOrderTransfer = $this->tester->haveOrder($prices, $processName);

        (new SpyPaymentAfterPay())
            ->setFkSalesOrder($saveOrderTransfer->getIdSalesOrder())
            ->setPaymentMethod(AfterPayConfig::PAYMENT_TYPE_INVOICE)
            ->save();

        return $saveOrderTransfer->getIdSalesOrder();
    }
}

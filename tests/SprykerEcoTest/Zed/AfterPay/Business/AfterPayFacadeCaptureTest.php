<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\AfterPay\Business;

use Generated\Shared\Transfer\AfterPayCallTransfer;
use Generated\Shared\Transfer\AfterPayPaymentTransfer;
use Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPay;
use Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayOrderItem;
use Spryker\Shared\Oms\OmsConstants;
use SprykerEco\Shared\AfterPay\AfterPayConfig;

class AfterPayFacadeCaptureTest extends AfterPayFacadeAbstractTest
{
    /**
     * @var \SprykerEcoTest\Zed\AfterPay\AfterPayZedTester
     */
    protected $tester;

    /**
     * @var \SprykerEco\Zed\AfterPay\Persistence\AfterPayQueryContainerInterface
     */
    protected $afterPayQueryContainer;

    /**
     * @return void
     */
    public function testCapture(): void
    {
        $call = $this->prepareData();
        $afterPayPaymentTransferBeforeCapture = $this->facade->getPaymentByIdSalesOrder($call->getIdSalesOrder());
        $this->doFacadeCall((array)$call->getItems(), $call);
        $this->doTest($call, $afterPayPaymentTransferBeforeCapture);
    }

    /**
     * @param array<\Generated\Shared\Transfer\ItemTransfer> $item
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $call
     *
     * @return void
     */
    protected function doFacadeCall(array $item, AfterPayCallTransfer $call): void
    {
        $this->facade->capturePayment($item, $call);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     * @param \Generated\Shared\Transfer\AfterPayPaymentTransfer $afterPayPaymentTransferBeforeCapture
     *
     * @return void
     */
    protected function doTest(
        AfterPayCallTransfer $afterPayCallTransfer,
        AfterPayPaymentTransfer $afterPayPaymentTransferBeforeCapture
    ): void {
        $afterPayPaymentTransferAfterCapture = $this->facade->getPaymentByIdSalesOrder($afterPayCallTransfer->getIdSalesOrder());

        $this->assertNotEquals(
            $afterPayPaymentTransferBeforeCapture->getCapturedTotal(),
            $afterPayPaymentTransferAfterCapture->getCapturedTotal(),
        );
    }

    /**
     * @return \Generated\Shared\Transfer\AfterPayCallTransfer
     */
    protected function prepareData(): AfterPayCallTransfer
    {
        $call = $this->createCallTransfer();

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

        $afterPayPaymentEntity = $this->afterPayQueryContainer
            ->queryPaymentByIdSalesOrder($saveOrderTransfer->getIdSalesOrder())
            ->findOne();

        foreach ($saveOrderTransfer->getOrderItems() as $item) {
            $item->setUnitPriceToPayAggregation((int)$item->getUnitPriceToPayAggregation());
            $item->setUnitTaxAmountFullAggregation((int)$item->getUnitTaxAmountFullAggregation());

            (new SpyPaymentAfterPayOrderItem())
                ->setFkPaymentAfterPay($afterPayPaymentEntity->getIdPaymentAfterPay())
                ->setFkSalesOrderItem($item->getIdSalesOrderItem())
                ->setCaptureNumber('testCaptureNumber')
                ->save();
        }

        $call->setItems($saveOrderTransfer->getOrderItems());

        $call->setIdSalesOrder($saveOrderTransfer->getIdSalesOrder());

        return $call;
    }
}

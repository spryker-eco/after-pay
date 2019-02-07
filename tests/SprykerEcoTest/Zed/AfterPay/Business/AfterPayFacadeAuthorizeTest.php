<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\AfterPay\Business;

use Generated\Shared\Transfer\AfterPayApiResponseTransfer;
use Generated\Shared\Transfer\AfterPayCallTransfer;
use Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPay;
use Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayOrderItem;
use Orm\Zed\Payment\Persistence\SpySalesPayment;
use Orm\Zed\Payment\Persistence\SpySalesPaymentMethodType;
use Orm\Zed\Payment\Persistence\SpySalesPaymentMethodTypeQuery;
use Spryker\Shared\Oms\OmsConstants;
use SprykerEco\Shared\AfterPay\AfterPayConfig;
use SprykerEco\Shared\AfterPay\AfterPayConstants;

class AfterPayFacadeAuthorizeTest extends AfterPayFacadeAbstractTest
{
    /**
     * @var \SprykerEcoTest\Zed\AfterPay\AfterPayZedTester
     */
    protected $tester;

    /**
     * @return void
     */
    public function testAuthorize(): void
    {
        // Assign
        $processName = 'AfterPayInvoice01';
        $this->tester->setConfig(OmsConstants::ACTIVE_PROCESSES, [$processName]);
        $this->tester->setConfig(AfterPayConstants::AFTERPAY_AUTHORIZE_WORKFLOW, AfterPayConfig::AFTERPAY_AUTHORIZE_WORKFLOW_ONE_STEP);

        $prices = [
            'unitPrice' => 100,
            'sumPrice' => 100,
        ];
        $savedOrderTransfer = $this->tester->haveOrder($prices, $processName);

        (new SpyPaymentAfterPay())
            ->setFkSalesOrder($savedOrderTransfer->getIdSalesOrder())
            ->setPaymentMethod(AfterPayConfig::PAYMENT_TYPE_INVOICE)
            ->save();

        $afterPayPaymentEntity = $this->afterPayQueryContainer
            ->queryPaymentByIdSalesOrder($savedOrderTransfer->getIdSalesOrder())
            ->findOne();

        $input = $this->createCallTransfer()
            ->setIdSalesOrder($savedOrderTransfer->getIdSalesOrder())
            ->setOrderReference($savedOrderTransfer->getOrderReference());

        (new SpySalesPaymentMethodType())
            ->setPaymentProvider(AfterPayConfig::PROVIDER_NAME)
            ->setPaymentMethod($input->getPaymentMethod())
            ->save();

        $afterPayPaymentMethod = (new SpySalesPaymentMethodTypeQuery())
            ->filterByPaymentMethod($input->getPaymentMethod())
            ->filterByPaymentProvider(AfterPayConfig::PROVIDER_NAME)
            ->findOne();

        (new SpySalesPayment())
            ->setAmount(100)
            ->setFkSalesOrder($savedOrderTransfer->getIdSalesOrder())
            ->setFkSalesPaymentMethodType($afterPayPaymentMethod->getIdSalesPaymentMethodType())
            ->save();

        foreach ($savedOrderTransfer->getOrderItems() as $item) {
            $item->setUnitPriceToPayAggregation((int)$item->getUnitPriceToPayAggregation());
            $item->setUnitTaxAmountFullAggregation((int)$item->getUnitTaxAmountFullAggregation());

            (new SpyPaymentAfterPayOrderItem())
                ->setFkPaymentAfterPay($afterPayPaymentEntity->getIdPaymentAfterPay())
                ->setFkSalesOrderItem($item->getIdSalesOrderItem())
                ->setCaptureNumber('testCaptureNumber')
                ->save();
        }
        $input->setItems($savedOrderTransfer->getOrderItems());

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
        $this->assertNotNull($output->getOutcome());
        $this->assertEquals($output->getOutcome(), AfterPayConfig::API_TRANSACTION_OUTCOME_ACCEPTED);
        $this->assertNotNull($output->getCheckoutId());
        $this->assertNotNull($output->getReservationId());
        $this->assertNotNull($output->getResponsePayload());
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $input
     *
     * @return void
     */
    protected function savePaymentAfterPay(AfterPayCallTransfer $input): void
    {
        (new SpyPaymentAfterPay())
            ->setFkSalesOrder($input->getIdSalesOrder())
            ->setPaymentMethod('Invoice')
            ->save();
    }
}

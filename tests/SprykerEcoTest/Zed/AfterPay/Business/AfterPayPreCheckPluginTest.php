<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\AfterPay\Business;

use Generated\Shared\Transfer\AfterPayApiResponseTransfer;
use Generated\Shared\Transfer\AfterPayCallTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPay;
use Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayOrderItem;
use Orm\Zed\Payment\Persistence\SpySalesPayment;
use Orm\Zed\Payment\Persistence\SpySalesPaymentMethodType;
use Orm\Zed\Payment\Persistence\SpySalesPaymentMethodTypeQuery;
use Spryker\Shared\Oms\OmsConstants;
use SprykerEco\Shared\AfterPay\AfterPayConfig;
use SprykerEco\Shared\AfterPay\AfterPayConstants;
use SprykerEco\Zed\AfterPay\Communication\AfterPayCommunicationFactory;
use SprykerEco\Zed\AfterPay\Communication\Plugin\Checkout\AfterPayPreCheckPlugin;

class AfterPayPreCheckPluginTest extends AfterPayFacadeAbstractTest
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
        $activeProcessName = 'AfterPayInvoice01';
        $this->tester->setConfig(OmsConstants::ACTIVE_PROCESSES, [$activeProcessName]);
        $this->tester->setConfig(AfterPayConstants::AFTERPAY_AUTHORIZE_WORKFLOW, AfterPayConfig::AFTERPAY_AUTHORIZE_WORKFLOW_ONE_STEP);

        $savedOrderTransfer = $this->tester->haveOrder(
            [
                'unitPrice' => 100,
                'sumPrice' => 100,
            ],
            $activeProcessName
        );

        (new SpyPaymentAfterPay())
            ->setFkSalesOrder($savedOrderTransfer->getIdSalesOrder())
            ->setPaymentMethod(AfterPayConfig::PAYMENT_TYPE_INVOICE)
            ->save();

        $afterPayPaymentEntity = $this->afterPayQueryContainer
            ->queryPaymentByIdSalesOrder($savedOrderTransfer->getIdSalesOrder())
            ->findOne();

        $afterPayPaymentTransfer = (new PaymentTransfer())
            ->setIdSalesOrder($savedOrderTransfer->getIdSalesOrder())
            ->setPaymentSelection('afterPayInvoice');

        $input = $this->createQuoteTransfer()
            ->setPayment($afterPayPaymentTransfer)
            ->setOrderReference($savedOrderTransfer->getOrderReference());

        (new SpySalesPaymentMethodType())
            ->setPaymentProvider(AfterPayConfig::PROVIDER_NAME)
            ->setPaymentMethod($input->getPayment()->getPaymentSelection())
            ->save();

        $afterPayPaymentMethod = (new SpySalesPaymentMethodTypeQuery())
            ->filterByPaymentMethod($input->getPayment()->getPaymentSelection())
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

        // Act
        //$output = (new AfterPayPreCheckPlugin())->preSave($input, new CheckoutResponseTransfer());
        $afterPayCallTransfer = (new AfterPayCommunicationFactory())
            ->createQuoteToCallConverter()
            ->convert($input);
        $output = $this->facade->authorizePayment($afterPayCallTransfer);

        // Assert
        $this->assertNotNull($output->getOutcome());
        $this->assertEquals($output->getOutcome(), AfterPayConfig::API_TRANSACTION_OUTCOME_ACCEPTED);
        $this->assertNotNull($output->getCheckoutId());
        $this->assertNotNull($output->getReservationId());
        $this->assertNotNull($output->getResponsePayload());
    }
}

<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\AfterPay\Business;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPay;
use Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayOrderItem;
use Orm\Zed\Payment\Persistence\SpySalesPayment;
use Orm\Zed\Payment\Persistence\SpySalesPaymentMethodType;
use Orm\Zed\Payment\Persistence\SpySalesPaymentMethodTypeQuery;
use Spryker\Shared\Oms\OmsConstants;
use SprykerEco\Shared\AfterPay\AfterPayConfig;
use SprykerEco\Shared\AfterPay\AfterPayConstants;
use SprykerEco\Zed\AfterPay\Communication\AfterPayCommunicationFactory;

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

        $quoteTransfer = $this->createQuoteFromSavedOrder($savedOrderTransfer);

        $this->savePaymentAfterPay($savedOrderTransfer);
        $this->saveSalesPaymentMethodType($quoteTransfer);
        $this->saveSalesPayment($savedOrderTransfer, $quoteTransfer);
        $this->savePaymentAfterPayOrderItems($savedOrderTransfer);

        // Act
        $afterPayCallTransfer = (new AfterPayCommunicationFactory())
            ->createQuoteToCallConverter()
            ->convert($quoteTransfer);
        $afterPayApiResponseTransfer = $this->facade->authorizePayment($afterPayCallTransfer);

        // Assert
        $this->assertNotNull($afterPayApiResponseTransfer->getOutcome());
        $this->assertEquals($afterPayApiResponseTransfer->getOutcome(), AfterPayConfig::API_TRANSACTION_OUTCOME_ACCEPTED);
        $this->assertNotNull($afterPayApiResponseTransfer->getCheckoutId());
        $this->assertNotNull($afterPayApiResponseTransfer->getReservationId());
        $this->assertNotNull($afterPayApiResponseTransfer->getResponsePayload());
    }

    /**
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $savedOrderTransfer
     *
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function createQuoteFromSavedOrder($savedOrderTransfer): QuoteTransfer
    {
        $afterPayPaymentTransfer = (new PaymentTransfer())
            ->setIdSalesOrder($savedOrderTransfer->getIdSalesOrder())
            ->setPaymentSelection('afterPayInvoice');

        return $this->createQuoteTransfer()
            ->setPayment($afterPayPaymentTransfer)
            ->setOrderReference($savedOrderTransfer->getOrderReference())
            ->setItems($savedOrderTransfer->getOrderItems());
    }

    /**
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $savedOrderTransfer
     *
     * @return void
     */
    protected function savePaymentAfterPay(SaveOrderTransfer $savedOrderTransfer): void
    {
        (new SpyPaymentAfterPay())
            ->setFkSalesOrder($savedOrderTransfer->getIdSalesOrder())
            ->setPaymentMethod(AfterPayConfig::PAYMENT_TYPE_INVOICE)
            ->save();
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return void
     */
    protected function saveSalesPaymentMethodType(QuoteTransfer $quoteTransfer): void
    {
        (new SpySalesPaymentMethodType())
            ->setPaymentProvider(AfterPayConfig::PROVIDER_NAME)
            ->setPaymentMethod($quoteTransfer->getPayment()->getPaymentSelection())
            ->save();
    }

    /**
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $savedOrderTransfer
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return void
     */
    protected function saveSalesPayment(
        SaveOrderTransfer $savedOrderTransfer,
        QuoteTransfer $quoteTransfer
    ): void {
        $afterPayPaymentMethod = (new SpySalesPaymentMethodTypeQuery())
            ->filterByPaymentMethod($quoteTransfer->getPayment()->getPaymentSelection())
            ->filterByPaymentProvider(AfterPayConfig::PROVIDER_NAME)
            ->findOne();

        (new SpySalesPayment())
            ->setAmount(100)
            ->setFkSalesOrder($savedOrderTransfer->getIdSalesOrder())
            ->setFkSalesPaymentMethodType($afterPayPaymentMethod->getIdSalesPaymentMethodType())
            ->save();
    }

    /**
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $savedOrderTransfer
     *
     * @return void
     */
    protected function savePaymentAfterPayOrderItems(SaveOrderTransfer $savedOrderTransfer): void
    {
        $afterPayPaymentEntity = $this->afterPayQueryContainer
            ->queryPaymentByIdSalesOrder($savedOrderTransfer->getIdSalesOrder())
            ->findOne();

        foreach ($savedOrderTransfer->getOrderItems() as $item) {
            $item->setUnitPriceToPayAggregation((int)$item->getUnitPriceToPayAggregation());
            $item->setUnitTaxAmountFullAggregation((int)$item->getUnitTaxAmountFullAggregation());

            $this->savePaymentAfterPayOrderItem($item, $afterPayPaymentEntity);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $orderItemTransfer
     * @param \Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPay $afterPayPaymentEntity
     *
     * @return void
     */
    protected function savePaymentAfterPayOrderItem(
        ItemTransfer $orderItemTransfer,
        SpyPaymentAfterPay $afterPayPaymentEntity
    ): void {
        (new SpyPaymentAfterPayOrderItem())
            ->setFkPaymentAfterPay($afterPayPaymentEntity->getIdPaymentAfterPay())
            ->setFkSalesOrderItem($orderItemTransfer->getIdSalesOrderItem())
            ->setCaptureNumber('testCaptureNumber')
            ->save();
    }
}

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

class AfterPayFacadeAuthorizeForQuoteTest extends AfterPayFacadeAbstractTest
{
    /**
     * @var string
     */
    protected const OMS_PROCESS_INVOICE = 'AfterPayInvoice01';

    /**
     * @var \SprykerEcoTest\Zed\AfterPay\AfterPayZedTester
     */
    protected $tester;

    /**
     * @return void
     */
    public function testAuthorizePaymentMustSendAuthorizeRequest(): void
    {
        // Assign
        $this->tester->setConfig(OmsConstants::ACTIVE_PROCESSES, [static::OMS_PROCESS_INVOICE]);
        $this->tester->setConfig(AfterPayConstants::AFTERPAY_AUTHORIZE_WORKFLOW, AfterPayConfig::AFTERPAY_AUTHORIZE_WORKFLOW_ONE_STEP);

        $savedOrderTransfer = $this->tester->haveOrder(
            [
                ItemTransfer::UNIT_PRICE => 100,
                ItemTransfer::SUM_PRICE => 100,
            ],
            static::OMS_PROCESS_INVOICE,
        );

        $quoteTransfer = $this->createQuoteFromSavedOrder($savedOrderTransfer);

        $this->savePaymentAfterPay($savedOrderTransfer);
        $this->saveSalesPaymentMethodType($quoteTransfer);
        $this->saveSalesPayment($savedOrderTransfer, $quoteTransfer);
        $this->savePaymentAfterPayOrderItems($savedOrderTransfer);

        // Act
        $afterPayApiResponseTransfer = $this->facade->authorizePaymentForQuote($quoteTransfer);

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
    protected function createQuoteFromSavedOrder(SaveOrderTransfer $savedOrderTransfer): QuoteTransfer
    {
        $paymentTransfer = (new PaymentTransfer())
            ->setIdSalesOrder($savedOrderTransfer->getIdSalesOrder())
            ->setPaymentSelection(AfterPayConfig::PAYMENT_METHOD_INVOICE);

        return $this->createQuoteTransfer()
            ->setPayment($paymentTransfer)
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
        $salesPaymentMethodTypeQuery = (new SpySalesPaymentMethodTypeQuery())
            ->filterByPaymentMethod($quoteTransfer->getPayment()->getPaymentSelection())
            ->filterByPaymentProvider(AfterPayConfig::PROVIDER_NAME)
            ->findOne();

        (new SpySalesPayment())
            ->setAmount(100)
            ->setFkSalesOrder($savedOrderTransfer->getIdSalesOrder())
            ->setFkSalesPaymentMethodType($salesPaymentMethodTypeQuery->getIdSalesPaymentMethodType())
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

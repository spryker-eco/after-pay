<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Payment;

use Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPay;
use Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayOrderItem;
use SprykerEco\Zed\AfterPay\Persistence\AfterPayQueryContainerInterface;

class PaymentWriter implements PaymentWriterInterface
{
    /**
     * @var \SprykerEco\Zed\AfterPay\Persistence\AfterPayQueryContainerInterface
     */
    protected $afterpayQueryContainer;

    /**
     * @param \SprykerEco\Zed\AfterPay\Persistence\AfterPayQueryContainerInterface $afterpayQueryContainer
     */
    public function __construct(AfterPayQueryContainerInterface $afterpayQueryContainer)
    {
        $this->afterpayQueryContainer = $afterpayQueryContainer;
    }

    /**
     * @param string $idReservation
     * @param int $idSalesOrder
     *
     * @return void
     */
    public function setIdReservationByIdSalesOrder(string $idReservation, int $idSalesOrder): void
    {
        $afterpayPaymentEntity = $this->getPaymentEntityByIdSalesOrder($idSalesOrder);
        $afterpayPaymentEntity
            ->setIdReservation($idReservation)
            ->save();
    }

    /**
     * @param int $authorizedTotal
     * @param int $idSalesOrder
     *
     * @return void
     */
    public function setAuthorizedTotalByIdSalesOrder(int $authorizedTotal, int $idSalesOrder): void
    {
        $afterpayPaymentEntity = $this->getPaymentEntityByIdSalesOrder($idSalesOrder);
        $afterpayPaymentEntity
            ->setAuthorizedTotal($authorizedTotal)
            ->save();
    }

    /**
     * @param int $amountToAdd
     * @param int $idSalesOrder
     *
     * @return void
     */
    public function increaseTotalCapturedAmountByIdSalesOrder(int $amountToAdd, int $idSalesOrder): void
    {
        $afterpayPaymentEntity = $this->getPaymentEntityByIdSalesOrder($idSalesOrder);
        $afterpayPaymentEntity
            ->setCapturedTotal($afterpayPaymentEntity->getCapturedTotal() + $amountToAdd)
            ->save();
    }

    /**
     * @param string $captureNumber
     * @param int $idSalesOrder
     *
     * @return void
     */
    public function updateExpensesCaptureNumber(string $captureNumber, int $idSalesOrder): void
    {
        $afterpayPaymentEntity = $this->getPaymentEntityByIdSalesOrder($idSalesOrder);
        $afterpayPaymentEntity
            ->setExpensesCaptureNumber($captureNumber)
            ->save();
    }

    /**
     * @param int $refundedAmount
     * @param int $idSalesOrder
     *
     * @return void
     */
    public function increaseRefundedTotalByIdSalesOrder(int $refundedAmount, int $idSalesOrder): void
    {
        $afterpayPaymentEntity = $this->getPaymentEntityByIdSalesOrder($idSalesOrder);
        $refundedTotal = $afterpayPaymentEntity->getRefundedTotal();
        $afterpayPaymentEntity
            ->setRefundedTotal($refundedTotal + $refundedAmount)
            ->save();
    }

    /**
     * @param int $captureNumber
     * @param int $idSalesOrderItem
     * @param int $idPayment
     *
     * @return void
     */
    public function setCaptureNumberByIdSalesOrderItemAndIdPayment(
        int $captureNumber,
        int $idSalesOrderItem,
        int $idPayment
    ): void {
        $afterpayPaymentOrderItemEntity = $this->getPaymentOrderItemEntityByIdSalesOrderItemAndIdPayment(
            $idSalesOrderItem,
            $idPayment
        );
        $afterpayPaymentOrderItemEntity
            ->setCaptureNumber($captureNumber)
            ->save();
    }

    /**
     * @param int $amountToAdd
     * @param int $idSalesOrder
     *
     * @return void
     */
    public function increaseTotalCancelledAmountByIdSalesOrder(int $amountToAdd, int $idSalesOrder): void
    {
        $afterpayPaymentEntity = $this->getPaymentEntityByIdSalesOrder($idSalesOrder);
        $afterpayPaymentEntity
            ->setCancelledTotal($afterpayPaymentEntity->getCancelledTotal() + $amountToAdd)
            ->save();
    }

    /**
     * @param int $idSalesOrder
     *
     * @return \Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPay|null
     */
    protected function getPaymentEntityByIdSalesOrder(int $idSalesOrder): ?SpyPaymentAfterPay
    {
        $afterpayPaymentEntity = $this->afterpayQueryContainer
            ->queryPaymentByIdSalesOrder($idSalesOrder)
            ->findOne();

        return $afterpayPaymentEntity;
    }

    /**
     * @param int $idSalesOrderItem
     * @param int $idPayment
     *
     * @return \Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayOrderItem
     */
    protected function getPaymentOrderItemEntityByIdSalesOrderItemAndIdPayment(int $idSalesOrderItem, int $idPayment): ?SpyPaymentAfterPayOrderItem
    {
        $afterpayPaymentOrderItemEntity = $this->afterpayQueryContainer
            ->queryPaymentOrderItemByIdSalesOrderAndIdPayment($idSalesOrderItem, $idPayment)
            ->findOne();

        return $afterpayPaymentOrderItemEntity;
    }
}

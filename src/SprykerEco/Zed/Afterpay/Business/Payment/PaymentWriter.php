<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment;

use Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpay;
use Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayOrderItem;
use SprykerEco\Zed\Afterpay\Persistence\AfterpayQueryContainerInterface;

class PaymentWriter implements PaymentWriterInterface
{
    /**
     * @var \SprykerEco\Zed\Afterpay\Persistence\AfterpayQueryContainerInterface
     */
    protected $afterpayQueryContainer;

    /**
     * @param \SprykerEco\Zed\Afterpay\Persistence\AfterpayQueryContainerInterface $afterpayQueryContainer
     */
    public function __construct(AfterpayQueryContainerInterface $afterpayQueryContainer)
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
     * @return \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpay|null
     */
    protected function getPaymentEntityByIdSalesOrder(int $idSalesOrder): ?SpyPaymentAfterpay
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
     * @return \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayOrderItem
     */
    protected function getPaymentOrderItemEntityByIdSalesOrderItemAndIdPayment(int $idSalesOrderItem, int $idPayment): ?SpyPaymentAfterpayOrderItem
    {
        $afterpayPaymentOrderItemEntity = $this->afterpayQueryContainer
            ->queryPaymentOrderItemByIdSalesOrderAndIdPayment($idSalesOrderItem, $idPayment)
            ->findOne();

        return $afterpayPaymentOrderItemEntity;
    }
}

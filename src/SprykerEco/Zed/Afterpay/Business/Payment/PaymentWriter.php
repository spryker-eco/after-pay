<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment;

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
    public function setIdReservationByIdSalesOrder($idReservation, $idSalesOrder)
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
    public function setAuthorizedTotalByIdSalesOrder($authorizedTotal, $idSalesOrder)
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
    public function increaseTotalCapturedAmountByIdSalesOrder($amountToAdd, $idSalesOrder)
    {
        $afterpayPaymentEntity = $this->getPaymentEntityByIdSalesOrder($idSalesOrder);

        $afterpayPaymentEntity
            ->setCapturedTotal(
                $afterpayPaymentEntity->getCapturedTotal() + $amountToAdd
            )
            ->save();
    }

    /**
     * @param string $captureNumber
     * @param int $idSalesOrder
     *
     * @return void
     */
    public function updateExpensesCaptureNumber($captureNumber, $idSalesOrder)
    {
        $afterpayPaymentEntity = $this->getPaymentEntityByIdSalesOrder($idSalesOrder);
        $afterpayPaymentEntity
            ->setExpensesCaptureNumber(
                $captureNumber
            )
            ->save();
    }

    /**
     * @param int $refundedAmount
     * @param int $idSalesOrder
     *
     * @return void
     */
    public function increaseRefundedTotalByIdSalesOrder($refundedAmount, $idSalesOrder)
    {
        $afterpayPaymentEntity = $this->getPaymentEntityByIdSalesOrder($idSalesOrder);

        $refundedTotal = $afterpayPaymentEntity->getRefundedTotal();

        $afterpayPaymentEntity
            ->setRefundedTotal(
                $refundedTotal + $refundedAmount
            )
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
        $captureNumber,
        $idSalesOrderItem,
        $idPayment
    ) {
        $afterpayPaymentOrderItemEntity = $this->getPaymentOrderItemEntityByIdSalesOrderItemAndIdPayment(
            $idSalesOrderItem,
            $idPayment
        );

        $afterpayPaymentOrderItemEntity->setCaptureNumber($captureNumber)->save();
    }

    /**
     * @param int $amountToAdd
     * @param int $idSalesOrder
     *
     * @return void
     */
    public function increaseTotalCancelledAmountByIdSalesOrder($amountToAdd, $idSalesOrder)
    {
        $afterpayPaymentEntity = $this->getPaymentEntityByIdSalesOrder($idSalesOrder);

        $afterpayPaymentEntity
            ->setCancelledTotal(
                $afterpayPaymentEntity->getCancelledTotal() + $amountToAdd
            )
            ->save();
    }

    /**
     * @param int $idSalesOrder
     *
     * @return \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpay
     */
    protected function getPaymentEntityByIdSalesOrder($idSalesOrder)
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
    protected function getPaymentOrderItemEntityByIdSalesOrderItemAndIdPayment($idSalesOrderItem, $idPayment)
    {
        $afterpayPaymentOrderItemEntity = $this->afterpayQueryContainer
            ->queryPaymentOrderItemByIdSalesOrderAndIdPayment($idSalesOrderItem, $idPayment)
            ->findOne();

        return $afterpayPaymentOrderItemEntity;
    }
}

<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment;

interface PaymentWriterInterface
{
    /**
     * @param string $idReservation
     * @param int $idSalesOrder
     *
     * @return void
     */
    public function setIdReservationByIdSalesOrder($idReservation, $idSalesOrder);

    /**
     * @param int $authorizedTotal
     * @param int $idSalesOrder
     *
     * @return void
     */
    public function setAuthorizedTotalByIdSalesOrder($authorizedTotal, $idSalesOrder);

    /**
     * @param int $amountToAdd
     * @param int $idSalesOrder
     *
     * @return void
     */
    public function increaseTotalCapturedAmountByIdSalesOrder($amountToAdd, $idSalesOrder);

    /**
     * @param string $captureNumber
     * @param int $idSalesOrder
     *
     * @return void
     */
    public function updateExpensesCaptureNumber($captureNumber, $idSalesOrder);

    /**
     * @param int $amountToAdd
     * @param int $idSalesOrder
     *
     * @return void
     */
    public function increaseTotalCancelledAmountByIdSalesOrder($amountToAdd, $idSalesOrder);

    /**
     * @param int $refundedAmount
     * @param int $idSalesOrder
     *
     * @return void
     */
    public function increaseRefundedTotalByIdSalesOrder($refundedAmount, $idSalesOrder);

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
    );
}

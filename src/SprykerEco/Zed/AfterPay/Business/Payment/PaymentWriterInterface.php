<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Payment;

interface PaymentWriterInterface
{
    /**
     * @param string $idReservation
     * @param int $idSalesOrder
     *
     * @return void
     */
    public function setIdReservationByIdSalesOrder(string $idReservation, int $idSalesOrder): void;

    /**
     * @param int $authorizedTotal
     * @param int $idSalesOrder
     *
     * @return void
     */
    public function setAuthorizedTotalByIdSalesOrder(int $authorizedTotal, int $idSalesOrder): void;

    /**
     * @param int $amountToAdd
     * @param int $idSalesOrder
     *
     * @return void
     */
    public function increaseTotalCapturedAmountByIdSalesOrder(int $amountToAdd, int $idSalesOrder): void;

    /**
     * @param string $captureNumber
     * @param int $idSalesOrder
     *
     * @return void
     */
    public function updateExpensesCaptureNumber(string $captureNumber, int $idSalesOrder): void;

    /**
     * @param int $amountToAdd
     * @param int $idSalesOrder
     *
     * @return void
     */
    public function increaseTotalCancelledAmountByIdSalesOrder(int $amountToAdd, int $idSalesOrder): void;

    /**
     * @param int $refundedAmount
     * @param int $idSalesOrder
     *
     * @return void
     */
    public function increaseRefundedTotalByIdSalesOrder(int $refundedAmount, int $idSalesOrder): void;

    /**
     * @param string $captureNumber
     * @param int $idSalesOrderItem
     * @param int $idPayment
     *
     * @return void
     */
    public function setCaptureNumberByIdSalesOrderItemAndIdPayment(
        string $captureNumber,
        int $idSalesOrderItem,
        int $idPayment
    ): void;
}

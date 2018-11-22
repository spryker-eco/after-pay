<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment;

interface PaymentReaderInterface
{
    /**
     * @param int $idSalesOrder
     *
     * @return \Generated\Shared\Transfer\AfterpayPaymentTransfer
     */
    public function getPaymentByIdSalesOrder($idSalesOrder);

    /**
     * @param int $idSalesOrderItem
     * @param int $idPayment
     *
     * @return \Generated\Shared\Transfer\AfterpayPaymentOrderItemTransfer
     */
    public function getPaymentOrderItemByIdSalesOrderItemAndIdPayment($idSalesOrderItem, $idPayment);
}

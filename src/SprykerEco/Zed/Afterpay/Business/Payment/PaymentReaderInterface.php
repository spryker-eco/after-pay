<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment;

use Generated\Shared\Transfer\AfterpayPaymentOrderItemTransfer;
use Generated\Shared\Transfer\AfterpayPaymentTransfer;

interface PaymentReaderInterface
{
    /**
     * @param int $idSalesOrder
     *
     * @return \Generated\Shared\Transfer\AfterpayPaymentTransfer
     */
    public function getPaymentByIdSalesOrder(int $idSalesOrder): AfterpayPaymentTransfer;

    /**
     * @param int $idSalesOrderItem
     * @param int $idPayment
     *
     * @return \Generated\Shared\Transfer\AfterpayPaymentOrderItemTransfer
     */
    public function getPaymentOrderItemByIdSalesOrderItemAndIdPayment(int $idSalesOrderItem, int $idPayment): AfterpayPaymentOrderItemTransfer;
}

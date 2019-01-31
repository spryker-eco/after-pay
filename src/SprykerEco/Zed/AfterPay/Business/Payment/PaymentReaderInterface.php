<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Payment;

use Generated\Shared\Transfer\AfterPayPaymentOrderItemTransfer;
use Generated\Shared\Transfer\AfterPayPaymentTransfer;

interface PaymentReaderInterface
{
    /**
     * @param int $idSalesOrder
     *
     * @return \Generated\Shared\Transfer\AfterPayPaymentTransfer
     */
    public function getPaymentByIdSalesOrder(int $idSalesOrder): AfterPayPaymentTransfer;

    /**
     * @param int $idSalesOrderItem
     * @param int $idPayment
     *
     * @return \Generated\Shared\Transfer\AfterPayPaymentOrderItemTransfer
     */
    public function getPaymentOrderItemByIdSalesOrderItemAndIdPayment(int $idSalesOrderItem, int $idPayment): AfterPayPaymentOrderItemTransfer;
}

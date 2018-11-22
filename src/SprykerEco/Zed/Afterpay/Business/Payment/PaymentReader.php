<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment;

use Generated\Shared\Transfer\AfterpayPaymentOrderItemTransfer;
use Generated\Shared\Transfer\AfterpayPaymentTransfer;
use SprykerEco\Zed\Afterpay\Persistence\AfterpayQueryContainerInterface;

class PaymentReader implements PaymentReaderInterface
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
     * @param int $idSalesOrder
     *
     * @return \Generated\Shared\Transfer\AfterpayPaymentTransfer
     */
    public function getPaymentByIdSalesOrder($idSalesOrder)
    {
        $afterpayPaymentEntity = $this->getPaymentEntityByIdSalesOrder($idSalesOrder);

        $paymentTransfer = new AfterpayPaymentTransfer();
        $paymentTransfer->fromArray($afterpayPaymentEntity->toArray(), true);

        return $paymentTransfer;
    }

    /**
     * @param int $idSalesOrderItem
     * @param int $idPayment
     *
     * @return \Generated\Shared\Transfer\AfterpayPaymentOrderItemTransfer
     */
    public function getPaymentOrderItemByIdSalesOrderItemAndIdPayment($idSalesOrderItem, $idPayment)
    {
        $afterpayPaymentOrderItemEntity = $this->getPaymentOrderItemEntityByIdSalesOrderItemAndIdPayment(
            $idSalesOrderItem,
            $idPayment
        );

        $paymentOrderItemTransfer = new AfterpayPaymentOrderItemTransfer();
        $paymentOrderItemTransfer->fromArray($afterpayPaymentOrderItemEntity->toArray(), true);

        return $paymentOrderItemTransfer;
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
    protected function getPaymentOrderItemEntityByIdSalesOrderItemAndIdPayment(
        $idSalesOrderItem,
        $idPayment
    ) {
        $afterpayPaymentOrderItemEntity = $this->afterpayQueryContainer
            ->queryPaymentOrderItemByIdSalesOrderAndIdPayment($idSalesOrderItem, $idPayment)
            ->findOne();
        return $afterpayPaymentOrderItemEntity;
    }
}

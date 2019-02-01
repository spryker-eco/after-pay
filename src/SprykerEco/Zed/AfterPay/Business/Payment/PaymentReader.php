<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Payment;

use Generated\Shared\Transfer\AfterPayPaymentOrderItemTransfer;
use Generated\Shared\Transfer\AfterPayPaymentTransfer;
use Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPay;
use Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayOrderItem;
use SprykerEco\Zed\AfterPay\Persistence\AfterPayQueryContainerInterface;

class PaymentReader implements PaymentReaderInterface
{
    /**
     * @var \SprykerEco\Zed\AfterPay\Persistence\AfterPayQueryContainerInterface
     */
    protected $afterPayQueryContainer;

    /**
     * @param \SprykerEco\Zed\AfterPay\Persistence\AfterPayQueryContainerInterface $afterPayQueryContainer
     */
    public function __construct(AfterPayQueryContainerInterface $afterPayQueryContainer)
    {
        $this->afterPayQueryContainer = $afterPayQueryContainer;
    }

    /**
     * @param int $idSalesOrder
     *
     * @return \Generated\Shared\Transfer\AfterPayPaymentTransfer
     */
    public function getPaymentByIdSalesOrder(int $idSalesOrder): AfterPayPaymentTransfer
    {
        $afterPayPaymentEntity = $this->getPaymentEntityByIdSalesOrder($idSalesOrder);

        return (new AfterPayPaymentTransfer())
            ->fromArray($afterPayPaymentEntity->toArray(), true);
    }

    /**
     * @param int $idSalesOrderItem
     * @param int $idPayment
     *
     * @return \Generated\Shared\Transfer\AfterPayPaymentOrderItemTransfer
     */
    public function getPaymentOrderItemByIdSalesOrderItemAndIdPayment(int $idSalesOrderItem, int $idPayment): AfterPayPaymentOrderItemTransfer
    {
        $afterPayPaymentOrderItemEntity = $this->getPaymentOrderItemEntityByIdSalesOrderItemAndIdPayment(
            $idSalesOrderItem,
            $idPayment
        );

        return (new AfterPayPaymentOrderItemTransfer())
            ->fromArray($afterPayPaymentOrderItemEntity->toArray(), true);
    }

    /**
     * @param int $idSalesOrder
     *
     * @return \Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPay
     */
    protected function getPaymentEntityByIdSalesOrder(int $idSalesOrder): SpyPaymentAfterPay
    {
        $afterPayPaymentEntity = $this->afterPayQueryContainer
            ->queryPaymentByIdSalesOrder($idSalesOrder)
            ->findOne();

        return $afterPayPaymentEntity;
    }

    /**
     * @param int $idSalesOrderItem
     * @param int $idPayment
     *
     * @return \Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayOrderItem
     */
    protected function getPaymentOrderItemEntityByIdSalesOrderItemAndIdPayment(int $idSalesOrderItem, int $idPayment): SpyPaymentAfterPayOrderItem
    {
        $afterPayPaymentOrderItemEntity = $this->afterPayQueryContainer
            ->queryPaymentOrderItemByIdSalesOrderAndIdPayment($idSalesOrderItem, $idPayment)
            ->findOne();
        return $afterPayPaymentOrderItemEntity;
    }
}

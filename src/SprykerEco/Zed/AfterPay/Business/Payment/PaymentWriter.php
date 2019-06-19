<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Payment;

use Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPay;
use Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayOrderItem;
use SprykerEco\Zed\AfterPay\Persistence\AfterPayEntityManagerInterface;
use SprykerEco\Zed\AfterPay\Persistence\AfterPayQueryContainerInterface;

class PaymentWriter implements PaymentWriterInterface
{
    /**
     * @var \SprykerEco\Zed\AfterPay\Persistence\AfterPayQueryContainerInterface
     */
    protected $afterPayQueryContainer;

    /**
     * @var \SprykerEco\Zed\AfterPay\Persistence\AfterPayEntityManagerInterface
     */
    protected $afterPayEntityManager;

    /**
     * @param \SprykerEco\Zed\AfterPay\Persistence\AfterPayQueryContainerInterface $afterPayQueryContainer
     * @param \SprykerEco\Zed\AfterPay\Persistence\AfterPayEntityManagerInterface $afterPayEntityManager
     */
    public function __construct(
        AfterPayQueryContainerInterface $afterPayQueryContainer,
        AfterPayEntityManagerInterface $afterPayEntityManager
    ) {
        $this->afterPayQueryContainer = $afterPayQueryContainer;
        $this->afterPayEntityManager = $afterPayEntityManager;
    }

    /**
     * @param string $idReservation
     * @param int $idSalesOrder
     *
     * @return void
     */
    public function setIdReservationByIdSalesOrder(int $idSalesOrder, string $idReservation = null): void
    {
        $afterPayPaymentEntity = $this->getPaymentEntityByIdSalesOrder($idSalesOrder);
        $afterPayPaymentEntity
            ->setIdReservation($idReservation)
            ->save();
    }

    /**
     * @param string $customerNumber
     * @param int $idSalesOrder
     *
     * @return void
     */
    public function setCustomerNumberByIdSalesOrder(string $customerNumber, int $idSalesOrder): void
    {
        $this->afterPayEntityManager->addCustomerNumberToAfterPayPaymentByIdSalesOrder($customerNumber, $idSalesOrder);
    }

    /**
     * @param int $authorizedTotal
     * @param int $idSalesOrder
     *
     * @return void
     */
    public function setAuthorizedTotalByIdSalesOrder(int $authorizedTotal, int $idSalesOrder): void
    {
        $afterPayPaymentEntity = $this->getPaymentEntityByIdSalesOrder($idSalesOrder);
        $afterPayPaymentEntity
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
        $afterPayPaymentEntity = $this->getPaymentEntityByIdSalesOrder($idSalesOrder);
        $afterPayPaymentEntity
            ->setCapturedTotal($afterPayPaymentEntity->getCapturedTotal() + $amountToAdd)
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
        $afterPayPaymentEntity = $this->getPaymentEntityByIdSalesOrder($idSalesOrder);
        $afterPayPaymentEntity
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
        $afterPayPaymentEntity = $this->getPaymentEntityByIdSalesOrder($idSalesOrder);
        $refundedTotal = $afterPayPaymentEntity->getRefundedTotal();
        $afterPayPaymentEntity
            ->setRefundedTotal($refundedTotal + $refundedAmount)
            ->save();
    }

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
    ): void {
        $afterPayPaymentOrderItemEntity = $this->getPaymentOrderItemEntityByIdSalesOrderItemAndIdPayment(
            $idSalesOrderItem,
            $idPayment
        );
        $afterPayPaymentOrderItemEntity
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
        $afterPayPaymentEntity = $this->getPaymentEntityByIdSalesOrder($idSalesOrder);
        $afterPayPaymentEntity
            ->setCancelledTotal($afterPayPaymentEntity->getCancelledTotal() + $amountToAdd)
            ->save();
    }

    /**
     * @param int $idSalesOrder
     *
     * @return \Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPay|null
     */
    protected function getPaymentEntityByIdSalesOrder(int $idSalesOrder): ?SpyPaymentAfterPay
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
     * @return \Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayOrderItem|null
     */
    protected function getPaymentOrderItemEntityByIdSalesOrderItemAndIdPayment(int $idSalesOrderItem, int $idPayment): ?SpyPaymentAfterPayOrderItem
    {
        $afterPayPaymentOrderItemEntity = $this->afterPayQueryContainer
            ->queryPaymentOrderItemByIdSalesOrderAndIdPayment($idSalesOrderItem, $idPayment)
            ->findOne();

        return $afterPayPaymentOrderItemEntity;
    }
}

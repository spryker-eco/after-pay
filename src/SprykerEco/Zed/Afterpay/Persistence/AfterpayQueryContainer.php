<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Persistence;

use Spryker\Zed\Kernel\Persistence\AbstractQueryContainer;
use SprykerEco\Shared\Afterpay\AfterpayConstants;

/**
 * @method \SprykerEco\Zed\Afterpay\Persistence\AfterpayPersistenceFactory getFactory()
 */
class AfterpayQueryContainer extends AbstractQueryContainer implements AfterpayQueryContainerInterface
{
    const TRANSACTION_TYPE_AUTHORIZE = AfterpayConstants::TRANSACTION_TYPE_AUTHORIZE;
    const TRANSACTION_TYPE_CAPTURE = AfterpayConstants::TRANSACTION_TYPE_CAPTURE;
    const TRANSACTION_TYPE_CANCEL = AfterpayConstants::TRANSACTION_TYPE_CANCEL;
    const TRANSACTION_TYPE_REFUND = AfterpayConstants::TRANSACTION_TYPE_REFUND;

    /**
     * @api
     *
     * @param string $orderReference
     *
     * @return \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayTransactionLogQuery
     */
    public function queryAuthorizeTransactionLog($orderReference)
    {
        return $this->getFactory()
            ->createPaymentAfterpayTransactionLogQuery()
            ->filterByOrderReference($orderReference)
            ->filterByTransactionType(static::TRANSACTION_TYPE_AUTHORIZE);
    }

    /**
     * @api
     *
     * @param string $orderReference
     *
     * @return \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayTransactionLogQuery
     */
    public function queryCaptureTransactionLog($orderReference)
    {
        return $this->getFactory()
            ->createPaymentAfterpayTransactionLogQuery()
            ->filterByOrderReference($orderReference)
            ->filterByTransactionType(static::TRANSACTION_TYPE_CAPTURE);
    }

    /**
     * @api
     *
     * @param string $orderReference
     *
     * @return \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayTransactionLogQuery
     */
    public function queryCancelTransactionLog($orderReference)
    {
        return $this->getFactory()
            ->createPaymentAfterpayTransactionLogQuery()
            ->filterByOrderReference($orderReference)
            ->filterByTransactionType(static::TRANSACTION_TYPE_CANCEL);
    }

    /**
     * @api
     *
     * @param int $idSalesOrder
     *
     * @return \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayTransactionLogQuery
     */
    public function queryRefundTransactionLog($idSalesOrder)
    {
        return $this->getFactory()
            ->createPaymentAfterpayTransactionLogQuery()
            ->filterByFkSalesOrder($idSalesOrder)
            ->filterByTransactionType(static::TRANSACTION_TYPE_REFUND);
    }

    /**
     * @api
     *
     * @param int $idSalesOrder
     *
     * @return \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayQuery
     */
    public function queryPaymentByIdSalesOrder($idSalesOrder)
    {
        return $this
            ->getFactory()
            ->createPaymentAfterpayQuery()
            ->filterByFkSalesOrder($idSalesOrder);
    }

    /**
     * @api
     *
     * @param int $idSalesOrderItem
     * @param int $idPayment
     *
     * @return \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayOrderItemQuery
     */
    public function queryPaymentOrderItemByIdSalesOrderAndIdPayment($idSalesOrderItem, $idPayment)
    {
        return $this
            ->getFactory()
            ->createPaymentAfterpayOrderItemQuery()
            ->filterByFkSalesOrderItem($idSalesOrderItem)
            ->filterByFkPaymentAfterpay($idPayment);
    }

    /**
     * @api
     *
     * @param string $orderReference
     * @param string $transactionType
     *
     * @return \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayTransactionLogQuery
     */
    public function queryTransactionByIdSalesOrderAndType($orderReference, $transactionType)
    {
        return $this->getFactory()
            ->createPaymentAfterpayTransactionLogQuery()
            ->filterByOrderReference($orderReference)
            ->filterByTransactionType($transactionType);
    }

    /**
     * @param string $orderReference
     *
     * @return \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayAuthorizationQuery
     */
    public function queryAuthorizationByOrderReference($orderReference)
    {
        return $this->getFactory()
            ->createPaymentAfterpayAuthorizationQuery()
            ->filterByOrderReference($orderReference);
    }
}

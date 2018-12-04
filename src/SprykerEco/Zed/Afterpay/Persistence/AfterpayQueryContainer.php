<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Persistence;

use Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayAuthorizationQuery;
use Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayOrderItemQuery;
use Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayQuery;
use Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayTransactionLogQuery;
use Spryker\Zed\Kernel\Persistence\AbstractQueryContainer;
use SprykerEco\Shared\Afterpay\AfterpayConfig;
use SprykerEco\Shared\Afterpay\AfterpayConstants;

/**
 * @method \SprykerEco\Zed\Afterpay\Persistence\AfterpayPersistenceFactory getFactory()
 */
class AfterpayQueryContainer extends AbstractQueryContainer implements AfterpayQueryContainerInterface
{
    public const TRANSACTION_TYPE_AUTHORIZE = AfterpayConfig::TRANSACTION_TYPE_AUTHORIZE;
    public const TRANSACTION_TYPE_CAPTURE = AfterpayConfig::TRANSACTION_TYPE_CAPTURE;
    public const TRANSACTION_TYPE_CANCEL = AfterpayConfig::TRANSACTION_TYPE_CANCEL;
    public const TRANSACTION_TYPE_REFUND = AfterpayConfig::TRANSACTION_TYPE_REFUND;

    /**
     * @api
     *
     * @param string $orderReference
     *
     * @return \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayTransactionLogQuery
     */
    public function queryAuthorizeTransactionLog(string $orderReference): SpyPaymentAfterpayTransactionLogQuery
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
    public function queryCaptureTransactionLog(string $orderReference): SpyPaymentAfterpayTransactionLogQuery
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
    public function queryCancelTransactionLog(string $orderReference): SpyPaymentAfterpayTransactionLogQuery
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
    public function queryRefundTransactionLog(int $idSalesOrder): SpyPaymentAfterpayTransactionLogQuery
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
    public function queryPaymentByIdSalesOrder(int $idSalesOrder): SpyPaymentAfterpayQuery
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
    public function queryPaymentOrderItemByIdSalesOrderAndIdPayment(int $idSalesOrderItem, int $idPayment): SpyPaymentAfterpayOrderItemQuery
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
    public function queryTransactionByIdSalesOrderAndType(string $orderReference, string $transactionType): SpyPaymentAfterpayTransactionLogQuery
    {
        return $this->getFactory()
            ->createPaymentAfterpayTransactionLogQuery()
            ->filterByOrderReference($orderReference)
            ->filterByTransactionType($transactionType);
    }

    /**
     * @api
     *
     * @param string $orderReference
     *
     * @return \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayAuthorizationQuery
     */
    public function queryAuthorizationByOrderReference(string $orderReference): SpyPaymentAfterpayAuthorizationQuery
    {
        return $this->getFactory()
            ->createPaymentAfterpayAuthorizationQuery()
            ->filterByOrderReference($orderReference);
    }
}

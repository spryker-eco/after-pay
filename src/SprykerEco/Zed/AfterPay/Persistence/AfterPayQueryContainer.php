<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Persistence;

use Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayAuthorizationQuery;
use Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayOrderItemQuery;
use Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayQuery;
use Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayTransactionLogQuery;
use Spryker\Zed\Kernel\Persistence\AbstractQueryContainer;
use SprykerEco\Shared\AfterPay\AfterPayConfig;
use SprykerEco\Shared\AfterPay\AfterPayConstants;

/**
 * @method \SprykerEco\Zed\AfterPay\Persistence\AfterPayPersistenceFactory getFactory()
 */
class AfterPayQueryContainer extends AbstractQueryContainer implements AfterPayQueryContainerInterface
{
    public const TRANSACTION_TYPE_AUTHORIZE = AfterPayConfig::TRANSACTION_TYPE_AUTHORIZE;
    public const TRANSACTION_TYPE_CAPTURE = AfterPayConfig::TRANSACTION_TYPE_CAPTURE;
    public const TRANSACTION_TYPE_CANCEL = AfterPayConfig::TRANSACTION_TYPE_CANCEL;
    public const TRANSACTION_TYPE_REFUND = AfterPayConfig::TRANSACTION_TYPE_REFUND;

    /**
     * @api
     *
     * @param string $orderReference
     *
     * @return \Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayTransactionLogQuery
     */
    public function queryAuthorizeTransactionLog(string $orderReference): SpyPaymentAfterPayTransactionLogQuery
    {
        return $this->getFactory()
            ->createPaymentAfterPayTransactionLogQuery()
            ->filterByOrderReference($orderReference)
            ->filterByTransactionType(static::TRANSACTION_TYPE_AUTHORIZE);
    }

    /**
     * @api
     *
     * @param string $orderReference
     *
     * @return \Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayTransactionLogQuery
     */
    public function queryCaptureTransactionLog(string $orderReference): SpyPaymentAfterPayTransactionLogQuery
    {
        return $this->getFactory()
            ->createPaymentAfterPayTransactionLogQuery()
            ->filterByOrderReference($orderReference)
            ->filterByTransactionType(static::TRANSACTION_TYPE_CAPTURE);
    }

    /**
     * @api
     *
     * @param string $orderReference
     *
     * @return \Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayTransactionLogQuery
     */
    public function queryCancelTransactionLog(string $orderReference): SpyPaymentAfterPayTransactionLogQuery
    {
        return $this->getFactory()
            ->createPaymentAfterPayTransactionLogQuery()
            ->filterByOrderReference($orderReference)
            ->filterByTransactionType(static::TRANSACTION_TYPE_CANCEL);
    }

    /**
     * @api
     *
     * @param int $idSalesOrder
     *
     * @return \Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayTransactionLogQuery
     */
    public function queryRefundTransactionLog(int $idSalesOrder): SpyPaymentAfterPayTransactionLogQuery
    {
        return $this->getFactory()
            ->createPaymentAfterPayTransactionLogQuery()
            ->filterByFkSalesOrder($idSalesOrder)
            ->filterByTransactionType(static::TRANSACTION_TYPE_REFUND);
    }

    /**
     * @api
     *
     * @param int $idSalesOrder
     *
     * @return \Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayQuery
     */
    public function queryPaymentByIdSalesOrder(int $idSalesOrder): SpyPaymentAfterPayQuery
    {
        return $this
            ->getFactory()
            ->createPaymentAfterPayQuery()
            ->filterByFkSalesOrder($idSalesOrder);
    }

    /**
     * @api
     *
     * @param int $idSalesOrderItem
     * @param int $idPayment
     *
     * @return \Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayOrderItemQuery
     */
    public function queryPaymentOrderItemByIdSalesOrderAndIdPayment(int $idSalesOrderItem, int $idPayment): SpyPaymentAfterPayOrderItemQuery
    {
        return $this
            ->getFactory()
            ->createPaymentAfterPayOrderItemQuery()
            ->filterByFkSalesOrderItem($idSalesOrderItem)
            ->filterByFkPaymentAfterPay($idPayment);
    }

    /**
     * @api
     *
     * @param string $orderReference
     * @param string $transactionType
     *
     * @return \Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayTransactionLogQuery
     */
    public function queryTransactionByIdSalesOrderAndType(string $orderReference, string $transactionType): SpyPaymentAfterPayTransactionLogQuery
    {
        return $this->getFactory()
            ->createPaymentAfterPayTransactionLogQuery()
            ->filterByOrderReference($orderReference)
            ->filterByTransactionType($transactionType);
    }

    /**
     * @api
     *
     * @param string $orderReference
     *
     * @return \Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayAuthorizationQuery
     */
    public function queryAuthorizationByOrderReference(string $orderReference): SpyPaymentAfterPayAuthorizationQuery
    {
        return $this->getFactory()
            ->createPaymentAfterPayAuthorizationQuery()
            ->filterByOrderReference($orderReference);
    }
}

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
use Orm\Zed\Sales\Persistence\SpySalesOrderQuery;
use Spryker\Zed\Kernel\Persistence\QueryContainer\QueryContainerInterface;

interface AfterPayQueryContainerInterface extends QueryContainerInterface
{
    /**
     * Specification:
     * - Returns `SpyPaymentAfterPayQuery` query for a given `idSalesOrder`.
     *
     * @api
     *
     * @param int $idSalesOrder
     *
     * @return \Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayQuery
     */
    public function queryPaymentByIdSalesOrder(int $idSalesOrder): SpyPaymentAfterPayQuery;

    /**
     * Specification:
     * - Returns `SpyPaymentAfterPayOrderItemQuery` query for a given `idSalesOrderItem` and `idPayment`.
     *
     * @api
     *
     * @param int $idSalesOrderItem
     * @param int $idPayment
     *
     * @return \Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayOrderItemQuery
     */
    public function queryPaymentOrderItemByIdSalesOrderAndIdPayment(int $idSalesOrderItem, int $idPayment): SpyPaymentAfterPayOrderItemQuery;

    /**
     * Specification:
     * - Returns `SpyPaymentAfterPayTransactionLogQuery` query for a given `orderReference` and `transactionType`.
     *
     * @api
     *
     * @param string $orderReference
     * @param string $transactionType
     *
     * @return \Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayTransactionLogQuery
     */
    public function queryTransactionByIdSalesOrderAndType(string $orderReference, string $transactionType): SpyPaymentAfterPayTransactionLogQuery;

    /**
     * Specification:
     * - Returns `SpyPaymentAfterPayAuthorizationQuery` query for a given `orderReference`.
     *
     * @api
     *
     * @param string $orderReference
     *
     * @return \Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayAuthorizationQuery
     */
    public function queryAuthorizationByOrderReference(string $orderReference): SpyPaymentAfterPayAuthorizationQuery;

    /**
     * Specification:
     * - Returns `SpyPaymentAfterPayTransactionLogQuery` query with `authorize` operation for a given `orderReference`.
     *
     * @api
     *
     * @param string $orderReference
     *
     * @return \Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayTransactionLogQuery
     */
    public function queryAuthorizeTransactionLog(string $orderReference): SpyPaymentAfterPayTransactionLogQuery;

    /**
     * Specification:
     * - Returns `SpyPaymentAfterPayTransactionLogQuery` query with `capture` operation for a given `orderReference`.
     *
     * @api
     *
     * @param string $orderReference
     *
     * @return \Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayTransactionLogQuery
     */
    public function queryCaptureTransactionLog(string $orderReference): SpyPaymentAfterPayTransactionLogQuery;

    /**
     * Specification:
     * - Returns `SpyPaymentAfterPayTransactionLogQuery` query with `cancel` operation for a given `orderReference`.
     *
     * @api
     *
     * @param string $orderReference
     *
     * @return \Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayTransactionLogQuery
     */
    public function queryCancelTransactionLog(string $orderReference): SpyPaymentAfterPayTransactionLogQuery;

    /**
     * Specification:
     * - Returns `SpySalesOrderQuery` query for a given `idSalesOrder`.
     *
     * @api
     *
     * @param int $idSalesOrder
     *
     * @return \Orm\Zed\Sales\Persistence\SpySalesOrderQuery
     */
    public function querySalesOrder(int $idSalesOrder): SpySalesOrderQuery;

    /**
     * Specification:
     * - Returns `SpyPaymentAfterPayTransactionLogQuery` query with `refund` operation for a given `orderReference`.
     *
     * @api
     *
     * @param string $orderReference
     *
     * @return \Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayTransactionLogQuery
     */
    public function queryRefundTransactionLog(string $orderReference): SpyPaymentAfterPayTransactionLogQuery;
}

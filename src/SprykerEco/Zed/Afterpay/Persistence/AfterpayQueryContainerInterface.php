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
use Spryker\Zed\Kernel\Persistence\QueryContainer\QueryContainerInterface;

interface AfterpayQueryContainerInterface extends QueryContainerInterface
{
    /**
     * @api
     *
     * @param int $idSalesOrder
     *
     * @return \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayQuery
     */
    public function queryPaymentByIdSalesOrder(int $idSalesOrder): SpyPaymentAfterpayQuery;

    /**
     * @api
     *
     * @param int $idSalesOrderItem
     * @param int $idPayment
     *
     * @return \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayOrderItemQuery
     */
    public function queryPaymentOrderItemByIdSalesOrderAndIdPayment(int $idSalesOrderItem, int $idPayment): SpyPaymentAfterpayOrderItemQuery;

    /**
     * @api
     *
     * @param string $orderReference
     * @param string $transactionType
     *
     * @return \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayTransactionLogQuery
     */
    public function queryTransactionByIdSalesOrderAndType(string $orderReference, string $transactionType): SpyPaymentAfterpayTransactionLogQuery;

    /**
     * @api
     *
     * @param string $orderReference
     *
     * @return \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayAuthorizationQuery
     */
    public function queryAuthorizationByOrderReference(string $orderReference): SpyPaymentAfterpayAuthorizationQuery;

    /**
     * @api
     *
     * @param string $orderReference
     *
     * @return \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayTransactionLogQuery
     */
    public function queryAuthorizeTransactionLog(string $orderReference): SpyPaymentAfterpayTransactionLogQuery;

    /**
     * @api
     *
     * @param string $orderReference
     *
     * @return \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayTransactionLogQuery
     */
    public function queryCaptureTransactionLog(string $orderReference): SpyPaymentAfterpayTransactionLogQuery;

    /**
     * @api
     *
     * @param string $orderReference
     *
     * @return \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayTransactionLogQuery
     */
    public function queryCancelTransactionLog(string $orderReference): SpyPaymentAfterpayTransactionLogQuery;
}

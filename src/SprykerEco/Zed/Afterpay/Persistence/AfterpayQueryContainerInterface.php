<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Persistence;

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
    public function queryPaymentByIdSalesOrder($idSalesOrder);

    /**
     * @api
     *
     * @param int $idSalesOrderItem
     * @param int $idPayment
     *
     * @return \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayOrderItemQuery
     */
    public function queryPaymentOrderItemByIdSalesOrderAndIdPayment($idSalesOrderItem, $idPayment);

    /**
     * @api
     *
     * @param string $orderReference
     * @param string $transactionType
     *
     * @return \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayTransactionLogQuery
     */
    public function queryTransactionByIdSalesOrderAndType($orderReference, $transactionType);

    /**
     * @param string $orderReference
     *
     * @return \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayAuthorizationQuery
     */
    public function queryAuthorizationByOrderReference($orderReference);

    /**
     * @api
     *
     * @param string $orderReference
     *
     * @return \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayTransactionLogQuery
     */
    public function queryAuthorizeTransactionLog($orderReference);

    /**
     * @api
     *
     * @param string $orderReference
     *
     * @return \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayTransactionLogQuery
     */
    public function queryCaptureTransactionLog($orderReference);

    /**
     * @api
     *
     * @param string $orderReference
     *
     * @return \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayTransactionLogQuery
     */
    public function queryCancelTransactionLog($orderReference);

    /**
     * @api
     *
     * @param int $idSalesOrder
     *
     * @return \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayTransactionLogQuery
     */
    public function queryAuthorizeTransactionLog($idSalesOrder);
}

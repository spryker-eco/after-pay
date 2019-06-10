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
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;

/**
 * @method \SprykerEco\Zed\AfterPay\AfterPayConfig getConfig()
 * @method \SprykerEco\Zed\AfterPay\Persistence\AfterPayQueryContainerInterface getQueryContainer()
 * @method \SprykerEco\Zed\AfterPay\Persistence\AfterPayEntityManagerInterface getEntityManager()
 */
class AfterPayPersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return \Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayQuery
     */
    public function createPaymentAfterPayQuery(): SpyPaymentAfterPayQuery
    {
        return SpyPaymentAfterPayQuery::create();
    }

    /**
     * @return \Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayOrderItemQuery
     */
    public function createPaymentAfterPayOrderItemQuery(): SpyPaymentAfterPayOrderItemQuery
    {
        return SpyPaymentAfterPayOrderItemQuery::create();
    }

    /**
     * @return \Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayTransactionLogQuery
     */
    public function createPaymentAfterPayTransactionLogQuery(): SpyPaymentAfterPayTransactionLogQuery
    {
        return SpyPaymentAfterPayTransactionLogQuery::create();
    }

    /**
     * @return \Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayAuthorizationQuery
     */
    public function createPaymentAfterPayAuthorizationQuery(): SpyPaymentAfterPayAuthorizationQuery
    {
        return SpyPaymentAfterPayAuthorizationQuery::create();
    }

    /**
     * @return \Orm\Zed\Sales\Persistence\SpySalesOrderQuery
     */
    public function createSalesOrderQuery(): SpySalesOrderQuery
    {
        return SpySalesOrderQuery::create();
    }
}

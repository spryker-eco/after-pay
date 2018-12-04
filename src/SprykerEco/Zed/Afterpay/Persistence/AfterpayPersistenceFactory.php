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
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;

/**
 * @method \SprykerEco\Zed\Afterpay\AfterpayConfig getConfig()
 * @method \SprykerEco\Zed\Afterpay\Persistence\AfterpayQueryContainerInterface getQueryContainer()
 */
class AfterpayPersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayQuery
     */
    public function createPaymentAfterpayQuery(): SpyPaymentAfterpayQuery
    {
        return SpyPaymentAfterpayQuery::create();
    }

    /**
     * @return \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayOrderItemQuery
     */
    public function createPaymentAfterpayOrderItemQuery(): SpyPaymentAfterpayOrderItemQuery
    {
        return SpyPaymentAfterpayOrderItemQuery::create();
    }

    /**
     * @return \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayTransactionLogQuery
     */
    public function createPaymentAfterpayTransactionLogQuery(): SpyPaymentAfterpayTransactionLogQuery
    {
        return SpyPaymentAfterpayTransactionLogQuery::create();
    }

    /**
     * @return \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayAuthorizationQuery
     */
    public function createPaymentAfterpayAuthorizationQuery(): SpyPaymentAfterpayAuthorizationQuery
    {
        return SpyPaymentAfterpayAuthorizationQuery::create();
    }
}

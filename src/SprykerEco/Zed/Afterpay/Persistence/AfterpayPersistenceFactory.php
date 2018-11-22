<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Persistence;

use Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayAuthorizationQuery;
use Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayOrderItemQuery;
use Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayQuery;
use Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayTransactionLogQuery;
use Spryker\Zed\Kernel\Persistence\AbstractPersistenceFactory;

/**
 * @method \SprykerEco\Zed\Afterpay\AfterpayConfig getConfig()
 * @method \SprykerEco\Zed\Afterpay\Persistence\AfterpayQueryContainer getQueryContainer()
 */
class AfterpayPersistenceFactory extends AbstractPersistenceFactory
{
    /**
     * @return \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayQuery
     */
    public function createPaymentAfterpayQuery()
    {
        return SpyPaymentAfterpayQuery::create();
    }

    /**
     * @return \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayOrderItemQuery
     */
    public function createPaymentAfterpayOrderItemQuery()
    {
        return SpyPaymentAfterpayOrderItemQuery::create();
    }

    /**
     * @return \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayTransactionLogQuery
     */
    public function createPaymentAfterpayTransactionLogQuery()
    {
        return SpyPaymentAfterpayTransactionLogQuery::create();
    }

    /**
     * @return \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayAuthorizationQuery
     */
    public function createPaymentAfterpayAuthorizationQuery()
    {
        return SpyPaymentAfterpayAuthorizationQuery::create();
    }
}

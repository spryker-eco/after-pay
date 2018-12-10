<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Communication\Plugin\Oms\Condition;

use Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayTransactionLog;
use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Oms\Dependency\Plugin\Condition\ConditionInterface;
use SprykerEco\Shared\AfterPay\AfterPayConfig;

/**
 * @method \SprykerEco\Zed\AfterPay\Business\AfterPayFacadeInterface getFacade()
 * @method \SprykerEco\Zed\AfterPay\Persistence\AfterPayQueryContainerInterface getQueryContainer()
 * @method \SprykerEco\Zed\AfterPay\Communication\AfterPayCommunicationFactory getFactory()
 * @method \SprykerEco\Zed\AfterPay\AfterPayConfig getConfig()
 */
class IsAuthorizationCompletedPlugin extends AbstractPlugin implements ConditionInterface
{
    public const AUTHORIZE_TRANSACTION_ACCEPTED = AfterPayConfig::API_TRANSACTION_OUTCOME_ACCEPTED;

    /**
     * @api
     *
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderItem $orderItem
     *
     * @return bool
     */
    public function check(SpySalesOrderItem $orderItem): bool
    {
        return $this->isAuthorizationTransactionSuccessful($orderItem->getFkSalesOrder());
    }

    /**
     * @param int $idSalesOrder
     *
     * @return bool
     */
    protected function isAuthorizationTransactionSuccessful(int $idSalesOrder): bool
    {
        $authorizeTransactionLog = $this->getAuthorizeTransactionLogEntry($idSalesOrder);
        if ($authorizeTransactionLog === null) {
            return false;
        }

        return $this->isTransactionSuccessful($authorizeTransactionLog);
    }

    /**
     * @param int $idSalesOrder
     *
     * @return \Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayTransactionLog|null
     */
    protected function getAuthorizeTransactionLogEntry(int $idSalesOrder): ?SpyPaymentAfterPayTransactionLog
    {
        $transactionLogQuery = $this->getQueryContainer()->queryAuthorizeTransactionLog($idSalesOrder);

        return $transactionLogQuery->findOne();
    }

    /**
     * @param \Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayTransactionLog $authorizeTransactionLog
     *
     * @return bool
     */
    protected function isTransactionSuccessful(SpyPaymentAfterPayTransactionLog $authorizeTransactionLog): bool
    {
        return $authorizeTransactionLog->getOutcome() === static::AUTHORIZE_TRANSACTION_ACCEPTED;
    }
}

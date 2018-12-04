<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Communication\Plugin\Checkout\Oms\Condition;

use Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayTransactionLog;
use Orm\Zed\Sales\Persistence\SpySalesOrderItem;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Oms\Dependency\Plugin\Condition\ConditionInterface;
use SprykerEco\Shared\Afterpay\AfterpayConfig;
use SprykerEco\Shared\Afterpay\AfterpayConstants;

/**
 * @method \SprykerEco\Zed\Afterpay\Business\AfterpayFacadeInterface getFacade()
 * @method \SprykerEco\Zed\Afterpay\Persistence\AfterpayQueryContainer getQueryContainer()
 * @method \SprykerEco\Zed\Afterpay\Communication\AfterpayCommunicationFactory getFactory()
 * @method \SprykerEco\Zed\Afterpay\AfterpayConfig getConfig()
 */
class IsAuthorizationCompletedPlugin extends AbstractPlugin implements ConditionInterface
{
    public const AUTHORIZE_TRANSACTION_ACCEPTED = AfterpayConfig::API_TRANSACTION_OUTCOME_ACCEPTED;

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
     * @return \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayTransactionLog|null
     */
    protected function getAuthorizeTransactionLogEntry(int $idSalesOrder): ?SpyPaymentAfterpayTransactionLog
    {
        $transactionLogQuery = $this->getQueryContainer()->queryAuthorizeTransactionLog($idSalesOrder);

        return $transactionLogQuery->findOne();
    }

    /**
     * @param \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayTransactionLog $authorizeTransactionLog
     *
     * @return bool
     */
    protected function isTransactionSuccessful(SpyPaymentAfterpayTransactionLog $authorizeTransactionLog): bool
    {
        return $authorizeTransactionLog->getOutcome() === static::AUTHORIZE_TRANSACTION_ACCEPTED;
    }
}

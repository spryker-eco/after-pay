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
class IsCancellationCompletedPlugin extends AbstractPlugin implements ConditionInterface
{
    public const CANCEL_TRANSACTION_ACCEPTED = AfterPayConfig::API_TRANSACTION_OUTCOME_ACCEPTED;

    /**
     * {@inheritDoc}
     * - Checks if the `cancel` operation was successfully accepted for the order.
     *
     * @api
     *
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderItem $orderItem
     *
     * @return bool
     */
    public function check(SpySalesOrderItem $orderItem): bool
    {
        return $this->isCancelTransactionSuccessful($orderItem->getFkSalesOrder());
    }

    /**
     * @param int $idSalesOrder
     *
     * @return bool
     */
    protected function isCancelTransactionSuccessful(int $idSalesOrder): bool
    {
        $order = $this->getQueryContainer()->querySalesOrder($idSalesOrder)->findOne();
        if ($order === null) {
            return false;
        }

        $fullCancelTransactionLog = $this->getCancelTransactionLogEntry($order->getOrderReference());
        if ($fullCancelTransactionLog === null) {
            return false;
        }

        return $this->isTransactionSuccessful($fullCancelTransactionLog);
    }

    /**
     * @param string $orderReference
     *
     * @return \Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayTransactionLog|null
     */
    protected function getCancelTransactionLogEntry(string $orderReference): ?SpyPaymentAfterPayTransactionLog
    {
        $transactionLogQuery = $this->getQueryContainer()->queryCancelTransactionLog($orderReference);

        return $transactionLogQuery->findOne();
    }

    /**
     * @param \Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayTransactionLog $fullCancelTransactionLog
     *
     * @return bool
     */
    protected function isTransactionSuccessful(SpyPaymentAfterPayTransactionLog $fullCancelTransactionLog): bool
    {
        return $fullCancelTransactionLog->getOutcome() === static::CANCEL_TRANSACTION_ACCEPTED;
    }
}

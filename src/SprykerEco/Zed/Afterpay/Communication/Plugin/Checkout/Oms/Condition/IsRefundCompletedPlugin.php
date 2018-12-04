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
class IsRefundCompletedPlugin extends AbstractPlugin implements ConditionInterface
{
    public const REFUND_TRANSACTION_ACCEPTED = AfterpayConfig::API_TRANSACTION_OUTCOME_ACCEPTED;

    /**
     * @api
     *
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderItem $orderItem
     *
     * @return bool
     */
    public function check(SpySalesOrderItem $orderItem): bool
    {
        return $this->isRefundTransactionSuccessful($orderItem->getFkSalesOrder());
    }

    /**
     * @param int $idSalesOrder
     *
     * @return bool
     */
    protected function isRefundTransactionSuccessful(int $idSalesOrder): bool
    {
        $captureTransactionLog = $this->getFullRefundTransactionLogEntry($idSalesOrder);
        if ($captureTransactionLog === null) {
            return false;
        }

        return $this->isTransactionSuccessful($captureTransactionLog);
    }

    /**
     * @param int $idSalesOrder
     *
     * @return \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayTransactionLog|null
     */
    protected function getFullRefundTransactionLogEntry(int $idSalesOrder): ?SpyPaymentAfterpayTransactionLog
    {
        $transactionLogQuery = $this->getQueryContainer()->queryRefundTransactionLog($idSalesOrder);

        return $transactionLogQuery->findOne();
    }

    /**
     * @param \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayTransactionLog $refundTransactionLog
     *
     * @return bool
     */
    protected function isTransactionSuccessful(SpyPaymentAfterpayTransactionLog $refundTransactionLog): bool
    {
        return $refundTransactionLog->getOutcome() === static::REFUND_TRANSACTION_ACCEPTED;
    }
}

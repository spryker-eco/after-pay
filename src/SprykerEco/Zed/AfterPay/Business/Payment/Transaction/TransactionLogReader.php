<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Payment\Transaction;

use Generated\Shared\Transfer\AfterPayTransactionLogTransfer;
use Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayTransactionLog;
use SprykerEco\Shared\AfterPay\AfterPayConfig;
use SprykerEco\Zed\AfterPay\Persistence\AfterPayQueryContainerInterface;

class TransactionLogReader implements TransactionLogReaderInterface
{
    public const TRANSACTION_TYPE_AUTHORIZE = AfterPayConfig::TRANSACTION_TYPE_AUTHORIZE;

    /**
     * @var \SprykerEco\Zed\AfterPay\Persistence\AfterPayQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @param \SprykerEco\Zed\AfterPay\Persistence\AfterPayQueryContainerInterface $queryContainer
     */
    public function __construct(AfterPayQueryContainerInterface $queryContainer)
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @param string $orderReference
     *
     * @return \Generated\Shared\Transfer\AfterPayTransactionLogTransfer|null
     */
    public function findOrderAuthorizeTransactionLogByIdSalesOrder(string $orderReference): ?AfterPayTransactionLogTransfer
    {
        $spyTransactionLog = $this->findOrderAuthorizeTransactionEntity($orderReference);

        if ($spyTransactionLog === null) {
            return null;
        }

        return $this->buildTransactionTransfer($spyTransactionLog);
    }

    /**
     * @param string $orderReference
     *
     * @return \Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayTransactionLog|null
     */
    protected function findOrderAuthorizeTransactionEntity(string $orderReference): ?SpyPaymentAfterPayTransactionLog
    {
        $transactionLogEntity = $this->queryContainer
            ->queryTransactionByIdSalesOrderAndType($orderReference, static::TRANSACTION_TYPE_AUTHORIZE)
            ->findOne();

        return $transactionLogEntity;
    }

    /**
     * @param \Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayTransactionLog $transactionLogEntry
     *
     * @return \Generated\Shared\Transfer\AfterPayTransactionLogTransfer
     */
    protected function buildTransactionTransfer(SpyPaymentAfterPayTransactionLog $transactionLogEntry): AfterPayTransactionLogTransfer
    {
        return (new AfterPayTransactionLogTransfer())
            ->fromArray($transactionLogEntry->toArray(), true);
    }
}

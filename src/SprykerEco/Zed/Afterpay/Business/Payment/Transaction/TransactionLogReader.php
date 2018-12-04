<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment\Transaction;

use Generated\Shared\Transfer\AfterpayTransactionLogTransfer;
use Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayTransactionLog;
use SprykerEco\Shared\Afterpay\AfterpayConfig;
use SprykerEco\Shared\Afterpay\AfterpayConstants;
use SprykerEco\Zed\Afterpay\Persistence\AfterpayQueryContainerInterface;

class TransactionLogReader implements TransactionLogReaderInterface
{
    public const TRANSACTION_TYPE_AUTHORIZE = AfterpayConfig::TRANSACTION_TYPE_AUTHORIZE;

    /**
     * @var \SprykerEco\Zed\Afterpay\Persistence\AfterpayQueryContainerInterface
     */
    protected $queryContainer;

    /**
     * @param \SprykerEco\Zed\Afterpay\Persistence\AfterpayQueryContainerInterface $queryContainer
     */
    public function __construct(AfterpayQueryContainerInterface $queryContainer)
    {
        $this->queryContainer = $queryContainer;
    }

    /**
     * @param int $idSalesOrder
     *
     * @return \Generated\Shared\Transfer\AfterpayTransactionLogTransfer|null
     */
    public function findOrderAuthorizeTransactionLogByIdSalesOrder(int $idSalesOrder): ?AfterpayTransactionLogTransfer
    {
        $spyTransactionLog = $this->findOrderAuthorizeTransactionEntity($idSalesOrder);

        if ($spyTransactionLog === null) {
            return null;
        }

        return $this->buildTransactionTransfer($spyTransactionLog);
    }

    /**
     * @param int $idSalesOrder
     *
     * @return \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayTransactionLog|null
     */
    protected function findOrderAuthorizeTransactionEntity(int $idSalesOrder): ?SpyPaymentAfterpayTransactionLog
    {
        $transactionLogEntity = $this
            ->queryContainer
            ->queryTransactionByIdSalesOrderAndType(
                $idSalesOrder,
                static::TRANSACTION_TYPE_AUTHORIZE
            )
            ->findOne();

        return $transactionLogEntity;
    }

    /**
     * @param \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayTransactionLog $transactionLogEntry
     *
     * @return \Generated\Shared\Transfer\AfterpayTransactionLogTransfer
     */
    protected function buildTransactionTransfer(SpyPaymentAfterpayTransactionLog $transactionLogEntry): AfterpayTransactionLogTransfer
    {
        $transactionLogTransfer = new AfterpayTransactionLogTransfer();
        $transactionLogTransfer->fromArray($transactionLogEntry->toArray(), true);

        return $transactionLogTransfer;
    }
}

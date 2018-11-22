<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment\Transaction;

use Generated\Shared\Transfer\AfterpayTransactionLogTransfer;
use Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayTransactionLog;
use SprykerEco\Shared\Afterpay\AfterpayConstants;
use SprykerEco\Zed\Afterpay\Persistence\AfterpayQueryContainerInterface;

class TransactionLogReader implements TransactionLogReaderInterface
{
    const TRANSACTION_TYPE_AUTHORIZE = AfterpayConstants::TRANSACTION_TYPE_AUTHORIZE;

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
    public function findOrderAuthorizeTransactionLogByIdSalesOrder($idSalesOrder)
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
    protected function findOrderAuthorizeTransactionEntity($idSalesOrder)
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
    protected function buildTransactionTransfer(SpyPaymentAfterpayTransactionLog $transactionLogEntry)
    {
        $transactionLogTransfer = new AfterpayTransactionLogTransfer();
        $transactionLogTransfer->fromArray($transactionLogEntry->toArray(), true);

        return $transactionLogTransfer;
    }
}

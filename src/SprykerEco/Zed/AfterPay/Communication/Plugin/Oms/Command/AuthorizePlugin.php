<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Communication\Plugin\Oms\Command;

use Generated\Shared\Transfer\AfterPayCallTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrder;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject;
use Spryker\Zed\Oms\Dependency\Plugin\Command\CommandByOrderInterface;

/**
 * @method \SprykerEco\Zed\AfterPay\Business\AfterPayFacadeInterface getFacade()
 * @method \SprykerEco\Zed\AfterPay\Communication\AfterPayCommunicationFactory getFactory()
 * @method \SprykerEco\Zed\AfterPay\Persistence\AfterPayQueryContainer getQueryContainer()
 * @method \SprykerEco\Zed\AfterPay\AfterPayConfig getConfig()
 */
class AuthorizePlugin extends AbstractPlugin implements CommandByOrderInterface
{
    /**
     * Specification:
     * - Sends payment `authorize` request to AfterPay gateway.
     * - Saves the transaction result in Quote for future recognition.
     *
     * @api
     *
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrderItem[] $orderItems
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $orderEntity
     * @param \Spryker\Zed\Oms\Business\Util\ReadOnlyArrayObject $data
     *
     * @return array
     */
    public function run(array $orderItems, SpySalesOrder $orderEntity, ReadOnlyArrayObject $data): array
    {
        $afterPayCallTransfer = $this->createAuthorizeCallTransfer($orderEntity);
        $this->getFacade()->authorizePayment($afterPayCallTransfer);

        return [];
    }

    /**
     * @param \Orm\Zed\Sales\Persistence\SpySalesOrder $orderEntity
     *
     * @return \Generated\Shared\Transfer\AfterPayCallTransfer
     */
    protected function createAuthorizeCallTransfer(SpySalesOrder $orderEntity): AfterPayCallTransfer
    {
        $orderTransfer = $this->getOrderWithPaymentTransfer($orderEntity->getIdSalesOrder());

        return $this->getFactory()
            ->createOrderToCallConverter()
            ->convert($orderTransfer);
    }

    /**
     * @param int $idSalesOrder
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    protected function getOrderWithPaymentTransfer(int $idSalesOrder): OrderTransfer
    {
        $orderTransfer = $this->getFactory()
            ->getSalesFacade()
            ->getOrderByIdSalesOrder($idSalesOrder);

        $orderTransfer = $this->hydrateAfterPayPayment($orderTransfer);

        return $orderTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    protected function hydrateAfterPayPayment(OrderTransfer $orderTransfer): OrderTransfer
    {
        $paymentTransfer = $this->getFacade()->getPaymentByIdSalesOrder($orderTransfer->getIdSalesOrder());
        $orderTransfer->setAfterPayPayment($paymentTransfer);

        return $orderTransfer;
    }
}

<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Persistence;

use Spryker\Zed\Kernel\Persistence\AbstractEntityManager;

/**
 * @method \SprykerEco\Zed\AfterPay\Persistence\AfterPayPersistenceFactory getFactory()
 */
class AfterPayEntityManager extends AbstractEntityManager implements AfterPayEntityManagerInterface
{
    /**
     * @param string $customerNumber
     * @param int $idSalesOrder
     *
     * @return void
     */
    public function addCustomerNumberToAfterPayPaymentByIdSalesOrder(string $customerNumber, int $idSalesOrder): void
    {
        $paymentEntity = $this->getFactory()
            ->createPaymentAfterPayQuery()
            ->findOneByFkSalesOrder($idSalesOrder);

        if ($paymentEntity) {
            $paymentEntity->setInfoscoreCustomerNumber($customerNumber);
            $paymentEntity->save();
        }
    }
}

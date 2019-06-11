<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Persistence;

interface AfterPayEntityManagerInterface
{
    /**
     * @param string $customerNumber
     * @param int $idSalesOrder
     *
     * @return void
     */
    public function addCustomerNumberToAfterPayPaymentByIdSalesOrder(string $customerNumber, int $idSalesOrder): void;
}

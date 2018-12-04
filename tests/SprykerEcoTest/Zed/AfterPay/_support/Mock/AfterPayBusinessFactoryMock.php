<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\AfterPay\Mock;

use SprykerEco\Zed\AfterPay\Business\AfterPayBusinessFactory;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\AdapterFactoryInterface;

class AfterPayBusinessFactoryMock extends AfterPayBusinessFactory
{
    /**
     * @return \SprykerEcoTest\Zed\AfterPay\Mock\AdapterFactoryMock
     */
    public function createAdapterFactory(): AdapterFactoryInterface
    {
        return new AdapterFactoryMock();
    }
}

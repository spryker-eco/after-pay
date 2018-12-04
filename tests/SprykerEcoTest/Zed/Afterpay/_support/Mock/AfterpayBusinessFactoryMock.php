<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Afterpay\Mock;

use SprykerEco\Zed\Afterpay\Business\AfterpayBusinessFactory;

class AfterpayBusinessFactoryMock extends AfterpayBusinessFactory
{
    /**
     * @return \SprykerEcoTest\Zed\Afterpay\Mock\AdapterFactoryMock
     */
    protected function createAdapterFactory()
    {
        return new AdapterFactoryMock();
    }
}

<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Afterpay\Mock;

use SprykerEco\Zed\Afterpay\Business\AfterpayFacade;

class AfterpayFacadeMock extends AfterpayFacade
{
    /**
     * @return \SprykerEcoTest\Zed\Afterpay\Mock\AfterpayBusinessFactoryMock
     */
    public function getFactory()
    {
        return new AfterpayBusinessFactoryMock();
    }
}

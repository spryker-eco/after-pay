<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\AfterPay\Mock;

use SprykerEco\Zed\AfterPay\Business\AfterPayFacade;

class AfterPayFacadeMock extends AfterPayFacade
{
    /**
     * @return \SprykerEcoTest\Zed\AfterPay\Mock\AfterPayBusinessFactoryMock
     */
    public function getFactory()
    {
        return new AfterPayBusinessFactoryMock();
    }
}

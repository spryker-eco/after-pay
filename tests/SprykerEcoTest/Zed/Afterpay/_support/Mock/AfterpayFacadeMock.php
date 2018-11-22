<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
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

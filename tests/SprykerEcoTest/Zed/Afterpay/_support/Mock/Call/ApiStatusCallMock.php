<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Afterpay\Mock\Call;

use SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\ApiStatusCall;

class ApiStatusCallMock extends ApiStatusCall
{
    /**
     * @return int
     */
    public function execute()
    {
        return 200;
    }
}

<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\AfterPay\Mock\Call;

use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\ApiVersionCall;

class ApiVersionCallMock extends ApiVersionCall
{
    /**
     * @return string
     */
    public function execute(): string
    {
        return '1.0.0';
    }
}

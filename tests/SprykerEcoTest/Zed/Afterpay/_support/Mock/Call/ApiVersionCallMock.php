<?php

namespace SprykerEcoTest\Zed\Afterpay\Mock\Call;

use SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\ApiVersionCall;

class ApiVersionCallMock extends ApiVersionCall
{
    /**
     * @return string
     */
    public function execute()
    {
        return '1.0.0';
    }
}

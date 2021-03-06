<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall;

interface ApiVersionCallInterface
{
    /**
     * @return string
     */
    public function execute(): string;
}

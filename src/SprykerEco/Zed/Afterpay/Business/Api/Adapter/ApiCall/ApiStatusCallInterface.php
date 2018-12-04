<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall;

interface ApiStatusCallInterface
{
    /**
     * @return int
     */
    public function execute(): int;
}

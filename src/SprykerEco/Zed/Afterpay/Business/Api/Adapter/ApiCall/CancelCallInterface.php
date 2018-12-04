<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall;

use Generated\Shared\Transfer\AfterpayCancelRequestTransfer;
use Generated\Shared\Transfer\AfterpayCancelResponseTransfer;

interface CancelCallInterface
{
    /**
     * @param \Generated\Shared\Transfer\AfterpayCancelRequestTransfer $requestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayCancelResponseTransfer
     */
    public function execute(AfterpayCancelRequestTransfer $requestTransfer): AfterpayCancelResponseTransfer;
}

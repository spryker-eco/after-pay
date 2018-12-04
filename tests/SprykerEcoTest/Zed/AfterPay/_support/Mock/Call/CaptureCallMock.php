<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\AfterPay\Mock\Call;

use Generated\Shared\DataBuilder\AfterPayCaptureResponseBuilder;
use Generated\Shared\Transfer\AfterPayCaptureRequestTransfer;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\CaptureCall;

class CaptureCallMock extends CaptureCall
{
    /**
     * @param \Generated\Shared\Transfer\AfterPayCaptureRequestTransfer $requestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayCaptureResponseTransfer
     */
    public function execute(AfterPayCaptureRequestTransfer $requestTransfer)
    {
        return (new AfterPayCaptureResponseBuilder())
            ->withApiResponse()
            ->build();
    }
}

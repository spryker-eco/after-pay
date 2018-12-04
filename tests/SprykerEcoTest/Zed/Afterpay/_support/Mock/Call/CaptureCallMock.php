<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Afterpay\Mock\Call;

use Generated\Shared\DataBuilder\AfterpayCaptureResponseBuilder;
use Generated\Shared\Transfer\AfterpayCaptureRequestTransfer;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\CaptureCall;

class CaptureCallMock extends CaptureCall
{
    /**
     * @param \Generated\Shared\Transfer\AfterpayCaptureRequestTransfer $requestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayCaptureResponseTransfer
     */
    public function execute(AfterpayCaptureRequestTransfer $requestTransfer)
    {
        return (new AfterpayCaptureResponseBuilder())
            ->withApiResponse()
            ->build();
    }
}

<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Afterpay\Mock\Call;

use Generated\Shared\DataBuilder\AfterpayApiResponseBuilder;
use Generated\Shared\Transfer\AfterpayAuthorizeRequestTransfer;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\AuthorizePaymentCall;

class AuthorizePaymentCallMock extends AuthorizePaymentCall
{
    /**
     * @param \Generated\Shared\Transfer\AfterpayAuthorizeRequestTransfer $requestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayApiResponseTransfer
     */
    public function execute(AfterpayAuthorizeRequestTransfer $requestTransfer)
    {
        return (new AfterpayApiResponseBuilder())->build();
    }
}

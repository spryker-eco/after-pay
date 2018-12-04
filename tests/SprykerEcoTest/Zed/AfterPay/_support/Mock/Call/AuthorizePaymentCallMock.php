<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\AfterPay\Mock\Call;

use Generated\Shared\DataBuilder\AfterPayApiResponseBuilder;
use Generated\Shared\Transfer\AfterPayAuthorizeRequestTransfer;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\AuthorizePaymentCall;

class AuthorizePaymentCallMock extends AuthorizePaymentCall
{
    /**
     * @param \Generated\Shared\Transfer\AfterPayAuthorizeRequestTransfer $requestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayApiResponseTransfer
     */
    public function execute(AfterPayAuthorizeRequestTransfer $requestTransfer)
    {
        return (new AfterPayApiResponseBuilder())->build();
    }
}

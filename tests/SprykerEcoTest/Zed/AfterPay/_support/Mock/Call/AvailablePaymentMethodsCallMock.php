<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\AfterPay\Mock\Call;

use Generated\Shared\DataBuilder\AfterPayAvailablePaymentMethodsResponseBuilder;
use Generated\Shared\Transfer\AfterPayAvailablePaymentMethodsRequestTransfer;
use Generated\Shared\Transfer\AfterPayAvailablePaymentMethodsResponseTransfer;
use SprykerEco\Shared\AfterPay\AfterPayConfig;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\AvailablePaymentMethodsCall;

class AvailablePaymentMethodsCallMock extends AvailablePaymentMethodsCall
{
    /**
     * @param \Generated\Shared\Transfer\AfterPayAvailablePaymentMethodsRequestTransfer $requestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayAvailablePaymentMethodsResponseTransfer
     */
    public function execute(AfterPayAvailablePaymentMethodsRequestTransfer $requestTransfer): AfterPayAvailablePaymentMethodsResponseTransfer
    {
        $response = (new AfterPayAvailablePaymentMethodsResponseBuilder())
            ->build();
        $response->addPaymentMethods(['type' => AfterPayConfig::RISK_CHECK_METHOD_INVOICE]);

        return $response;
    }
}

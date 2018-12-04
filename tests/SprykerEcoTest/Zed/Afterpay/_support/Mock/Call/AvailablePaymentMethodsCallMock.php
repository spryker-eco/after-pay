<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\Afterpay\Mock\Call;

use Generated\Shared\DataBuilder\AfterpayAvailablePaymentMethodsResponseBuilder;
use Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsRequestTransfer;
use SprykerEco\Shared\Afterpay\AfterpayConfig;
use SprykerEco\Shared\Afterpay\AfterpayConstants;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\AvailablePaymentMethodsCall;

class AvailablePaymentMethodsCallMock extends AvailablePaymentMethodsCall
{
    /**
     * @param \Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsRequestTransfer $requestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsResponseTransfer
     */
    public function execute(AfterpayAvailablePaymentMethodsRequestTransfer $requestTransfer)
    {
        $response = (new AfterpayAvailablePaymentMethodsResponseBuilder())
            ->build();
        $response->addPaymentMethods(['type' => AfterpayConfig::RISK_CHECK_METHOD_INVOICE]);

        return $response;
    }
}

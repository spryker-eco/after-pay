<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Yves\Afterpay;

use Spryker\Zed\Kernel\AbstractBundleConfig;
use SprykerEco\Shared\Afterpay\AfterpayConstants;

class AfterpayConfig extends AbstractBundleConfig
{

    /**
     * @return array
     */
    public function getSubFormToPaymentMethodMapping()
    {
        return $this->get(AfterpayConstants::AFTERPAY_RISK_CHECK_CONFIGURATION);
    }

    /**
     * @return string
     */
    public function getAfterpayAuthorizeWorkflow()
    {
        return $this->get(AfterpayConstants::AFTERPAY_AUTHORIZE_WORKFLOW);
    }

}

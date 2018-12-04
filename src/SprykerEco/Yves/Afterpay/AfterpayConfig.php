<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\Afterpay;

use Spryker\Yves\Kernel\AbstractBundleConfig;
use SprykerEco\Shared\Afterpay\AfterpayConstants;

class AfterpayConfig extends AbstractBundleConfig
{
    /**
     * @return array
     */
    public function getSubFormToPaymentMethodMapping(): array
    {
        return $this->get(AfterpayConstants::AFTERPAY_RISK_CHECK_CONFIGURATION);
    }

    /**
     * @return string
     */
    public function getAfterpayAuthorizeWorkflow(): string
    {
        return $this->get(AfterpayConstants::AFTERPAY_AUTHORIZE_WORKFLOW);
    }
}

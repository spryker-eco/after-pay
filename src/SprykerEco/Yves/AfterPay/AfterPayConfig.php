<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Yves\AfterPay;

use Spryker\Yves\Kernel\AbstractBundleConfig;
use SprykerEco\Shared\AfterPay\AfterPayConstants;

class AfterPayConfig extends AbstractBundleConfig
{
    /**
     * @return array
     */
    public function getSubFormToPaymentMethodMapping(): array
    {
        return $this->get(AfterPayConstants::AFTERPAY_RISK_CHECK_CONFIGURATION);
    }

    /**
     * @return string
     */
    public function getAfterPayAuthorizeWorkflow(): string
    {
        return $this->get(AfterPayConstants::AFTERPAY_AUTHORIZE_WORKFLOW);
    }
}

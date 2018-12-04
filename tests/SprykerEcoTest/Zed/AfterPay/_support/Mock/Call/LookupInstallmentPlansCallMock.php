<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\AfterPay\Mock\Call;

use Generated\Shared\DataBuilder\AfterPayInstallmentPlansResponseBuilder;
use Generated\Shared\Transfer\AfterPayInstallmentPlansRequestTransfer;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\LookupInstallmentPlansCall;

class LookupInstallmentPlansCallMock extends LookupInstallmentPlansCall
{
    /**
     * @param \Generated\Shared\Transfer\AfterPayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayInstallmentPlansResponseTransfer
     */
    public function execute(AfterPayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer)
    {
        return (new AfterPayInstallmentPlansResponseBuilder())
            ->withInstallmentPlan()
            ->build();
    }
}

<?php

namespace SprykerEcoTest\Zed\Afterpay\Mock\Call;

use Generated\Shared\DataBuilder\AfterpayInstallmentPlansResponseBuilder;
use Generated\Shared\Transfer\AfterpayInstallmentPlansRequestTransfer;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\LookupInstallmentPlansCall;

class LookupInstallmentPlansCallMock extends LookupInstallmentPlansCall
{
    /**
     * @param \Generated\Shared\Transfer\AfterpayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayInstallmentPlansResponseTransfer
     */
    public function execute(AfterpayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer)
    {
        return (new AfterpayInstallmentPlansResponseBuilder())
            ->withInstallmentPlan()
            ->build();
    }
}

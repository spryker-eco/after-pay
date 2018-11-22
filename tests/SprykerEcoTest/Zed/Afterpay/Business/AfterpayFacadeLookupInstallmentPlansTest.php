<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEcoTest\Zed\Afterpay\Business;

use Generated\Shared\DataBuilder\AfterpayInstallmentPlansRequestBuilder;
use Generated\Shared\Transfer\AfterpayInstallmentPlansRequestTransfer;

class AfterpayFacadeLookupInstallmentPlansTest extends AfterpayFacadeAbstractTest
{

    /**
     * @return void
     */
    public function testsLookupInstallmentPlans()
    {
        $request = $this->prepareRequest();
        $output = $this->doFacadeCall($request);
        $this->doTest($output);
    }

    /**
     * @return \Generated\Shared\Transfer\AfterpayInstallmentPlansRequestTransfer
     */
    protected function prepareRequest()
    {
        return (new AfterpayInstallmentPlansRequestBuilder())
            ->build();
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayInstallmentPlansRequestTransfer $request
     *
     * @return \Generated\Shared\Transfer\AfterpayInstallmentPlansResponseTransfer
     */
    protected function doFacadeCall(AfterpayInstallmentPlansRequestTransfer $request)
    {
        return $this->facade->lookupInstallmentPlans($request);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayInstallmentPlansResponseTransfer $output
     *
     * @return void
     */
    protected function doTest($output)
    {
        foreach ($output->getInstallmentPlans() as $plan) {
            $this->assertNotEmpty($plan->getBasketAmount());
            $this->assertNotEmpty($plan->getEffectiveAnnualPercentageRate());
            $this->assertNotEmpty($plan->getEffectiveInterestRate());
            $this->assertNotEmpty($plan->getFirstInstallmentAmount());
            $this->assertNotEmpty($plan->getInstallmentAmount());
            $this->assertNotEmpty($plan->getInstallmentProfileNumber());
            $this->assertNotEmpty($plan->getInterestRate());
            $this->assertNotEmpty($plan->getLastInstallmentAmount());
            $this->assertNotEmpty($plan->getMonthlyFee());
            $this->assertNotEmpty($plan->getNumberOfInstallments());
            $this->assertNotEmpty($plan->getReadMore());
            $this->assertNotEmpty($plan->getStartupFee());
            $this->assertNotEmpty($plan->getTotalAmount());
            $this->assertNotEmpty($plan->getTotalInterestAmount());
        }
    }

}

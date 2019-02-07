<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\AfterPay\Business;

use Generated\Shared\DataBuilder\AfterPayInstallmentPlansRequestBuilder;
use Generated\Shared\Transfer\AfterPayInstallmentPlansRequestTransfer;
use Generated\Shared\Transfer\AfterPayInstallmentPlansResponseTransfer;

class AfterPayFacadeLookupInstallmentPlansTest extends AfterPayFacadeAbstractTest
{
    /**
     * @return void
     */
    public function testsLookupInstallmentPlans(): void
    {
        $request = $this->prepareRequest();
        $output = $this->doFacadeCall($request);
        $this->doTest($output);
    }

    /**
     * @return \Generated\Shared\Transfer\AfterPayInstallmentPlansRequestTransfer
     */
    protected function prepareRequest(): AfterPayInstallmentPlansRequestTransfer
    {
        $request = (new AfterPayInstallmentPlansRequestBuilder())
            ->build();

        return $request->setAmount((int)$request->getAmount());
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayInstallmentPlansRequestTransfer $request
     *
     * @return \Generated\Shared\Transfer\AfterPayInstallmentPlansResponseTransfer
     */
    protected function doFacadeCall(AfterPayInstallmentPlansRequestTransfer $request): AfterPayInstallmentPlansResponseTransfer
    {
        return $this->facade->lookupInstallmentPlans($request);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayInstallmentPlansResponseTransfer $output
     *
     * @return void
     */
    protected function doTest(AfterPayInstallmentPlansResponseTransfer $output): void
    {
        foreach ($output->getInstallmentPlans() as $plan) {
            $this->assertNotNull($plan->getBasketAmount());
            $this->assertNotNull($plan->getEffectiveAnnualPercentageRate());
            $this->assertNotNull($plan->getEffectiveInterestRate());
            $this->assertNotNull($plan->getFirstInstallmentAmount());
            $this->assertNotNull($plan->getInstallmentAmount());
            $this->assertNotNull($plan->getInstallmentProfileNumber());
            $this->assertNotNull($plan->getInterestRate());
            $this->assertNotNull($plan->getLastInstallmentAmount());
            $this->assertNotNull($plan->getMonthlyFee());
            $this->assertNotNull($plan->getNumberOfInstallments());
            $this->assertNotNull($plan->getReadMore());
            $this->assertNotNull($plan->getStartupFee());
            $this->assertNotNull($plan->getTotalAmount());
            $this->assertNotNull($plan->getTotalInterestAmount());
        }
    }
}

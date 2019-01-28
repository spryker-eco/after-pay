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

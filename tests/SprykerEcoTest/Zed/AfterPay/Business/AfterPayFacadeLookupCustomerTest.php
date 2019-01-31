<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\AfterPay\Business;

use Generated\Shared\DataBuilder\AfterPayCustomerLookupRequestBuilder;
use Generated\Shared\Transfer\AfterPayCustomerLookupRequestTransfer;
use Generated\Shared\Transfer\AfterPayCustomerLookupResponseTransfer;

class AfterPayFacadeLookupCustomerTest extends AfterPayFacadeAbstractTest
{
    /**
     * @return void
     */
    public function testLookupCustomer(): void
    {
        $request = $this->prepareRequest();
        $output = $this->doFacadeCall($request);
        $this->doTest($output);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCustomerLookupRequestTransfer $request
     *
     * @return \Generated\Shared\Transfer\AfterPayCustomerLookupResponseTransfer
     */
    protected function doFacadeCall(AfterPayCustomerLookupRequestTransfer $request): AfterPayCustomerLookupResponseTransfer
    {
        return $this->facade->lookupCustomer($request);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCustomerLookupResponseTransfer $output
     *
     * @return void
     */
    protected function doTest(AfterPayCustomerLookupResponseTransfer $output): void
    {
        foreach ($output->getUserProfiles() as $profile) {
            $this->assertNotEmpty($profile->getEmail());
            $this->assertNotEmpty($profile->getFirstName());
            $this->assertNotEmpty($profile->getLanguageCode());
            $this->assertNotEmpty($profile->getLastName());
            $this->assertNotEmpty($profile->getMobileNumber());
        }
    }

    /**
     * @return \Generated\Shared\Transfer\AfterPayCustomerLookupRequestTransfer
     */
    protected function prepareRequest()
    {
        return (new AfterPayCustomerLookupRequestBuilder())
            ->build();
    }
}

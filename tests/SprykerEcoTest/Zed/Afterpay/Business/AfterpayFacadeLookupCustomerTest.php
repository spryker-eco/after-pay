<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEcoTest\Zed\Afterpay\Business;

use Generated\Shared\DataBuilder\AfterpayCustomerLookupRequestBuilder;
use Generated\Shared\Transfer\AfterpayCustomerLookupResponseTransfer;

class AfterpayFacadeLookupCustomerTest extends AfterpayFacadeAbstractTest
{
    /**
     * @return void
     */
    public function testLookupCustomer()
    {
        $request = $this->prepareRequest();
        $output = $this->doFacadeCall($request);
        $this->doTest($output);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayCustomerLookupRequestTransfer $request
     *
     * @return \Generated\Shared\Transfer\AfterpayCustomerLookupResponseTransfer
     */
    protected function doFacadeCall($request)
    {
        return $this->facade->lookupCustomer($request);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayCustomerLookupResponseTransfer $output
     *
     * @return void
     */
    protected function doTest(AfterpayCustomerLookupResponseTransfer $output)
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
     * @return \Generated\Shared\Transfer\AfterpayCustomerLookupRequestTransfer
     */
    protected function prepareRequest()
    {
        return (new AfterpayCustomerLookupRequestBuilder())
            ->build();
    }
}

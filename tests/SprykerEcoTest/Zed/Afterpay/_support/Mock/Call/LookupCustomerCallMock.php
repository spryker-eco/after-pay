<?php

namespace SprykerEcoTest\Zed\Afterpay\Mock\Call;

use Generated\Shared\DataBuilder\AfterpayCustomerLookupResponseBuilder;
use Generated\Shared\Transfer\AfterpayCustomerLookupRequestTransfer;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall\LookupCustomerCall;

class LookupCustomerCallMock extends LookupCustomerCall
{
    /**
     * @param \Generated\Shared\Transfer\AfterpayCustomerLookupRequestTransfer $customerLookupRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayCustomerLookupResponseTransfer
     */
    public function execute(AfterpayCustomerLookupRequestTransfer $customerLookupRequestTransfer)
    {
        return (new AfterpayCustomerLookupResponseBuilder())
            ->withUserProfile()
            ->withAnotherUserProfile()
            ->build();
    }
}

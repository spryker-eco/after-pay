<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\AfterPay\Mock\Call;

use Generated\Shared\DataBuilder\AfterPayCustomerLookupResponseBuilder;
use Generated\Shared\Transfer\AfterPayCustomerLookupRequestTransfer;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall\LookupCustomerCall;

class LookupCustomerCallMock extends LookupCustomerCall
{
    /**
     * @param \Generated\Shared\Transfer\AfterPayCustomerLookupRequestTransfer $customerLookupRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayCustomerLookupResponseTransfer
     */
    public function execute(AfterPayCustomerLookupRequestTransfer $customerLookupRequestTransfer)
    {
        return (new AfterPayCustomerLookupResponseBuilder())
            ->withUserProfile()
            ->withAnotherUserProfile()
            ->build();
    }
}

<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\AdditionalServices\Handler;

use Generated\Shared\Transfer\AfterPayValidateBankAccountRequestTransfer;
use Generated\Shared\Transfer\AfterPayValidateBankAccountResponseTransfer;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\AdapterInterface;

class ValidateBankAccountHandler implements ValidateBankAccountHandlerInterface
{
    /**
     * @var \SprykerEco\Zed\AfterPay\Business\Api\Adapter\AdapterInterface
     */
    protected $apiAdapter;

    /**
     * @var \SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToCustomerFacadeInterface
     */
    protected $customerFacade;

    /**
     * @param \SprykerEco\Zed\AfterPay\Business\Api\Adapter\AdapterInterface $apiAdapter
     */
    public function __construct(AdapterInterface $apiAdapter)
    {
        $this->apiAdapter = $apiAdapter;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayValidateBankAccountRequestTransfer $validateBankAccountRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayValidateBankAccountResponseTransfer
     */
    public function validateBankAccount(AfterPayValidateBankAccountRequestTransfer $validateBankAccountRequestTransfer): AfterPayValidateBankAccountResponseTransfer
    {
        return $this->apiAdapter->sendValidateBankAccountRequest($validateBankAccountRequestTransfer);
    }
}

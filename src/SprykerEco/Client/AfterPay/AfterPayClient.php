<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\AfterPay;

use Generated\Shared\Transfer\AfterPayCustomerLookupRequestTransfer;
use Generated\Shared\Transfer\AfterPayCustomerLookupResponseTransfer;
use Generated\Shared\Transfer\AfterPayInstallmentPlansRequestTransfer;
use Generated\Shared\Transfer\AfterPayInstallmentPlansResponseTransfer;
use Generated\Shared\Transfer\AfterPayValidateBankAccountRequestTransfer;
use Generated\Shared\Transfer\AfterPayValidateBankAccountResponseTransfer;
use Generated\Shared\Transfer\AfterPayValidateCustomerRequestTransfer;
use Generated\Shared\Transfer\AfterPayValidateCustomerResponseTransfer;
use Spryker\Client\Kernel\AbstractClient;

/**
 * @method \SprykerEco\Client\AfterPay\AfterPayFactory getFactory()
 */
class AfterPayClient extends AbstractClient implements AfterPayClientInterface
{
    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AfterPayValidateCustomerRequestTransfer $validateCustomerRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayValidateCustomerResponseTransfer
     */
    public function validateCustomerAddress(AfterPayValidateCustomerRequestTransfer $validateCustomerRequestTransfer): AfterPayValidateCustomerResponseTransfer
    {
        return $this->getFactory()
            ->createZedAfterPayStub()
            ->validateCustomerAddress($validateCustomerRequestTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AfterPayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayInstallmentPlansResponseTransfer
     */
    public function getAvailableInstallmentPlans(AfterPayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer): AfterPayInstallmentPlansResponseTransfer
    {
        return $this->getFactory()
            ->createZedAfterPayStub()
            ->getAvailableInstallmentPlans($installmentPlansRequestTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AfterPayCustomerLookupRequestTransfer $customerLookupRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayCustomerLookupResponseTransfer
     */
    public function lookupCustomer(AfterPayCustomerLookupRequestTransfer $customerLookupRequestTransfer): AfterPayCustomerLookupResponseTransfer
    {
        return $this->getFactory()
            ->createZedAfterPayStub()
            ->lookupCustomer($customerLookupRequestTransfer);
    }

    /**
     * {@inheritdoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\AfterPayValidateBankAccountRequestTransfer $bankAccountValidationRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayValidateBankAccountResponseTransfer
     */
    public function validateBankAccount(AfterPayValidateBankAccountRequestTransfer $bankAccountValidationRequestTransfer): AfterPayValidateBankAccountResponseTransfer
    {
        return $this->getFactory()
            ->createZedAfterPayStub()
            ->validateBankAccount($bankAccountValidationRequestTransfer);
    }
}

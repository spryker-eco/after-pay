<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Communication\Controller;

use Generated\Shared\Transfer\AfterpayCustomerLookupRequestTransfer;
use Generated\Shared\Transfer\AfterpayInstallmentPlansRequestTransfer;
use Generated\Shared\Transfer\AfterpayValidateBankAccountRequestTransfer;
use Generated\Shared\Transfer\AfterpayValidateCustomerRequestTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\Kernel\Communication\Controller\AbstractGatewayController;

/**
 * @method \SprykerEco\Zed\Afterpay\Business\AfterpayFacade getFacade()
 */
class GatewayController extends AbstractGatewayController
{

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsTransfer
     */
    public function getAvailablePaymentMethodsAction(QuoteTransfer $quoteTransfer)
    {
        return $this->getFacade()->getAvailablePaymentMethods($quoteTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayValidateCustomerRequestTransfer $validateCustomerRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayValidateCustomerResponseTransfer
     */
    public function validateCustomerAddressAction(
        AfterpayValidateCustomerRequestTransfer $validateCustomerRequestTransfer
    ) {
        return $this->getFacade()->validateCustomerAddress($validateCustomerRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayValidateBankAccountRequestTransfer $validateBankAccountRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayValidateBankAccountResponseTransfer
     */
    public function validateBankAccountAction(
        AfterpayValidateBankAccountRequestTransfer $validateBankAccountRequestTransfer
    ) {
        return $this->getFacade()->validateBankAccount($validateBankAccountRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayCustomerLookupRequestTransfer $customerLookupTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayCustomerLookupResponseTransfer
     */
    public function lookupCustomerAction(
        AfterpayCustomerLookupRequestTransfer $customerLookupTransfer
    ) {
        return $this->getFacade()->lookupCustomer($customerLookupTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayInstallmentPlansResponseTransfer
     */
    public function lookupInstallmentPlansAction(
        AfterpayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer
    ) {
        return $this->getFacade()->lookupInstallmentPlans($installmentPlansRequestTransfer);
    }

}

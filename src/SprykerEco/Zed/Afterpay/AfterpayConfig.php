<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay;

use Spryker\Zed\Kernel\AbstractBundleConfig;
use SprykerEco\Shared\Afterpay\AfterpayConstants;

class AfterpayConfig extends AbstractBundleConfig
{

    /**
     * @param string $orderNumber
     *
     * @return string
     */
    public function getCaptureApiEndpointUrl($orderNumber)
    {
        return $this->getApiEndpointUrl(
            sprintf(AfterpayConstants::API_ENDPOINT_CAPTURE_PATH, $orderNumber)
        );
    }

    /**
     * @param string $orderNumber
     *
     * @return string
     */
    public function getCancelApiEndpointUrl($orderNumber)
    {
        return $this->getApiEndpointUrl(
            sprintf(AfterpayConstants::API_ENDPOINT_CANCEL_PATH, $orderNumber)
        );
    }

    /**
     * @return string
     */
    public function getAuthorizeApiEndpointUrl()
    {
        return $this->getApiEndpointUrl(
            AfterpayConstants::API_ENDPOINT_AUTHORIZE_PATH
        );
    }

    /**
     * @return string
     */
    public function getValidateAddressApiEndpointUrl()
    {
        return $this->getApiEndpointUrl(
            AfterpayConstants::API_ENDPOINT_VALIDATE_ADDRESS_PATH
        );
    }

    /**
     * @return string
     */
    public function getLookupCustomerApiEndpointUrl()
    {
        return $this->getApiEndpointUrl(
            AfterpayConstants::API_ENDPOINT_LOOKUP_CUSTOMER_PATH
        );
    }

    /**
     * @return string
     */
    public function getLookupInstallmentPlansApiEndpointUrl()
    {
        return $this->getApiEndpointUrl(
            AfterpayConstants::API_ENDPOINT_LOOKUP_INSTALLMENT_PLANS_PATH
        );
    }

    /**
     * @return string
     */
    public function getValidateBankAccountApiEndpointUrl()
    {
        return $this->getApiEndpointUrl(
            AfterpayConstants::API_ENDPOINT_VALIDATE_BANK_ACCOUNT_PATH
        );
    }

    /**
     * @return string
     */
    public function getStatusApiEndpointUrl()
    {
        return $this->getApiEndpointUrl(
            AfterpayConstants::API_ENDPOINT_API_STATUS_PATH
        );
    }

    /**
     * @return string
     */
    public function getVersionApiEndpointUrl()
    {
        return $this->getApiEndpointUrl(
            AfterpayConstants::API_ENDPOINT_API_VERSION_PATH
        );
    }

    /**
     * @return string
     */
    public function getAvailablePaymentMethodsApiEndpointUrl()
    {
        return $this->getApiEndpointUrl(
            AfterpayConstants::API_ENDPOINT_AVAILABLE_PAYMENT_METHODS_PATH
        );
    }

    /**
     * @return string
     */
    public function getApiCredentialsAuthKey()
    {
        return $this->get(AfterpayConstants::API_CREDENTIALS_AUTH_KEY);
    }

    /**
     * @return string
     */
    public function getAfterpayAuthorizeWorkflow()
    {
        return $this->get(AfterpayConstants::AFTERPAY_AUTHORIZE_WORKFLOW);
    }

    /**
     * @param string $paymentMethod
     *
     * @return string
     */
    public function getPaymentChannelId($paymentMethod)
    {
        switch ($paymentMethod) {
            case AfterpayConstants::PAYMENT_METHOD_INVOICE:
                return $this->get(AfterpayConstants::PAYMENT_INVOICE_CHANNEL_ID);
            default:
                return $this->get(AfterpayConstants::PAYMENT_INVOICE_CHANNEL_ID);
        }
    }

    /**
     * @return string
     */
    public function getPaymentAuthorizationFailedUrl()
    {
        return $this->get(AfterpayConstants::AFTERPAY_YVES_AUTHORIZE_PAYMENT_FAILED_URL);
    }

    /**
     * @param string $endpointPath
     *
     * @return string
     */
    protected function getApiEndpointUrl($endpointPath)
    {
        return $this->get(AfterpayConstants::API_ENDPOINT_BASE_URL) . $endpointPath;
    }

}

<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay;

use Spryker\Zed\Kernel\AbstractBundleConfig;
use SprykerEco\Shared\AfterPay\AfterPayConfig as SharedAfterPayConfig;
use SprykerEco\Shared\AfterPay\AfterPayConstants;

class AfterPayConfig extends AbstractBundleConfig
{
    /**
     * @param string $orderNumber
     *
     * @return string
     */
    public function getCaptureApiEndpointUrl(string $orderNumber): string
    {
        return $this->getApiEndpointUrl(
            sprintf(SharedAfterPayConfig::API_ENDPOINT_CAPTURE_PATH, $orderNumber)
        );
    }

    /**
     * @param string $orderNumber
     *
     * @return string
     */
    public function getRefundApiEndpointUrl(string $orderNumber): string
    {
        return $this->getApiEndpointUrl(
            sprintf(SharedAfterPayConfig::API_ENDPOINT_REFUND_PATH, $orderNumber)
        );
    }

    /**
     * @param string $orderNumber
     *
     * @return string
     */
    public function getCancelApiEndpointUrl(string $orderNumber): string
    {
        return $this->getApiEndpointUrl(
            sprintf(SharedAfterPayConfig::API_ENDPOINT_CANCEL_PATH, $orderNumber)
        );
    }

    /**
     * @return string
     */
    public function getAuthorizeApiEndpointUrl(): string
    {
        return $this->getApiEndpointUrl(
            SharedAfterPayConfig::API_ENDPOINT_AUTHORIZE_PATH
        );
    }

    /**
     * @return string
     */
    public function getValidateAddressApiEndpointUrl(): string
    {
        return $this->getApiEndpointUrl(
            SharedAfterPayConfig::API_ENDPOINT_VALIDATE_ADDRESS_PATH
        );
    }

    /**
     * @return string
     */
    public function getLookupCustomerApiEndpointUrl(): string
    {
        return $this->getApiEndpointUrl(
            SharedAfterPayConfig::API_ENDPOINT_LOOKUP_CUSTOMER_PATH
        );
    }

    /**
     * @return string
     */
    public function getLookupInstallmentPlansApiEndpointUrl(): string
    {
        return $this->getApiEndpointUrl(
            SharedAfterPayConfig::API_ENDPOINT_LOOKUP_INSTALLMENT_PLANS_PATH
        );
    }

    /**
     * @return string
     */
    public function getValidateBankAccountApiEndpointUrl(): string
    {
        return $this->getApiEndpointUrl(
            SharedAfterPayConfig::API_ENDPOINT_VALIDATE_BANK_ACCOUNT_PATH
        );
    }

    /**
     * @return string
     */
    public function getStatusApiEndpointUrl(): string
    {
        return $this->getApiEndpointUrl(
            SharedAfterPayConfig::API_ENDPOINT_API_STATUS_PATH
        );
    }

    /**
     * @return string
     */
    public function getVersionApiEndpointUrl(): string
    {
        return $this->getApiEndpointUrl(
            SharedAfterPayConfig::API_ENDPOINT_API_VERSION_PATH
        );
    }

    /**
     * @return string
     */
    public function getAvailablePaymentMethodsApiEndpointUrl(): string
    {
        return $this->getApiEndpointUrl(
            SharedAfterPayConfig::API_ENDPOINT_AVAILABLE_PAYMENT_METHODS_PATH
        );
    }

    /**
     * @return string
     */
    public function getApiCredentialsAuthKey(): string
    {
        return $this->get(AfterPayConstants::API_CREDENTIALS_AUTH_KEY);
    }

    /**
     * @return string
     */
    public function getAfterPayAuthorizeWorkflow(): string
    {
        return $this->get(AfterPayConstants::AFTERPAY_AUTHORIZE_WORKFLOW);
    }

    /**
     * @param string $paymentMethod
     *
     * @return string
     */
    public function getPaymentChannelId(string $paymentMethod): string
    {
        switch ($paymentMethod) {
            case SharedAfterPayConfig::PAYMENT_METHOD_INVOICE:
                return $this->get(AfterPayConstants::PAYMENT_INVOICE_CHANNEL_ID);
            default:
                return $this->get(AfterPayConstants::PAYMENT_INVOICE_CHANNEL_ID);
        }
    }

    /**
     * @return string
     */
    public function getPaymentAuthorizationFailedUrl(): string
    {
        return $this->get(AfterPayConstants::AFTERPAY_YVES_AUTHORIZE_PAYMENT_FAILED_URL);
    }

    /**
     * @param string $endpointPath
     *
     * @return string
     */
    protected function getApiEndpointUrl(string $endpointPath): string
    {
        return $this->get(AfterPayConstants::API_ENDPOINT_BASE_URL) . $endpointPath;
    }
}

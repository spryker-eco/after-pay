<?php
/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

/**
 * Copy over the following configs to your config
 */

use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Shared\Kernel\KernelConstants;
use Spryker\Shared\Oms\OmsConstants;
use Spryker\Shared\Sales\SalesConstants;
use Spryker\Zed\Oms\OmsConfig;
use SprykerEco\Shared\Afterpay\AfterpayConstants;

// Afterpay configuration

// Merchant config values, got from Afterpay
$config[AfterpayConstants::VENDOR_ROOT] = APPLICATION_ROOT_DIR . '/vendor/spryker-eco';

$config[AfterpayConstants::API_ENDPOINT_BASE_URL] = 'https://sandboxapi.horizonafs.com/eCommerceServicesWebApi/api/v3/';
$config[AfterpayConstants::API_CREDENTIALS_AUTH_KEY] = '';
$config[AfterpayConstants::PAYMENT_INVOICE_CHANNEL_ID] = '';

$config[OmsConstants::PROCESS_LOCATION] = [
    OmsConfig::DEFAULT_PROCESS_LOCATION,
    $config[AfterpayConstants::VENDOR_ROOT] . '/after-pay/config/Zed/Oms',
];

$config[OmsConstants::ACTIVE_PROCESSES] = [
    'AfterpayInvoice01',
];

$config[SalesConstants::PAYMENT_METHOD_STATEMACHINE_MAPPING] = [
    AfterpayConstants::PAYMENT_METHOD_INVOICE => 'AfterpayInvoice01',
];

$config[AfterpayConstants::HOST_SSL_YVES] = $config[ApplicationConstants::HOST_SSL_YVES];
$config[AfterpayConstants::HOST_YVES] = $config[ApplicationConstants::HOST_YVES];

$config[AfterpayConstants::AFTERPAY_YVES_AUTHORIZE_PAYMENT_FAILED_URL] = 'http://' . $config[AfterpayConstants::HOST_YVES] . '/checkout/payment';

$config[AfterpayConstants::AFTERPAY_AUTHORIZE_WORKFLOW] = AfterpayConstants::AFTERPAY_AUTHORIZE_WORKFLOW_TWO_STEPS;

$config[AfterpayConstants::AFTERPAY_RISK_CHECK_CONFIGURATION] = [
    AfterpayConstants::PAYMENT_METHOD_INVOICE => AfterpayConstants::RISK_CHECK_METHOD_INVOICE,
];

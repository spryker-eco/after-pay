<?php
/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

/**
 * Copy over the following configs to your config
 */

use Orm\Zed\Sales\Persistence\Map\SpySalesOrderTableMap;
use Spryker\Shared\Application\ApplicationConstants;
use Spryker\Shared\Oms\OmsConstants;
use Spryker\Shared\Sales\SalesConstants;
use Spryker\Zed\Oms\OmsConfig;
use SprykerEco\Shared\AfterPay\AfterPayConfig;
use SprykerEco\Shared\AfterPay\AfterPayConstants;

// AfterPay configuration

// Merchant config values, got from AfterPay
$config[AfterPayConstants::VENDOR_ROOT] = APPLICATION_ROOT_DIR . '/vendor/spryker-eco';

$config[AfterPayConstants::API_ENDPOINT_BASE_URL] = 'https://sandboxapi.horizonafs.com/eCommerceServicesWebApi/api/v3/';
$config[AfterPayConstants::API_CREDENTIALS_AUTH_KEY] = '';
$config[AfterPayConstants::PAYMENT_INVOICE_CHANNEL_ID] = '';

$config[OmsConstants::PROCESS_LOCATION] = [
    OmsConfig::DEFAULT_PROCESS_LOCATION,
    $config[AfterPayConstants::VENDOR_ROOT] . '/after-pay/config/Zed/Oms',
];

$config[OmsConstants::ACTIVE_PROCESSES] = [
    'AfterPayInvoice01',
];

$config[SalesConstants::PAYMENT_METHOD_STATEMACHINE_MAPPING] = [
    AfterPayConfig::PAYMENT_METHOD_INVOICE => 'AfterPayInvoice01',
];

$config[AfterPayConstants::HOST_SSL_YVES] = $config[ApplicationConstants::HOST_SSL_YVES];
$config[AfterPayConstants::HOST_YVES] = $config[ApplicationConstants::HOST_YVES];

$config[AfterPayConstants::AFTERPAY_YVES_AUTHORIZE_PAYMENT_FAILED_URL] = 'http://' . $config[AfterPayConstants::HOST_YVES] . '/checkout/payment';

$config[AfterPayConstants::AFTERPAY_AUTHORIZE_WORKFLOW] = AfterPayConfig::AFTERPAY_AUTHORIZE_WORKFLOW_TWO_STEPS;

$config[AfterPayConstants::AFTERPAY_RISK_CHECK_CONFIGURATION] = [
    AfterPayConfig::PAYMENT_METHOD_INVOICE => AfterPayConfig::RISK_CHECK_METHOD_INVOICE,
];

$config[AfterPayConstants::SALUTATION_DEFAULT] = 'Mr';
$config[AfterPayConstants::SALUTATION_MAP] = [
    SpySalesOrderTableMap::COL_SALUTATION_MR => 'Mr',
    SpySalesOrderTableMap::COL_SALUTATION_MS => 'Miss',
    SpySalesOrderTableMap::COL_SALUTATION_MRS => 'Mrs',
    SpySalesOrderTableMap::COL_SALUTATION_DR => 'Mr',
];

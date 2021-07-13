<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Shared\AfterPay;

/**
 * Declares global environment configuration keys. Do not use it for other class constants.
 */
interface AfterPayConstants
{
    public const AFTERPAY_AUTHORIZE_WORKFLOW = 'AFTER_PAY:AFTERPAY_AUTHORIZE_WORKFLOW';
    public const HOST_YVES = 'AFTER_PAY:HOST_YVES';
    public const HOST_SSL_YVES = 'AFTER_PAY:HOST_SSL_YVES';
    public const AFTERPAY_YVES_AUTHORIZE_PAYMENT_FAILED_URL = 'AFTER_PAY:AFTERPAY_YVES_AUTHORIZE_PAYMENT_FAILED_URL';
    public const VENDOR_ROOT = 'AFTER_PAY:VENDOR_ROOT';
    public const AFTERPAY_RISK_CHECK_CONFIGURATION = 'AFTER_PAY:AFTERPAY_RISK_CHECK_CONFIGURATION';
    public const API_CREDENTIALS_AUTH_KEY = 'AFTER_PAY:API_CREDENTIALS_AUTH_KEY';
    public const PAYMENT_INVOICE_CHANNEL_ID = 'AFTER_PAY:PAYMENT_INVOICE_CHANNEL_ID';
    public const API_ENDPOINT_BASE_URL = 'AFTER_PAY:API_ENDPOINT_BASE_URL';
    public const SALUTATION_MAP = 'AFTER_PAY:SALUTATION_MAP';
    public const SALUTATION_DEFAULT = 'AFTER_PAY:SALUTATION_DEFAULT';
}

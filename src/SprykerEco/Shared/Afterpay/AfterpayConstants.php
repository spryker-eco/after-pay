<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Shared\Afterpay;

/**
 * Declares global environment configuration keys. Do not use it for other class constants.
 */
interface AfterpayConstants
{
    public const AFTERPAY_AUTHORIZE_WORKFLOW = 'AFTERPAY:AFTERPAY_AUTHORIZE_WORKFLOW';
    public const HOST_YVES = 'AFTERPAY:HOST_YVES';
    public const HOST_SSL_YVES = 'AFTERPAY:HOST_SSL_YVES';
    public const AFTERPAY_YVES_AUTHORIZE_PAYMENT_FAILED_URL = 'AFTERPAY:AFTERPAY_YVES_AUTHORIZE_PAYMENT_FAILED_URL';
    public const VENDOR_ROOT = 'AFTERPAY:VENDOR_ROOT';
    public const AFTERPAY_RISK_CHECK_CONFIGURATION = 'AFTERPAY:AFTERPAY_RISK_CHECK_CONFIGURATION';
    public const API_CREDENTIALS_AUTH_KEY = 'AFTERPAY:API_CREDENTIALS_AUTH_KEY';
    public const PAYMENT_INVOICE_CHANNEL_ID = 'AFTERPAY:PAYMENT_INVOICE_CHANNEL_ID';
    public const API_ENDPOINT_BASE_URL = 'AFTERPAY:API_ENDPOINT_BASE_URL';
}

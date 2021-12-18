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
    /**
     * @var string
     */
    public const AFTERPAY_AUTHORIZE_WORKFLOW = 'AFTER_PAY:AFTERPAY_AUTHORIZE_WORKFLOW';

    /**
     * @var string
     */
    public const HOST_YVES = 'AFTER_PAY:HOST_YVES';

    /**
     * @var string
     */
    public const HOST_SSL_YVES = 'AFTER_PAY:HOST_SSL_YVES';

    /**
     * @var string
     */
    public const AFTERPAY_YVES_AUTHORIZE_PAYMENT_FAILED_URL = 'AFTER_PAY:AFTERPAY_YVES_AUTHORIZE_PAYMENT_FAILED_URL';

    /**
     * @var string
     */
    public const VENDOR_ROOT = 'AFTER_PAY:VENDOR_ROOT';

    /**
     * @var string
     */
    public const AFTERPAY_RISK_CHECK_CONFIGURATION = 'AFTER_PAY:AFTERPAY_RISK_CHECK_CONFIGURATION';

    /**
     * @var string
     */
    public const API_CREDENTIALS_AUTH_KEY = 'AFTER_PAY:API_CREDENTIALS_AUTH_KEY';

    /**
     * @var string
     */
    public const PAYMENT_INVOICE_CHANNEL_ID = 'AFTER_PAY:PAYMENT_INVOICE_CHANNEL_ID';

    /**
     * @var string
     */
    public const API_ENDPOINT_BASE_URL = 'AFTER_PAY:API_ENDPOINT_BASE_URL';
}

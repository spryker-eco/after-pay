<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Shared\AfterPay;

use Spryker\Shared\Kernel\AbstractBundleConfig;

class AfterPayConfig extends AbstractBundleConfig
{
    /**
     * @var string
     */
    public const PROVIDER_NAME = 'afterPay';

    /**
     * @var string
     */
    public const AFTERPAY_AUTHORIZE_WORKFLOW_ONE_STEP = 'one step authorize workflow';

    /**
     * @var string
     */
    public const AFTERPAY_AUTHORIZE_WORKFLOW_TWO_STEPS = 'two steps authorize workflow';

    /**
     * @var string
     */
    public const PAYMENT_TYPE_INVOICE = 'Invoice';
    public const PAYMENT_METHOD_INVOICE = self::PROVIDER_NAME . self::PAYMENT_TYPE_INVOICE;

    /**
     * @var string
     */
    public const TRANSACTION_TYPE_CANCEL = 'cancel';

    /**
     * @var string
     */
    public const TRANSACTION_TYPE_AUTHORIZE = 'authorize';

    /**
     * @var string
     */
    public const TRANSACTION_TYPE_CAPTURE = 'capture';

    /**
     * @var string
     */
    public const TRANSACTION_TYPE_REFUND = 'refund';

    /**
     * @var string
     */
    public const API_ENDPOINT_CAPTURE_PATH = 'orders/%s/captures';

    /**
     * @var string
     */
    public const API_ENDPOINT_LOOKUP_CUSTOMER_PATH = 'lookup/customer';

    /**
     * @var string
     */
    public const API_ENDPOINT_API_STATUS_PATH = 'status';

    /**
     * @var string
     */
    public const API_ENDPOINT_REFUND_PATH = 'orders/%s/refunds';

    /**
     * @var string
     */
    public const API_ENDPOINT_VALIDATE_ADDRESS_PATH = 'validate/address';

    /**
     * @var string
     */
    public const API_ENDPOINT_CANCEL_PATH = 'orders/%s/voids';

    /**
     * @var string
     */
    public const API_ENDPOINT_API_VERSION_PATH = 'version';

    /**
     * @var string
     */
    public const API_ENDPOINT_AVAILABLE_PAYMENT_METHODS_PATH = 'checkout/payment-methods';

    /**
     * @var string
     */
    public const API_ENDPOINT_AUTHORIZE_PATH = 'checkout/authorize';

    /**
     * @var string
     */
    public const API_ENDPOINT_VALIDATE_BANK_ACCOUNT_PATH = 'validate/bank-account';

    /**
     * @var string
     */
    public const API_ENDPOINT_LOOKUP_INSTALLMENT_PLANS_PATH = 'lookup/installment-plans';

    /**
     * @var string
     */
    public const RISK_CHECK_METHOD_INVOICE = 'Invoice';

    /**
     * @var string
     */
    public const API_TRANSACTION_OUTCOME_REJECTED = 'Rejected';

    /**
     * @var string
     */
    public const API_CUSTOMER_CATEGORY_PERSON = 'Person';

    /**
     * @var string
     */
    public const API_TRANSACTION_OUTCOME_ACCEPTED = 'Accepted';

    /**
     * @var string
     */
    public const CAPTURE_EXPENSE_DESCRIPTION = 'Expense total amount';

    /**
     * @var string
     */
    public const REFUND_EXPENSE_SKU = 'REFUND_EXPENSE';

    /**
     * @var string
     */
    public const CANCEL_EXPENSE_DESCRIPTION = 'Expense total amount';

    /**
     * @var string
     */
    public const CANCEL_EXPENSE_SKU = 'EXPENSE';

    /**
     * @var string
     */
    public const CAPTURE_EXPENSE_SKU = 'EXPENSE';

    /**
     * @var string
     */
    public const REFUND_EXPENSE_DECRIPTION = 'Refund expence total amount';
}

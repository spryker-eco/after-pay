<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Shared\Afterpay;

use Spryker\Shared\Kernel\AbstractBundleConfig;

class AfterpayConfig extends AbstractBundleConfig
{
    public const PROVIDER_NAME = 'afterpay';
    public const AFTERPAY_AUTHORIZE_WORKFLOW_ONE_STEP = 'one step authorize workflow';
    public const AFTERPAY_AUTHORIZE_WORKFLOW_TWO_STEPS = 'two steps authorize workflow';
    public const PAYMENT_TYPE_INVOICE = 'Invoice';
    public const PAYMENT_METHOD_INVOICE = self::PROVIDER_NAME . self::PAYMENT_TYPE_INVOICE;
    public const TRANSACTION_TYPE_CANCEL = 'cancel';
    public const TRANSACTION_TYPE_AUTHORIZE = 'authorize';
    public const TRANSACTION_TYPE_CAPTURE = 'capture';
    public const TRANSACTION_TYPE_REFUND = 'refund';
    public const API_ENDPOINT_CAPTURE_PATH = 'orders/%s/captures';
    public const API_ENDPOINT_LOOKUP_CUSTOMER_PATH = 'lookup/customer';
    public const API_ENDPOINT_API_STATUS_PATH = 'status';
    public const API_ENDPOINT_REFUND_PATH = 'orders/%s/refunds';
    public const API_ENDPOINT_VALIDATE_ADDRESS_PATH = 'validate/address';
    public const API_ENDPOINT_CANCEL_PATH = 'orders/%s/voids';
    public const API_ENDPOINT_API_VERSION_PATH = 'version';
    public const API_ENDPOINT_AVAILABLE_PAYMENT_METHODS_PATH = 'checkout/payment-methods';
    public const API_ENDPOINT_AUTHORIZE_PATH = 'checkout/authorize';
    public const API_ENDPOINT_VALIDATE_BANK_ACCOUNT_PATH = 'validate/bank-account';
    public const API_ENDPOINT_LOOKUP_INSTALLMENT_PLANS_PATH = 'lookup/installment-plans';
    public const RISK_CHECK_METHOD_INVOICE = 'Invoice';
    public const API_TRANSACTION_OUTCOME_REJECTED = 'Rejected';
    public const API_CUSTOMER_CATEGORY_PERSON = 'Person';
    public const API_TRANSACTION_OUTCOME_ACCEPTED = 'Accepted';
    public const CAPTURE_EXPENSE_DESCRIPTION = 'Expense total amount';
    public const REFUND_EXPENSE_SKU = 'REFUND_EXPENSE';
    public const CANCEL_EXPENSE_DESCRIPTION = 'Expense total amount';
    public const CANCEL_EXPENSE_SKU = 'EXPENSE';
    public const CAPTURE_EXPENSE_SKU = 'EXPENSE';
    public const REFUND_EXPENSE_DECRIPTION = 'Refund expence total amount';
}

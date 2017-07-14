<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Shared\Afterpay;

interface AfterpayConstants
{

    const PROVIDER_NAME = 'afterpay';

    const AFTERPAY_AUTHORIZE_WORKFLOW = 'AFTERPAY_AUTHORIZE_WORKFLOW';

    const AFTERPAY_AUTHORIZE_WORKFLOW_ONE_STEP = 'one step authorize workflow';
    const AFTERPAY_AUTHORIZE_WORKFLOW_TWO_STEPS = 'two steps authorize workflow';

    const HOST_YVES = 'HOST_YVES';
    const HOST_SSL_YVES = 'HOST_SSL_YVES';

    const AFTERPAY_YVES_AUTHORIZE_PAYMENT_FAILED_URL = 'AFTERPAY_YVES_AUTHORIZE_PAYMENT_FAILED_URL';

    const VENDOR_ROOT = 'VENDOR_ROOT';
    const PAYMENT_TYPE_INVOICE = 'Invoice';
    const PAYMENT_METHOD_INVOICE = self::PROVIDER_NAME . self::PAYMENT_TYPE_INVOICE;

    const TRANSACTION_TYPE_AUTHORIZE = 'authorize';
    const TRANSACTION_TYPE_CAPTURE = 'capture';
    const TRANSACTION_TYPE_CANCEL = 'cancel';

    const AFTERPAY_RISK_CHECK_CONFIGURATION = 'AFTERPAY_RISK_CHECK_CONFIGURATION';

    const API_ENDPOINT_BASE_URL = 'API_ENDPOINT_URL';

    const API_ENDPOINT_AVAILABLE_PAYMENT_METHODS_PATH = 'checkout/payment-methods';
    const API_ENDPOINT_CAPTURE_PATH = 'orders/%s/captures';
    const API_ENDPOINT_CANCEL_PATH = 'orders/%s/voids';
    const API_ENDPOINT_AUTHORIZE_PATH = 'checkout/authorize';
    const API_ENDPOINT_VALIDATE_ADDRESS_PATH = 'validate/address';
    const API_ENDPOINT_LOOKUP_CUSTOMER_PATH = 'lookup/customer';
    const API_ENDPOINT_LOOKUP_INSTALLMENT_PLANS_PATH = 'lookup/installment-plans';
    const API_ENDPOINT_VALIDATE_BANK_ACCOUNT_PATH = 'validate/bank-account';
    const API_ENDPOINT_API_STATUS_PATH = 'status';
    const API_ENDPOINT_API_VERSION_PATH = 'version';

    const API_CREDENTIALS_AUTH_KEY = 'API_CREDENTIALS_AUTH_KEY';

    const PAYMENT_SUB_FORM_CHECKERS = 'PAYMENT_SUB_FORM_CHECKERS';
    const PRE_PAYMENT_QUOTE_EXPANDERS = 'PRE_PAYMENT_QUOTE_EXPANDERS';

    const PAYMENT_INVOICE_CHANNEL_ID = 'API_CREDENTIALS_INVOICE_CHANNEL_ID';

    const RISK_CHECK_METHOD_INVOICE = 'Invoice';

    const API_CUSTOMER_CATEGORY_PERSON = 'Person';

    const API_TRANSACTION_OUTCOME_ACCEPTED = 'Accepted';
    const API_TRANSACTION_OUTCOME_REJECTED = 'Rejected';

    const CAPTURE_EXPENSE_SKU = 'EXPENSE';
    const CAPTURE_EXPENSE_DESCRIPTION = 'Expense total amount';

    const CANCEL_EXPENSE_SKU = 'EXPENSE';
    const CANCEL_EXPENSE_DESCRIPTION = 'Expense total amount';

}

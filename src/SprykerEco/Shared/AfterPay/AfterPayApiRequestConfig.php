<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Shared\AfterPay;

class AfterPayApiRequestConfig
{
    public const TRANSACTION_OUTCOME = 'outcome';
    public const TRANSACTION_RESERVATION_ID = 'reservationId';
    public const TRANSACTION_CHECKOUT_ID = 'checkoutId';

    public const PAYMENT_METHODS = 'paymentMethods';
    public const ADDITIONAL_RESPONSE_INFO = 'additionalResponseInfo';
    public const RISK_CHECK_CODE = 'rsS_RiskCheck_ResultCode';
    public const CUSTOMER = 'customer';
    public const CUSTOMER_NUMBER = 'customerNumber';
    public const RISK_CHECK_MESSAGES = 'riskCheckMessages';
    public const RISK_CHECK_MESSAGE_CODE = 'code';
    public const RISK_CHECK_MESSAGE_CUSTOMER_FACING_MESSAGE = 'customerFacingMessage';
    public const RISK_CHECK_MESSAGE_MESSAGE = 'message';
    public const RISK_CHECK_MESSAGE_TYPE = 'type';

    public const CAPTURE_CAPTURED_AMOUNT = 'capturedAmount';
    public const CAPTURE_AUTHORIZED_AMOUNT = 'authorizedAmount';
    public const CAPTURE_REMAINING_AUTHORIZED_AMOUNT = 'remainingAuthorizedAmount';
    public const CAPTURE_CAPTURE_NUMBER = 'captureNumber';

    public const REFUND_TOTAL_CAPTURED_AMOUNT = 'totalCapturedAmount';
    public const REFUND_TOTAL_AUTHORIZE_AMOUNT = 'totalAuthorizedAmount';

    public const CANCEL_CAPTURED_AMOUNT = 'totalCapturedAmount';
    public const CANCEL_AUTHORIZED_AMOUNT = 'totalAuthorizedAmount';

    public const VALIDATE_BANK_ACCOUNT_IS_VALID = 'isValid';

    public const VALIDATE_ADDRESS_IS_VALID = 'isValid';
    public const CORRECTED_ADDRESS = 'correctedAddress';

    public const USER_PROFILES = 'userProfiles';
    public const USER_PROFILE_FIRST_NAME = 'firstName';
    public const USER_PROFILE_LAST_NAME = 'lastName';
    public const USER_PROFILE_MOBILE_NUMBER = 'mobileNumber';
    public const USER_PROFILE_EMAIL = 'eMail';
    public const USER_PROFILE_LANGUAGE_CODE = 'languageCode';
    public const USER_PROFILE_ADDRESS_LIST = 'addressList';

    public const USER_PROFILE_ADDRESS_STREET = 'street';
    public const USER_PROFILE_ADDRESS_STREET2 = 'street2';
    public const USER_PROFILE_ADDRESS_STREET3 = 'street3';
    public const USER_PROFILE_ADDRESS_STREET4 = 'street4';
    public const USER_PROFILE_ADDRESS_STREET_NUMBER = 'streetNumber';
    public const USER_PROFILE_ADDRESS_FLAT = 'flatNo';
    public const USER_PROFILE_ADDRESS_ENTRANCE = 'entrance';
    public const USER_PROFILE_ADDRESS_CITY = 'city';
    public const USER_PROFILE_ADDRESS_POSTAL_CODE = 'postalCode';
    public const USER_PROFILE_ADDRESS_COUNTRY = 'country';
    public const USER_PROFILE_ADDRESS_COUNTRY_CODE = 'countryCode';

    public const AVAILABLE_PLANS = 'availableInstallmentPlans';
    public const BASKET_AMOUNT = 'basketAmount';
    public const NUMBER_OF_INSTALLMENTS = 'numberOfInstallments';
    public const INSTALLMENT_AMOUNT = 'installmentAmount';
    public const FIRST_INSTALLMENT_AMOUNT = 'firstInstallmentAmount';
    public const LAST_INSTALLMENT_AMOUNT = 'lastInstallmentAmount';
    public const INTEREST_RATE = 'interestRate';
    public const EFFECTIVE_INTEREST_RATE = 'effectiveInterestRate';
    public const EFFECTIVE_ANNUAL_PERCENTAGE_RATE = 'effectiveAnnualPercentageRate';
    public const TOTAL_INTEREST_AMOUNT = 'totalInterestAmount';
    public const STARTUP_FEE = 'startupFee';
    public const MONTHLY_FEE = 'monthlyFee';
    public const TOTAL_AMOUNT = 'totalAmount';
    public const INSTALLMENT_PROFILE_NUMBER = 'installmentProfileNumber';
    public const READ_MORE = 'readMore';

    public const API_VERSION = 'version';
}

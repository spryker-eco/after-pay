<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Shared\AfterPay;

class AfterPayApiRequestConfig
{
    /**
     * @var string
     */
    public const TRANSACTION_OUTCOME = 'outcome';

    /**
     * @var string
     */
    public const TRANSACTION_RESERVATION_ID = 'reservationId';

    /**
     * @var string
     */
    public const TRANSACTION_CHECKOUT_ID = 'checkoutId';

    /**
     * @var string
     */
    public const PAYMENT_METHODS = 'paymentMethods';

    /**
     * @var string
     */
    public const ADDITIONAL_RESPONSE_INFO = 'additionalResponseInfo';

    /**
     * @var string
     */
    public const RISK_CHECK_CODE = 'rsS_RiskCheck_ResultCode';

    /**
     * @var string
     */
    public const CUSTOMER = 'customer';

    /**
     * @var string
     */
    public const CUSTOMER_NUMBER = 'customerNumber';

    /**
     * @var string
     */
    public const RISK_CHECK_MESSAGES = 'riskCheckMessages';

    /**
     * @var string
     */
    public const RISK_CHECK_MESSAGE_CODE = 'code';

    /**
     * @var string
     */
    public const RISK_CHECK_MESSAGE_CUSTOMER_FACING_MESSAGE = 'customerFacingMessage';

    /**
     * @var string
     */
    public const RISK_CHECK_MESSAGE_MESSAGE = 'message';

    /**
     * @var string
     */
    public const RISK_CHECK_MESSAGE_TYPE = 'type';

    /**
     * @var string
     */
    public const CAPTURE_CAPTURED_AMOUNT = 'capturedAmount';

    /**
     * @var string
     */
    public const CAPTURE_AUTHORIZED_AMOUNT = 'authorizedAmount';

    /**
     * @var string
     */
    public const CAPTURE_REMAINING_AUTHORIZED_AMOUNT = 'remainingAuthorizedAmount';

    /**
     * @var string
     */
    public const CAPTURE_CAPTURE_NUMBER = 'captureNumber';

    /**
     * @var string
     */
    public const REFUND_TOTAL_CAPTURED_AMOUNT = 'totalCapturedAmount';

    /**
     * @var string
     */
    public const REFUND_TOTAL_AUTHORIZE_AMOUNT = 'totalAuthorizedAmount';

    /**
     * @var string
     */
    public const CANCEL_CAPTURED_AMOUNT = 'totalCapturedAmount';

    /**
     * @var string
     */
    public const CANCEL_AUTHORIZED_AMOUNT = 'totalAuthorizedAmount';

    /**
     * @var string
     */
    public const VALIDATE_BANK_ACCOUNT_IS_VALID = 'isValid';

    /**
     * @var string
     */
    public const VALIDATE_ADDRESS_IS_VALID = 'isValid';

    /**
     * @var string
     */
    public const CORRECTED_ADDRESS = 'correctedAddress';

    /**
     * @var string
     */
    public const USER_PROFILES = 'userProfiles';

    /**
     * @var string
     */
    public const USER_PROFILE_FIRST_NAME = 'firstName';

    /**
     * @var string
     */
    public const USER_PROFILE_LAST_NAME = 'lastName';

    /**
     * @var string
     */
    public const USER_PROFILE_MOBILE_NUMBER = 'mobileNumber';

    /**
     * @var string
     */
    public const USER_PROFILE_EMAIL = 'eMail';

    /**
     * @var string
     */
    public const USER_PROFILE_LANGUAGE_CODE = 'languageCode';

    /**
     * @var string
     */
    public const USER_PROFILE_ADDRESS_LIST = 'addressList';

    /**
     * @var string
     */
    public const USER_PROFILE_ADDRESS_STREET = 'street';

    /**
     * @var string
     */
    public const USER_PROFILE_ADDRESS_STREET2 = 'street2';

    /**
     * @var string
     */
    public const USER_PROFILE_ADDRESS_STREET3 = 'street3';

    /**
     * @var string
     */
    public const USER_PROFILE_ADDRESS_STREET4 = 'street4';

    /**
     * @var string
     */
    public const USER_PROFILE_ADDRESS_STREET_NUMBER = 'streetNumber';

    /**
     * @var string
     */
    public const USER_PROFILE_ADDRESS_FLAT = 'flatNo';

    /**
     * @var string
     */
    public const USER_PROFILE_ADDRESS_ENTRANCE = 'entrance';

    /**
     * @var string
     */
    public const USER_PROFILE_ADDRESS_CITY = 'city';

    /**
     * @var string
     */
    public const USER_PROFILE_ADDRESS_POSTAL_CODE = 'postalCode';

    /**
     * @var string
     */
    public const USER_PROFILE_ADDRESS_COUNTRY = 'country';

    /**
     * @var string
     */
    public const USER_PROFILE_ADDRESS_COUNTRY_CODE = 'countryCode';

    /**
     * @var string
     */
    public const AVAILABLE_PLANS = 'availableInstallmentPlans';

    /**
     * @var string
     */
    public const BASKET_AMOUNT = 'basketAmount';

    /**
     * @var string
     */
    public const NUMBER_OF_INSTALLMENTS = 'numberOfInstallments';

    /**
     * @var string
     */
    public const INSTALLMENT_AMOUNT = 'installmentAmount';

    /**
     * @var string
     */
    public const FIRST_INSTALLMENT_AMOUNT = 'firstInstallmentAmount';

    /**
     * @var string
     */
    public const LAST_INSTALLMENT_AMOUNT = 'lastInstallmentAmount';

    /**
     * @var string
     */
    public const INTEREST_RATE = 'interestRate';

    /**
     * @var string
     */
    public const EFFECTIVE_INTEREST_RATE = 'effectiveInterestRate';

    /**
     * @var string
     */
    public const EFFECTIVE_ANNUAL_PERCENTAGE_RATE = 'effectiveAnnualPercentageRate';

    /**
     * @var string
     */
    public const TOTAL_INTEREST_AMOUNT = 'totalInterestAmount';

    /**
     * @var string
     */
    public const STARTUP_FEE = 'startupFee';

    /**
     * @var string
     */
    public const MONTHLY_FEE = 'monthlyFee';

    /**
     * @var string
     */
    public const TOTAL_AMOUNT = 'totalAmount';

    /**
     * @var string
     */
    public const INSTALLMENT_PROFILE_NUMBER = 'installmentProfileNumber';

    /**
     * @var string
     */
    public const READ_MORE = 'readMore';

    /**
     * @var string
     */
    public const API_VERSION = 'version';
}

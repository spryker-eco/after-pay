<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Shared\Afterpay;

interface AfterpayApiConstants
{

    /** Transactional calls */
    const TRANSACTION_OUTCOME = 'outcome';
    const TRANSACTION_RESERVATION_ID = 'reservationId';
    const TRANSACTION_CHECKOUT_ID = 'checkoutId';

    /** Available payment methods */
    const PAYMENT_METHODS = 'paymentMethods';
    const ADDITIONAL_RESPONSE_INFO = 'additionalResponseInfo';
    const RISK_CHECK_CODE = 'rsS_RiskCheck_ResultCode';
    const CUSTOMER = 'customer';
    const CUSTOMER_NUMBER = 'customer';

    /** Capture call */
    const CAPTURE_CAPTURED_AMOUNT = 'capturedAmount';
    const CAPTURE_AUTHORIZED_AMOUNT = 'authorizedAmount';
    const CAPTURE_REMAINING_AUTHORIZED_AMOUNT = 'remainingAuthorizedAmount';
    const CAPTURE_CAPTURE_NUMBER = 'captureNumber';

    /** Cancellation (void) call */
    const CANCEL_CAPTURED_AMOUNT = 'totalCapturedAmount';
    const CANCEL_AUTHORIZED_AMOUNT = 'totalAuthorizedAmount';

    /** Validate bank account call */
    const VALIDATE_BANK_ACCOUNT_IS_VALID = 'isValid';

    /** Validate address call */
    const VALIDATE_ADDRESS_IS_VALID = 'isValid';
    const CORRECTED_ADDRESS = 'correctedAddress';

    /** Lookup customer call */
    const USER_PROFILES = 'userProfiles';
    const USER_PROFILE_FIRST_NAME = 'firstName';
    const USER_PROFILE_LAST_NAME = 'lastName';
    const USER_PROFILE_MOBILE_NUMBER = 'mobileNumber';
    const USER_PROFILE_EMAIL = 'eMail';
    const USER_PROFILE_LANGUAGE_CODE = 'languageCode';
    const USER_PROFILE_ADDRESS_LIST = 'addressList';

    const USER_PROFILE_ADDRESS_STREET = 'street';
    const USER_PROFILE_ADDRESS_STREET2 = 'street2';
    const USER_PROFILE_ADDRESS_STREET3 = 'street3';
    const USER_PROFILE_ADDRESS_STREET4 = 'street4';
    const USER_PROFILE_ADDRESS_STREET_NUMBER = 'streetNumber';
    const USER_PROFILE_ADDRESS_FLAT = 'flatNo';
    const USER_PROFILE_ADDRESS_ENTRANCE = 'entrance';
    const USER_PROFILE_ADDRESS_CITY = 'city';
    const USER_PROFILE_ADDRESS_POSTAL_CODE = 'postalCode';
    const USER_PROFILE_ADDRESS_COUNTRY = 'country';
    const USER_PROFILE_ADDRESS_COUNTRY_CODE = 'countryCode';

    /** Lookup installment plans call */
    const AVAILABLE_PLANS = 'availableInstallmentPlans';
    const BASKET_AMOUNT = "basketAmount";
    const NUMBER_OF_INSTALLMENTS = "numberOfInstallments";
    const INSTALLMENT_AMOUNT = "installmentAmount";
    const FIRST_INSTALLMENT_AMOUNT = "firstInstallmentAmount";
    const LAST_INSTALLMENT_AMOUNT = "lastInstallmentAmount";
    const INTEREST_RATE = "interestRate";
    const EFFECTIVE_INTEREST_RATE = "effectiveInterestRate";
    const EFFECTIVE_ANNUAL_PERCENTAGE_RATE = "effectiveAnnualPercentageRate";
    const TOTAL_INTEREST_AMOUNT = "totalInterestAmount";
    const STARTUP_FEE = "startupFee";
    const MONTHLY_FEE = "monthlyFee";
    const TOTAL_AMOUNT = "totalAmount";
    const INSTALLMENT_PROFILE_NUMBER = "installmentProfileNumber";
    const READ_MORE = "readMore";

    /** Technical calls */
    const API_VERSION = 'version';

}

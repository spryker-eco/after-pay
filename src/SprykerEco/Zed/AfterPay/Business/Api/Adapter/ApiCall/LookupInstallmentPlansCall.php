<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall;

use Generated\Shared\Transfer\AfterPayInstallmentPlansRequestTransfer;
use Generated\Shared\Transfer\AfterPayInstallmentPlansResponseTransfer;
use Generated\Shared\Transfer\AfterPayInstallmentPlanTransfer;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use SprykerEco\Shared\AfterPay\AfterPayApiRequestConfig;
use SprykerEco\Zed\AfterPay\AfterPayConfig;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\Client\ClientInterface;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\Converter\TransferToCamelCaseArrayConverterInterface;
use SprykerEco\Zed\AfterPay\Business\Exception\ApiHttpRequestException;
use SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToMoneyFacadeInterface;
use SprykerEco\Zed\AfterPay\Dependency\Service\AfterPayToUtilEncodingServiceInterface;

class LookupInstallmentPlansCall extends AbstractApiCall implements LookupInstallmentPlansCallInterface
{
    /**
     * @var \SprykerEco\Zed\AfterPay\Business\Api\Adapter\Client\ClientInterface
     */
    protected $client;

    /**
     * @var \SprykerEco\Zed\AfterPay\Dependency\Service\AfterPayToUtilTextServiceInterface
     */
    protected $utilText;

    /**
     * @var \SprykerEco\Zed\AfterPay\AfterPayConfig
     */
    protected $config;

    /**
     * @var \SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToMoneyFacadeInterface
     */
    protected $money;

    /**
     * @param \SprykerEco\Zed\AfterPay\Business\Api\Adapter\Client\ClientInterface $client
     * @param \SprykerEco\Zed\AfterPay\Business\Api\Adapter\Converter\TransferToCamelCaseArrayConverterInterface $transferConverter
     * @param \SprykerEco\Zed\AfterPay\Dependency\Service\AfterPayToUtilEncodingServiceInterface $utilEncoding
     * @param \SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToMoneyFacadeInterface $money
     * @param \SprykerEco\Zed\AfterPay\AfterPayConfig $config
     */
    public function __construct(
        ClientInterface $client,
        TransferToCamelCaseArrayConverterInterface $transferConverter,
        AfterPayToUtilEncodingServiceInterface $utilEncoding,
        AfterPayToMoneyFacadeInterface $money,
        AfterPayConfig $config
    ) {
        $this->client = $client;
        $this->utilEncoding = $utilEncoding;
        $this->config = $config;
        $this->transferConverter = $transferConverter;
        $this->money = $money;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayInstallmentPlansResponseTransfer
     */
    public function execute(AfterPayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer): AfterPayInstallmentPlansResponseTransfer
    {
        $jsonRequest = $this->buildJsonRequestFromTransferObject($installmentPlansRequestTransfer);

        try {
            $jsonResponse = $this->client->sendPost(
                $this->config->getLookupInstallmentPlansApiEndpointUrl(),
                $jsonRequest
            );
        } catch (ApiHttpRequestException $apiHttpRequestException) {
            $this->logApiException($apiHttpRequestException);
            $jsonResponse = '[]';
        }

        return $this->buildLookupCustomerResponseTransfer($jsonResponse);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer
     *
     * @return string
     */
    protected function buildJsonRequestFromTransferObject(AbstractTransfer $installmentPlansRequestTransfer): string
    {
        $this->convertIntegerFieldsToDecimal($installmentPlansRequestTransfer);

        return parent::buildJsonRequestFromTransferObject($installmentPlansRequestTransfer);
    }

    /**
     * @param string $jsonResponse
     *
     * @return \Generated\Shared\Transfer\AfterPayInstallmentPlansResponseTransfer
     */
    protected function buildLookupCustomerResponseTransfer(string $jsonResponse): AfterPayInstallmentPlansResponseTransfer
    {
        $jsonResponseArray = $this->utilEncoding->decodeJson($jsonResponse, true);

        $responseTransfer = new AfterPayInstallmentPlansResponseTransfer();

        if (!isset($jsonResponseArray[AfterPayApiRequestConfig::AVAILABLE_PLANS])) {
            return $responseTransfer;
        }

        foreach ($jsonResponseArray[AfterPayApiRequestConfig::AVAILABLE_PLANS] as $planArray) {
            $responseTransfer->addInstallmentPlan(
                $this->buildInstallmentPlanTransfer($planArray)
            );
        }

        return $responseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer
     *
     * @return void
     */
    protected function convertIntegerFieldsToDecimal(AfterPayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer): void
    {
        $amount = (int)$this->money->convertIntegerToDecimal($installmentPlansRequestTransfer->getAmount());
        $installmentPlansRequestTransfer->setAmount($amount);
    }

    /**
     * @param array $installmentPlanArray
     *
     * @return \Generated\Shared\Transfer\AfterPayInstallmentPlanTransfer
     */
    protected function buildInstallmentPlanTransfer(array $installmentPlanArray): AfterPayInstallmentPlanTransfer
    {
        return (new AfterPayInstallmentPlanTransfer())
            ->setBasketAmount(
                $this->money->convertDecimalToInteger(
                    $installmentPlanArray[AfterPayApiRequestConfig::BASKET_AMOUNT]
                )
            )
            ->setInstallmentAmount(
                $this->money->convertDecimalToInteger(
                    $installmentPlanArray[AfterPayApiRequestConfig::INSTALLMENT_AMOUNT]
                )
            )
            ->setFirstInstallmentAmount(
                $this->money->convertDecimalToInteger(
                    $installmentPlanArray[AfterPayApiRequestConfig::FIRST_INSTALLMENT_AMOUNT]
                )
            )
            ->setLastInstallmentAmount(
                $this->money->convertDecimalToInteger(
                    $installmentPlanArray[AfterPayApiRequestConfig::LAST_INSTALLMENT_AMOUNT]
                )
            )
            ->setTotalAmount(
                $this->money->convertDecimalToInteger(
                    $installmentPlanArray[AfterPayApiRequestConfig::TOTAL_AMOUNT]
                )
            )
            ->setNumberOfInstallments(
                $installmentPlanArray[AfterPayApiRequestConfig::NUMBER_OF_INSTALLMENTS]
            )
            ->setInterestRate(
                $installmentPlanArray[AfterPayApiRequestConfig::INTEREST_RATE]
            )
            ->setEffectiveInterestRate(
                $installmentPlanArray[AfterPayApiRequestConfig::EFFECTIVE_INTEREST_RATE]
            )
            ->setEffectiveAnnualPercentageRate(
                $installmentPlanArray[AfterPayApiRequestConfig::EFFECTIVE_ANNUAL_PERCENTAGE_RATE]
            )
            ->setTotalInterestAmount(
                $installmentPlanArray[AfterPayApiRequestConfig::TOTAL_INTEREST_AMOUNT]
            )
            ->setStartupFee(
                $installmentPlanArray[AfterPayApiRequestConfig::STARTUP_FEE]
            )
            ->setMonthlyFee(
                $installmentPlanArray[AfterPayApiRequestConfig::MONTHLY_FEE]
            )
            ->setInstallmentProfileNumber(
                $installmentPlanArray[AfterPayApiRequestConfig::INSTALLMENT_PROFILE_NUMBER]
            )
            ->setReadMore(
                $installmentPlanArray[AfterPayApiRequestConfig::READ_MORE]
            );
    }
}

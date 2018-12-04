<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall;

use Generated\Shared\Transfer\AfterpayInstallmentPlansRequestTransfer;
use Generated\Shared\Transfer\AfterpayInstallmentPlansResponseTransfer;
use Generated\Shared\Transfer\AfterpayInstallmentPlanTransfer;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use SprykerEco\Shared\Afterpay\AfterpayApiRequestConfig;
use SprykerEco\Zed\Afterpay\AfterpayConfig;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\Client\ClientInterface;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\Converter\TransferToCamelCaseArrayConverterInterface;
use SprykerEco\Zed\Afterpay\Business\Exception\ApiHttpRequestException;
use SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToMoneyInterface;
use SprykerEco\Zed\Afterpay\Dependency\Service\AfterpayToUtilEncodingInterface;

class LookupInstallmentPlansCall extends AbstractApiCall implements LookupInstallmentPlansCallInterface
{
    /**
     * @var \SprykerEco\Zed\Afterpay\Business\Api\Adapter\Client\ClientInterface
     */
    protected $client;

    /**
     * @var \SprykerEco\Zed\Afterpay\Dependency\Service\AfterpayToUtilTextInterface
     */
    protected $utilText;

    /**
     * @var \SprykerEco\Zed\Afterpay\AfterpayConfig
     */
    protected $config;

    /**
     * @var \SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToMoneyInterface
     */
    protected $money;

    /**
     * @param \SprykerEco\Zed\Afterpay\Business\Api\Adapter\Client\ClientInterface $client
     * @param \SprykerEco\Zed\Afterpay\Business\Api\Adapter\Converter\TransferToCamelCaseArrayConverterInterface $transferConverter
     * @param \SprykerEco\Zed\Afterpay\Dependency\Service\AfterpayToUtilEncodingInterface $utilEncoding
     * @param \SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToMoneyInterface $money
     * @param \SprykerEco\Zed\Afterpay\AfterpayConfig $config
     */
    public function __construct(
        ClientInterface $client,
        TransferToCamelCaseArrayConverterInterface $transferConverter,
        AfterpayToUtilEncodingInterface $utilEncoding,
        AfterpayToMoneyInterface $money,
        AfterpayConfig $config
    ) {
        $this->client = $client;
        $this->utilEncoding = $utilEncoding;
        $this->config = $config;
        $this->transferConverter = $transferConverter;
        $this->money = $money;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayInstallmentPlansResponseTransfer
     */
    public function execute(AfterpayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer): AfterpayInstallmentPlansResponseTransfer
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
     * @param \Generated\Shared\Transfer\AfterpayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer
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
     * @return \Generated\Shared\Transfer\AfterpayInstallmentPlansResponseTransfer
     */
    protected function buildLookupCustomerResponseTransfer(string $jsonResponse): AfterpayInstallmentPlansResponseTransfer
    {
        $jsonResponseArray = $this->utilEncoding->decodeJson($jsonResponse, true);

        $responseTransfer = new AfterpayInstallmentPlansResponseTransfer();

        if (!isset($jsonResponseArray[AfterpayApiRequestConfig::AVAILABLE_PLANS])) {
            return $responseTransfer;
        }

        foreach ($jsonResponseArray[AfterpayApiRequestConfig::AVAILABLE_PLANS] as $planArray) {
            $responseTransfer->addInstallmentPlan(
                $this->buildInstallmentPlanTransfer($planArray)
            );
        }

        return $responseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer
     *
     * @return void
     */
    protected function convertIntegerFieldsToDecimal(AfterpayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer): void
    {
        $integerAmount = $installmentPlansRequestTransfer->getAmount();
        $installmentPlansRequestTransfer->setAmount(
            (string)$this->money->convertIntegerToDecimal($integerAmount)
        );
    }

    /**
     * @param array $installmentPlanArray
     *
     * @return \Generated\Shared\Transfer\AfterpayInstallmentPlanTransfer
     */
    protected function buildInstallmentPlanTransfer(array $installmentPlanArray): AfterpayInstallmentPlanTransfer
    {
        $installmentPlanTransfer = new AfterpayInstallmentPlanTransfer();

        $installmentPlanTransfer
            ->setBasketAmount(
                $this->money->convertDecimalToInteger(
                    $installmentPlanArray[AfterpayApiRequestConfig::BASKET_AMOUNT]
                )
            )
            ->setInstallmentAmount(
                $this->money->convertDecimalToInteger(
                    $installmentPlanArray[AfterpayApiRequestConfig::INSTALLMENT_AMOUNT]
                )
            )
            ->setFirstInstallmentAmount(
                $this->money->convertDecimalToInteger(
                    $installmentPlanArray[AfterpayApiRequestConfig::FIRST_INSTALLMENT_AMOUNT]
                )
            )
            ->setLastInstallmentAmount(
                $this->money->convertDecimalToInteger(
                    $installmentPlanArray[AfterpayApiRequestConfig::LAST_INSTALLMENT_AMOUNT]
                )
            )
            ->setTotalAmount(
                $this->money->convertDecimalToInteger(
                    $installmentPlanArray[AfterpayApiRequestConfig::TOTAL_AMOUNT]
                )
            )
            ->setNumberOfInstallments(
                $installmentPlanArray[AfterpayApiRequestConfig::NUMBER_OF_INSTALLMENTS]
            )
            ->setInterestRate(
                $installmentPlanArray[AfterpayApiRequestConfig::INTEREST_RATE]
            )
            ->setEffectiveInterestRate(
                $installmentPlanArray[AfterpayApiRequestConfig::EFFECTIVE_INTEREST_RATE]
            )
            ->setEffectiveAnnualPercentageRate(
                $installmentPlanArray[AfterpayApiRequestConfig::EFFECTIVE_ANNUAL_PERCENTAGE_RATE]
            )
            ->setTotalInterestAmount(
                $installmentPlanArray[AfterpayApiRequestConfig::TOTAL_INTEREST_AMOUNT]
            )
            ->setStartupFee(
                $installmentPlanArray[AfterpayApiRequestConfig::STARTUP_FEE]
            )
            ->setMonthlyFee(
                $installmentPlanArray[AfterpayApiRequestConfig::MONTHLY_FEE]
            )
            ->setInstallmentProfileNumber(
                $installmentPlanArray[AfterpayApiRequestConfig::INSTALLMENT_PROFILE_NUMBER]
            )
            ->setReadMore(
                $installmentPlanArray[AfterpayApiRequestConfig::READ_MORE]
            );

        return $installmentPlanTransfer;
    }
}

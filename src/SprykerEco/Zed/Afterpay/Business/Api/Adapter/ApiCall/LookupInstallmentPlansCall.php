<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall;

use Generated\Shared\Transfer\AfterpayInstallmentPlansRequestTransfer;
use Generated\Shared\Transfer\AfterpayInstallmentPlansResponseTransfer;
use Generated\Shared\Transfer\AfterpayInstallmentPlanTransfer;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use SprykerEco\Shared\Afterpay\AfterpayApiConstants;
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
    public function execute(AfterpayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer)
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
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer|\Generated\Shared\Transfer\AfterpayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer
     *
     * @return string
     */
    protected function buildJsonRequestFromTransferObject(AbstractTransfer $installmentPlansRequestTransfer)
    {
        $this->convertIntegerFieldsToDecimal($installmentPlansRequestTransfer);

        return parent::buildJsonRequestFromTransferObject($installmentPlansRequestTransfer);
    }

    /**
     * @param string $jsonResponse
     *
     * @return \Generated\Shared\Transfer\AfterpayInstallmentPlansResponseTransfer
     */
    protected function buildLookupCustomerResponseTransfer($jsonResponse)
    {
        $jsonResponseArray = $this->utilEncoding->decodeJson($jsonResponse, true);

        $responseTransfer = new AfterpayInstallmentPlansResponseTransfer();

        if (!isset($jsonResponseArray[AfterpayApiConstants::AVAILABLE_PLANS])) {
            return $responseTransfer;
        }

        foreach ($jsonResponseArray[AfterpayApiConstants::AVAILABLE_PLANS] as $planArray) {
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
    protected function convertIntegerFieldsToDecimal(
        AfterpayInstallmentPlansRequestTransfer $installmentPlansRequestTransfer
    ) {
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
    protected function buildInstallmentPlanTransfer(array $installmentPlanArray)
    {
        $installmentPlanTransfer = new AfterpayInstallmentPlanTransfer();

        $installmentPlanTransfer
            ->setBasketAmount(
                $this->money->convertDecimalToInteger(
                    $installmentPlanArray[AfterpayApiConstants::BASKET_AMOUNT]
                )
            )
            ->setInstallmentAmount(
                $this->money->convertDecimalToInteger(
                    $installmentPlanArray[AfterpayApiConstants::INSTALLMENT_AMOUNT]
                )
            )
            ->setFirstInstallmentAmount(
                $this->money->convertDecimalToInteger(
                    $installmentPlanArray[AfterpayApiConstants::FIRST_INSTALLMENT_AMOUNT]
                )
            )
            ->setLastInstallmentAmount(
                $this->money->convertDecimalToInteger(
                    $installmentPlanArray[AfterpayApiConstants::LAST_INSTALLMENT_AMOUNT]
                )
            )
            ->setTotalAmount(
                $this->money->convertDecimalToInteger(
                    $installmentPlanArray[AfterpayApiConstants::TOTAL_AMOUNT]
                )
            )
            ->setNumberOfInstallments(
                $installmentPlanArray[AfterpayApiConstants::NUMBER_OF_INSTALLMENTS]
            )
            ->setInterestRate(
                $installmentPlanArray[AfterpayApiConstants::INTEREST_RATE]
            )
            ->setEffectiveInterestRate(
                $installmentPlanArray[AfterpayApiConstants::EFFECTIVE_INTEREST_RATE]
            )
            ->setEffectiveAnnualPercentageRate(
                $installmentPlanArray[AfterpayApiConstants::EFFECTIVE_ANNUAL_PERCENTAGE_RATE]
            )
            ->setTotalInterestAmount(
                $installmentPlanArray[AfterpayApiConstants::TOTAL_INTEREST_AMOUNT]
            )
            ->setStartupFee(
                $installmentPlanArray[AfterpayApiConstants::STARTUP_FEE]
            )
            ->setMonthlyFee(
                $installmentPlanArray[AfterpayApiConstants::MONTHLY_FEE]
            )
            ->setInstallmentProfileNumber(
                $installmentPlanArray[AfterpayApiConstants::INSTALLMENT_PROFILE_NUMBER]
            )
            ->setReadMore(
                $installmentPlanArray[AfterpayApiConstants::READ_MORE]
            );

        return $installmentPlanTransfer;
    }
}

<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business;

use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use SprykerEco\Shared\Afterpay\AfterpayConfig;
use SprykerEco\Shared\Afterpay\AfterpayConstants;
use SprykerEco\Zed\Afterpay\AfterpayDependencyProvider;
use SprykerEco\Zed\Afterpay\Business\AdditionalServices\Handler\LookupCustomerHandler;
use SprykerEco\Zed\Afterpay\Business\AdditionalServices\Handler\LookupCustomerHandlerInterface;
use SprykerEco\Zed\Afterpay\Business\AdditionalServices\Handler\LookupInstallmentPlansHandler;
use SprykerEco\Zed\Afterpay\Business\AdditionalServices\Handler\LookupInstallmentPlansHandlerInterface;
use SprykerEco\Zed\Afterpay\Business\AdditionalServices\Handler\ValidateBankAccountHandler;
use SprykerEco\Zed\Afterpay\Business\AdditionalServices\Handler\ValidateBankAccountHandlerInterface;
use SprykerEco\Zed\Afterpay\Business\AdditionalServices\Handler\ValidateCustomerHandler;
use SprykerEco\Zed\Afterpay\Business\AdditionalServices\Handler\ValidateCustomerHandlerInterface;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\AdapterFactory;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\AdapterFactoryInterface;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\AdapterInterface;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\AfterpayApiAdapter;
use SprykerEco\Zed\Afterpay\Business\Hook\PostSaveHook;
use SprykerEco\Zed\Afterpay\Business\Hook\PostSaveHookInterface;
use SprykerEco\Zed\Afterpay\Business\Order\Saver;
use SprykerEco\Zed\Afterpay\Business\Order\SaverInterface;
use SprykerEco\Zed\Afterpay\Business\Payment\Handler\RiskCheck\AvailablePaymentMethodsHandler;
use SprykerEco\Zed\Afterpay\Business\Payment\Handler\RiskCheck\AvailablePaymentMethodsHandlerInterface;
use SprykerEco\Zed\Afterpay\Business\Payment\Mapper\OrderToRequestTransfer;
use SprykerEco\Zed\Afterpay\Business\Payment\Mapper\OrderToRequestTransferInterface;
use SprykerEco\Zed\Afterpay\Business\Payment\Mapper\QuoteToRequestTransfer;
use SprykerEco\Zed\Afterpay\Business\Payment\Mapper\QuoteToRequestTransferInterface;
use SprykerEco\Zed\Afterpay\Business\Payment\PaymentReader;
use SprykerEco\Zed\Afterpay\Business\Payment\PaymentReaderInterface;
use SprykerEco\Zed\Afterpay\Business\Payment\PaymentWriter;
use SprykerEco\Zed\Afterpay\Business\Payment\PaymentWriterInterface;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Authorize\PaymentAuthorizeWriter;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Authorize\PaymentAuthorizeWriterInterface;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Authorize\RequestBuilder\AuthorizeRequestBuilderInterface;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Authorize\RequestBuilder\OneStepAuthorizeRequestBuilder;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Authorize\RequestBuilder\TwoStepsAuthorizeRequestBuilder;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\AuthorizeTransaction;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\AuthorizeTransactionInterface;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Cancel\CancelRequestBuilder;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Cancel\CancelRequestBuilderInterface;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\CancelTransaction;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\CancelTransactionInterface;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Capture\CaptureRequestBuilder;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Capture\CaptureRequestBuilderInterface;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\CaptureTransaction;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\CaptureTransactionInterface;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Handler\AuthorizeTransactionHandler;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Handler\AuthorizeTransactionHandlerInterface;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Handler\CancelTransactionHandler;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Handler\CancelTransactionHandlerInterface;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Handler\CaptureTransactionHandler;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Handler\CaptureTransactionHandlerInterface;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Handler\RefundTransactionHandler;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Handler\RefundTransactionHandlerInterface;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Logger\TransactionLogger;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Logger\TransactionLoggerInterface;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\PriceToPayProvider;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\PriceToPayProviderInterface;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Refund\RefundRequestBuilder;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Refund\RefundRequestBuilderInterface;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\RefundTransaction;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\RefundTransactionInterface;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\TransactionLogReader;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\TransactionLogReaderInterface;
use SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToCustomerInterface;
use SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToMoneyInterface;
use SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToPaymentInterface;
use SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToStoreInterface;
use SprykerEco\Zed\Afterpay\Dependency\Service\AfterpayToUtilEncodingInterface;

/**
 * @method \SprykerEco\Zed\Afterpay\Persistence\AfterpayQueryContainerInterface getQueryContainer()
 * @method \SprykerEco\Zed\Afterpay\AfterpayConfig getConfig()
 */
class AfterpayBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Payment\Handler\RiskCheck\AvailablePaymentMethodsHandlerInterface
     */
    public function createAvailablePaymentMethodsHandler(): AvailablePaymentMethodsHandlerInterface
    {
        return new AvailablePaymentMethodsHandler(
            $this->createApiAdapter(),
            $this->createQuoteToRequestMapper()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Order\SaverInterface
     */
    public function createOrderSaver(): SaverInterface
    {
        return new Saver($this->getConfig());
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Handler\AuthorizeTransactionHandlerInterface
     */
    public function createAuthorizeTransactionHandler(): AuthorizeTransactionHandlerInterface
    {
        return new AuthorizeTransactionHandler(
            $this->createAuthorizeTransaction(),
            $this->getAuthorizeRequestBuilder(),
            $this->createPaymentWriter(),
            $this->createPriceToPayProvider()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Handler\CaptureTransactionHandlerInterface
     */
    public function createCaptureTransactionHandler(): CaptureTransactionHandlerInterface
    {
        return new CaptureTransactionHandler(
            $this->createCaptureTransaction(),
            $this->createPaymentReader(),
            $this->createPaymentWriter(),
            $this->createCaptureRequestBuilder()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Handler\RefundTransactionHandlerInterface
     */
    public function createRefundTransactionHandler(): RefundTransactionHandlerInterface
    {
        return new RefundTransactionHandler(
            $this->createRefundTransaction(),
            $this->createPaymentReader(),
            $this->createPaymentWriter(),
            $this->getMoneyFacade(),
            $this->createRefundRequestBuilder()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Handler\CancelTransactionHandlerInterface
     */
    public function createCancelTransactionHandler(): CancelTransactionHandlerInterface
    {
        return new CancelTransactionHandler(
            $this->createCancelTransaction(),
            $this->createPaymentReader(),
            $this->createPaymentWriter(),
            $this->getMoneyFacade(),
            $this->createCancelRequestBuilder()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Payment\PaymentReaderInterface
     */
    public function createPaymentReader(): PaymentReaderInterface
    {
        return new PaymentReader($this->getQueryContainer());
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Hook\PostSaveHookInterface
     */
    public function createPostSaveHook(): PostSaveHookInterface
    {
        return new PostSaveHook(
            $this->createTransactionLogReader(),
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Capture\CaptureRequestBuilderInterface
     */
    public function createCaptureRequestBuilder(): CaptureRequestBuilderInterface
    {
        return new CaptureRequestBuilder(
            $this->createOrderToRequestMapper(),
            $this->getMoneyFacade()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\CaptureTransactionInterface
     */
    public function createCaptureTransaction(): CaptureTransactionInterface
    {
        return new CaptureTransaction(
            $this->createTransactionLogger(),
            $this->createApiAdapter()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\RefundTransactionInterface
     */
    public function createRefundTransaction(): RefundTransactionInterface
    {
        return new RefundTransaction(
            $this->createTransactionLogger(),
            $this->createApiAdapter()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\AdditionalServices\Handler\ValidateCustomerHandlerInterface
     */
    public function createValidateCustomerHandler(): ValidateCustomerHandlerInterface
    {
        return new ValidateCustomerHandler(
            $this->createApiAdapter(),
            $this->getAfterpayToCustomerBridge()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\AdditionalServices\Handler\ValidateBankAccountHandlerInterface
     */
    public function createValidateBankAccountHandler(): ValidateBankAccountHandlerInterface
    {
        return new ValidateBankAccountHandler($this->createApiAdapter());
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\AdditionalServices\Handler\LookupCustomerHandlerInterface
     */
    public function createLookupCustomerHandler(): LookupCustomerHandlerInterface
    {
        return new LookupCustomerHandler($this->createApiAdapter());
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\AdditionalServices\Handler\LookupInstallmentPlansHandlerInterface
     */
    public function createLookupInstallmentPlansHandler(): LookupInstallmentPlansHandlerInterface
    {
        return new LookupInstallmentPlansHandler($this->createApiAdapter());
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Payment\PaymentWriterInterface
     */
    public function createPaymentWriter(): PaymentWriterInterface
    {
        return new PaymentWriter($this->getQueryContainer());
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\AuthorizeTransactionInterface
     */
    public function createAuthorizeTransaction(): AuthorizeTransactionInterface
    {
        return new AuthorizeTransaction(
            $this->createTransactionLogger(),
            $this->createApiAdapter(),
            $this->createPaymentAuthorizeWriter()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\CancelTransactionInterface
     */
    public function createCancelTransaction(): CancelTransactionInterface
    {
        return new CancelTransaction(
            $this->createTransactionLogger(),
            $this->createApiAdapter()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Logger\TransactionLoggerInterface
     */
    public function createTransactionLogger(): TransactionLoggerInterface
    {
        return new TransactionLogger($this->getUtilEncodingService());
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Api\Adapter\AdapterInterface
     */
    public function createApiAdapter(): AdapterInterface
    {
        return new AfterpayApiAdapter($this->createAdapterFactory());
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Authorize\PaymentAuthorizeWriterInterface
     */
    public function createPaymentAuthorizeWriter(): PaymentAuthorizeWriterInterface
    {
        return new PaymentAuthorizeWriter($this->getQueryContainer());
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Api\Adapter\AdapterFactoryInterface
     */
    public function createAdapterFactory(): AdapterFactoryInterface
    {
        return new AdapterFactory();
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Authorize\RequestBuilder\AuthorizeRequestBuilderInterface
     */
    public function getAuthorizeRequestBuilder(): AuthorizeRequestBuilderInterface
    {
        $authorizeWorkflow = $this->getConfig()->getAfterpayAuthorizeWorkflow();

        switch ($authorizeWorkflow) {
            case AfterpayConfig::AFTERPAY_AUTHORIZE_WORKFLOW_ONE_STEP:
                return $this->createOneStepAuthorizeRequestBuilder();
            case AfterpayConfig::AFTERPAY_AUTHORIZE_WORKFLOW_TWO_STEPS:
                return $this->createTwoStepsAuthorizeRequestBuilder();
            default:
                return $this->createOneStepAuthorizeRequestBuilder();
        }
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Cancel\CancelRequestBuilderInterface
     */
    public function createCancelRequestBuilder(): CancelRequestBuilderInterface
    {
        return new CancelRequestBuilder(
            $this->createOrderToRequestMapper(),
            $this->getMoneyFacade()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Refund\RefundRequestBuilderInterface
     */
    public function createRefundRequestBuilder(): RefundRequestBuilderInterface
    {
        return new RefundRequestBuilder(
            $this->createOrderToRequestMapper(),
            $this->getMoneyFacade()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Authorize\RequestBuilder\AuthorizeRequestBuilderInterface
     */
    public function createOneStepAuthorizeRequestBuilder(): AuthorizeRequestBuilderInterface
    {
        return new OneStepAuthorizeRequestBuilder($this->createOrderToRequestMapper());
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Payment\Mapper\OrderToRequestTransferInterface
     */
    public function createOrderToRequestMapper(): OrderToRequestTransferInterface
    {
        return new OrderToRequestTransfer(
            $this->getMoneyFacade(),
            $this->getStoreFacade(),
            $this->createPriceToPayProvider()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Authorize\RequestBuilder\AuthorizeRequestBuilderInterface
     */
    public function createTwoStepsAuthorizeRequestBuilder(): AuthorizeRequestBuilderInterface
    {
        return new TwoStepsAuthorizeRequestBuilder($this->createOrderToRequestMapper());
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Dependency\Service\AfterpayToUtilEncodingInterface
     */
    public function getUtilEncodingService(): AfterpayToUtilEncodingInterface
    {
        return $this->getProvidedDependency(AfterpayDependencyProvider::SERVICE_UTIL_ENCODING);
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Payment\Mapper\QuoteToRequestTransferInterface
     */
    public function createQuoteToRequestMapper(): QuoteToRequestTransferInterface
    {
        return new QuoteToRequestTransfer(
            $this->getMoneyFacade(),
            $this->getStoreFacade()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToMoneyInterface
     */
    public function getMoneyFacade(): AfterpayToMoneyInterface
    {
        return $this->getProvidedDependency(AfterpayDependencyProvider::FACADE_MONEY);
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToCustomerInterface
     */
    public function getAfterpayToCustomerBridge(): AfterpayToCustomerInterface
    {
        return $this->getProvidedDependency(AfterpayDependencyProvider::FACADE_CUSTOMER);
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToStoreInterface
     */
    public function getStoreFacade(): AfterpayToStoreInterface
    {
        return $this->getProvidedDependency(AfterpayDependencyProvider::FACADE_STORE);
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToPaymentInterface
     */
    public function getPaymentFacade(): AfterpayToPaymentInterface
    {
        return $this->getProvidedDependency(AfterpayDependencyProvider::FACADE_PAYMENT);
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\TransactionLogReaderInterface
     */
    public function createTransactionLogReader(): TransactionLogReaderInterface
    {
        return new TransactionLogReader($this->getQueryContainer());
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\PriceToPayProviderInterface
     */
    public function createPriceToPayProvider(): PriceToPayProviderInterface
    {
        return new PriceToPayProvider($this->getPaymentFacade());
    }
}

<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business;

use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use SprykerEco\Shared\AfterPay\AfterPayConfig;
use SprykerEco\Zed\AfterPay\AfterPayDependencyProvider;
use SprykerEco\Zed\AfterPay\Business\AdditionalServices\Handler\LookupCustomerHandler;
use SprykerEco\Zed\AfterPay\Business\AdditionalServices\Handler\LookupCustomerHandlerInterface;
use SprykerEco\Zed\AfterPay\Business\AdditionalServices\Handler\LookupInstallmentPlansHandler;
use SprykerEco\Zed\AfterPay\Business\AdditionalServices\Handler\LookupInstallmentPlansHandlerInterface;
use SprykerEco\Zed\AfterPay\Business\AdditionalServices\Handler\ValidateBankAccountHandler;
use SprykerEco\Zed\AfterPay\Business\AdditionalServices\Handler\ValidateBankAccountHandlerInterface;
use SprykerEco\Zed\AfterPay\Business\AdditionalServices\Handler\ValidateCustomerHandler;
use SprykerEco\Zed\AfterPay\Business\AdditionalServices\Handler\ValidateCustomerHandlerInterface;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\AdapterFactory;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\AdapterFactoryInterface;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\AdapterInterface;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\AfterPayApiAdapter;
use SprykerEco\Zed\AfterPay\Business\Exception\InvalidAfterPayAuthorizeRequestBuilderException;
use SprykerEco\Zed\AfterPay\Business\Exception\InvalidAfterPayPaymentMethodsFilterException;
use SprykerEco\Zed\AfterPay\Business\Hook\PostSaveHook;
use SprykerEco\Zed\AfterPay\Business\Hook\PostSaveHookInterface;
use SprykerEco\Zed\AfterPay\Business\Mapper\AfterPayMapper;
use SprykerEco\Zed\AfterPay\Business\Mapper\AfterPayMapperInterface;
use SprykerEco\Zed\AfterPay\Business\Order\Saver;
use SprykerEco\Zed\AfterPay\Business\Order\SaverInterface;
use SprykerEco\Zed\AfterPay\Business\Payment\Filter\AfterPayPaymentMethodsFilterInterface;
use SprykerEco\Zed\AfterPay\Business\Payment\Filter\OneStepAuthorizePaymentMethodsFilter;
use SprykerEco\Zed\AfterPay\Business\Payment\Filter\Provider\AfterPayPaymentMethodsProvider;
use SprykerEco\Zed\AfterPay\Business\Payment\Filter\Provider\AfterPayPaymentMethodsProviderInterface;
use SprykerEco\Zed\AfterPay\Business\Payment\Filter\TwoStepAuthorizePaymentMethodsFilter;
use SprykerEco\Zed\AfterPay\Business\Payment\Mapper\OrderToRequestTransfer;
use SprykerEco\Zed\AfterPay\Business\Payment\Mapper\OrderToRequestTransferInterface;
use SprykerEco\Zed\AfterPay\Business\Payment\Mapper\QuoteToRequestTransfer;
use SprykerEco\Zed\AfterPay\Business\Payment\Mapper\QuoteToRequestTransferInterface;
use SprykerEco\Zed\AfterPay\Business\Payment\PaymentReader;
use SprykerEco\Zed\AfterPay\Business\Payment\PaymentReaderInterface;
use SprykerEco\Zed\AfterPay\Business\Payment\PaymentWriter;
use SprykerEco\Zed\AfterPay\Business\Payment\PaymentWriterInterface;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Authorize\PaymentAuthorizeWriter;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Authorize\PaymentAuthorizeWriterInterface;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Authorize\RequestBuilder\AuthorizeRequestBuilderInterface;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Authorize\RequestBuilder\OneStepAuthorizeRequestBuilder;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Authorize\RequestBuilder\TwoStepsAuthorizeRequestBuilder;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\AuthorizeTransaction;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\AuthorizeTransactionInterface;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Cancel\CancelRequestBuilder;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Cancel\CancelRequestBuilderInterface;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\CancelTransaction;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\CancelTransactionInterface;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Capture\CaptureRequestBuilder;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Capture\CaptureRequestBuilderInterface;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\CaptureTransaction;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\CaptureTransactionInterface;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Handler\AuthorizeTransactionHandler;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Handler\AuthorizeTransactionHandlerInterface;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Handler\CancelTransactionHandler;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Handler\CancelTransactionHandlerInterface;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Handler\CaptureTransactionHandler;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Handler\CaptureTransactionHandlerInterface;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Handler\RefundTransactionHandler;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Handler\RefundTransactionHandlerInterface;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Logger\TransactionLogger;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Logger\TransactionLoggerInterface;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\PriceToPayProvider;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\PriceToPayProviderInterface;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Refund\RefundRequestBuilder;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Refund\RefundRequestBuilderInterface;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\RefundTransaction;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\RefundTransactionInterface;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\TransactionLogReader;
use SprykerEco\Zed\AfterPay\Business\Payment\Transaction\TransactionLogReaderInterface;
use SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToCustomerFacadeInterface;
use SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToMoneyFacadeInterface;
use SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToPaymentFacadeInterface;
use SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToStoreFacadeInterface;
use SprykerEco\Zed\AfterPay\Dependency\Service\AfterPayToUtilEncodingServiceInterface;

/**
 * @method \SprykerEco\Zed\AfterPay\Persistence\AfterPayQueryContainerInterface getQueryContainer()
 * @method \SprykerEco\Zed\AfterPay\AfterPayConfig getConfig()
 * @method \SprykerEco\Zed\AfterPay\Persistence\AfterPayEntityManagerInterface getEntityManager()
 */
class AfterPayBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Order\SaverInterface
     */
    public function createOrderSaver(): SaverInterface
    {
        return new Saver($this->getConfig());
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Handler\AuthorizeTransactionHandlerInterface
     */
    public function createAuthorizeTransactionHandler(): AuthorizeTransactionHandlerInterface
    {
        return new AuthorizeTransactionHandler(
            $this->createAuthorizeTransaction(),
            $this->getAuthorizeRequestBuilder(),
            $this->createPaymentWriter(),
            $this->createPriceToPayProvider(),
            $this->createAfterPayMapper()
        );
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Handler\CaptureTransactionHandlerInterface
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
     * @return \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Handler\RefundTransactionHandlerInterface
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
     * @return \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Handler\CancelTransactionHandlerInterface
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
     * @return \SprykerEco\Zed\AfterPay\Business\Payment\PaymentReaderInterface
     */
    public function createPaymentReader(): PaymentReaderInterface
    {
        return new PaymentReader($this->getQueryContainer());
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Hook\PostSaveHookInterface
     */
    public function createPostSaveHook(): PostSaveHookInterface
    {
        return new PostSaveHook(
            $this->createTransactionLogReader(),
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Capture\CaptureRequestBuilderInterface
     */
    public function createCaptureRequestBuilder(): CaptureRequestBuilderInterface
    {
        return new CaptureRequestBuilder(
            $this->createOrderToRequestMapper(),
            $this->getMoneyFacade()
        );
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\CaptureTransactionInterface
     */
    public function createCaptureTransaction(): CaptureTransactionInterface
    {
        return new CaptureTransaction(
            $this->createTransactionLogger(),
            $this->createApiAdapter()
        );
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\RefundTransactionInterface
     */
    public function createRefundTransaction(): RefundTransactionInterface
    {
        return new RefundTransaction(
            $this->createTransactionLogger(),
            $this->createApiAdapter()
        );
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\AdditionalServices\Handler\ValidateCustomerHandlerInterface
     */
    public function createValidateCustomerHandler(): ValidateCustomerHandlerInterface
    {
        return new ValidateCustomerHandler(
            $this->createApiAdapter(),
            $this->getAfterPayToCustomerBridge()
        );
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\AdditionalServices\Handler\ValidateBankAccountHandlerInterface
     */
    public function createValidateBankAccountHandler(): ValidateBankAccountHandlerInterface
    {
        return new ValidateBankAccountHandler($this->createApiAdapter());
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\AdditionalServices\Handler\LookupCustomerHandlerInterface
     */
    public function createLookupCustomerHandler(): LookupCustomerHandlerInterface
    {
        return new LookupCustomerHandler($this->createApiAdapter());
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\AdditionalServices\Handler\LookupInstallmentPlansHandlerInterface
     */
    public function createLookupInstallmentPlansHandler(): LookupInstallmentPlansHandlerInterface
    {
        return new LookupInstallmentPlansHandler($this->createApiAdapter());
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Payment\PaymentWriterInterface
     */
    public function createPaymentWriter(): PaymentWriterInterface
    {
        return new PaymentWriter($this->getQueryContainer(), $this->getEntityManager());
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\AuthorizeTransactionInterface
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
     * @return \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\CancelTransactionInterface
     */
    public function createCancelTransaction(): CancelTransactionInterface
    {
        return new CancelTransaction(
            $this->createTransactionLogger(),
            $this->createApiAdapter()
        );
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Logger\TransactionLoggerInterface
     */
    public function createTransactionLogger(): TransactionLoggerInterface
    {
        return new TransactionLogger($this->getUtilEncodingService());
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Api\Adapter\AdapterInterface
     */
    public function createApiAdapter(): AdapterInterface
    {
        return new AfterPayApiAdapter($this->createAdapterFactory());
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Authorize\PaymentAuthorizeWriterInterface
     */
    public function createPaymentAuthorizeWriter(): PaymentAuthorizeWriterInterface
    {
        return new PaymentAuthorizeWriter($this->getQueryContainer());
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Api\Adapter\AdapterFactoryInterface
     */
    public function createAdapterFactory(): AdapterFactoryInterface
    {
        return new AdapterFactory();
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Authorize\RequestBuilder\AuthorizeRequestBuilderInterface
     */
    public function getAuthorizeRequestBuilder(): AuthorizeRequestBuilderInterface
    {
        return $this->createAuthorizeRequestBuilder();
    }

    /**
     * @throws \SprykerEco\Zed\AfterPay\Business\Exception\InvalidAfterPayAuthorizeRequestBuilderException
     *
     * @return \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Authorize\RequestBuilder\AuthorizeRequestBuilderInterface
     */
    public function createAuthorizeRequestBuilder(): AuthorizeRequestBuilderInterface
    {
        $authorizeWorkflow = $this->getConfig()->getAfterPayAuthorizeWorkflow();
        $authorizeRequestBuilderStack = $this->getAuthorizeRequestBuilderStack();

        if (!isset($authorizeRequestBuilderStack[$authorizeWorkflow])) {
            throw new InvalidAfterPayAuthorizeRequestBuilderException(sprintf(
                '%s is not a valid AfterPay authorize request builder.',
                $authorizeWorkflow
            ));
        }

        return $authorizeRequestBuilderStack[$authorizeWorkflow];
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Authorize\RequestBuilder\AuthorizeRequestBuilderInterface[]
     */
    public function getAuthorizeRequestBuilderStack(): array
    {
        return [
            AfterPayConfig::AFTERPAY_AUTHORIZE_WORKFLOW_ONE_STEP => $this->createOneStepAuthorizeRequestBuilder(),
            AfterPayConfig::AFTERPAY_AUTHORIZE_WORKFLOW_TWO_STEPS => $this->createTwoStepsAuthorizeRequestBuilder(),
        ];
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Cancel\CancelRequestBuilderInterface
     */
    public function createCancelRequestBuilder(): CancelRequestBuilderInterface
    {
        return new CancelRequestBuilder(
            $this->createOrderToRequestMapper(),
            $this->getMoneyFacade()
        );
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Refund\RefundRequestBuilderInterface
     */
    public function createRefundRequestBuilder(): RefundRequestBuilderInterface
    {
        return new RefundRequestBuilder(
            $this->createOrderToRequestMapper(),
            $this->getMoneyFacade()
        );
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Authorize\RequestBuilder\AuthorizeRequestBuilderInterface
     */
    public function createOneStepAuthorizeRequestBuilder(): AuthorizeRequestBuilderInterface
    {
        return new OneStepAuthorizeRequestBuilder($this->createOrderToRequestMapper());
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Payment\Mapper\OrderToRequestTransferInterface
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
     * @return \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Authorize\RequestBuilder\AuthorizeRequestBuilderInterface
     */
    public function createTwoStepsAuthorizeRequestBuilder(): AuthorizeRequestBuilderInterface
    {
        return new TwoStepsAuthorizeRequestBuilder($this->createOrderToRequestMapper());
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\TransactionLogReaderInterface
     */
    public function createTransactionLogReader(): TransactionLogReaderInterface
    {
        return new TransactionLogReader($this->getQueryContainer());
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Payment\Transaction\PriceToPayProviderInterface
     */
    public function createPriceToPayProvider(): PriceToPayProviderInterface
    {
        return new PriceToPayProvider($this->getPaymentFacade());
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Mapper\AfterPayMapperInterface
     */
    public function createAfterPayMapper(): AfterPayMapperInterface
    {
        return new AfterPayMapper();
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Payment\Mapper\QuoteToRequestTransferInterface
     */
    public function createQuoteToRequestMapper(): QuoteToRequestTransferInterface
    {
        return new QuoteToRequestTransfer(
            $this->getMoneyFacade(),
            $this->getStoreFacade()
        );
    }

    /**
     * @throws \SprykerEco\Zed\AfterPay\Business\Exception\InvalidAfterPayPaymentMethodsFilterException
     *
     * @return \SprykerEco\Zed\AfterPay\Business\Payment\Filter\AfterPayPaymentMethodsFilterInterface
     */
    public function createPaymentMethodsFilter(): AfterPayPaymentMethodsFilterInterface
    {
        $authorizeWorkflow = $this->getConfig()->getAfterPayAuthorizeWorkflow();
        $paymentMethodsFilterStack = $this->getPaymentMethodsFilterStack();

        if (!isset($paymentMethodsFilterStack[$authorizeWorkflow])) {
            throw new InvalidAfterPayPaymentMethodsFilterException(sprintf(
                '%s is not a valid AfterPay payment methods filter.',
                $authorizeWorkflow
            ));
        }

        return $paymentMethodsFilterStack[$authorizeWorkflow];
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Payment\Filter\AfterPayPaymentMethodsFilterInterface[]
     */
    public function getPaymentMethodsFilterStack(): array
    {
        return [
            AfterPayConfig::AFTERPAY_AUTHORIZE_WORKFLOW_ONE_STEP => $this->createOneStepAuthorizePaymentMethodsFilter(),
            AfterPayConfig::AFTERPAY_AUTHORIZE_WORKFLOW_TWO_STEPS => $this->createTwoStepAuthorizePaymentMethodsFilter(),
        ];
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Payment\Filter\AfterPayPaymentMethodsFilterInterface
     */
    public function createOneStepAuthorizePaymentMethodsFilter(): AfterPayPaymentMethodsFilterInterface
    {
        return new OneStepAuthorizePaymentMethodsFilter();
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Payment\Filter\AfterPayPaymentMethodsFilterInterface
     */
    public function createTwoStepAuthorizePaymentMethodsFilter(): AfterPayPaymentMethodsFilterInterface
    {
        return new TwoStepAuthorizePaymentMethodsFilter();
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Business\Payment\Filter\Provider\AfterPayPaymentMethodsProviderInterface
     */
    public function createPaymentMethodsProvider(): AfterPayPaymentMethodsProviderInterface
    {
        return new AfterPayPaymentMethodsProvider(
            $this->createApiAdapter(),
            $this->createQuoteToRequestMapper()
        );
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToMoneyFacadeInterface
     */
    public function getMoneyFacade(): AfterPayToMoneyFacadeInterface
    {
        return $this->getProvidedDependency(AfterPayDependencyProvider::FACADE_MONEY);
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToCustomerFacadeInterface
     */
    public function getAfterPayToCustomerBridge(): AfterPayToCustomerFacadeInterface
    {
        return $this->getProvidedDependency(AfterPayDependencyProvider::FACADE_CUSTOMER);
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToStoreFacadeInterface
     */
    public function getStoreFacade(): AfterPayToStoreFacadeInterface
    {
        return $this->getProvidedDependency(AfterPayDependencyProvider::FACADE_STORE);
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToPaymentFacadeInterface
     */
    public function getPaymentFacade(): AfterPayToPaymentFacadeInterface
    {
        return $this->getProvidedDependency(AfterPayDependencyProvider::FACADE_PAYMENT);
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Dependency\Service\AfterPayToUtilEncodingServiceInterface
     */
    public function getUtilEncodingService(): AfterPayToUtilEncodingServiceInterface
    {
        return $this->getProvidedDependency(AfterPayDependencyProvider::SERVICE_UTIL_ENCODING);
    }
}

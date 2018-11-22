<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Business;

use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use SprykerEco\Shared\Afterpay\AfterpayConstants;
use SprykerEco\Zed\Afterpay\AfterpayDependencyProvider;
use SprykerEco\Zed\Afterpay\Business\AdditionalServices\Handler\LookupCustomerHandler;
use SprykerEco\Zed\Afterpay\Business\AdditionalServices\Handler\LookupInstallmentPlansHandler;
use SprykerEco\Zed\Afterpay\Business\AdditionalServices\Handler\ValidateBankAccountHandler;
use SprykerEco\Zed\Afterpay\Business\AdditionalServices\Handler\ValidateCustomerHandler;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\AdapterFactory;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\AfterpayApiAdapter;
use SprykerEco\Zed\Afterpay\Business\Hook\PostSaveHook;
use SprykerEco\Zed\Afterpay\Business\Order\Saver;
use SprykerEco\Zed\Afterpay\Business\Payment\Handler\RiskCheck\AvailablePaymentMethodsHandler;
use SprykerEco\Zed\Afterpay\Business\Payment\Mapper\OrderToRequestTransfer;
use SprykerEco\Zed\Afterpay\Business\Payment\Mapper\QuoteToRequestTransfer;
use SprykerEco\Zed\Afterpay\Business\Payment\PaymentReader;
use SprykerEco\Zed\Afterpay\Business\Payment\PaymentWriter;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Authorize\PaymentAuthorizeWriter;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Authorize\RequestBuilder\OneStepAuthorizeRequestBuilder;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Authorize\RequestBuilder\TwoStepsAuthorizeRequestBuilder;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\AuthorizeTransaction;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Cancel\CancelRequestBuilder;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\CancelTransaction;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Capture\CaptureRequestBuilder;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\CaptureTransaction;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Handler\AuthorizeTransactionHandler;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Handler\CancelTransactionHandler;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Handler\CaptureTransactionHandler;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Handler\RefundTransactionHandler;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Logger\TransactionLogger;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Refund\RefundRequestBuilder;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\RefundTransaction;
use SprykerEco\Zed\Afterpay\Business\Payment\Transaction\TransactionLogReader;

/**
 * @method \SprykerEco\Zed\Afterpay\Persistence\AfterpayQueryContainerInterface getQueryContainer()
 * @method \SprykerEco\Zed\Afterpay\AfterpayConfig getConfig()
 */
class AfterpayBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Payment\Handler\RiskCheck\AvailablePaymentMethodsHandlerInterface
     */
    public function createAvailablePaymentMethodsHandler()
    {
        return new AvailablePaymentMethodsHandler(
            $this->createApiAdapter(),
            $this->createQuoteToRequestMapper()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Order\SaverInterface
     */
    public function createOrderSaver()
    {
        return new Saver(
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Handler\AuthorizeTransactionHandlerInterface
     */
    public function createAuthorizeTransactionHandler()
    {
        return new AuthorizeTransactionHandler(
            $this->createAuthorizeTransaction(),
            $this->getAuthorizeRequestBuilder(),
            $this->createPaymentWriter()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Handler\CaptureTransactionHandlerInterface
     */
    public function createCaptureTransactionHandler()
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
    public function createRefundTransactionHandler()
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
    public function createCancelTransactionHandler()
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
    public function createPaymentReader()
    {
        return new PaymentReader(
            $this->getQueryContainer()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Hook\PostSaveHookInterface
     */
    public function createPostSaveHook()
    {
        return new PostSaveHook(
            $this->createTransactionLogReader(),
            $this->getConfig()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Capture\CaptureRequestBuilderInterface
     */
    protected function createCaptureRequestBuilder()
    {
        return new CaptureRequestBuilder(
            $this->createOrderToRequestMapper(),
            $this->getMoneyFacade()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\CaptureTransactionInterface
     */
    protected function createCaptureTransaction()
    {
        return new CaptureTransaction(
            $this->createTransactionLogger(),
            $this->createApiAdapter()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\RefundTransactionInterface
     */
    protected function createRefundTransaction()
    {
        return new RefundTransaction(
            $this->createTransactionLogger(),
            $this->createApiAdapter()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\AdditionalServices\Handler\ValidateCustomerHandlerInterface
     */
    public function createValidateCustomerHandler()
    {
        return new ValidateCustomerHandler(
            $this->createApiAdapter(),
            $this->getAfterpayToCustomerBridge()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\AdditionalServices\Handler\ValidateBankAccountHandlerInterface
     */
    public function createValidateBankAccountHandler()
    {
        return new ValidateBankAccountHandler(
            $this->createApiAdapter()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\AdditionalServices\Handler\LookupCustomerHandlerInterface
     */
    public function createLookupCustomerHandler()
    {
        return new LookupCustomerHandler(
            $this->createApiAdapter()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\AdditionalServices\Handler\LookupInstallmentPlansHandlerInterface
     */
    public function createLookupInstallmentPlansHandler()
    {
        return new LookupInstallmentPlansHandler(
            $this->createApiAdapter()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Payment\PaymentWriterInterface
     */
    protected function createPaymentWriter()
    {
        return new PaymentWriter(
            $this->getQueryContainer()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\AuthorizeTransactionInterface
     */
    protected function createAuthorizeTransaction()
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
    protected function createCancelTransaction()
    {
        return new CancelTransaction(
            $this->createTransactionLogger(),
            $this->createApiAdapter()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Logger\TransactionLoggerInterface
     */
    protected function createTransactionLogger()
    {
        return new TransactionLogger(
            $this->getUtilEncodingService()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Api\Adapter\AdapterInterface
     */
    public function createApiAdapter()
    {
        return new AfterpayApiAdapter(
            $this->createAdapterFactory()
        );
    }

    /**
     * @return
     */
    protected function createPaymentAuthorizeWriter()
    {
        return new PaymentAuthorizeWriter(
            $this->getQueryContainer()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Api\Adapter\AdapterFactoryInterface
     */
    protected function createAdapterFactory()
    {
        return new AdapterFactory();
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Authorize\RequestBuilder\AuthorizeRequestBuilderInterface
     */
    protected function getAuthorizeRequestBuilder()
    {
        $authorizeWorkflow = $this->getConfig()->getAfterpayAuthorizeWorkflow();

        switch ($authorizeWorkflow) {
            case AfterpayConstants::AFTERPAY_AUTHORIZE_WORKFLOW_ONE_STEP:
                return $this->createOneStepAuthorizeRequestBuilder();
            case AfterpayConstants::AFTERPAY_AUTHORIZE_WORKFLOW_TWO_STEPS:
                return $this->createTwoStepsAuthorizeRequestBuilder();
            default:
                return $this->createOneStepAuthorizeRequestBuilder();
        }
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Cancel\CancelRequestBuilderInterface
     */
    protected function createCancelRequestBuilder()
    {
        return new CancelRequestBuilder(
            $this->createOrderToRequestMapper(),
            $this->getMoneyFacade()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Refund\RefundRequestBuilderInterface
     */
    protected function createRefundRequestBuilder()
    {
        return new RefundRequestBuilder(
            $this->createOrderToRequestMapper(),
            $this->getMoneyFacade()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Authorize\RequestBuilder\OneStepAuthorizeRequestBuilder|\SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Authorize\RequestBuilder\AuthorizeRequestBuilderInterface
     */
    protected function createOneStepAuthorizeRequestBuilder()
    {
        return new OneStepAuthorizeRequestBuilder(
            $this->createOrderToRequestMapper()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Payment\Mapper\OrderToRequestTransferInterface
     */
    protected function createOrderToRequestMapper()
    {
        return new OrderToRequestTransfer(
            $this->getMoneyFacade(),
            $this->getStoreFacade()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Authorize\RequestBuilder\TwoStepsAuthorizeRequestBuilder|\SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Authorize\RequestBuilder\AuthorizeRequestBuilderInterface
     */
    protected function createTwoStepsAuthorizeRequestBuilder()
    {
        return new TwoStepsAuthorizeRequestBuilder(
            $this->createOrderToRequestMapper()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Dependency\Service\AfterpayToUtilEncodingInterface
     */
    protected function getUtilEncodingService()
    {
        return $this->getProvidedDependency(AfterpayDependencyProvider::SERVICE_UTIL_ENCODING);
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Payment\Mapper\QuoteToRequestTransferInterface
     */
    protected function createQuoteToRequestMapper()
    {
        return new QuoteToRequestTransfer(
            $this->getMoneyFacade(),
            $this->getStoreFacade()
        );
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToMoneyInterface
     */
    protected function getMoneyFacade()
    {
        return $this->getProvidedDependency(AfterpayDependencyProvider::FACADE_MONEY);
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToCustomerInterface
     */
    protected function getAfterpayToCustomerBridge()
    {
        return $this->getProvidedDependency(AfterpayDependencyProvider::FACADE_CUSTOMER);
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToSalesInterface
     */
    protected function getAfterpayToSalesBridge()
    {
        return $this->getProvidedDependency(AfterpayDependencyProvider::FACADE_SALES);
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToStoreInterface
     */
    protected function getStoreFacade()
    {
        return $this->getProvidedDependency(AfterpayDependencyProvider::FACADE_STORE);
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Business\Payment\Transaction\TransactionLogReaderInterface
     */
    protected function createTransactionLogReader()
    {
        return new TransactionLogReader(
            $this->getQueryContainer()
        );
    }
}

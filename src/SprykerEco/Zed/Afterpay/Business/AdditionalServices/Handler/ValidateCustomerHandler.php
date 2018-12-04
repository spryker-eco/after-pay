<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business\AdditionalServices\Handler;

use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\AfterpayValidateCustomerRequestTransfer;
use Generated\Shared\Transfer\AfterpayValidateCustomerResponseTransfer;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\AdapterInterface;
use SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToCustomerInterface;

class ValidateCustomerHandler implements ValidateCustomerHandlerInterface
{
    /**
     * @var \SprykerEco\Zed\Afterpay\Business\Api\Adapter\AdapterInterface
     */
    protected $apiAdapter;

    /**
     * @var \SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToCustomerInterface
     */
    protected $customerFacade;

    /**
     * @param \SprykerEco\Zed\Afterpay\Business\Api\Adapter\AdapterInterface $apiAdapter
     * @param \SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToCustomerInterface $customerFacade
     */
    public function __construct(
        AdapterInterface $apiAdapter,
        AfterpayToCustomerInterface $customerFacade
    ) {
        $this->apiAdapter = $apiAdapter;
        $this->customerFacade = $customerFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayValidateCustomerRequestTransfer $validateCustomerRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayValidateCustomerResponseTransfer
     */
    public function validateCustomer(AfterpayValidateCustomerRequestTransfer $validateCustomerRequestTransfer): AfterpayValidateCustomerResponseTransfer
    {
        if ($this->needToLoadAddressById($validateCustomerRequestTransfer)) {
            $this->loadCustomerAddressById($validateCustomerRequestTransfer);
        }

        return $this->apiAdapter->sendValidateCustomerRequest($validateCustomerRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayValidateCustomerRequestTransfer $validateCustomerRequestTransfer
     *
     * @return bool
     */
    protected function needToLoadAddressById(AfterpayValidateCustomerRequestTransfer $validateCustomerRequestTransfer): bool
    {
        $idCustomerAddress =
            $validateCustomerRequestTransfer
            ->getCustomer()
            ->getAddress()
            ->getIdCustomerAddress();

        return $idCustomerAddress !== null;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayValidateCustomerRequestTransfer $validateCustomerRequestTransfer
     *
     * @return void
     */
    protected function loadCustomerAddressById(AfterpayValidateCustomerRequestTransfer $validateCustomerRequestTransfer): void
    {
        $customerAddress = $this->createCustomerTransfer($validateCustomerRequestTransfer);

        $customerAddress = $this->customerFacade->getAddress($customerAddress);

        $requestCustomer = $validateCustomerRequestTransfer->getCustomer();
        $requestAddress = $requestCustomer->getAddress();

        $requestCustomer
            ->setSalutation($customerAddress->getSalutation())
            ->setFirstName($customerAddress->getFirstName())
            ->setLastName($customerAddress->getLastName());

        $requestAddress
            ->setStreet($customerAddress->getAddress1())
            ->setStreetNumber($customerAddress->getAddress2())
            ->setStreetNumberAdditional($customerAddress->getAddress3())
            ->setPostalCode($customerAddress->getZipCode())
            ->setCountryCode($customerAddress->getIso2Code())
            ->setPostalPlace($customerAddress->getCity());
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayValidateCustomerRequestTransfer $validateCustomerRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AddressTransfer
     */
    protected function createCustomerTransfer(AfterpayValidateCustomerRequestTransfer $validateCustomerRequestTransfer): AddressTransfer
    {
        $idCustomerAddress =
            $validateCustomerRequestTransfer
                ->getCustomer()
                ->getAddress()
                ->getIdCustomerAddress();

        $customerAddress = (new AddressTransfer())
            ->setIdCustomerAddress($idCustomerAddress);

        return $customerAddress;
    }
}

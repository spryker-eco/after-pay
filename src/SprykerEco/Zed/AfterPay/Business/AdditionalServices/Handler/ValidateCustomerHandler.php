<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\AdditionalServices\Handler;

use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\AfterPayValidateCustomerRequestTransfer;
use Generated\Shared\Transfer\AfterPayValidateCustomerResponseTransfer;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\AdapterInterface;
use SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToCustomerFacadeInterface;

class ValidateCustomerHandler implements ValidateCustomerHandlerInterface
{
    /**
     * @var \SprykerEco\Zed\AfterPay\Business\Api\Adapter\AdapterInterface
     */
    protected $apiAdapter;

    /**
     * @var \SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToCustomerFacadeInterface
     */
    protected $customerFacade;

    /**
     * @param \SprykerEco\Zed\AfterPay\Business\Api\Adapter\AdapterInterface $apiAdapter
     * @param \SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToCustomerFacadeInterface $customerFacade
     */
    public function __construct(
        AdapterInterface $apiAdapter,
        AfterPayToCustomerFacadeInterface $customerFacade
    ) {
        $this->apiAdapter = $apiAdapter;
        $this->customerFacade = $customerFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayValidateCustomerRequestTransfer $validateCustomerRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayValidateCustomerResponseTransfer
     */
    public function validateCustomer(AfterPayValidateCustomerRequestTransfer $validateCustomerRequestTransfer): AfterPayValidateCustomerResponseTransfer
    {
        if ($this->needToLoadAddressById($validateCustomerRequestTransfer)) {
            $this->loadCustomerAddressById($validateCustomerRequestTransfer);
        }

        return $this->apiAdapter->sendValidateCustomerRequest($validateCustomerRequestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayValidateCustomerRequestTransfer $validateCustomerRequestTransfer
     *
     * @return bool
     */
    protected function needToLoadAddressById(AfterPayValidateCustomerRequestTransfer $validateCustomerRequestTransfer): bool
    {
        $idCustomerAddress =
            $validateCustomerRequestTransfer
            ->getCustomer()
            ->getAddress()
            ->getIdCustomerAddress();

        return $idCustomerAddress !== null;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayValidateCustomerRequestTransfer $validateCustomerRequestTransfer
     *
     * @return void
     */
    protected function loadCustomerAddressById(AfterPayValidateCustomerRequestTransfer $validateCustomerRequestTransfer): void
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
     * @param \Generated\Shared\Transfer\AfterPayValidateCustomerRequestTransfer $validateCustomerRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AddressTransfer
     */
    protected function createCustomerTransfer(AfterPayValidateCustomerRequestTransfer $validateCustomerRequestTransfer): AddressTransfer
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

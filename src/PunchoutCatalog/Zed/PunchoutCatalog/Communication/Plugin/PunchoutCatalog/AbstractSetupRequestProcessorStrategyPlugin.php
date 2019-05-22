<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Communication\Plugin\PunchoutCatalog;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\CompanyUserTransfer;
use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCommonContextTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupResponseTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupRequestDocumentTransfer;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer;

use PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutConnectionConstsInterface;

/**
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutCatalogFacade getFacade()
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\PunchoutCatalogConfig getConfig()
 */
abstract class AbstractSetupRequestProcessorStrategyPlugin extends AbstractPlugin
{
    /**
     * @param string $mapping
     *
     * @return array
     */
    protected function convertToArray(string $mapping): array
    {
        $mapping = parent::convertToArray($mapping);
        if (!empty($mapping['cart_item']) && empty($mapping['multi_lines'])) {
            $mapping['cart_item']['multi_lines'] = true;
        }
        return $mapping;
    }
    
    /**
     * @return string
     * @throws \PunchoutCatalog\Zed\PunchoutCatalog\Exception\MissingZedUrlConfigurationException
     */
    protected function getHostname()
    {
        $zedUrl = $this->getConfig()->getBaseUrlZed();
        return parse_url($zedUrl)['host'];
    }
    
    /**
     * Specification:
     * - Processes request message.
     * - Returns with prepared content and content type.
     * - Adds error message and sets "isSuccess=true".
     *
     * @param \Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogSetupResponseTransfer
     */
    public function processRequest(PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer): PunchoutCatalogSetupResponseTransfer
    {
        $punchoutCatalogRequestTransfer->requireContext();
        $connection = $punchoutCatalogRequestTransfer->getContext()->getPunchoutCatalogConnection();
        
        $documentTransfer = $this->decode($punchoutCatalogRequestTransfer);
        $impersonalDetails = $this->prepareImpersonalDetails($punchoutCatalogRequestTransfer, $documentTransfer);
        
        $punchoutCatalogRequestTransfer->getContext()->setRawData($documentTransfer->toArray());
    
        if ($connection->getSetup()->getLoginMode() == PunchoutConnectionConstsInterface::CUSTOMER_LOGIN_MODE_DYNAMIC) {
            $documentTransfer->requireCustomer();
        }
        
        $customerStrategy = $this->getFactory()->createCustomerLoginModeStrategy(
            $connection->getSetup()->getLoginMode()
        );
        
        /** @var CustomerTransfer $customerTransfer */
        $customerTransfer = $customerStrategy->getCustomerTransfer($connection, $documentTransfer->getCustomer());
        $customerTransfer->setPunchoutCatalogImpersonationDetails($impersonalDetails);

        $oAuthResponseTransfer = $this->getFactory()
            ->getOauthCompanyUserFacade()
            ->createCompanyUserAccessToken($customerTransfer);

        /** TEST STUB */
        $landingUrl = $this->getFactory()->createUrlHandler()->getLoginUrl(
            $oAuthResponseTransfer->getAccessToken()
        );
        /** /TEST STUB */
    
        dd($landingUrl);
        //Mark Request as Success TRX
        $punchoutCatalogRequestTransfer->setIsSuccess(true);
        
        return (new PunchoutCatalogSetupResponseTransfer())
            ->setContext((clone $punchoutCatalogRequestTransfer->getContext())->setRawData(null))
            ->setContentType(PunchoutConnectionConstsInterface::CONTENT_TYPE_TEXT_XML)
            ->setIsSuccess(true)
            ->setContent($this->createEntryResponse($landingUrl));
    }

    /**
     * Specification:
     * - Processes request error.
     * - Returns with prepared content and content type.
     * - Adds error message and sets "isSuccess=false".
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\MessageTransfer $punchoutCatalogRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogSetupResponseTransfer
     */
    public function processError(MessageTransfer $messageTransfer): PunchoutCatalogSetupResponseTransfer
    {
        return (new PunchoutCatalogSetupResponseTransfer())
            ->setContentType(PunchoutConnectionConstsInterface::CONTENT_TYPE_TEXT_XML)
            ->setIsSuccess(false)
            ->setContent($this->createErrorResponse($messageTransfer));
    }
    
    /**
     * @todo: re-use prepared Token Stub
     * @todo: fix login mode
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer $connectionTransfer
     * @param \Generated\Shared\Transfer\PunchoutCatalogSetupRequestDocumentTransfer $documentTransfer
     *
     * @return \Generated\Shared\Transfer\CustomerTransfer
     */
    protected function loginDynamicCustomer(
        PunchoutCatalogConnectionTransfer $connectionTransfer,
        PunchoutCatalogSetupRequestDocumentTransfer $documentTransfer
    ): CustomerTransfer
    {
        $documentTransfer->requireCustomer();
    
        if (null === $documentTransfer->getCustomer()->getFkCompanyBusinessUnit()) {
            $documentTransfer->getCustomer()->setFkCompanyBusinessUnit(
                $connectionTransfer->getSetup()->getFkCompanyBusinessUnit()
            );
        }
        
        $customerTransfer = $this->getFactory()
            ->createCustomerMapper()
            ->mapSetupRequestDocumentCustomerTransferToCustomerTransfer($documentTransfer->getCustomer());
        
        return $customerTransfer;
    }
    
    protected function prepareImpersonalDetails(
        PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer,
        PunchoutCatalogSetupRequestDocumentTransfer $documentTransfer
    )
    {
        $connection = $punchoutCatalogRequestTransfer->getContext()->getPunchoutCatalogConnection();
        
        return [
            'is_punchout' => true,
            //
            'protocol_data' => $punchoutCatalogRequestTransfer->getProtocolData(),
            'punchout_session_id' => $punchoutCatalogRequestTransfer->getContext()->getPunchoutSessionId(),
            //
            'punchout_catalog_connection_id' => $connection->getIdPunchoutCatalogConnection(),
            'punchout_catalog_connection_cart' => [
                'default_supplier_id' => $connection->getCart()->getDefaultSupplierId(),
                'max_description_length' => $connection->getCart()->getMaxDescriptionLength(),
            ],
            'punchout_login_mode' => $connection->getSetup()->getLoginMode(),
            //store it in session - for sake of different customizations
            //'punchout_data' => $documentTransfer->toArray(),
        ];
    }
    
    /**
     * @param string $landingUrl
     *
     * @return string
     */
    abstract protected function createEntryResponse(string $landingUrl): string;

    /**
     * @param \Generated\Shared\Transfer\MessageTransfer $messageTransfer
     *
     * @return string
     */
    abstract protected function createErrorResponse(MessageTransfer $messageTransfer): string;

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogSetupRequestDocumentTransfer
     */
    abstract protected function decode(PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer): PunchoutCatalogSetupRequestDocumentTransfer;
}

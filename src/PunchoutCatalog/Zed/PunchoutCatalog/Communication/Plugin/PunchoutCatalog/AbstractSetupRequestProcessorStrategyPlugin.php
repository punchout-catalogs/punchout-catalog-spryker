<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Communication\Plugin\PunchoutCatalog;

use Generated\Shared\Transfer\CompanyUserTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCommonContextTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupResponseTransfer;

use PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutConnectionConstsInterface;

/**
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutCatalogFacade getFacade()
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\PunchoutCatalogConfig getConfig()
 */
abstract class AbstractSetupRequestProcessorStrategyPlugin extends AbstractPlugin
{
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
     todo: use Token Stub to generate urls
     *
     * Specification:
     * - Processes request message.
     * - Returns with prepared content and content type.
     * - Adds error message and sets "isSuccess=true".
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogSetupResponseTransfer
     */
    public function processRequest(PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer): PunchoutCatalogSetupResponseTransfer
    {
        $this->prepareSetupRequestTransfer($punchoutCatalogRequestTransfer);
        
        /**
         * @Karoly here is some info for you
         *
         * @todo: #1. CREATE/UPDATE/FIND company user
         * @todo: #2. Login user and generate token, provide Landing URL with Token
         */
        
        /** TEST STUB */
        $companyUser = $punchoutCatalogRequestTransfer->getCompanyUser();
        $accessToken = "testTokenHere"; // ResourceShare->generateToken(); (idCustomer, idCompany, idConnection, ErpRequestParams);
        $landingUrl = $this->getFactory()->createUrlHandler()->getLoginUrl(
            $accessToken
        );
        /** /TEST STUB */
    
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
     * @param PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return PunchoutCatalogSetupRequestTransfer
     */
    protected function prepareSetupRequestTransfer(
        PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer
    ): PunchoutCatalogSetupRequestTransfer
    {
        $punchoutCatalogRequestTransfer->requireContext();
        $connection = $punchoutCatalogRequestTransfer->getContext()->getPunchoutCatalogConnection();
        
        $map = $this->decode($punchoutCatalogRequestTransfer);
        $punchoutCatalogRequestTransfer->getContext()->setRawData($map);
        
        $customerTransfer = new CustomerTransfer();
        if (!empty($map['customer']) && is_array($map['customer'])) {
            $customerTransfer->fromArray($map['customer'], true);
        }
        
        //List of PunchoutPatams - necessary to store in customer session
        $customerTransfer->setPunchoutCatalogImpersonationDetails([
            'is_punchout' => true,
            'protocol_data' => $punchoutCatalogRequestTransfer->getProtocolData(),
            'punchout_session_id' => $punchoutCatalogRequestTransfer->getContext()->getPunchoutSessionId(),
            'punchout_catalog_connection_id' => $connection->getIdPunchoutCatalogConnection(),
            'punchout_catalog_connection_cart' => $connection->getCart()->toArray(),
            'punchout_data' => $map,//Store it in session - for sake of different customizations && custom fields
            'punchout_login_mode' => $connection->getSetup()->getLoginMode(),
        ]);
    
        $customerTransfer->setIsGuest(false);
        
        $punchoutCatalogRequestTransfer
            ->setCompanyUser(
                (new CompanyUserTransfer())
                    ->setFkCompanyBusinessUnit($connection->getSetup()->getFkCompanyBusinessUnit())
                    ->setCustomer($customerTransfer)
                    ->setIsActive(true)
            );

        return $punchoutCatalogRequestTransfer;
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
     * @return array
     */
    abstract protected function decode(PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer): array;
}

<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Communication\Plugin\PunchoutCatalog;

use Generated\Shared\Transfer\CompanyUserTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\PunchoutCatalogRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogResponseTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutConnectionConstsInterface;

/**
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutCatalogFacade getFacade()
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\PunchoutCatalogConfig getConfig()
 */
abstract class AbstractSetupRequestProcessorStrategyPlugin extends AbstractPlugin
{
    /**
     * Specification:
     * - Processes request message.
     * - Returns with prepared content and content type.
     * - Adds error message and sets "isSuccess=true".
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\PunchoutCatalogRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogResponseTransfer
     */
    public function processRequest(PunchoutCatalogRequestTransfer $punchoutCatalogRequestTransfer): PunchoutCatalogResponseTransfer
    {
        $map = $this->decode($punchoutCatalogRequestTransfer);

        $punchoutCatalogRequestTransfer->setDecodedContent(json_encode($map, JSON_PRETTY_PRINT));

        /**
         * @todo: move to a separate class like Persistence/Mapper/ConnectionMapper
         */
        $customerTransfer = new CustomerTransfer();
        if (!empty($map['customer']) && is_array($map['customer'])) {
            $customerTransfer->fromArray($map['customer'], true);
        }

        $request = (new PunchoutCatalogSetupRequestTransfer())
            ->setCompanyUser(
                (new CompanyUserTransfer())
                    ->setFkCompany($punchoutCatalogRequestTransfer->getPunchoutCatalogConnection()->getFkCompany())
                    ->setCustomer($customerTransfer)
            );

        /**
         * @Karoly here is some info for you
         *
         * @todo: #1. CREATE/UPDATE/FIND comany user
         * @todo: #2. Login user and generate token, provide Landing URL with Token
         * @todo: #3. SAVE to session $punchoutCatalogRequestTransfer->getProtocolData() (we need to re-use it when submit cart to ERP)
         * @todo: #4. SAVE To session $punchoutCatalogRequestTransfer->getProtocolOperation()
         * @todo: #5. SAVE To session $punchoutCatalogRequestTransfer->getPunchoutCatalogConnection()->getId()
        */

        /** TEST STUB */
        $companyUser = $request->getCompanyUser();
        $accessToken = ""; // ResourceShare->generateToken(); (idCustomer, idCompany, idConnection, ErpRequestParams)
        $landingUrl = 'http://www.democe23.com/?SID=f59a04fdb07a77053dcbdf36e71c52f9&test=' . rand(0, 1000);
        /** /TEST STUB */

        $punchoutCatalogResponseTransfer = new PunchoutCatalogResponseTransfer();
        $punchoutCatalogResponseTransfer->setRequest($punchoutCatalogRequestTransfer);
        return $punchoutCatalogResponseTransfer
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
     * @return \Generated\Shared\Transfer\PunchoutCatalogResponseTransfer
     */
    public function processError(MessageTransfer $messageTransfer): PunchoutCatalogResponseTransfer
    {
        return (new PunchoutCatalogResponseTransfer())
            ->setContentType(PunchoutConnectionConstsInterface::CONTENT_TYPE_TEXT_XML)
            ->setIsSuccess(false)
            ->setContent($this->createErrorResponse($messageTransfer));
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
     * @param \Generated\Shared\Transfer\PunchoutCatalogRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return array
     */
    abstract protected function decode(PunchoutCatalogRequestTransfer $punchoutCatalogRequestTransfer): array;
}

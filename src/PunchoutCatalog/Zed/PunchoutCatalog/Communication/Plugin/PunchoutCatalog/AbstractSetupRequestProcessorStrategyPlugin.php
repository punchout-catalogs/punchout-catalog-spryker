<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Communication\Plugin\PunchoutCatalog;

use Generated\Shared\Transfer\CompanyUserTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCommonContextTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer;
use Generated\Shared\Transfer\PunchoutCatalogMappingTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupRequestDocumentTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupResponseTransfer;
use PunchoutCatalog\Shared\PunchoutCatalog\PunchoutCatalogConstsInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Exception\AuthenticateException;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Propel\Runtime\Exception\PropelException;

/**
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutCatalogFacade getFacade()
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\Communication\PunchoutCatalogCommunicationFactory getFactory()
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\PunchoutCatalogConfig getConfig()
 */
abstract class AbstractSetupRequestProcessorStrategyPlugin extends AbstractPlugin
{
    protected const ERROR_UNEXPECTED = 'punchout-catalog.error.unexpected';
    protected const ERROR_AUTH_TOKEN = 'customer.token.invalid';
    protected const ERROR_AUTH_TOKEN_CREATE = 'punchout-catalog.error.auth.token.create';

    /**
     * @uses \PunchoutCatalog\Client\PunchoutCatalog\Plugin\Quote\SingleCompanyUserDatabaseStrategyPreCheckPlugin::check
     */
    protected const CUSTOMER_LOGIN_MODE_SINGLE = 'single_user';
    protected const CUSTOMER_LOGIN_MODE_DYNAMIC = 'dynamic_user_creation';

    /**
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
        $punchoutCatalogRequestTransfer->requireContext();
        $punchoutCatalogRequestTransfer->getContext()->requirePunchoutCatalogConnection();

        $documentTransfer = $this->decode($punchoutCatalogRequestTransfer);
        $punchoutCatalogRequestTransfer->getContext()->setRawData($documentTransfer->toArray());

        $customerTransfer = $this->prepareCustomerTransfer($punchoutCatalogRequestTransfer, $documentTransfer);

        try {
            /** @var \Generated\Shared\Transfer\OauthResponseTransfer $oAuthResponseTransfer */
            $oAuthResponseTransfer = $this->getFactory()
                ->getOauthCompanyUserFacade()
                ->createCompanyUserAccessToken($customerTransfer);
        } catch (PropelException $e) {
            throw new AuthenticateException(self::ERROR_AUTH_TOKEN_CREATE);
        }

        if (!$oAuthResponseTransfer->getIsValid() && $oAuthResponseTransfer->getError()) {
            throw new AuthenticateException($oAuthResponseTransfer->getError()->getMessage());
        } elseif (!$oAuthResponseTransfer->getIsValid() || !$oAuthResponseTransfer->getAccessToken()) {
            throw new AuthenticateException(self::ERROR_AUTH_TOKEN);
        }

        $storeName = $this->getFactory()->getStoreFacade()->getCurrentStore()->getName();

        $landingUrl = $this->getFactory()
            ->createUrlHandler()
            ->getLoginUrl($oAuthResponseTransfer->getAccessToken(), $storeName);

        //Mark Request as Success TRX
        $punchoutCatalogRequestTransfer->setIsSuccess(true);

        return (new PunchoutCatalogSetupResponseTransfer())
            ->setContext((clone $punchoutCatalogRequestTransfer->getContext())->setRawData(null))
            ->setContentType(PunchoutCatalogConstsInterface::CONTENT_TYPE_TEXT_XML)
            ->setIsSuccess(true)
            ->setContent($this->createEntryResponse($landingUrl));
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogSetupRequestDocumentTransfer
     */
    abstract protected function decode(PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer): PunchoutCatalogSetupRequestDocumentTransfer;

    /**
     * @param PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer
     * @param PunchoutCatalogSetupRequestDocumentTransfer $documentTransfer
     *
     * @return CustomerTransfer
     */
    protected function prepareCustomerTransfer(
        PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer,
        PunchoutCatalogSetupRequestDocumentTransfer $documentTransfer
    )
    {
        $connection = $punchoutCatalogRequestTransfer->getContext()->getPunchoutCatalogConnection();

        if ($connection->getSetup()->getLoginMode() == self::CUSTOMER_LOGIN_MODE_DYNAMIC) {
            $customerStrategy = $this->getFactory()->createCustomerLoginDynamicStrategy();
        } elseif ($connection->getSetup()->getLoginMode() == self::CUSTOMER_LOGIN_MODE_SINGLE) {
            $customerStrategy = $this->getFactory()->createCustomerLoginSingleStrategy();
        } else {
            throw new AuthenticateException(self::ERROR_UNEXPECTED);
        }

        $impersonationDetails = $this->prepareImpersonationDetails(
            $punchoutCatalogRequestTransfer, $documentTransfer
        );

        /** @var CustomerTransfer $customerTransfer */
        $customerTransfer = $customerStrategy->getCustomerTransfer($connection, $documentTransfer->getCustomer());
        $customerTransfer->setPunchoutCatalogImpersonationDetails($impersonationDetails);

        return $customerTransfer;
    }

    /**
     * @param PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer
     * @param PunchoutCatalogSetupRequestDocumentTransfer $documentTransfer
     *
     * @return array
     */
    protected function prepareImpersonationDetails(
        PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer,
        PunchoutCatalogSetupRequestDocumentTransfer $documentTransfer
    )
    {
        $connection = $punchoutCatalogRequestTransfer->getContext()->getPunchoutCatalogConnection();

        return [
            PunchoutCatalogConstsInterface::IS_PUNCHOUT => true,
            'protocol_data' => $punchoutCatalogRequestTransfer->getProtocolData()->toArray(),
            'punchout_session_id' => $punchoutCatalogRequestTransfer->getContext()->getPunchoutSessionId(),
            'punchout_catalog_connection_id' => $connection->getIdPunchoutCatalogConnection(),
            'punchout_catalog_connection_cart' => [
                'default_supplier_id' => $connection->getCart()->getDefaultSupplierId(),
                'max_description_length' => $connection->getCart()->getMaxDescriptionLength(),
                'bundle_mode' => $connection->getCart()->getBundleMode(),
                'totals_mode' => $connection->getCart()->getTotalsMode(),
            ],
            PunchoutCatalogConstsInterface::PUNCHOUT_LOGIN_MODE => $connection->getSetup()->getLoginMode(),
            //store it in session - for sake of different customizations - currently can't use it as
            //oAuth token table has 1024 symbold only length for storing all impersonalization details
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
            ->setContentType(PunchoutCatalogConstsInterface::CONTENT_TYPE_TEXT_XML)
            ->setIsSuccess(false)
            ->setContent($this->createErrorResponse($messageTransfer));
    }

    /**
     * @param \Generated\Shared\Transfer\MessageTransfer $messageTransfer
     *
     * @return string
     */
    abstract protected function createErrorResponse(MessageTransfer $messageTransfer): string;
}

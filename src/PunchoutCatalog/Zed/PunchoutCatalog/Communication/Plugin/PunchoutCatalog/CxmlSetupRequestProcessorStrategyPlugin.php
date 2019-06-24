<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Communication\Plugin\PunchoutCatalog;

use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupRequestDocumentTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupResponseTransfer;
use PunchoutCatalog\Shared\PunchoutCatalog\PunchoutCatalogConstsInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Plugin\PunchoutCatalogRequestProcessorStrategyPluginInterface;
use SimpleXMLElement;

/**
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutCatalogFacade getFacade()
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\PunchoutCatalogConfig getConfig()
 */
class CxmlSetupRequestProcessorStrategyPlugin
    extends AbstractSetupRequestProcessorStrategyPlugin
    implements PunchoutCatalogRequestProcessorStrategyPluginInterface
{
    protected const ERROR_CODE_INTERNAL = 500;
    protected const ERROR_TEXT_INTERNAL = 'Internal Server Error';

    protected const ERROR_CODE_UNATHORIZED = 401;
    protected const ERROR_TEXT_UNATHORIZED = 'Unauthorized';

    protected const ERROR_CODE_NOT_ACCEPTABLE = 406;
    protected const ERROR_TEXT_NOT_ACCEPTABLE = 'Not Acceptable';

    /**
     * @see \PunchoutCatalog\Zed\PunchoutCatalog\Communication\Plugin\PunchoutCatalog\CxmlRequestProtocolStrategyPlugin::setRequestProtocol
     * @see \PunchoutCatalog\Zed\PunchoutCatalog\Communication\Plugin\PunchoutCatalog\OciRequestProtocolStrategyPlugin::assertOciRequestData
     * @see \PunchoutCatalog\Zed\PunchoutCatalog\Business\Authenticator\ConnectionAuthenticator::applyProtocolStrategy
     */
    protected const ERROR_AUTHENTICATION = 'punchout-catalog.error.authentication';

    /**
     * @see \PunchoutCatalog\Zed\PunchoutCatalog\Business\Authenticator\ConnectionAuthenticator::authenticateRequest
     * @see \PunchoutCatalog\Zed\PunchoutCatalog\Business\RequestProcessor\RequestProcessor::process
     * @see \PunchoutCatalog\Zed\PunchoutCatalog\Business\RequestProcessor\RequestProcessor::processException
     */
    protected const ERROR_INVALID_DATA = 'punchout-catalog.error.invalid-data';

    protected const PROTOCOL_OPERATION_SETUP_REQUEST = 'request/punchoutsetuprequest';

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return bool
     */
    public function isApplicable(PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer): bool
    {
        return (
            ($punchoutCatalogRequestTransfer->getContentType() === PunchoutCatalogConstsInterface::CONTENT_TYPE_TEXT_XML)
            && ($punchoutCatalogRequestTransfer->getProtocolType() === PunchoutCatalogConstsInterface::FORMAT_CXML)
            && ($punchoutCatalogRequestTransfer->getProtocolOperation() === self::PROTOCOL_OPERATION_SETUP_REQUEST)
        );
    }

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogSetupResponseTransfer
     */
    public function processRequest(PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer): PunchoutCatalogSetupResponseTransfer
    {
        return parent::processRequest($punchoutCatalogRequestTransfer)
            ->setContentType(PunchoutCatalogConstsInterface::CONTENT_TYPE_TEXT_XML);
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
        return parent::processError($messageTransfer)
            ->setContentType(PunchoutCatalogConstsInterface::CONTENT_TYPE_TEXT_XML);
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogSetupRequestDocumentTransfer
     */
    protected function decode(
        PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer
    ): PunchoutCatalogSetupRequestDocumentTransfer
    {
        $content = $punchoutCatalogRequestTransfer->getContent();

        $xmlContent = new SimpleXMLElement($content);
        if (!$xmlContent) {
            return [];
        }

        $mappingTransfer = $this->getFacade()->convertToMappingTransfer(
            (string)$punchoutCatalogRequestTransfer->getContext()->getPunchoutCatalogConnection()->getMapping()
        );

        $map = $this->getFactory()->createXmlDecoder()->execute($mappingTransfer, $xmlContent);

        return (new PunchoutCatalogSetupRequestDocumentTransfer())->fromArray($map, true);
    }

    /**
     * @param string $landingUrl
     *
     * @return string
     */
    protected function createEntryResponse(string $landingUrl): string
    {
        $landingUrl = htmlspecialchars($landingUrl);
        $timestamp = $this->getFacade()->getTimestamp();
        $zedPayloadId = $this->getFacade()->getZedPayloadId();
        return '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE cXML SYSTEM "http://xml.cxml.org/schemas/cXML/1.2.021/cXML.dtd">
<cXML payloadID="' . $zedPayloadId . '" timestamp="' . $timestamp . '" xml:lang="' . $this->getDefaultLocale() . '">
    <Response>
        <Status code="200" text="OK"/>
        <PunchOutSetupResponse>
            <StartPage>
                <URL>' . $landingUrl . '</URL>
            </StartPage>
        </PunchOutSetupResponse>
    </Response>
</cXML>';
    }

    /**
     * @return string
     */
    protected function getDefaultLocale(): string
    {
        return str_replace('_', '-', $this->getConfig()->getDefaultLocaleName());
    }

    /**
     * @param \Generated\Shared\Transfer\MessageTransfer $messageTransfer
     *
     * @return string
     */
    protected function createErrorResponse(MessageTransfer $messageTransfer): string
    {
        $status = static::ERROR_CODE_INTERNAL;
        $statusText = static::ERROR_TEXT_INTERNAL;
        $statusMessage = $messageTransfer->getTranslatedMessage();

        if ($messageTransfer->getValue() == self::ERROR_INVALID_DATA) {
            $statusText = static::ERROR_TEXT_NOT_ACCEPTABLE;
            $status = static::ERROR_CODE_NOT_ACCEPTABLE;
        } elseif ($messageTransfer->getValue() == self::ERROR_AUTHENTICATION) {
            $statusText = static::ERROR_TEXT_UNATHORIZED;
            $status = static::ERROR_CODE_UNATHORIZED;
        }

        $statusMessage = htmlspecialchars($statusMessage);
        $timestamp = $this->getFacade()->getTimestamp();
        $zedPayloadId = $this->getFacade()->getZedPayloadId();
        return '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE cXML SYSTEM "http://xml.cxml.org/schemas/cXML/1.2.021/cXML.dtd">
<cXML payloadID="' . $zedPayloadId . '" timestamp="' . $timestamp . '" xml:lang="' . $this->getDefaultLocale() . '">
    <Response>
        <Status code="' . $status . '" text="' . $statusText . '">' . $statusMessage . '</Status>
    </Response>
</cXML>';
    }
}

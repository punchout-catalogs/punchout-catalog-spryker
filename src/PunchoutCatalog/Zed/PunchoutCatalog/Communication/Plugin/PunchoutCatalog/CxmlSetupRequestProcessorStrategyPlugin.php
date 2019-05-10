<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Communication\Plugin\PunchoutCatalog;

use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\PunchoutCatalogRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogResponseTransfer;
use SimpleXMLElement;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Xml\Decoder;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutConnectionConstsInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Plugin\PunchoutCatalogRequestProcessorStrategyPluginInterface;

/**
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutCatalogFacade getFacade()
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\PunchoutCatalogConfig getConfig()
 */
class CxmlSetupRequestProcessorStrategyPlugin extends AbstractSetupRequestProcessorStrategyPlugin implements PunchoutCatalogRequestProcessorStrategyPluginInterface
{
    protected const ERROR_CODE_INTERNAL = 500;
    protected const ERROR_TEXT_INTERNAL = 'Internal Server Error';

    protected const ERROR_CODE_UNATHORIZED = 401;
    protected const ERROR_TEXT_UNATHORIZED = 'Unauthorized';

    protected const ERROR_CODE_NOT_ACCEPTABLE = 406;
    protected const ERROR_TEXT_NOT_ACCEPTABLE = 'Not Acceptable';

    protected const CXML_VERSION = '1.2.021';

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\PunchoutCatalogRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return bool
     */
    public function isApplicable(PunchoutCatalogRequestTransfer $punchoutCatalogRequestTransfer): bool
    {
        return (
            ($punchoutCatalogRequestTransfer->getContentType() === PunchoutConnectionConstsInterface::CONTENT_TYPE_TEXT_XML)
            && ($punchoutCatalogRequestTransfer->getProtocolType() === PunchoutConnectionConstsInterface::FORMAT_CXML)
            && ($punchoutCatalogRequestTransfer->getProtocolOperation() === PunchoutConnectionConstsInterface::PROTOCOL_OPERATION_SETUP_REQUEST)
        );
    }

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\PunchoutCatalogRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogResponseTransfer
     */
    public function processRequest(PunchoutCatalogRequestTransfer $punchoutCatalogRequestTransfer): PunchoutCatalogResponseTransfer
    {
        return parent::processRequest($punchoutCatalogRequestTransfer)
            ->setContentType(PunchoutConnectionConstsInterface::CONTENT_TYPE_TEXT_XML);
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
        return parent::processError($messageTransfer)
            ->setContentType(PunchoutConnectionConstsInterface::CONTENT_TYPE_TEXT_XML);
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return array
     */
    protected function decode(PunchoutCatalogRequestTransfer $punchoutCatalogRequestTransfer): array
    {
        $content = $punchoutCatalogRequestTransfer->getContent();

        $xmlContent = new SimpleXMLElement($content);
        if (!$xmlContent) {
            return [];
        }

        $mappingTransfer = $this->convertToMappingTransfer(
            (string)$punchoutCatalogRequestTransfer->getPunchoutCatalogConnection()->getMapping()
        );

        return (new Decoder())->execute($mappingTransfer, $xmlContent);
    }

    /**
     * @param string $landingUrl
     *
     * @return string
     */
    protected function createEntryResponse(string $landingUrl): string
    {
        $landingUrl = htmlspecialchars($landingUrl);
        return '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE cXML SYSTEM "http://xml.cxml.org/schemas/cXML/1.2.021/cXML.dtd">
<cXML payloadID="' . $this->getPayloadId() . '" timestamp="' . $this->getTimestamp() . '" xml:lang="' . $this->getLang() . '" version="' . static::CXML_VERSION . '">
    <Response>
        <Status code="200" text="OK"/>
        <PunchoutSetupResponse>
            <StartPage>
                <URL>' . $landingUrl . '</URL>
            </StartPage>
        </PunchoutSetupResponse>
    </Response>
</cXML>';
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
        $statusMessage = $messageTransfer->getValue();

        if ($messageTransfer->getValue() == PunchoutConnectionConstsInterface::ERROR_INVALID_DATA
            || $messageTransfer->getValue() == PunchoutConnectionConstsInterface::ERROR_MISSING_REQUEST_PROCESSOR
        ) {
            $statusText = static::ERROR_TEXT_NOT_ACCEPTABLE;
            $status = static::ERROR_CODE_NOT_ACCEPTABLE;
        } elseif ($messageTransfer->getValue() == PunchoutConnectionConstsInterface::ERROR_AUTHENTICATION) {
            $statusText = static::ERROR_TEXT_UNATHORIZED;
            $status = static::ERROR_CODE_UNATHORIZED;
        }

        $statusMessage = htmlspecialchars($statusMessage);
        return '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE cXML SYSTEM "http://xml.cxml.org/schemas/cXML/1.2.021/cXML.dtd">
<cXML payloadID="' . $this->getPayloadId() . '" timestamp="' . $this->getTimestamp() . '" xml:lang="' . $this->getLang() . '" version="' . static::CXML_VERSION . '">
    <Response>
        <Status code="' . $status . '" text="' . $statusText . '">' . $statusMessage . '</Status>
    </Response>
</cXML>';
    }

    /**
     * @todo: get current locale and extract cxml lang
     *
     * @return string
     */
    protected function getLang(): string
    {
        return 'en-US';
    }
}

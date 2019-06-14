<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Communication\Plugin\PunchoutCatalog;

use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupResponseTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupRequestDocumentTransfer;

use PunchoutCatalog\Zed\PunchoutCatalog\Business\Mapping\Oci\Decoder;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutConnectionConstsInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Plugin\PunchoutCatalogRequestProcessorStrategyPluginInterface;

/**
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutCatalogFacade getFacade()
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\PunchoutCatalogConfig getConfig()
 */
class OciSetupRequestProcessorStrategyPlugin
    extends AbstractSetupRequestProcessorStrategyPlugin
    implements PunchoutCatalogRequestProcessorStrategyPluginInterface
{
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
            ($punchoutCatalogRequestTransfer->getContentType() === PunchoutConnectionConstsInterface::CONTENT_TYPE_FORM_MULTIPART)
            && ($punchoutCatalogRequestTransfer->getProtocolType() === PunchoutConnectionConstsInterface::FORMAT_OCI)
            && ($punchoutCatalogRequestTransfer->getProtocolOperation() === PunchoutConnectionConstsInterface::PROTOCOL_OPERATION_SETUP_REQUEST)
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
            ->setContentType(PunchoutConnectionConstsInterface::CONTENT_TYPE_TEXT_HTML);
    }

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\MessageTransfer $punchoutCatalogRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogSetupResponseTransfer
     */
    public function processError(MessageTransfer $messageTransfer): PunchoutCatalogSetupResponseTransfer
    {
        return parent::processError($messageTransfer)
            ->setContentType(PunchoutConnectionConstsInterface::CONTENT_TYPE_TEXT_PLAIN);
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogSetupRequestDocumentTransfer
     */
    protected function decode(PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer): PunchoutCatalogSetupRequestDocumentTransfer
    {
        $ociContent = $punchoutCatalogRequestTransfer->getContent();
        if (!is_array($ociContent)) {
            return [];
        }

        $mappingTransfer = $this->convertToMappingTransfer(
            (string)$punchoutCatalogRequestTransfer->getContext()->getPunchoutCatalogConnection()->getMapping()
        );
    
        $map = (new Decoder())->execute($mappingTransfer, $ociContent);
        
        return (new PunchoutCatalogSetupRequestDocumentTransfer())->fromArray($map, true);
    }

    /**
     * @param string $landingUrl
     *
     * @return string
     */
    protected function createEntryResponse(string $landingUrl): string
    {
        return "<html>
                    <body>
                        <span style=\"font-style:italic; font-size:12px; color:#7D8083;\">Redirecting to supplier website...</span>
                        <script type=\"text/javascript\">window.location.href = '{$landingUrl}';</script>
                    </body>
               </html>";
    }

    /**
     * @param \Generated\Shared\Transfer\MessageTransfer $messageTransfer
     *
     * @return string
     */
    protected function createErrorResponse(MessageTransfer $messageTransfer): string
    {
        return sprintf('Punchout Error: %s', (string)$messageTransfer->getTranslatedMessage());
    }
}

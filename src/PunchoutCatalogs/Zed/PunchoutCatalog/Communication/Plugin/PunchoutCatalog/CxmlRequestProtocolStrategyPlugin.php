<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogs\Zed\PunchoutCatalog\Communication\Plugin\PunchoutCatalog;

use Generated\Shared\Transfer\PunchoutCatalogConnectionCredentialSearchTransfer;
use Generated\Shared\Transfer\PunchoutCatalogProtocolDataTransfer;
use Generated\Shared\Transfer\PunchoutCatalogRequestTransfer;
use PunchoutCatalogs\Service\UtilCxml\UtilCxmlService;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use PunchoutCatalogs\Zed\PunchoutCatalog\Business\PunchoutConnectionConstsInterface;
use PunchoutCatalogs\Zed\PunchoutCatalog\Business\Validator\Cxml\ProtocolDataValidator;
use PunchoutCatalogs\Zed\PunchoutCatalogExtension\Dependency\Plugin\PunchoutCatalogProtocolStrategyPluginInterface;

/**
 * @method \PunchoutCatalogs\Zed\PunchoutCatalog\Business\PunchoutCatalogFacade getFacade()
 * @method \PunchoutCatalogs\Zed\PunchoutCatalog\PunchoutCatalogConfig getConfig()
 */
class CxmlRequestProtocolStrategyPlugin extends AbstractPlugin implements PunchoutCatalogProtocolStrategyPluginInterface
{
    /**
     * @api
     *
     * @var \PunchoutCatalogs\Service\UtilCxml\UtilCxmlServiceInterface
     */
    protected $utilCxmlService;

    public function __construct()
    {
        $this->utilCxmlService = new UtilCxmlService();
    }

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\PunchoutCatalogRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return bool
     */
    public function isApplicable(PunchoutCatalogRequestTransfer $punchoutCatalogRequestTransfer): bool
    {
        if ($punchoutCatalogRequestTransfer->getContentType() !== PunchoutConnectionConstsInterface::CONTENT_TYPE_TEXT_XML) {
            return false;
        }

        if (empty($punchoutCatalogRequestTransfer->getContent())) {
            return false;
        }

        return $this->utilCxmlService->isCXml($punchoutCatalogRequestTransfer->getContent());
    }

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\PunchoutCatalogRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogRequestTransfer
     */
    public function setRequestProtocol(PunchoutCatalogRequestTransfer $punchoutCatalogRequestTransfer): PunchoutCatalogRequestTransfer
    {
        $protocolData = $this->utilCxmlService->fetchHeaderAsArray($punchoutCatalogRequestTransfer->getContent());
        $protocolOperation = $this->utilCxmlService->getOperation($punchoutCatalogRequestTransfer->getContent());

        $punchoutCatalogRequestTransfer
            ->setProtocolType(PunchoutConnectionConstsInterface::FORMAT_CXML)
            ->setProtocolOperation($protocolOperation)
            ->setProtocolData(
                (new PunchoutCatalogProtocolDataTransfer())->fromArray($protocolData)
            );

        $punchoutCatalogRequestTransfer->requireProtocolOperation();

        (new ProtocolDataValidator())->validate(
            $punchoutCatalogRequestTransfer->getProtocolData()
        );

        return $punchoutCatalogRequestTransfer;
    }

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\PunchoutCatalogRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogRequestTransfer
     */
    public function setPunchoutCatalogConnection(PunchoutCatalogRequestTransfer $punchoutCatalogRequestTransfer): PunchoutCatalogRequestTransfer
    {
        $type = $this->mapProtocolOperationToConnectionType($punchoutCatalogRequestTransfer->getProtocolOperation());

        $credentialSearchTransfer = new PunchoutCatalogConnectionCredentialSearchTransfer();
        $credentialSearchTransfer->setFkCompany($punchoutCatalogRequestTransfer->getCompany()->getIdCompany());
        $credentialSearchTransfer->setFormat($punchoutCatalogRequestTransfer->getProtocolType());
        $credentialSearchTransfer->setType($type);

        $credentialSearchTransfer->setUsername(
            $punchoutCatalogRequestTransfer->getProtocolData()
                ->getCxmlSenderCredentials()
                ->getIdentity()
        );
        $credentialSearchTransfer->setPassword(
            $punchoutCatalogRequestTransfer->getProtocolData()
                ->getCxmlSenderCredentials()
                ->getSharedSecret()
        );

        $punchoutCatalogConnectionTransfer = $this->getFacade()->findConnectionByCredential($credentialSearchTransfer);

        return $punchoutCatalogRequestTransfer->setPunchoutCatalogConnection($punchoutCatalogConnectionTransfer);
    }

    /**
     * @param $protocolOperation
     *
     * @return string|null
     */
    protected function mapProtocolOperationToConnectionType($protocolOperation): ?string
    {
        switch ($protocolOperation) {
            case PunchoutConnectionConstsInterface::PROTOCOL_OPERATION_SETUP_REQUEST:
                return PunchoutConnectionConstsInterface::CONNECTION_TYPE_SETUP_REQUEST;
            default:
                return null;
        }
    }
}

<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Communication\Plugin\PunchoutCatalog;

use Generated\Shared\Transfer\PunchoutCatalogConnectionCredentialSearchTransfer;
use Generated\Shared\Transfer\PunchoutCatalogProtocolDataTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer;
use PunchoutCatalog\Service\UtilCxml\UtilCxmlService;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutConnectionConstsInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Validator\Cxml\ProtocolDataValidator;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Plugin\PunchoutCatalogProtocolStrategyPluginInterface;

/**
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutCatalogFacade getFacade()
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\PunchoutCatalogConfig getConfig()
 */
class CxmlRequestProtocolStrategyPlugin extends AbstractPlugin implements PunchoutCatalogProtocolStrategyPluginInterface
{
    /**
     * @api
     *
     * @var \PunchoutCatalog\Service\UtilCxml\UtilCxmlServiceInterface
     */
    protected $utilCxmlService;

    public function __construct()
    {
        $this->utilCxmlService = new UtilCxmlService();
    }

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return bool
     */
    public function isApplicable(PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer): bool
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
     * @param \Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer
     */
    public function setRequestProtocol(PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer): PunchoutCatalogSetupRequestTransfer
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
     * @param \Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer
     */
    public function setPunchoutCatalogConnection(PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer): PunchoutCatalogSetupRequestTransfer
    {
        $punchoutCatalogRequestTransfer->requireContext();
        
        $type = $this->mapProtocolOperationToConnectionType($punchoutCatalogRequestTransfer->getProtocolOperation());

        $credentialSearchTransfer = new PunchoutCatalogConnectionCredentialSearchTransfer();
        $credentialSearchTransfer->setFkCompanyBusinessUnit(
            $punchoutCatalogRequestTransfer->getCompanyBusinessUnit()->getIdCompanyBusinessUnit()
        );
        
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
        $punchoutCatalogRequestTransfer->getContext()->setPunchoutCatalogConnection(
            $punchoutCatalogConnectionTransfer
        );
        
        return $punchoutCatalogRequestTransfer;
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

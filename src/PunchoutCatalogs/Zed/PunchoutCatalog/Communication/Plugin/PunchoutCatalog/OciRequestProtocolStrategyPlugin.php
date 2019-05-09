<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogs\Zed\PunchoutCatalog\Communication\Plugin\PunchoutCatalog;

use Generated\Shared\Transfer\PunchoutCatalogConnectionCredentialSearchTransfer;
use Generated\Shared\Transfer\PunchoutCatalogProtocolDataTransfer;
use Generated\Shared\Transfer\PunchoutCatalogRequestTransfer;
use PunchoutCatalogs\Service\UtilOci\UtilOciService;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use PunchoutCatalogs\Zed\PunchoutCatalog\Business\PunchoutConnectionConstsInterface;
use PunchoutCatalogs\Zed\PunchoutCatalog\Business\Validator\Oci\ProtocolDataValidator;
use PunchoutCatalogs\Zed\PunchoutCatalogExtension\Dependency\Plugin\PunchoutCatalogProtocolStrategyPluginInterface;

/**
 * @method \PunchoutCatalogs\Zed\PunchoutCatalog\Business\PunchoutCatalogFacade getFacade()
 * @method \PunchoutCatalogs\Zed\PunchoutCatalog\PunchoutCatalogConfig getConfig()
 */
class OciRequestProtocolStrategyPlugin extends AbstractPlugin implements PunchoutCatalogProtocolStrategyPluginInterface
{
    /**
     * @api
     *
     * @var \PunchoutCatalogs\Service\UtilOci\UtilOciServiceInterface
     */
    protected $utilOciService;

    public function __construct()
    {
        $this->utilOciService = new UtilOciService();
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
        if ($punchoutCatalogRequestTransfer->getContentType() !== PunchoutConnectionConstsInterface::CONTENT_TYPE_FORM_MULTIPART) {
            return false;
        }

        if (empty($punchoutCatalogRequestTransfer->getContent())) {
            return false;
        }

        return $this->utilOciService->isOci($punchoutCatalogRequestTransfer->getContent());
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
        $protocolData = $this->utilOciService->fetchHeaderAsArray($punchoutCatalogRequestTransfer->getContent());
        $protocolOperation = $this->utilOciService->getOperation($punchoutCatalogRequestTransfer->getContent());

        $punchoutCatalogRequestTransfer
            ->setProtocolType(PunchoutConnectionConstsInterface::FORMAT_OCI)
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
                ->getOciCredentials()
                ->getUsername()
        );
        $credentialSearchTransfer->setPassword(
            $punchoutCatalogRequestTransfer->getProtocolData()
                ->getOciCredentials()
                ->getPassword()
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

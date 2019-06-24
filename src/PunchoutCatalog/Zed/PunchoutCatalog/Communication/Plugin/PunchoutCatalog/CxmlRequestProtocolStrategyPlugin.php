<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Communication\Plugin\PunchoutCatalog;

use Generated\Shared\Transfer\PunchoutCatalogConnectionCredentialSearchTransfer;
use Generated\Shared\Transfer\PunchoutCatalogProtocolDataTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer;
use PunchoutCatalog\Shared\PunchoutCatalog\PunchoutCatalogConstsInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Plugin\PunchoutCatalogProtocolStrategyPluginInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Exception\AuthenticateException;
use Spryker\Shared\Kernel\Transfer\Exception\RequiredTransferPropertyException;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutCatalogFacade getFacade()
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\PunchoutCatalogConfig getConfig()
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\Communication\PunchoutCatalogCommunicationFactory getFactory()
 */
class CxmlRequestProtocolStrategyPlugin extends AbstractPlugin implements PunchoutCatalogProtocolStrategyPluginInterface
{
    protected const ERROR_AUTHENTICATION = 'punchout-catalog.error.authentication';

    protected const CONNECTION_TYPE_SETUP_REQUEST = 'setup_request';
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
        if ($punchoutCatalogRequestTransfer->getContentType() !== PunchoutCatalogConstsInterface::CONTENT_TYPE_TEXT_XML) {
            return false;
        } elseif (empty($punchoutCatalogRequestTransfer->getContent())) {
            return false;
        }

        return (is_string($punchoutCatalogRequestTransfer->getContent())
            && $this->getFacade()->isCXmlContent($punchoutCatalogRequestTransfer->getContent())
        );
    }

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer
     * @throws \Exception
     */
    public function setRequestProtocol(
        PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer
    ): PunchoutCatalogSetupRequestTransfer
    {
        $protocolData = $this->getFacade()->fetchCXmlHeaderAsArray($punchoutCatalogRequestTransfer->getContent());
        $protocolOperation = $this->getFacade()->fetchCXmlOperation($punchoutCatalogRequestTransfer->getContent());

        $punchoutCatalogRequestTransfer
            ->setProtocolType(PunchoutCatalogConstsInterface::FORMAT_CXML)
            ->setProtocolOperation($protocolOperation)
            ->setProtocolData(
                (new PunchoutCatalogProtocolDataTransfer())->fromArray($protocolData)
            );

        $punchoutCatalogRequestTransfer->requireProtocolOperation();

        try {
            $this->getFactory()->createXmlProtocolDataValidator()
                ->validate($punchoutCatalogRequestTransfer->getProtocolData());
        } catch (RequiredTransferPropertyException $e) {
            throw new AuthenticateException(
                self::ERROR_AUTHENTICATION, 0, $e
            );
        }

        return $punchoutCatalogRequestTransfer;
    }

    /**
     * @api
     *
     * @param \Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer
     */
    public function setPunchoutCatalogConnection(
        PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer
    ): PunchoutCatalogSetupRequestTransfer
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
        return $protocolOperation == self::PROTOCOL_OPERATION_SETUP_REQUEST ? self::CONNECTION_TYPE_SETUP_REQUEST : null;
    }
}

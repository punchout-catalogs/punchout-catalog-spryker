<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Communication\Plugin\PunchoutCatalog;

use Generated\Shared\Transfer\PunchoutCatalogConnectionCredentialSearchTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer;
use PunchoutCatalog\Shared\PunchoutCatalog\PunchoutCatalogConstsInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Plugin\PunchoutCatalogProtocolStrategyPluginInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Exception\AuthenticateException;
use Spryker\Shared\Kernel\Transfer\Exception\RequiredTransferPropertyException;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutCatalogFacadeInterface getFacade()
 * @method \PunchoutCatalog\Zed\PunchoutCatalog\PunchoutCatalogConfig getConfig()
 */
class OciRequestProtocolStrategyPlugin extends AbstractPlugin implements PunchoutCatalogProtocolStrategyPluginInterface
{
    protected const ERROR_AUTHENTICATION = 'punchout-catalog.error.authentication';

    protected const CONNECTION_TYPE_SETUP_REQUEST = 'setup_request';
    protected const PROTOCOL_OPERATION_SETUP_REQUEST = 'request/punchoutsetuprequest';

    /**
     * {@inheritdoc}
     * - Returns true if provided content has a multipart/form-data content type with a non-empty OCI content.
     * - Returns false otherwise.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return bool
     */
    public function isApplicable(PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer): bool
    {
        if ($punchoutCatalogRequestTransfer->getContentType() !== PunchoutCatalogConstsInterface::CONTENT_TYPE_FORM_MULTIPART) {
            return false;
        }

        if (empty($punchoutCatalogRequestTransfer->getContent()) || !is_array($punchoutCatalogRequestTransfer->getContent())) {
            return false;
        }

        /** @var array $content */
        $content = $punchoutCatalogRequestTransfer->getContent();

        return $this->getFacade()->isOciContent($content);
    }

    /**
     * {@inheritdoc}
     * - Sets protocol type, protocol operation and "protocol data property".
     * - Throws exception if any mandatory data is missing.
     *
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
        /** @var array $content */
        $content = $punchoutCatalogRequestTransfer->getContent();

        $protocolData = $this->getFacade()->fetchOciHeader($content);
        $protocolOperation = $this->getFacade()->fetchOciOperation($content);

        $punchoutCatalogRequestTransfer
            ->setProtocolType(PunchoutCatalogConstsInterface::FORMAT_OCI)
            ->setProtocolOperation($protocolOperation)
            ->setProtocolData($protocolData);

        $this->assertOciRequestData($punchoutCatalogRequestTransfer);

        return $punchoutCatalogRequestTransfer;
    }

    /**
     * @throws AuthenticateException
     *
     * @param PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return void
     */
    protected function assertOciRequestData(PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer): void
    {
        try {
            $punchoutCatalogRequestTransfer->requireProtocolOperation();

            $this->getFacade()->assertOciProtocolData($punchoutCatalogRequestTransfer->getProtocolData());
        } catch (RequiredTransferPropertyException $e) {
            throw new AuthenticateException(
                self::ERROR_AUTHENTICATION, 0, $e
            );
        }
    }

    /**
     * {@inheritdoc}
     * - Requires context to be set.
     * - Searches and sets connection by credential.
     *
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

        $connectionType = $this->mapProtocolOperationToConnectionType($punchoutCatalogRequestTransfer->getProtocolOperation());
        $punchoutCatalogConnectionTransfer = $this->findConnectionByCredential($connectionType, $punchoutCatalogRequestTransfer);

        $punchoutCatalogRequestTransfer
            ->getContext()
            ->setPunchoutCatalogConnection($punchoutCatalogConnectionTransfer);

        return $punchoutCatalogRequestTransfer;
    }

    /**
     * @param string|null $protocolOperation
     *
     * @return string|null
     */
    protected function mapProtocolOperationToConnectionType(?string $protocolOperation): ?string
    {
        return $protocolOperation === self::PROTOCOL_OPERATION_SETUP_REQUEST ? self::CONNECTION_TYPE_SETUP_REQUEST : null;
    }

    /**
     * @param null|string $connectionType
     * @param PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer|null
     */
    protected function findConnectionByCredential(?string $connectionType, PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer): ?PunchoutCatalogConnectionTransfer
    {
        $credentialSearchTransfer = (new PunchoutCatalogConnectionCredentialSearchTransfer())
            ->setFormat($punchoutCatalogRequestTransfer->getProtocolType())
            ->setType($connectionType)
            ->setFkCompanyBusinessUnit(
                $punchoutCatalogRequestTransfer
                    ->getCompanyBusinessUnit()
                    ->getIdCompanyBusinessUnit()
            )
            ->setUsername(
                $punchoutCatalogRequestTransfer
                    ->getProtocolData()
                    ->getOciCredentials()
                    ->getUsername()
            )
            ->setPassword(
                $punchoutCatalogRequestTransfer
                    ->getProtocolData()
                    ->getOciCredentials()
                    ->getPassword()
            );

        return $this->getFacade()->findConnectionByCredential($credentialSearchTransfer);
    }
}

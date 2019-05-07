<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogs\Zed\PunchoutCatalog\Business\Authenticator;

use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionCredentialSearchTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer;
use Generated\Shared\Transfer\PunchoutCatalogRequestTransfer;
use PunchoutCatalogs\Shared\PunchoutCatalog\PunchoutCatalogConfig;
use PunchoutCatalogs\Zed\PunchoutCatalog\Business\PunchoutConnectionConstsInterface;
use PunchoutCatalogs\Zed\PunchoutCatalog\Communication\Plugin\PunchoutCatalog\CxmlRequestProtocolStrategyPlugin;
use PunchoutCatalogs\Zed\PunchoutCatalog\Communication\Plugin\PunchoutCatalog\OciRequestProtocolStrategyPlugin;
use PunchoutCatalogs\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCompanyFacadeInterface;
use PunchoutCatalogs\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToVaultFacadeInterface;
use PunchoutCatalogs\Zed\PunchoutCatalog\Persistence\PunchoutCatalogRepositoryInterface;
use PunchoutCatalogs\Zed\PunchoutCatalogExtension\Dependency\Plugin\PunchoutCatalogProtocolStrategyPluginInterface;

class ConnectionAuthenticator implements ConnectionAuthenticatorInterface
{
    /**
     * @var \PunchoutCatalogs\Zed\PunchoutCatalogExtension\Dependency\Plugin\PunchoutCatalogProtocolStrategyPluginInterface[]
     */
    protected $protocolStrategyPlugins;

    /**
     * @var \PunchoutCatalogs\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCompanyFacadeInterface
     */
    protected $companyFacade;

    /**
     * @var \PunchoutCatalogs\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToVaultFacadeInterface
     */
    protected $vaultFacade;

    /**
     * @var \PunchoutCatalogs\Zed\PunchoutCatalog\Persistence\PunchoutCatalogRepositoryInterface
     */
    protected $punchoutCatalogRepository;

    public function __construct(
        PunchoutCatalogToCompanyFacadeInterface $companyFacade,
        PunchoutCatalogToVaultFacadeInterface $vaultFacade,
        PunchoutCatalogRepositoryInterface $punchoutCatalogRepository
    ) {
        $this->protocolStrategyPlugins = [
            new CxmlRequestProtocolStrategyPlugin(),
            new OciRequestProtocolStrategyPlugin(),
        ];
        $this->companyFacade = $companyFacade;
        $this->vaultFacade = $vaultFacade;
        $this->punchoutCatalogRepository = $punchoutCatalogRepository;
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogRequestTransfer
     */
    public function authenticateRequest(PunchoutCatalogRequestTransfer $punchoutCatalogRequestTransfer): PunchoutCatalogRequestTransfer
    {
        $punchoutCatalogRequestTransfer
            ->requireContent()
            ->requireContentType()
            ->requireCompanyUuid();

        $companyTransfer = $this->companyFacade->findCompanyByUuid($punchoutCatalogRequestTransfer->getCompanyUuid());
        if (!$companyTransfer || !$companyTransfer->getIsActive()) {
            return $punchoutCatalogRequestTransfer
                ->setIsSuccess(false)
                ->addMessage(
                    (new MessageTransfer())
                        ->setValue(PunchoutConnectionConstsInterface::ERROR_AUTHENTICATION)
                );
        }

        $punchoutCatalogRequestTransfer->setCompany($companyTransfer);
        foreach ($this->protocolStrategyPlugins as $protocolStrategyPlugin) {
            if ($protocolStrategyPlugin->isApplicable($punchoutCatalogRequestTransfer)) {
                return $punchoutCatalogRequestTransfer = $this->applyProtocolStrategy($protocolStrategyPlugin, $punchoutCatalogRequestTransfer);
            }
        }

        return $punchoutCatalogRequestTransfer
            ->setIsSuccess(false)
            ->addMessage(
                (new MessageTransfer())
                    ->setValue(PunchoutConnectionConstsInterface::ERROR_INVALID_DATA)
            );
    }

    /**
     * @param \PunchoutCatalogs\Zed\PunchoutCatalogExtension\Dependency\Plugin\PunchoutCatalogProtocolStrategyPluginInterface $protocolStrategyPlugin
     * @param \Generated\Shared\Transfer\PunchoutCatalogRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogRequestTransfer
     */
    public function applyProtocolStrategy(
        PunchoutCatalogProtocolStrategyPluginInterface $protocolStrategyPlugin,
        PunchoutCatalogRequestTransfer $punchoutCatalogRequestTransfer
    ): PunchoutCatalogRequestTransfer {
        $punchoutCatalogRequestTransfer = $protocolStrategyPlugin->setRequestProtocol($punchoutCatalogRequestTransfer);

        $punchoutCatalogRequestTransfer
            ->requireProtocolType()
            ->requireProtocolData();

        $punchoutCatalogRequestTransfer = $protocolStrategyPlugin->setPunchoutCatalogConnection($punchoutCatalogRequestTransfer);

        if ($punchoutCatalogRequestTransfer->getPunchoutCatalogConnection() === null) {
            return $punchoutCatalogRequestTransfer
                ->setIsSuccess(false)
                ->addMessage(
                    (new MessageTransfer())
                        ->setValue(PunchoutConnectionConstsInterface::ERROR_AUTHENTICATION)
                );
        }

        return $punchoutCatalogRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionCredentialSearchTransfer $connectionCredentialSearch
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer|null
     */
    public function findConnectionByCredential(PunchoutCatalogConnectionCredentialSearchTransfer $connectionCredentialSearch): ?PunchoutCatalogConnectionTransfer
    {
        $connection = $this->punchoutCatalogRepository->findConnectionByCredential($connectionCredentialSearch);
        if ($connection === null || $connection->getIdPunchoutCatalogConnection() === null) {
            return null;
        }

        $connectionPassword = $this->vaultFacade->retrieve(
            PunchoutCatalogConfig::VAULT_PASSWORD_DATA_TYPE,
            $connection->getIdPunchoutCatalogConnection()
        );

        if ($connectionPassword === $connectionCredentialSearch->getPassword()) {
            return $connection;
        }

        return null;
    }
}

<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\Authenticator;

use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionCredentialSearchTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer;
use PunchoutCatalog\Shared\PunchoutCatalog\PunchoutCatalogConfig;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutConnectionConstsInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Communication\Plugin\PunchoutCatalog\CxmlRequestProtocolStrategyPlugin;
use PunchoutCatalog\Zed\PunchoutCatalog\Communication\Plugin\PunchoutCatalog\OciRequestProtocolStrategyPlugin;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCompanyBusinessUnitFacadeInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToVaultFacadeInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Persistence\PunchoutCatalogRepositoryInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Plugin\PunchoutCatalogProtocolStrategyPluginInterface;

class ConnectionAuthenticator implements ConnectionAuthenticatorInterface
{
    /**
     * @var \PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Plugin\PunchoutCatalogProtocolStrategyPluginInterface[]
     */
    protected $protocolStrategyPlugins;

    /**
     * @var \PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCompanyBusinessUnitFacadeInterface
     */
    protected $companyBusinessUnitFacade;

    /**
     * @var \PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToVaultFacadeInterface
     */
    protected $vaultFacade;

    /**
     * @var \PunchoutCatalog\Zed\PunchoutCatalog\Persistence\PunchoutCatalogRepositoryInterface
     */
    protected $punchoutCatalogRepository;

    public function __construct(
        PunchoutCatalogToCompanyBusinessUnitFacadeInterface $companyBusinessUnitFacade,
        PunchoutCatalogToVaultFacadeInterface $vaultFacade,
        PunchoutCatalogRepositoryInterface $punchoutCatalogRepository
    ) {
        $this->protocolStrategyPlugins = [
            new CxmlRequestProtocolStrategyPlugin(),
            new OciRequestProtocolStrategyPlugin(),
        ];
        
        $this->companyBusinessUnitFacade = $companyBusinessUnitFacade;
        $this->vaultFacade = $vaultFacade;
        $this->punchoutCatalogRepository = $punchoutCatalogRepository;
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer
     */
    public function authenticateRequest(PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer): PunchoutCatalogSetupRequestTransfer
    {
        $punchoutCatalogRequestTransfer
            ->requireContent()
            ->requireContentType()
            ->requireContext()
            ->requireFkCompanyBusinessUnit();

        $companyBusinessUnitTransfer = $this->companyBusinessUnitFacade->findCompanyBusinessUnitById(
            $punchoutCatalogRequestTransfer->getFkCompanyBusinessUnit()
        );
        
        if (!$companyBusinessUnitTransfer) {
            return $punchoutCatalogRequestTransfer
                ->setIsSuccess(false)
                ->addMessage(
                    (new MessageTransfer())
                        ->setValue(PunchoutConnectionConstsInterface::ERROR_AUTHENTICATION)
                );
        }

        $punchoutCatalogRequestTransfer->setCompanyBusinessUnit($companyBusinessUnitTransfer);
        
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
     * @param \PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Plugin\PunchoutCatalogProtocolStrategyPluginInterface $protocolStrategyPlugin
     * @param \Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer
     */
    public function applyProtocolStrategy(
        PunchoutCatalogProtocolStrategyPluginInterface $protocolStrategyPlugin,
        PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer
    ): PunchoutCatalogSetupRequestTransfer {
        $punchoutCatalogRequestTransfer = $protocolStrategyPlugin->setRequestProtocol($punchoutCatalogRequestTransfer);

        $punchoutCatalogRequestTransfer
            ->requireProtocolType()
            ->requireContext()
            ->requireProtocolData();

        $punchoutCatalogRequestTransfer = $protocolStrategyPlugin->setPunchoutCatalogConnection($punchoutCatalogRequestTransfer);

        if (null === $punchoutCatalogRequestTransfer->getContext()->getPunchoutCatalogConnection()) {
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

<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\Authenticator;

use Generated\Shared\Transfer\PunchoutCatalogConnectionCredentialSearchTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer;

use PunchoutCatalog\Shared\PunchoutCatalog\PunchoutCatalogConfig;
use PunchoutCatalog\Zed\PunchoutCatalog\Communication\Plugin\PunchoutCatalog\CxmlRequestProtocolStrategyPlugin;
use PunchoutCatalog\Zed\PunchoutCatalog\Communication\Plugin\PunchoutCatalog\OciRequestProtocolStrategyPlugin;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToCompanyBusinessUnitFacadeInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToVaultFacadeInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Persistence\PunchoutCatalogRepositoryInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Plugin\PunchoutCatalogProtocolStrategyPluginInterface;

use Spryker\Shared\Kernel\Transfer\Exception\RequiredTransferPropertyException;
use InvalidArgumentException;
use PunchoutCatalog\Zed\PunchoutCatalog\Exception\AuthenticateException;

class ConnectionAuthenticator implements ConnectionAuthenticatorInterface
{
    protected const ERROR_AUTHENTICATION = 'punchout-catalog.error.authentication';

    protected const ERROR_INVALID_DATA = 'punchout-catalog.error.invalid-data';

    protected const ERROR_MISSING_COMPANY_BUSINESS_UNIT = 'punchout-catalog.error.missing-company-business-unit';

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
     *
     * @throws AuthenticateException
     * @throws RequiredTransferPropertyException
     * @throws InvalidArgumentException
     */
    public function authenticateRequest(
        PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer
    ): PunchoutCatalogSetupRequestTransfer
    {
        $punchoutCatalogRequestTransfer
            ->requireContent()
            ->requireContentType()
            ->requireContext();
            //->requireFkCompanyBusinessUnit();

        if ($punchoutCatalogRequestTransfer->getFkCompanyBusinessUnit()) {
            $companyBusinessUnitTransfer = $this->companyBusinessUnitFacade->findCompanyBusinessUnitById(
                $punchoutCatalogRequestTransfer->getFkCompanyBusinessUnit()
            );

            if (!$companyBusinessUnitTransfer) {
                throw new AuthenticateException(self::ERROR_MISSING_COMPANY_BUSINESS_UNIT);
            }

            $punchoutCatalogRequestTransfer->setCompanyBusinessUnit($companyBusinessUnitTransfer);
        }

        foreach ($this->protocolStrategyPlugins as $strategy) {
            if ($strategy->isApplicable($punchoutCatalogRequestTransfer)) {
                return $this->applyProtocolStrategy($strategy, $punchoutCatalogRequestTransfer);
            }
        }

        throw new AuthenticateException(self::ERROR_INVALID_DATA);
    }

    /**
     * @param \PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Plugin\PunchoutCatalogProtocolStrategyPluginInterface $protocolStrategyPlugin
     * @param \Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer
     *
     * @throws AuthenticateException
     * @throws RequiredTransferPropertyException
     */
    protected function applyProtocolStrategy(
        PunchoutCatalogProtocolStrategyPluginInterface $protocolStrategyPlugin,
        PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer
    ): PunchoutCatalogSetupRequestTransfer
    {
        $punchoutCatalogRequestTransfer = $protocolStrategyPlugin->setRequestProtocol($punchoutCatalogRequestTransfer);

        $punchoutCatalogRequestTransfer
            ->requireProtocolType()
            ->requireContext()
            ->requireProtocolData();

        $punchoutCatalogRequestTransfer = $protocolStrategyPlugin->setPunchoutCatalogConnection(
            $punchoutCatalogRequestTransfer
        );

        if (null === $punchoutCatalogRequestTransfer->getContext()->getPunchoutCatalogConnection()) {
            throw new AuthenticateException(self::ERROR_AUTHENTICATION);
        }

        return $punchoutCatalogRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogConnectionCredentialSearchTransfer $connectionCredentialSearch
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer|null
     */
    public function findConnectionByCredential(
        PunchoutCatalogConnectionCredentialSearchTransfer $connectionCredentialSearch
    ): ?PunchoutCatalogConnectionTransfer
    {
        $connection = $this->punchoutCatalogRepository->findConnectionByCredential($connectionCredentialSearch);
        if ($connection === null || $connection->getIdPunchoutCatalogConnection() === null) {
            return null;
        }

        $connectionPassword = $this->vaultFacade->retrieve(
            PunchoutCatalogConfig::VAULT_TYPE_PUNCHOUT_CATALOG_CONNECTION_PASSWORD,
            $connection->getIdPunchoutCatalogConnection()
        );

        if ($connectionPassword === $connectionCredentialSearch->getPassword()) {
            return $connection;
        }

        return null;
    }
}

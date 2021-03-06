<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\RequestProcessor;

use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupResponseTransfer;
use InvalidArgumentException;
use PunchoutCatalog\Shared\PunchoutCatalog\PunchoutCatalogConstsInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Authenticator\ConnectionAuthenticatorInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Communication\Plugin\PunchoutCatalog\CxmlSetupRequestProcessorStrategyPlugin;
use PunchoutCatalog\Zed\PunchoutCatalog\Communication\Plugin\PunchoutCatalog\OciSetupRequestProcessorStrategyPlugin;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToGlossaryFacadeInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Exception\AuthenticateException;
use PunchoutCatalog\Zed\PunchoutCatalog\PunchoutCatalogConfig;
use Spryker\Shared\Kernel\Transfer\Exception\RequiredTransferPropertyException;

/**
 * Class RequestProcessor
 *
 * @package PunchoutCatalog\Zed\PunchoutCatalog\Business\RequestProcessor
 */
class RequestProcessor implements RequestProcessorInterface
{
    protected const DEFAULT_FORMAT = PunchoutCatalogConstsInterface::FORMAT_CXML;

    protected const ERROR_INVALID_DATA = 'punchout-catalog.error.invalid-data';

    protected const ERROR_UNEXPECTED = 'punchout-catalog.error.unexpected';
    
    protected const ERROR_AUTHENTICATION = 'punchout-catalog.error.authentication';
    
    /**
     * @var \PunchoutCatalog\Zed\PunchoutCatalog\Business\Authenticator\ConnectionAuthenticatorInterface
     */
    protected $connectionAuthenticator;

    /**
     * @var \PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Plugin\PunchoutCatalogRequestProcessorStrategyPluginInterface[]
     */
    protected $requestProcessorPlugins;

    /**
     * @var \PunchoutCatalog\Zed\PunchoutCatalog\PunchoutCatalogConfig
     */
    protected $punchoutCatalogConfig;

    /**
     * @var \PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToGlossaryFacadeInterface
     */
    protected $punchoutCatalogToGlossaryFacade;

    /**
     * @param \PunchoutCatalog\Zed\PunchoutCatalog\Business\Authenticator\ConnectionAuthenticatorInterface $connectionAuthenticator
     * @param \PunchoutCatalog\Zed\PunchoutCatalog\PunchoutCatalogConfig $punchoutCatalogConfig
     * @param \PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToGlossaryFacadeInterface $punchoutCatalogToGlossaryFacade
     */
    public function __construct(
        ConnectionAuthenticatorInterface $connectionAuthenticator,
        PunchoutCatalogConfig $punchoutCatalogConfig,
        PunchoutCatalogToGlossaryFacadeInterface $punchoutCatalogToGlossaryFacade
    )
    {
        $this->connectionAuthenticator = $connectionAuthenticator;
        $this->punchoutCatalogConfig = $punchoutCatalogConfig;
        $this->punchoutCatalogToGlossaryFacade = $punchoutCatalogToGlossaryFacade;

        $this->requestProcessorPlugins = [
            PunchoutCatalogConstsInterface::FORMAT_CXML => new CxmlSetupRequestProcessorStrategyPlugin(),
            PunchoutCatalogConstsInterface::FORMAT_OCI => new OciSetupRequestProcessorStrategyPlugin(),
        ];
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogSetupResponseTransfer
     */
    public function processRequest(
        PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer
    ): PunchoutCatalogSetupResponseTransfer
    {
        $punchoutCatalogRequestTransfer->setIsSuccess(false);
        try {
            $punchoutCatalogRequestTransfer = $this->connectionAuthenticator->authenticateRequest(
                $punchoutCatalogRequestTransfer
            );

            return $this->process($punchoutCatalogRequestTransfer);
        } catch (\Exception $exception) {
            return $this->processException($punchoutCatalogRequestTransfer, $exception);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogSetupResponseTransfer
     * @throws AuthenticateException
     */
    protected function process(
        PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer
    ): ?PunchoutCatalogSetupResponseTransfer
    {
        $punchoutCatalogRequestTransfer
            ->requireProtocolType()
            ->requireProtocolData()
            ->requireContext();

        $punchoutCatalogRequestTransfer->getContext()->requirePunchoutCatalogConnection();

        foreach ($this->requestProcessorPlugins as $requestProcessorPlugin) {
            if ($requestProcessorPlugin->isApplicable($punchoutCatalogRequestTransfer)) {
                return $requestProcessorPlugin->processRequest($punchoutCatalogRequestTransfer);
            }
        }

        throw new AuthenticateException(self::ERROR_INVALID_DATA);
    }

    /**
     * @param PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer
     * @param \Exception $exception
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogSetupResponseTransfer
     */
    protected function processException(PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer, \Exception $exception): ?PunchoutCatalogSetupResponseTransfer
    {
        if ($exception instanceof AuthenticateException) {
            if ($exception->getMessage() == self::ERROR_INVALID_DATA) {
                $code = self::ERROR_INVALID_DATA;
                $message = $this->translate($exception->getMessage(), $this->punchoutCatalogConfig->getDefaultLocaleName());
            } else {
                $code = self::ERROR_AUTHENTICATION;
                $message = $this->translate($exception->getMessage(), $this->punchoutCatalogConfig->getDefaultLocaleName());
            }
        } elseif (($exception instanceof InvalidArgumentException)
            || ($exception instanceof RequiredTransferPropertyException)
        ) {
            $code = self::ERROR_INVALID_DATA;
            $message = $this->translate($code, $this->punchoutCatalogConfig->getDefaultLocaleName());
        } else {
            $code = self::ERROR_UNEXPECTED;
            $message = $this->translate($code, $this->punchoutCatalogConfig->getDefaultLocaleName());
        }

        $errorStrategy = $this->requestProcessorPlugins[static::DEFAULT_FORMAT];
        foreach ($this->requestProcessorPlugins as $requestProcessorPlugin) {
            if ($requestProcessorPlugin->isApplicable($punchoutCatalogRequestTransfer)) {
                $errorStrategy = $requestProcessorPlugin;
                break;
            }
        }

        $messageTransfer = (new MessageTransfer())->setValue($code)->setTranslatedMessage($message);

        $response = $errorStrategy->processError($messageTransfer);
        $response->setContext($punchoutCatalogRequestTransfer->getContext());
        $response->addException($exception->getMessage());
        $response->addException($exception->getTraceAsString());

        if ($exception->getPrevious()) {
            $response->addException("Original Exception:\n" . $exception->getPrevious()->getMessage());
            $response->addException($exception->getPrevious()->getTraceAsString());
        }

        return $response;
    }

    /**
     * @param string $id
     * @param string $localeName
     * @param array $parameters
     *
     * @return string
     */
    protected function translate($id, $localeName, array $parameters = []): string
    {
        return $this->punchoutCatalogToGlossaryFacade->translate($id, $localeName, $parameters);
    }
}

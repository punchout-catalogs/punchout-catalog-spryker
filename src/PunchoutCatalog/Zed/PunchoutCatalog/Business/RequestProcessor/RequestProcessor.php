<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\RequestProcessor;

use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupResponseTransfer;

use PunchoutCatalog\Zed\PunchoutCatalog\Business\Authenticator\ConnectionAuthenticatorInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutConnectionConstsInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Communication\Plugin\PunchoutCatalog\CxmlSetupRequestProcessorStrategyPlugin;
use PunchoutCatalog\Zed\PunchoutCatalog\Communication\Plugin\PunchoutCatalog\OciSetupRequestProcessorStrategyPlugin;

use Spryker\Shared\Kernel\Transfer\Exception\RequiredTransferPropertyException;
use Spryker\Zed\ProductStorage\Exception\InvalidArgumentException;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Authenticator\Exception as AuthenticatorException;

/**
 * Class RequestProcessor
 *
 * @package PunchoutCatalog\Zed\PunchoutCatalog\Business\RequestProcessor
 */
class RequestProcessor implements RequestProcessorInterface
{
    protected const DEFAULT_FORMAT = PunchoutConnectionConstsInterface::FORMAT_CXML;
    
    /**
     * @var \PunchoutCatalog\Zed\PunchoutCatalog\Business\Authenticator\ConnectionAuthenticatorInterface
     */
    protected $connectionAuthenticator;
    
    /**
     * @var \PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Plugin\PunchoutCatalogRequestProcessorStrategyPluginInterface[]
     */
    protected $requestProcessorPlugins;

    /**
     * @param \PunchoutCatalog\Zed\PunchoutCatalog\Business\Authenticator\ConnectionAuthenticatorInterface $connectionAuthenticator
     */
    public function __construct(ConnectionAuthenticatorInterface $connectionAuthenticator)
    {
        $this->requestProcessorPlugins = [
            PunchoutConnectionConstsInterface::FORMAT_CXML => new CxmlSetupRequestProcessorStrategyPlugin(),
            PunchoutConnectionConstsInterface::FORMAT_OCI => new OciSetupRequestProcessorStrategyPlugin(),
        ];

        $this->connectionAuthenticator = $connectionAuthenticator;
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
     * @throws AuthenticatorException
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

        throw new AuthenticatorException(PunchoutConnectionConstsInterface::ERROR_INVALID_DATA);
    }
    
    /**
     * @param PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer
     * @param \Exception $exception
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogSetupResponseTransfer
     */
    protected function processException(PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer, \Exception $exception): ?PunchoutCatalogSetupResponseTransfer
    {
        if ($exception instanceof AuthenticatorException) {
            $code = $exception->getMessage();
        } elseif (($exception instanceof InvalidArgumentException)
            || ($exception instanceof RequiredTransferPropertyException)
        ) {
            $code = PunchoutConnectionConstsInterface::ERROR_INVALID_DATA;
        } else {
            $code = PunchoutConnectionConstsInterface::ERROR_UNEXPECTED;
        }
        
        $errorStrategy = $this->requestProcessorPlugins[static::DEFAULT_FORMAT];
        foreach ($this->requestProcessorPlugins as $requestProcessorPlugin) {
            if ($requestProcessorPlugin->isApplicable($punchoutCatalogRequestTransfer)) {
                $errorStrategy = $requestProcessorPlugin;
                break;
            }
        }
    
        $message = $this->translate($code);
        $mesageTransfer = (new MessageTransfer())->setValue($message)->setCode($code);
    
        $response = $errorStrategy->processError($mesageTransfer);
        $response->setContext($punchoutCatalogRequestTransfer->getContext());
        $response->addException($exception->getMessage());
        
        if ($exception->getPrevious()) {
            $response->addException("Original Exception:\n" . $exception->getPrevious()->getMessage());
        }
        
        return $response;
    }
    
    /**
     * @todo: use glossary
     * @param string $message
     *
     * @return string
     */
    protected function translate(string $message): string
    {
        return 'Translated - ' . $message;
    }
}

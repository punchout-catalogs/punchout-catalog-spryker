<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\RequestProcessor;

use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogSetupResponseTransfer;
use Spryker\Shared\Kernel\Transfer\Exception\RequiredTransferPropertyException;
use Spryker\Zed\ProductStorage\Exception\InvalidArgumentException;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\Authenticator\ConnectionAuthenticatorInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutConnectionConstsInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Communication\Plugin\PunchoutCatalog\CxmlSetupRequestProcessorStrategyPlugin;
use PunchoutCatalog\Zed\PunchoutCatalog\Communication\Plugin\PunchoutCatalog\OciSetupRequestProcessorStrategyPlugin;

/**
 * Class RequestProcessor
 *
 * @package PunchoutCatalog\Zed\PunchoutCatalog\Business\RequestProcessor
 */
class RequestProcessor implements RequestProcessorInterface
{
    protected const DEFAULT_FORMAT = PunchoutConnectionConstsInterface::FORMAT_CXML;

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
        try {
            $punchoutCatalogRequestTransfer = $this->connectionAuthenticator->authenticateRequest(
                $punchoutCatalogRequestTransfer
            );
    
            if ($punchoutCatalogRequestTransfer->getIsSuccess() === false) {
                return $this->createErrorResponse(
                    $punchoutCatalogRequestTransfer->getMessages()[0],
                    $punchoutCatalogRequestTransfer->getProtocolType()
                );
            }
    
            $punchoutCatalogResponseTransfer = $this->process($punchoutCatalogRequestTransfer);
            
            if ($punchoutCatalogResponseTransfer === null) {
                return $this->createErrorResponse($this->getMissingProcessorErrorMessage());
            }
    
            return $punchoutCatalogResponseTransfer;
        } catch (RequiredTransferPropertyException $transferPropertyException) {
            return $this->createErrorResponse(
                (new MessageTransfer())
                    ->setValue(PunchoutConnectionConstsInterface::ERROR_INVALID_DATA)
            );
        } catch (InvalidArgumentException $invalidArgumentException) {
            return $this->createErrorResponse(
                (new MessageTransfer())
                    ->setValue(PunchoutConnectionConstsInterface::ERROR_INVALID_DATA)
            );
        }
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogSetupRequestTransfer $punchoutCatalogRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogSetupResponseTransfer|null
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
                try {
                    return $requestProcessorPlugin->processRequest($punchoutCatalogRequestTransfer);
                } catch (RequiredTransferPropertyException $transferPropertyException) {
                    return $requestProcessorPlugin->processError(
                        (new MessageTransfer())
                            ->setValue($transferPropertyException->getMessage())
                    );
                } catch (InvalidArgumentException $invalidArgumentException) {
                    return $requestProcessorPlugin->processError(
                        (new MessageTransfer())
                            ->setValue($invalidArgumentException->getMessage())
                    );
                }
            }
        }

        return null;
    }

    /**
     * @param \Generated\Shared\Transfer\MessageTransfer $message
     * @param string|null $format
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogSetupResponseTransfer
     */
    protected function createErrorResponse(MessageTransfer $message, ?string $format = null): PunchoutCatalogSetupResponseTransfer
    {
        $format = $format ?? static::DEFAULT_FORMAT;
        return $this->requestProcessorPlugins[$format]->processError($message);
    }

    /**
     * @return \Generated\Shared\Transfer\MessageTransfer
     */
    protected function getMissingProcessorErrorMessage(): MessageTransfer
    {
        return (new MessageTransfer())
            ->setValue(PunchoutConnectionConstsInterface::ERROR_MISSING_REQUEST_PROCESSOR);
    }
}

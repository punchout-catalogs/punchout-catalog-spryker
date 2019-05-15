<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\CartProcessor;

use Exception;
use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestContextTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCancelRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer;

use Spryker\Shared\Kernel\Transfer\Exception\RequiredTransferPropertyException;
use Spryker\Zed\ProductStorage\Exception\InvalidArgumentException;

use PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutConnectionConstsInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Communication\Plugin\PunchoutCatalog\CxmlCartProcessorStrategyPlugin;
use PunchoutCatalog\Zed\PunchoutCatalog\Communication\Plugin\PunchoutCatalog\OciCartProcessorStrategyPlugin;
use PunchoutCatalog\Zed\PunchoutCatalog\Persistence\PunchoutCatalogRepositoryInterface;

/**
 * Class CartProcessor
 *
 * @package PunchoutCatalog\Zed\PunchoutCatalog\Business\CartProcessor
 */
class CartProcessor implements CartProcessorInterface
{
    protected const ERROR_MISSING_CONNECTION = 'punchout-catalog.error.missing-connection';
    protected const ERROR_MISSING_FORMAT_STRATEGY_PROCESSOR = 'punchout-catalog.error.missing-cart-format-strategy-processor';

    /**
     * @var \PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Plugin\PunchoutCatalogCartProcessorStrategyPluginInterface[]
     */
    protected $cartProcessorPlugins;

    /**
     * @var \PunchoutCatalog\Zed\PunchoutCatalog\Persistence\PunchoutCatalogRepositoryInterface
     */
    protected $punchoutCatalogRepository;

    public function __construct(PunchoutCatalogRepositoryInterface $punchoutCatalogRepository)
    {
        $this->punchoutCatalogRepository = $punchoutCatalogRepository;

        $this->cartProcessorPlugins = [
            PunchoutConnectionConstsInterface::FORMAT_CXML => new CxmlCartProcessorStrategyPlugin(),
            PunchoutConnectionConstsInterface::FORMAT_OCI => new OciCartProcessorStrategyPlugin(),
        ];
    }
    
    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogCancelRequestTransfer $punchoutCatalogCancelRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer
     */
    public function processCancel(PunchoutCatalogCancelRequestTransfer $punchoutCatalogCancelRequestTransfer): PunchoutCatalogCartResponseTransfer
    {
        $punchoutCatalogCartRequestTransfer = new PunchoutCatalogCartRequestTransfer();
        $punchoutCatalogCartRequestTransfer->fromArray($punchoutCatalogCancelRequestTransfer->toArray(), true);
        
        return $this->processCart($punchoutCatalogCartRequestTransfer);
    }
    
    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer $punchoutCatalogCartRequestTransfer
     *
     * @throws \Spryker\Zed\ProductStorage\Exception\InvalidArgumentException
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer
     */
    public function processCart(PunchoutCatalogCartRequestTransfer $punchoutCatalogCartRequestTransfer): PunchoutCatalogCartResponseTransfer
    {
        try {
            $punchoutCatalogCartRequestTransfer->requireContext();
            $punchoutCatalogCartRequestTransfer->getContext()->requireLocale();
            $punchoutCatalogCartRequestTransfer->getContext()->requirePunchoutCatalogConnectionId();
            
            $connection = $this->punchoutCatalogRepository->findConnectionById(
                $punchoutCatalogCartRequestTransfer->getContext()->getPunchoutCatalogConnectionId()
            );
            
            if ($connection === null) {
                throw new InvalidArgumentException(static::ERROR_MISSING_CONNECTION);
            }
            $punchoutCatalogCartRequestTransfer->getContext()->setPunchoutCatalogConnection($connection);
            
            $format = $connection->getFormat();
            if (!$format || !isset($this->cartProcessorPlugins[$format])) {
                throw new InvalidArgumentException(static::ERROR_MISSING_FORMAT_STRATEGY_PROCESSOR);
            }
            
            $punchoutCatalogCartResponseTransfer = $this->cartProcessorPlugins[$format]->processCart(
                $punchoutCatalogCartRequestTransfer
            );

            return $punchoutCatalogCartResponseTransfer;
        } catch (Exception $e) {
            die($e->getMessage());
            $punchoutCatalogResponseTransfer = new PunchoutCatalogCartResponseTransfer();
            $punchoutCatalogResponseTransfer->setIsSuccess(false);

            if (($e instanceof RequiredTransferPropertyException) || ($e instanceof InvalidArgumentException)) {
                $punchoutCatalogResponseTransfer->addMessage(
                    (new MessageTransfer())->setValue($e->getMessage())
                );
            } else {
                $punchoutCatalogResponseTransfer->addMessage(
                    (new MessageTransfer())->setValue(PunchoutConnectionConstsInterface::ERROR_GENERAL)
                );
            }

            return $punchoutCatalogResponseTransfer;
        }
    }
}

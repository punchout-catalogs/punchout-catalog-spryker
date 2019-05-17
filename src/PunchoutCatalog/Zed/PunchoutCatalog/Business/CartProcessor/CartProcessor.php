<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\CartProcessor;

use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestContextTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCancelRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer;
use Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer;

use PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutConnectionConstsInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Communication\Plugin\PunchoutCatalog\CxmlCartProcessorStrategyPlugin;
use PunchoutCatalog\Zed\PunchoutCatalog\Communication\Plugin\PunchoutCatalog\OciCartProcessorStrategyPlugin;
use PunchoutCatalog\Zed\PunchoutCatalog\Persistence\PunchoutCatalogRepositoryInterface;

use Spryker\Shared\Kernel\Transfer\Exception\RequiredTransferPropertyException;
use Spryker\Zed\ProductStorage\Exception\InvalidArgumentException;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\CartProcessor\Exception as CartException;

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
                throw new CartException(static::ERROR_MISSING_CONNECTION);
            }
            $punchoutCatalogCartRequestTransfer->getContext()->setPunchoutCatalogConnection($connection);
            
            $format = $connection->getFormat();
            if (!$format || !isset($this->cartProcessorPlugins[$format])) {
                throw new CartException(static::ERROR_MISSING_FORMAT_STRATEGY_PROCESSOR);
            }
            
            $punchoutCatalogCartResponseTransfer = $this->cartProcessorPlugins[$format]->processCart(
                $punchoutCatalogCartRequestTransfer
            );

            return $punchoutCatalogCartResponseTransfer;
        } catch (\Exception $e) {
            $punchoutCatalogResponseTransfer = new PunchoutCatalogCartResponseTransfer();
            $punchoutCatalogResponseTransfer->setIsSuccess(false);
            $punchoutCatalogResponseTransfer->addMessage(PunchoutConnectionConstsInterface::ERROR_GENERAL);
            $punchoutCatalogResponseTransfer->addException($e->getMessage());
            return $punchoutCatalogResponseTransfer;
        }
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

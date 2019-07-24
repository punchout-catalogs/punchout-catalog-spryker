<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\CartProcessor;

use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCancelRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer;

use PunchoutCatalog\Shared\PunchoutCatalog\PunchoutCatalogConstsInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\PunchoutCatalogConfig;
use PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToGlossaryFacadeInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Persistence\PunchoutCatalogRepositoryInterface;

use PunchoutCatalog\Zed\PunchoutCatalog\Communication\Plugin\PunchoutCatalog\CxmlCartProcessorStrategyPlugin;
use PunchoutCatalog\Zed\PunchoutCatalog\Communication\Plugin\PunchoutCatalog\OciCartProcessorStrategyPlugin;

use PunchoutCatalog\Zed\PunchoutCatalog\Exception\TransferredCartException;

/**
 * Class CartProcessor
 *
 * @package PunchoutCatalog\Zed\PunchoutCatalog\Business\CartProcessor
 */
class CartProcessor implements CartProcessorInterface
{
    protected const ERROR_GENERAL = 'punchout-catalog.error.general';
    protected const ERROR_MISSING_CONNECTION = 'punchout-catalog.error.missing-connection';
    protected const ERROR_MISSING_FORMAT = 'punchout-catalog.error.missing-cart-format';

    /**
     * @var \PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Plugin\PunchoutCatalogCartProcessorStrategyPluginInterface[]
     */
    protected $cartProcessorPlugins;

    /**
     * @var \PunchoutCatalog\Zed\PunchoutCatalog\Persistence\PunchoutCatalogRepositoryInterface
     */
    protected $punchoutCatalogRepository;

    /**
     * @var \PunchoutCatalog\Zed\PunchoutCatalog\PunchoutCatalogConfig
     */
    protected $punchoutCatalogConfig;

    /**
     * @var \PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToGlossaryFacadeInterface
     */
    protected $punchoutCatalogToGlossaryFacade;

    /**
     * @param \PunchoutCatalog\Zed\PunchoutCatalog\Persistence\PunchoutCatalogRepositoryInterface $punchoutCatalogRepository
     * @param \PunchoutCatalog\Zed\PunchoutCatalog\PunchoutCatalogConfig $punchoutCatalogConfig
     * @param \PunchoutCatalog\Zed\PunchoutCatalog\Dependency\Facade\PunchoutCatalogToGlossaryFacadeInterface $punchoutCatalogToGlossaryFacade
     */
    public function __construct(
        PunchoutCatalogRepositoryInterface $punchoutCatalogRepository,
        PunchoutCatalogConfig $punchoutCatalogConfig,
        PunchoutCatalogToGlossaryFacadeInterface $punchoutCatalogToGlossaryFacade
    )
    {
        $this->punchoutCatalogRepository = $punchoutCatalogRepository;
        $this->punchoutCatalogConfig = $punchoutCatalogConfig;
        $this->punchoutCatalogToGlossaryFacade = $punchoutCatalogToGlossaryFacade;

        $this->cartProcessorPlugins = [
            PunchoutCatalogConstsInterface::FORMAT_CXML => new CxmlCartProcessorStrategyPlugin(),
            PunchoutCatalogConstsInterface::FORMAT_OCI => new OciCartProcessorStrategyPlugin(),
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
                throw new TransferredCartException(static::ERROR_MISSING_CONNECTION);
            }
            $punchoutCatalogCartRequestTransfer->getContext()->setPunchoutCatalogConnection($connection);

            $format = $connection->getFormat();
            if (!$format || !isset($this->cartProcessorPlugins[$format])) {
                throw new TransferredCartException(static::ERROR_MISSING_FORMAT);
            }

            $punchoutCatalogCartResponseTransfer = $this->cartProcessorPlugins[$format]->processCart(
                $punchoutCatalogCartRequestTransfer
            );

            return $punchoutCatalogCartResponseTransfer;
        } catch (\Exception $e) {
            if ($e instanceof TransferredCartException) {
                $message = $e->getMessage();
                $code = $e->getMessage();
            } else {
                $message = self::ERROR_GENERAL;
                $code = self::ERROR_GENERAL;
            }

            $localeName = $this->getCurrentLocale($punchoutCatalogCartRequestTransfer);

            $messageTransfer = (new MessageTransfer())
                ->setTranslatedMessage($this->translate($message, $localeName))
                ->setValue($code);

            $punchoutCatalogResponseTransfer = new PunchoutCatalogCartResponseTransfer();
            $punchoutCatalogResponseTransfer->setIsSuccess(false);
            $punchoutCatalogResponseTransfer->addMessage($messageTransfer);
            $punchoutCatalogResponseTransfer->addException($e->getMessage());

            if ($e->getPrevious()) {
                $punchoutCatalogResponseTransfer->addException("Original Exception:\n" . $e->getPrevious()->getMessage());
                $punchoutCatalogResponseTransfer->addException($e->getPrevious()->getTraceAsString());
            }

            return $punchoutCatalogResponseTransfer;
        }
    }

    /**
     * @param PunchoutCatalogCartRequestTransfer $punchoutCatalogCartRequestTransfer
     *
     * @return string
     */
    protected function getCurrentLocale(PunchoutCatalogCartRequestTransfer $punchoutCatalogCartRequestTransfer): string
    {
        if ($punchoutCatalogCartRequestTransfer->getContext()
            && $punchoutCatalogCartRequestTransfer->getContext()->getLocale()
        ) {
            return str_replace('-', '_', $punchoutCatalogCartRequestTransfer->getContext()->getLocale());
        }
        return $this->punchoutCatalogConfig->getDefaultLocaleName();
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

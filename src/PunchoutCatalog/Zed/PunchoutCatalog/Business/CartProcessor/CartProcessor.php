<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Zed\PunchoutCatalog\Business\CartProcessor;

use Exception;
use Generated\Shared\Transfer\MessageTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestOptionsTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer;
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
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer $punchoutCatalogCartRequestTransfer
     *
     * @throws \Spryker\Zed\ProductStorage\Exception\InvalidArgumentException
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogCartResponseTransfer
     */
    public function processCart(PunchoutCatalogCartRequestTransfer $punchoutCatalogCartRequestTransfer): PunchoutCatalogCartResponseTransfer
    {
        try {
            $connection = $this->getCurrentConnection();
            if ($connection === null) {
                throw new InvalidArgumentException(static::ERROR_MISSING_CONNECTION);
            }

            $format = $connection->getFormat();
            if (!$format || !isset($this->cartProcessorPlugins[$format])) {
                throw new InvalidArgumentException(static::ERROR_MISSING_FORMAT_STRATEGY_PROCESSOR);
            }

            $punchoutCatalogCartResponseTransfer = $this->cartProcessorPlugins[$format]->processCart(
                $punchoutCatalogCartRequestTransfer,
                $this->getCurrentCartOptions()
            );
            $punchoutCatalogCartRequestTransfer->setPunchoutCatalogConnection($this->getCurrentConnection());
            $punchoutCatalogCartResponseTransfer->getContext()->setRequest($punchoutCatalogCartRequestTransfer);
            return $punchoutCatalogCartResponseTransfer;
        } catch (Exception $e) {
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

    /**
     * @return \Generated\Shared\Transfer\PunchoutCatalogCartRequestOptionsTransfer
     */
    protected function getCurrentCartOptions(): PunchoutCatalogCartRequestOptionsTransfer
    {
        return (new PunchoutCatalogCartRequestOptionsTransfer())
            ->fromArray([
                //'protocol_data' => $this->getFakeOciSessionProtocolData(),
                'protocol_data' => $this->getFakeCxmlSessionProtocolData(),
                'punchout_catalog_connection' => $this->getCurrentConnection()->toArray(),
            ]);
    }

    /**
     * @todo: find connection id/uuid in session data
     *
     * @Karoly how can i get connection id from session?
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogConnectionTransfer|null
     */
    protected function getCurrentConnection(): ?PunchoutCatalogConnectionTransfer
    {
        //@todo: get data from session | for test add your uuid
        $uuidCxmlBase64 = '638fe408-e6ed-56ae-b52c-983488bcd4c1';
        $uuidCxmlUrlEncoded = 'becc5d59-94d4-5498-95d2-ee12c37c57b5';
        $uuidOci = '97915852-9cd5-5425-a568-fe1232d4e27c';

        //$uuid = $uuidOci;
        //$uuid = $uuidCxmlBase64;
        $uuid = $uuidCxmlUrlEncoded;

        return $this->punchoutCatalogRepository->findConnectionByUuid($uuid);
    }

    /**
     * @todo: find data in session data
     *
     * @Karoly how can i get it from session?
     *
     * @return array
     */
    protected function getFakeCxmlSessionProtocolData(): array
    {
        //Demo PEX cXML
        $url_cxml = 'https://dev.buyerquest.net/cs3/punchoutclient/transactions/cxmlresponse/conn_id/12/';
        $buyer_cookie = '9d5687e92e09c69f670bd0cfb9040b97';//value copied from Demo PEX cookie TRX list
        $data = [
            'cxml_to_credentials' => [
                'identity' => 'DemoCS3',
                'domain' => 'NetworkId',
            ],
            'cxml_sender_credentials' => [
                'identity' => 'BuyerQuestInc',
                'domain' => 'NetworkId',
            ],
            'cart' => [
                'url' => $url_cxml,
                'buyerCookie' => $buyer_cookie,
                'deploymentMode' => 'production',
                'operation' => 'create',
            ],
        ];
        return $data;
    }

    /**
     * @todo: find data in session data
     *
     * @Karoly how can i get it from session?
     *
     * @return array
     */
    protected function getFakeOciSessionProtocolData(): array
    {
        //Demo PEX OCI
        $url_oci = 'https://dev.buyerquest.net/cs3/punchoutclient/transactions/ociresponse/conn_id/18/';
        $data = [
            'oci_credentials' => [
                'username' => 'test',
                'password' => 'test12',
            ],
            'cart' => [
                'url' => $url_oci,
                'operation' => 'create',
            ],
        ];
        return $data;
    }
}

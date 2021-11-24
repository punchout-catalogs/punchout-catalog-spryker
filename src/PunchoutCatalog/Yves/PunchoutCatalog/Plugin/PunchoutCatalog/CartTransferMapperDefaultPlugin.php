<?php

/**
 * Copyright © 2018-present PunchOut Catalogs LLC. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Yves\PunchoutCatalog\Plugin\PunchoutCatalog;

use ArrayObject;
use Generated\Shared\Transfer\ItemTransfer as QuoteItemTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogDocumentCartCustomerTransfer;
use Generated\Shared\Transfer\PunchoutCatalogDocumentCartItemTransfer;
use Generated\Shared\Transfer\PunchoutCatalogDocumentCartTransfer;
use Generated\Shared\Transfer\PunchoutCatalogDocumentCustomAttributeTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Plugin\CartTransferMapperPluginInterface;
use Spryker\Yves\Kernel\AbstractPlugin;

/**
 * @method \PunchoutCatalog\Yves\PunchoutCatalog\PunchoutCatalogFactory getFactory()
 */
class CartTransferMapperDefaultPlugin extends AbstractPlugin implements CartTransferMapperPluginInterface
{
    protected const BUNDLE_MODE_SINGLE = 'single';
    protected const BUNDLE_MODE_COMPOSITE = 'composite';

    protected const TOTALS_MODE_HEADER = 'header';
    protected const TOTALS_MODE_LINE = 'line';

    protected const BUNDLE_COMPOSITE_PRICE_LEVEL = 'groupLevel';
    protected const BUNDLE_COMPOSITE_ITEM_TYPE = 'composite';
    protected const BUNDLE_CHILD_ITEM_TYPE = 'item';

    protected const PRICE_MODE_NET = 'NET_MODE';
    protected const PRICE_MODE_GROSS = 'GROSS_MODE';

    protected const CHILD_DESCRIPTION_SEPARATOR = ' x ';

    /**
     * @var \PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Plugin\CartItemTransformerPluginInterface[]
     */
    protected $cartItemTransformerPlugins;

    /**
     * @var string
     */
    protected $currentLocale;

    /**
     * @var \PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToGlossaryStorageClientInterface
     */
    protected $glossaryStorageClient;

    /**
     * @var \PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToMoneyClientInterface
     */
    protected $moneyClient;

    /**
     * @var \PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToProductStorageClientInterface
     */
    protected $productStorageClient;

    /**
     * @var \PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToCustomerClientInterface
     */
    protected $customerClient;

    /**
     * @var array
     */
    protected $cartMapping = [];

    /**
     * @var array
     */
    protected $cartItemMapping = [];

    /**
     * @var array
     */
    protected $cartCustomerMapping = [];

    /**
     * @var int
     */
    private $lineNumber = 0;

    /**
     * @param \PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Plugin\CartItemTransformerPluginInterface[] $cartItemTransformerPlugins
     */
    public function __construct(array $cartItemTransformerPlugins = [])
    {
        $this->cartItemTransformerPlugins = $cartItemTransformerPlugins;

        $this->glossaryStorageClient = $this->getFactory()->getGlossaryStorageClient();
        $this->moneyClient = $this->getFactory()->getMoneyClient();
        $this->productStorageClient = $this->getFactory()->getProductStorageClient();
        $this->customerClient = $this->getFactory()->getCustomerClient();
        $this->currentLocale = $this->getFactory()->getStore()->getCurrentLocale();

        $this->cartMapping = array_merge($this->cartMapping, $this->getFactory()->getModuleConfig()->getCustomCartMapping());
        $this->cartItemMapping = array_merge($this->cartItemMapping, $this->getFactory()->getModuleConfig()->getCustomCartItemMapping());
        $this->cartCustomerMapping = array_merge($this->cartCustomerMapping, $this->getFactory()->getModuleConfig()->getCustomCartCustomerMapping());
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer $cartRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer
     */
    public function mapQuoteTransferToPunchoutCatalogCartRequestTransfer(
        QuoteTransfer $quoteTransfer,
        PunchoutCatalogCartRequestTransfer $cartRequestTransfer
    ): PunchoutCatalogCartRequestTransfer
    {
        if (!$quoteTransfer || !$quoteTransfer->getItems()) {
            return $cartRequestTransfer;
        }

        $cartRequestTransfer->setCart(new PunchoutCatalogDocumentCartTransfer());

        $this->resetLineNumber();
        $this->prepareHeader($quoteTransfer, $cartRequestTransfer);
        $this->prepareCustomer($quoteTransfer, $cartRequestTransfer);
        $this->prepareLineItems($quoteTransfer, $cartRequestTransfer);
        $this->prepareTotals($quoteTransfer, $cartRequestTransfer);

        return $cartRequestTransfer->setCart(
            $this->applyCustomizations(
                $quoteTransfer,
                $cartRequestTransfer->getCart(),
                $this->cartMapping
            )
        );
    }

    /**
     * Reset line number counter
     * @see mapQuoteTransferToPunchoutCatalogCartRequestTransfer
     */
    protected function resetLineNumber()
    {
        $this->lineNumber = 0;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer $cartRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer
     */
    protected function prepareHeader(
        QuoteTransfer $quoteTransfer,
        PunchoutCatalogCartRequestTransfer $cartRequestTransfer
    ): PunchoutCatalogCartRequestTransfer
    {
        $documentCartTransfer = $cartRequestTransfer->getCart();

        $documentCartTransfer->setCurrency($quoteTransfer->getCurrency()->getCode());
        $documentCartTransfer->setCartNote($quoteTransfer->getCartNote());
        $documentCartTransfer->setLocale($this->toLang($this->currentLocale));

        //PRICING & RATES
        if ($quoteTransfer->getTotals()) {
            $documentCartTransfer->setSubtotal(
                $this->toAmount($quoteTransfer->getTotals()->getSubtotal(), $documentCartTransfer->getCurrency())
            );
            $documentCartTransfer->setGrandTotal(
                $this->toAmount($quoteTransfer->getTotals()->getGrandTotal(), $documentCartTransfer->getCurrency())
            );
            $documentCartTransfer->setNetTotal(
                $this->toAmount($quoteTransfer->getTotals()->getNetTotal(), $documentCartTransfer->getCurrency())
            );
        }

        return $cartRequestTransfer;
    }

    /**
     * @param string $locale
     *
     * @return string
     */
    protected function toLang($locale)
    {
        return str_replace('_', '-', $locale);
    }

    /**
     * @param int $amount
     * @param string|null $isoCode
     *
     * @return float
     */
    protected function toAmount(int $amount = null, ?string $isoCode)
    {
        $amount = (int)$amount;
        $currency = $this->moneyClient->fromInteger($amount, $isoCode);

        $fraction = 10;

        if ($currency->getCurrency()->getFractionDigits() > 1) {
            $fraction = pow(10, $currency->getCurrency()->getFractionDigits());
        }

        $floatAmount = $currency->getAmount() / $fraction;

        return round($floatAmount, (int)$currency->getCurrency()->getFractionDigits());
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer $cartRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer
     */
    protected function prepareCustomer(
        QuoteTransfer $quoteTransfer,
        PunchoutCatalogCartRequestTransfer $cartRequestTransfer
    ): PunchoutCatalogCartRequestTransfer
    {
        if ($quoteTransfer->getCustomer()) {
            $customer = new PunchoutCatalogDocumentCartCustomerTransfer();
            $customer->setEmail($quoteTransfer->getCustomer()->getEmail());
            $customer->setFirstName($quoteTransfer->getCustomer()->getFirstName());
            $customer->setLastName($quoteTransfer->getCustomer()->getLastName());
            $customer->setInternalId($quoteTransfer->getCustomer()->getIdCustomer());

            $cartRequestTransfer->setCustomer($customer);
        }

        return $cartRequestTransfer->setCustomer(
            $this->applyCustomizations(
                $quoteTransfer->getCustomer(),
                $cartRequestTransfer->getCustomer(),
                $this->cartCustomerMapping,
                [$quoteTransfer]
            )
        );
    }

    /**
     * @param \Spryker\Shared\Kernel\Transfer\TransferInterface $inputTransfer
     * @param \Spryker\Shared\Kernel\Transfer\TransferInterface $outputTransfer
     * @param array $mapping
     * @param array $getterArgs
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer|\Generated\Shared\Transfer\PunchoutCatalogDocumentCartItemTransfer|mixed|string
     */
    protected function applyCustomizations($inputTransfer, $outputTransfer, array $mapping, array $getterArgs = [])
    {
        foreach ($mapping as $field => $value) {
            if (is_string($value)) {
                $getter = 'get' . $this->underscoreToCamelCase($value);
                if (method_exists($inputTransfer, $getter)) {
                    $value = call_user_func([$inputTransfer, $getter]);
                }
            } elseif (is_callable($value)) {
                $value = call_user_func_array($value, array_merge([$inputTransfer, $outputTransfer], $getterArgs, [$this]));
            }

            if (is_string($field)) {
                $setter = 'set' . $this->underscoreToCamelCase($field);
                if (method_exists($outputTransfer, $setter)) {
                    call_user_func_array([$outputTransfer, $setter], [$value]);
                }
            } else {
                $outputTransfer = $value;
            }
        }

        return $outputTransfer;
    }

    /**
     * Convert underscore_text to CamelCase
     *
     * @param string $string
     * @param string $separator
     *
     * @return string
     */
    protected function underscoreToCamelCase($string, $separator = '_')
    {
        $explodedString = explode($separator, $string);

        $result = '';

        foreach ($explodedString as $part) {
            $result .= ucfirst($part);
        }

        return $result;
    }

    /**
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer $cartRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer
     */
    protected function prepareLineItems(
        QuoteTransfer $quoteTransfer,
        PunchoutCatalogCartRequestTransfer $cartRequestTransfer
    ): PunchoutCatalogCartRequestTransfer
    {
        $childItems = [];
        $totalQty = 0;

        $cartItems = $this->getCartItems($quoteTransfer);
        if ($cartItems) {
            foreach ($cartItems as $quoteItemTransfer) {
                $documentCartItemTransfer = new PunchoutCatalogDocumentCartItemTransfer();

                $documentCartItemTransfer = $this->mapQuoteItemTransferToPunchoutCatalogDocumentCartItemTransfer(
                    $quoteTransfer,
                    $quoteItemTransfer,
                    $documentCartItemTransfer->setLineNumber($this->getNextLineNumber())
                );

                if ($quoteItemTransfer->getChildBundleItems()->count()
                    && $this->getBundleMode() == self::BUNDLE_MODE_COMPOSITE
                ) {
                    $documentCartItemTransfer->setCompositeItemType(self::BUNDLE_COMPOSITE_PRICE_LEVEL);
                    $documentCartItemTransfer->setItemType(self::BUNDLE_COMPOSITE_ITEM_TYPE);

                    $childItems[$documentCartItemTransfer->getInternalId()] = [
                        'bundleProduct' => $documentCartItemTransfer, 'bundleItems' => [],
                    ];

                    foreach ($quoteItemTransfer->getChildBundleItems() as $childCartItem) {
                        $childItems[$documentCartItemTransfer->getInternalId()]['bundleItems'][] = $childCartItem;
                        $totalQty += ((int)$quoteItemTransfer->getQuantity() * (int)$childCartItem->getQuantity());
                    }
                } else {
                    $totalQty += (int)$quoteItemTransfer->getQuantity();
                }

                $cartRequestTransfer->addCartItem($documentCartItemTransfer);
            }
        }

        if ($childItems) {
            foreach ($childItems as $complexData) {
                foreach ($complexData['bundleItems'] as $quoteItemTransfer) {
                    $bundleProduct = $complexData['bundleProduct'];

                    $documentCartItemTransfer = new PunchoutCatalogDocumentCartItemTransfer();
                    $documentCartItemTransfer->setItemType(self::BUNDLE_CHILD_ITEM_TYPE);
                    $documentCartItemTransfer->setParentLineNumber($bundleProduct->getLineNumber());
                    $documentCartItemTransfer->setParentInternalId($bundleProduct->getInternalId());

                    $documentCartItemTransfer = $this->mapQuoteItemTransferToPunchoutCatalogDocumentCartItemTransfer(
                        $quoteTransfer,
                        $quoteItemTransfer,
                        $documentCartItemTransfer->setLineNumber($this->getNextLineNumber())
                    );

                    $cartRequestTransfer->addCartItem($documentCartItemTransfer);
                }
            }
        }

        if ($totalQty > 0) {
            $cartRequestTransfer->getCart()->setTotalQty($totalQty);
        }

        return $cartRequestTransfer;
    }

    /**
     * @see: \SprykerShop\Yves\CartPage\Model\CartItemReader
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\ItemTransfer[]
     */
    public function getCartItems(QuoteTransfer $quoteTransfer): array
    {
        $cartItems = $quoteTransfer->getItems()->getArrayCopy();

        foreach ($this->cartItemTransformerPlugins as $cartItemTransformerPlugin) {
            $cartItems = $cartItemTransformerPlugin->transformCartItems($cartItems, $quoteTransfer);
        }

        return $cartItems;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\ItemTransfer $quoteItemTransfer
     * @param \Generated\Shared\Transfer\PunchoutCatalogDocumentCartItemTransfer $documentCartItemTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogDocumentCartItemTransfer
     */
    public function mapQuoteItemTransferToPunchoutCatalogDocumentCartItemTransfer(
        QuoteTransfer $quoteTransfer,
        QuoteItemTransfer $quoteItemTransfer,
        PunchoutCatalogDocumentCartItemTransfer $documentCartItemTransfer
    ): PunchoutCatalogDocumentCartItemTransfer
    {
        $this->addItemCommonDetails($quoteTransfer, $quoteItemTransfer, $documentCartItemTransfer);
        $this->addItemPriceDetails($quoteTransfer, $quoteItemTransfer, $documentCartItemTransfer);
        $this->addItemOptionDetails($quoteTransfer, $quoteItemTransfer, $documentCartItemTransfer);
        $this->addItemDescriptionDetails($quoteTransfer, $quoteItemTransfer, $documentCartItemTransfer);
        $this->addItemAttributesDetails($quoteTransfer, $quoteItemTransfer, $documentCartItemTransfer);

        $this->applyCustomizations(
            $quoteItemTransfer,
            $documentCartItemTransfer,
            $this->cartItemMapping,
            [$quoteTransfer]
        );

        return $this->addItemAfterCustomDetails($documentCartItemTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\ItemTransfer $quoteItemTransfer
     * @param \Generated\Shared\Transfer\PunchoutCatalogDocumentCartItemTransfer $documentCartItemTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogDocumentCartItemTransfer
     */
    public function addItemCommonDetails(
        QuoteTransfer $quoteTransfer,
        QuoteItemTransfer $quoteItemTransfer,
        PunchoutCatalogDocumentCartItemTransfer $documentCartItemTransfer
    ): PunchoutCatalogDocumentCartItemTransfer
    {
        $documentCartItemTransfer->setInternalId($this->getQuoteItemInternalId($quoteItemTransfer));
        $documentCartItemTransfer->setSupplierId($this->getDefaultSupplierId());
        $documentCartItemTransfer->setLocale($this->toLang($this->currentLocale));
        $documentCartItemTransfer->setName(trim($quoteItemTransfer->getName()));
        $documentCartItemTransfer->setQuantity($quoteItemTransfer->getQuantity());
        $documentCartItemTransfer->setSku($quoteItemTransfer->getSku());
        $documentCartItemTransfer->setGroupKey($quoteItemTransfer->getGroupKey());
        $documentCartItemTransfer->setAbstractSku($quoteItemTransfer->getAbstractSku());
        $documentCartItemTransfer->setCartNote($quoteItemTransfer->getCartNote());
        $code = 'ITEM';

        $documentCartItemTransfer->setUom($this->convertUom($code));

        return $documentCartItemTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $quoteItemTransfer
     *
     * @return string
     */
    protected function getQuoteItemInternalId(QuoteItemTransfer $quoteItemTransfer = null): string
    {
        $internalId = md5(
            json_encode($quoteItemTransfer ? $quoteItemTransfer->toArray() : null)
            . '_' . microtime(true)
            . '_' . uniqid('', true)
        );

        return $this->getFactory()
            ->getUtilUuidGeneratorService()
            ->generateUuid5FromObjectId($internalId);
    }

    /**
     * @return string
     */
    protected function getDefaultSupplierId(): string
    {
        $cartDetails = $this->getPunchoutCartDetails();

        return $cartDetails['default_supplier_id'] ?? null;
    }

    /**
     * @return array
     */
    protected function getPunchoutCartDetails()
    {
        $impersonalDetails = $this->customerClient
            ->getCustomer()
            ->getPunchoutCatalogImpersonationDetails();

        return $impersonalDetails['punchout_catalog_connection_cart'] ?? [];
    }

    /**
     * @param $value
     * @return mixed
     */
    protected function convertUom($value)
    {
        $units = [
            'KILO' => 'KGM',
            'ITEM' => 'EA',
            'BOX' => 'BX',
            'GRAM' => 'GRM',
            'TONE' => 'TNE',
            'PUND' => 'LBR',
            'METR' => 'MTR',
            'CMET' => 'CMT',
            'MMET' => 'MMT',
            'KMET' => 'KMT',
            'INCH' => 'INH',
            'YARD' => 'YRD',
            'FOOT' => 'FT',
            'MILE' => 'M52',
        ];

        return $units[$value] ?? $value;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\ItemTransfer $quoteItemTransfer
     * @param \Generated\Shared\Transfer\PunchoutCatalogDocumentCartItemTransfer $documentCartItemTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogDocumentCartItemTransfer
     */
    public function addItemPriceDetails(
        QuoteTransfer $quoteTransfer,
        QuoteItemTransfer $quoteItemTransfer,
        PunchoutCatalogDocumentCartItemTransfer $documentCartItemTransfer
    ): PunchoutCatalogDocumentCartItemTransfer
    {
        if ($quoteTransfer->getCurrency() !== null) {
            $documentCartItemTransfer->setCurrency($quoteTransfer->getCurrency()->getCode());
        }

        //PRICING
        $documentCartItemTransfer->setUnitPrice(
            $this->toAmount($quoteItemTransfer->getUnitPrice(), $documentCartItemTransfer->getCurrency())
        );
        $documentCartItemTransfer->setSumPrice(
            $this->toAmount($quoteItemTransfer->getSumPrice(), $documentCartItemTransfer->getCurrency())
        );
        $documentCartItemTransfer->setUnitTotal(
            $this->toAmount($quoteItemTransfer->getUnitSubtotalAggregation(), $documentCartItemTransfer->getCurrency())
        );
        $documentCartItemTransfer->setSumTotal(
            $this->toAmount($quoteItemTransfer->getSumSubtotalAggregation(), $documentCartItemTransfer->getCurrency())
        );

        //Taxes
        $documentCartItemTransfer->setTaxRate($quoteItemTransfer->getTaxRate());

        if ($quoteItemTransfer->getUnitTaxAmount() !== null) {
            $documentCartItemTransfer->setUnitTaxAmount(
                $this->toAmount($quoteItemTransfer->getUnitTaxAmount(), $documentCartItemTransfer->getCurrency())
            );
        }
        if ($quoteItemTransfer->getSumTaxAmount() !== null) {
            $documentCartItemTransfer->setSumTaxAmount(
                $this->toAmount($quoteItemTransfer->getSumTaxAmount(), $documentCartItemTransfer->getCurrency())
            );
        }
        if ($quoteItemTransfer->getUnitTaxAmountFullAggregation() !== null) {
            $documentCartItemTransfer->setUnitTaxTotal(
                $this->toAmount($quoteItemTransfer->getUnitTaxAmountFullAggregation(), $documentCartItemTransfer->getCurrency())
            );
        }
        if ($quoteItemTransfer->getSumTaxAmountFullAggregation() !== null) {
            $documentCartItemTransfer->setSumTaxTotal(
                $this->toAmount($quoteItemTransfer->getSumTaxAmountFullAggregation(), $documentCartItemTransfer->getCurrency())
            );
        }

        //Discounts
        if ($quoteItemTransfer->getSumDiscountAmountAggregation() !== null) {
            $documentCartItemTransfer->setSumDiscountTotal(
                $this->toAmount($quoteItemTransfer->getSumDiscountAmountAggregation(), $documentCartItemTransfer->getCurrency())
            );
        }
        if ($quoteItemTransfer->getUnitDiscountAmountAggregation() !== null) {
            $documentCartItemTransfer->setUnitDiscountTotal(
                $this->toAmount($quoteItemTransfer->getUnitDiscountAmountAggregation(), $documentCartItemTransfer->getCurrency())
            );
        }

        return $documentCartItemTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\ItemTransfer $quoteItemTransfer
     * @param \Generated\Shared\Transfer\PunchoutCatalogDocumentCartItemTransfer $documentCartItemTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogDocumentCartItemTransfer
     */
    public function addItemOptionDetails(
        QuoteTransfer $quoteTransfer,
        QuoteItemTransfer $quoteItemTransfer,
        PunchoutCatalogDocumentCartItemTransfer $documentCartItemTransfer
    ): PunchoutCatalogDocumentCartItemTransfer
    {
        if ($quoteItemTransfer->getProductOptions()) {
            $options = new ArrayObject();
            foreach ($quoteItemTransfer->getProductOptions() as $optionTransfer) {
                $option = (new PunchoutCatalogDocumentCustomAttributeTransfer())
                    ->setCode($optionTransfer->getGroupName())
                    ->setValue($optionTransfer->getValue());

                $options->append(
                    $this->translateCustomAttribute($option)
                );
            }
            $documentCartItemTransfer->setOptions($options);
        }

        return $documentCartItemTransfer->setCustomAttributes(new ArrayObject());
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogDocumentCustomAttributeTransfer $attributeTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogDocumentCustomAttributeTransfer
     */
    protected function translateCustomAttribute(PunchoutCatalogDocumentCustomAttributeTransfer $attributeTransfer)
    {
        $attributeTransfer->setCode(
            $this->glossaryStorageClient->translate(
                $attributeTransfer->getCode(),
                $this->currentLocale
            )
        );

        $attributeTransfer->setValue(
            $this->glossaryStorageClient->translate(
                $attributeTransfer->getValue(),
                $this->currentLocale
            )
        );

        return $attributeTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\ItemTransfer $quoteItemTransfer
     * @param \Generated\Shared\Transfer\PunchoutCatalogDocumentCartItemTransfer $documentCartItemTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogDocumentCartItemTransfer
     */
    public function addItemDescriptionDetails(
        QuoteTransfer $quoteTransfer,
        QuoteItemTransfer $quoteItemTransfer,
        PunchoutCatalogDocumentCartItemTransfer $documentCartItemTransfer
    ): PunchoutCatalogDocumentCartItemTransfer
    {
        $productAbstractStorageData = $this->productStorageClient->findProductAbstractStorageData(
            $quoteItemTransfer->getIdProductAbstract(),
            $this->currentLocale
        );

        $productDescription = $this->limitDescription(trim($productAbstractStorageData['description']));
        $documentCartItemTransfer->setDescription(trim($productDescription));

        $childrenDescriptions = [];
        if ($quoteItemTransfer->getChildBundleItems()->count()
            && $this->getBundleMode() == self::BUNDLE_MODE_SINGLE
        ) {
            foreach ($quoteItemTransfer->getChildBundleItems() as $childCartItem) {
                $childrenDescriptions[] = $this->prepareChildDescription($childCartItem, $documentCartItemTransfer);
            }
        }

        $longDescriptionParts = [
            trim($productAbstractStorageData['description']),
        ];

        foreach ($quoteItemTransfer->getProductOptions() as $option) {
            $value = (string)$this->glossaryStorageClient->translate($option->getValue(), $this->currentLocale);
            $name = (string)$this->glossaryStorageClient->translate($option->getGroupName(), $this->currentLocale);
            if ($value || $name) {
                $longDescriptionParts[] = sprintf(trim($name) . '=' . trim($value));
            }
        }

        $longDescriptionParts = array_merge($longDescriptionParts, $childrenDescriptions);
        $longDescriptionParts = array_map("trim", $longDescriptionParts);
        $productLongDescription = implode("\n", array_filter($longDescriptionParts));

        $documentCartItemTransfer->setLongDescription(trim($productLongDescription));

        return $documentCartItemTransfer;
    }

    /**
     * @param string $description
     *
     * @return string
     */
    protected function limitDescription($description)
    {
        $cartDetails = $this->getPunchoutCartDetails();
        if (!empty($cartDetails['max_description_length'])) {
            $length = (int)($cartDetails['max_description_length']);

            return mb_substr($description, 0, $length);
        }

        return $description;
    }

    /**
     * @return string
     */
    protected function getBundleMode(): string
    {
        $cartDetails = $this->getPunchoutCartDetails();

        return $cartDetails['bundle_mode'] ?? self::BUNDLE_MODE_SINGLE;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $quoteItemTransfer
     * @param \Generated\Shared\Transfer\PunchoutCatalogDocumentCartItemTransfer $documentCartItemTransfer
     *
     * @return string
     */
    protected function prepareChildDescription(QuoteItemTransfer $quoteItemTransfer, PunchoutCatalogDocumentCartItemTransfer $documentCartItemTransfer)
    {
        $desc = [];
        $amount = $this->toAmount($quoteItemTransfer->getUnitPrice(), $documentCartItemTransfer->getCurrency());
        $desc[] = implode(self::CHILD_DESCRIPTION_SEPARATOR, [$quoteItemTransfer->getQuantity(), $amount, $quoteItemTransfer->getName()]);

        foreach ($quoteItemTransfer->getProductOptions() as $option) {
            $value = $this->glossaryStorageClient->translate($option->getValue(), $this->currentLocale);
            $name = $this->glossaryStorageClient->translate($option->getGroupName(), $this->currentLocale);
            if ($value || $name) {
                $desc[] = sprintf($name . '=' . $value);
            }
        }

        return implode("\n", array_filter(array_map("trim", $desc)));
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\ItemTransfer $quoteItemTransfer
     * @param \Generated\Shared\Transfer\PunchoutCatalogDocumentCartItemTransfer $documentCartItemTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogDocumentCartItemTransfer
     */
    public function addItemAttributesDetails(
        QuoteTransfer $quoteTransfer,
        QuoteItemTransfer $quoteItemTransfer,
        PunchoutCatalogDocumentCartItemTransfer $documentCartItemTransfer
    ): PunchoutCatalogDocumentCartItemTransfer
    {
        $productAbstractStorageData = $this->productStorageClient->findProductAbstractStorageData(
            $quoteItemTransfer->getIdProductAbstract(),
            $this->currentLocale
        );

        if ($quoteItemTransfer->getImages() && $quoteItemTransfer->getImages()->count()) {
            /** @var \Generated\Shared\Transfer\ProductImageTransfer $image */
            foreach ($quoteItemTransfer->getImages() as $key => $image) {
                $documentCartItemTransfer->addImage($image->getExternalUrlSmall());
                if ($key == 0) {
                    $documentCartItemTransfer->setImageUrl($image->getExternalUrlSmall());
                }
            }
        }

        if (!empty($productAbstractStorageData['attributes']['brand'])) {
            $documentCartItemTransfer->setBrand($productAbstractStorageData['attributes']['brand']);
        }

        return $documentCartItemTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\PunchoutCatalogDocumentCartItemTransfer $documentCartItemTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogDocumentCartItemTransfer
     */
    public function addItemAfterCustomDetails(
        PunchoutCatalogDocumentCartItemTransfer $documentCartItemTransfer
    ): PunchoutCatalogDocumentCartItemTransfer
    {
        //handle default sale bunch quantity
        if (!$this->hasValue($documentCartItemTransfer->getSaleBunchQuantity())) {
            $documentCartItemTransfer->setSaleBunchQuantity(1);
        }
        $saleBunchQuantity = $documentCartItemTransfer->getSaleBunchQuantity();

        //handle conversion factor
        if (!$this->hasValue($documentCartItemTransfer->getSaleBunchQuantityFactor())) {
            $documentCartItemTransfer->setSaleBunchQuantityFactor(1 / $saleBunchQuantity);
        }
        $factor = $documentCartItemTransfer->getSaleBunchQuantityFactor();

        //BUNCH PRICES
        if (!$this->hasValue($documentCartItemTransfer->getSaleBunchQuantityUnitPrice())) {
            $documentCartItemTransfer->setSaleBunchQuantityUnitPrice(
                $documentCartItemTransfer->getUnitPrice() * $factor
            );
        }
        if (!$this->hasValue($documentCartItemTransfer->getSaleBunchQuantitySumPrice())) {
            $documentCartItemTransfer->setSaleBunchQuantitySumPrice(
                $documentCartItemTransfer->getSumPrice() * $factor
            );
        }
        if (!$this->hasValue($documentCartItemTransfer->getSaleBunchQuantityUnitTotal())) {
            $documentCartItemTransfer->setSaleBunchQuantityUnitTotal(
                $documentCartItemTransfer->getUnitTotal() * $factor
            );
        }
        if (!$this->hasValue($documentCartItemTransfer->getSaleBunchQuantitySumTotal())) {
            $documentCartItemTransfer->setSaleBunchQuantitySumTotal(
                $documentCartItemTransfer->getSumTotal() * $factor
            );
        }

        //BUNCH TAX
        if (!$this->hasValue($documentCartItemTransfer->getSaleBunchQuantityUnitTaxAmount())) {
            $documentCartItemTransfer->setSaleBunchQuantityUnitTaxAmount(
                $documentCartItemTransfer->getUnitTaxAmount() * $factor
            );
        }
        if (!$this->hasValue($documentCartItemTransfer->getSaleBunchQuantitySumTaxAmount())) {
            $documentCartItemTransfer->setSaleBunchQuantitySumTaxAmount(
                $documentCartItemTransfer->getSumTaxAmount() * $factor
            );
        }
        if (!$this->hasValue($documentCartItemTransfer->getSaleBunchQuantityUnitTaxTotal())) {
            $documentCartItemTransfer->setSaleBunchQuantityUnitTaxTotal(
                $documentCartItemTransfer->getUnitTaxTotal() * $factor
            );
        }
        if (!$this->hasValue($documentCartItemTransfer->getSaleBunchQuantitySumTaxTotal())) {
            $documentCartItemTransfer->setSaleBunchQuantitySumTaxTotal(
                $documentCartItemTransfer->getSumTaxTotal() * $factor
            );
        }

        return $documentCartItemTransfer;
    }

    /**
     * @return int
     */
    protected function getNextLineNumber()
    {
        return ++$this->lineNumber;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer $cartRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer
     */
    protected function prepareTotals(
        QuoteTransfer $quoteTransfer, PunchoutCatalogCartRequestTransfer $cartRequestTransfer
    ): PunchoutCatalogCartRequestTransfer
    {
        if ($this->getTotalsMode() == self::TOTALS_MODE_LINE) {
            $this->prepareLineTotals($quoteTransfer, $cartRequestTransfer);
        } else {
            $this->prepareHeaderTotals($quoteTransfer, $cartRequestTransfer);
        }

        return $cartRequestTransfer;
    }

    /**
     * @return string
     */
    protected function getTotalsMode(): string
    {
        $cartDetails = $this->getPunchoutCartDetails();

        return $cartDetails['totals_mode'] ?? self::TOTALS_MODE_LINE;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer $cartRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer
     */
    protected function prepareLineTotals(
        QuoteTransfer $quoteTransfer, PunchoutCatalogCartRequestTransfer $cartRequestTransfer
    ): PunchoutCatalogCartRequestTransfer
    {
        $this->prepareLineTaxTotal($quoteTransfer, $cartRequestTransfer);
        $this->prepareLineDiscountTotal($quoteTransfer, $cartRequestTransfer);
        $this->prepareLinesExpenses($quoteTransfer, $cartRequestTransfer);
        return $cartRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer $cartRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer
     */
    protected function prepareLineTaxTotal(
        QuoteTransfer $quoteTransfer, PunchoutCatalogCartRequestTransfer $cartRequestTransfer
    ): PunchoutCatalogCartRequestTransfer
    {
        if ($this->isGrossPriceMode($quoteTransfer) ||
            empty($quoteTransfer->getTotals()) ||
            empty($quoteTransfer->getTotals()->getTaxTotal()) ||
            empty($quoteTransfer->getTotals()->getTaxTotal()->getAmount())) {
            return $cartRequestTransfer;
        }
        $documentCartTransfer = $cartRequestTransfer->getCart();
        $lineTaxTotalTransfer = new PunchoutCatalogDocumentCartItemTransfer();

        $lineTaxTotalTransfer->setInternalId($this->getQuoteItemInternalId());
        $lineTaxTotalTransfer->setSupplierId($this->getDefaultSupplierId());
        $lineTaxTotalTransfer->setLocale($this->toLang($this->currentLocale));

        $lineTaxTotalTransfer->setLineNumber($this->getNextLineNumber());
        $lineTaxTotalTransfer->setQuantity(1);
        $lineTaxTotalTransfer->setName('Estimated Tax');
        $lineTaxTotalTransfer->setSku('tax');
        $lineTaxTotalTransfer->setDescription($lineTaxTotalTransfer->getName());
        $lineTaxTotalTransfer->setLongDescription($lineTaxTotalTransfer->getDescription());


        if ($quoteTransfer->getCurrency() !== null) {
            $lineTaxTotalTransfer->setCurrency($quoteTransfer->getCurrency()->getCode());
        }

        $amount = $this->toAmount($quoteTransfer->getTotals()->getTaxTotal()->getAmount(),
            $documentCartTransfer->getCurrency());

        $lineTaxTotalTransfer->setUnitPrice($amount);
        $lineTaxTotalTransfer->setSumPrice($amount);
        $lineTaxTotalTransfer->setUnitTotal($amount);
        $lineTaxTotalTransfer->setSumTotal($amount);

        $this->addItemAfterCustomDetails($lineTaxTotalTransfer);

        $cartRequestTransfer->addCartItem($lineTaxTotalTransfer);
        return $cartRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer $cartRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer
     */
    protected function prepareLinesExpenses(
        QuoteTransfer $quoteTransfer, PunchoutCatalogCartRequestTransfer $cartRequestTransfer
    ): PunchoutCatalogCartRequestTransfer
    {
        foreach ($quoteTransfer->getExpenses() as $expense) {
            $lineExpenseTransfer = new PunchoutCatalogDocumentCartItemTransfer();

            $lineExpenseTransfer->setInternalId($this->getQuoteItemInternalId());
            $lineExpenseTransfer->setSupplierId($this->getDefaultSupplierId());
            $lineExpenseTransfer->setLocale($this->toLang($this->currentLocale));

            $lineExpenseTransfer->setLineNumber($this->getNextLineNumber());
            $lineExpenseTransfer->setQuantity(1);

            $name = $this->glossaryStorageClient->translate(
                $expense->getName(),
                $this->currentLocale
            );

            $lineExpenseTransfer->setName($name);
            $lineExpenseTransfer->setSku('expense');
            $lineExpenseTransfer->setDescription($lineExpenseTransfer->getName());
            $lineExpenseTransfer->setLongDescription($lineExpenseTransfer->getDescription());

            if ($quoteTransfer->getCurrency() !== null) {
                $lineExpenseTransfer->setCurrency($quoteTransfer->getCurrency()->getCode());
            }

            $amount = $this->toAmount($expense->getSumPrice(), $lineExpenseTransfer->getCurrency());

            $lineExpenseTransfer->setUnitPrice($amount);
            $lineExpenseTransfer->setSumPrice($amount);
            $lineExpenseTransfer->setUnitTotal($amount);
            $lineExpenseTransfer->setSumTotal($amount);

            $this->addItemAfterCustomDetails($lineExpenseTransfer);

            $cartRequestTransfer->addCartItem($lineExpenseTransfer);
        }
        return $cartRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer $cartRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer
     */
    protected function prepareLineDiscountTotal(
        QuoteTransfer $quoteTransfer, PunchoutCatalogCartRequestTransfer $cartRequestTransfer
    ): PunchoutCatalogCartRequestTransfer
    {
        if (empty($quoteTransfer->getTotals()) ||
            empty($quoteTransfer->getTotals()->getDiscountTotal())) {
            return $cartRequestTransfer;
        }
        $documentCartTransfer = $cartRequestTransfer->getCart();
        $lineDiscountTotalTransfer = new PunchoutCatalogDocumentCartItemTransfer();

        $lineDiscountTotalTransfer->setInternalId($this->getQuoteItemInternalId());
        $lineDiscountTotalTransfer->setSupplierId($this->getDefaultSupplierId());
        $lineDiscountTotalTransfer->setLocale($this->toLang($this->currentLocale));

        $lineDiscountTotalTransfer->setLineNumber($this->getNextLineNumber());
        $lineDiscountTotalTransfer->setQuantity(1);
        $lineDiscountTotalTransfer->setName('Estimated Discount');
        $lineDiscountTotalTransfer->setSku('discount');
        $lineDiscountTotalTransfer->setDescription($lineDiscountTotalTransfer->getName());

        $discountDescription = $this->getDiscountDescription($quoteTransfer);
        if ($discountDescription) {
            $lineDiscountTotalTransfer->setDescription($discountDescription);
        }

        $lineDiscountTotalTransfer->setLongDescription($lineDiscountTotalTransfer->getDescription());

        if ($quoteTransfer->getCurrency() !== null) {
            $lineDiscountTotalTransfer->setCurrency($quoteTransfer->getCurrency()->getCode());
        }

        // make discount amount with negative value if write it as line
        $amount = -$this->toAmount($quoteTransfer->getTotals()->getDiscountTotal(), $documentCartTransfer->getCurrency());
        $lineDiscountTotalTransfer->setUnitPrice($amount);
        $lineDiscountTotalTransfer->setSumPrice($amount);
        $lineDiscountTotalTransfer->setUnitTotal($amount);
        $lineDiscountTotalTransfer->setSumTotal($amount);

        $this->addItemAfterCustomDetails($lineDiscountTotalTransfer);

        $cartRequestTransfer->addCartItem($lineDiscountTotalTransfer);
        return $cartRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return string
     */
    protected function getDiscountDescription(QuoteTransfer $quoteTransfer)
    {
        $discountDescription = [];
        if ($quoteTransfer->getVoucherDiscounts()) {
            foreach ($quoteTransfer->getVoucherDiscounts() as $voucherDiscount) {
                $discountDescription[] = $voucherDiscount->getDisplayName();
            }
        }

        if ($quoteTransfer->getCartRuleDiscounts()) {
            foreach ($quoteTransfer->getCartRuleDiscounts() as $documentCartTransferRuleDiscount) {
                $discountDescription[] = $documentCartTransferRuleDiscount->getDisplayName();
            }
        }

        $discountDescription = array_filter($discountDescription);

        return implode("\n", $discountDescription);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer $cartRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer
     */
    protected function prepareHeaderTotals(
        QuoteTransfer $quoteTransfer, PunchoutCatalogCartRequestTransfer $cartRequestTransfer
    ): PunchoutCatalogCartRequestTransfer
    {
        $this->prepareHeaderTaxTotal($quoteTransfer, $cartRequestTransfer);
        $this->prepareHeaderDiscountTotal($quoteTransfer, $cartRequestTransfer);
        return $cartRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer $cartRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer
     */
    protected function prepareHeaderTaxTotal(
        QuoteTransfer $quoteTransfer, PunchoutCatalogCartRequestTransfer $cartRequestTransfer
    ): PunchoutCatalogCartRequestTransfer
    {
        if ($this->isGrossPriceMode($quoteTransfer)) {
            return $cartRequestTransfer;
        }
        $documentCartTransfer = $cartRequestTransfer->getCart();

        if ($quoteTransfer->getTotals()) {
            $documentCartTransfer->setTaxTotal(
                $this->toAmount($quoteTransfer->getTotals()->getTaxTotal()->getAmount(), $documentCartTransfer->getCurrency())
            );
        }
        return $cartRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer $cartRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer
     */
    protected function prepareHeaderDiscountTotal(
        QuoteTransfer $quoteTransfer, PunchoutCatalogCartRequestTransfer $cartRequestTransfer
    ): PunchoutCatalogCartRequestTransfer
    {
        $documentCartTransfer = $cartRequestTransfer->getCart();

        $coupons = $this->getCoupons($quoteTransfer);
        if ($coupons) {
            $documentCartTransfer->setCoupon($coupons);
        }

        $discountDescription = $this->getDiscountDescription($quoteTransfer);
        if ($discountDescription) {
            $documentCartTransfer->setDiscountDescription($discountDescription);
        }

        if ($quoteTransfer->getTotals()) {
            $documentCartTransfer->setDiscountTotal(
                $this->toAmount($quoteTransfer->getTotals()->getDiscountTotal(), $documentCartTransfer->getCurrency())
            );
        }
        return $cartRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return string
     */
    protected function getCoupons(QuoteTransfer $quoteTransfer)
    {
        $coupons = [];
        if ($quoteTransfer->getVoucherDiscounts()) {
            foreach ($quoteTransfer->getVoucherDiscounts() as $voucherDiscount) {
                $coupons[] = $voucherDiscount->getVoucherCode();
            }
        }

        $coupons = array_filter($coupons);

        return implode(',', $coupons);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return boolean
     */
    protected function isGrossPriceMode(QuoteTransfer $quoteTransfer)
    {
        return $quoteTransfer->getPriceMode() == self::PRICE_MODE_GROSS;
    }

    /**
     * @param string|float|int|array|null $val
     *
     * @return bool
     */
    protected function hasValue($val = null)
    {
        return (null !== $val);
    }
}

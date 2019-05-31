<?php

namespace PunchoutCatalog\Yves\PunchoutCatalog\Mapper;

use ArrayObject;
use Spryker\Yves\Kernel\AbstractPlugin;

use Generated\Shared\Transfer\ItemTransfer as QuoteItemTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCustomAttributeTransfer;
use Generated\Shared\Transfer\PunchoutCatalogDocumentCartCustomerTransfer;
use Generated\Shared\Transfer\PunchoutCatalogDocumentCartItemTransfer;
use Generated\Shared\Transfer\PunchoutCatalogDocumentCartTransfer;
use Generated\Shared\Transfer\PunchoutCatalogDocumentCustomAttributeTransfer;
use Generated\Shared\Transfer\QuoteTransfer;

use PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Plugin\CartTransferMapperPluginInterface;
use PunchoutCatalog\Zed\PunchoutCatalog\Business\PunchoutConnectionConstsInterface;

/**
 * @method \PunchoutCatalog\Yves\PunchoutCatalog\PunchoutCatalogFactory getFactory()
 */
class CartTransferMapperDefaultPlugin extends AbstractPlugin implements CartTransferMapperPluginInterface
{
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

        $this->prepareHeader($quoteTransfer, $cartRequestTransfer);
        $this->prepareCustomer($quoteTransfer, $cartRequestTransfer);
        $this->prepareLineItems($quoteTransfer, $cartRequestTransfer);
        
        return $cartRequestTransfer->setCart(
            $this->applyCustomizations(
                $quoteTransfer, $cartRequestTransfer->getCart(), $this->cartMapping
            )
        );
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

        $coupons = [];
        $discountDescription = [];
        if ($quoteTransfer->getVoucherDiscounts()) {
            foreach ($quoteTransfer->getVoucherDiscounts() as $voucherDiscount) {
                $discountDescription[] = $voucherDiscount->getDisplayName();
                $coupons[] = $voucherDiscount->getVoucherCode();
            }
        }

        if ($quoteTransfer->getCartRuleDiscounts()) {
            foreach ($quoteTransfer->getCartRuleDiscounts() as $documentCartTransferRuleDiscount) {
                $discountDescription[] = $documentCartTransferRuleDiscount->getDisplayName();
            }
        }

        $coupons = array_filter($coupons);
        if ($coupons) {
            $documentCartTransfer->setCoupon(implode(',', $coupons));
        }

        $discountDescription = array_filter($discountDescription);
        if ($discountDescription) {
            $documentCartTransfer->setDiscountDescription(implode("\n", $discountDescription));
        }

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
            $documentCartTransfer->setTaxTotal(
                $this->toAmount($quoteTransfer->getTotals()->getTaxTotal()->getAmount(), $documentCartTransfer->getCurrency())
            );
            $documentCartTransfer->setDiscountTotal(
                $this->toAmount($quoteTransfer->getTotals()->getDiscountTotal(), $documentCartTransfer->getCurrency())
            );
        }

        return $cartRequestTransfer;
    }

    /**
     * @param $locale
     *
     * @return mixed
     */
    protected function toLang($locale)
    {
        return str_replace('_', '-', $locale);
    }

    /**
     * @param int $amount
     * @param string|null $isoCode
     *
     * @return int
     */
    protected function toAmount(int $amount, ?string $isoCode)
    {
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
                $quoteTransfer->getCustomer(), $cartRequestTransfer->getCustomer(), $this->cartCustomerMapping, [$quoteTransfer]
            )
        );
    }

    /**
     * @todo: improve this method to cover bundle products: single + composite modes
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
        $idx = $totalQty = 0;
        
        $cartItems = $this->getCartItems($quoteTransfer);
        if ($cartItems) {
            foreach ($cartItems as $quoteItemTransfer) {
                $documentCartItemTransfer = new PunchoutCatalogDocumentCartItemTransfer();

                $documentCartItemTransfer = $this->mapQuoteItemTransferToPunchoutCatalogDocumentCartItemTransfer(
                    $quoteTransfer,
                    $quoteItemTransfer,
                    $documentCartItemTransfer->setLineNumber(++$idx)
                );
                
                if ($quoteItemTransfer->getChildBundleItems()->count()
                    && $this->getBundleMode() == PunchoutConnectionConstsInterface::BUNDLE_MODE_COMPOSITE
                ) {
                    $documentCartItemTransfer->setCompositeItemType(PunchoutConnectionConstsInterface::BUNDLE_COMPOSITE_PRICE_LEVEL);
                    $documentCartItemTransfer->setItemType(PunchoutConnectionConstsInterface::BUNDLE_COMPOSITE_ITEM_TYPE);
                    
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
                    $documentCartItemTransfer->setItemType(PunchoutConnectionConstsInterface::BUNDLE_CHILD_ITEM_TYPE);
                    $documentCartItemTransfer->setParentLineNumber($bundleProduct->getLineNumber());
                    $documentCartItemTransfer->setParentInternalId($bundleProduct->getInternalId());
                    
                    $documentCartItemTransfer = $this->mapQuoteItemTransferToPunchoutCatalogDocumentCartItemTransfer(
                        $quoteTransfer,
                        $quoteItemTransfer,
                        $documentCartItemTransfer->setLineNumber(++$idx)
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

        return $this->applyCustomizations(
            $quoteItemTransfer, $documentCartItemTransfer, $this->cartItemMapping, [$quoteTransfer]
        );
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
        if (null !== $quoteTransfer->getCurrency()) {
            $documentCartItemTransfer->setCurrency($quoteTransfer->getCurrency()->getCode());
        }
        
        //PRICING & RATES
        $documentCartItemTransfer->setTaxRate($quoteItemTransfer->getTaxRate());
    
        $documentCartItemTransfer->setUnitPrice(
            $this->toAmount($quoteItemTransfer->getUnitPrice(), $documentCartItemTransfer->getCurrency())
        );
    
        $documentCartItemTransfer->setSumPrice(
            $this->toAmount($quoteItemTransfer->getSumPrice(), $documentCartItemTransfer->getCurrency())
        );
    
        if (null !== $quoteItemTransfer->getSumTaxAmount()) {
            $documentCartItemTransfer->setSumTaxAmount(
                $this->toAmount($quoteItemTransfer->getSumTaxAmount(), $documentCartItemTransfer->getCurrency())
            );
        }
    
        if (null !== $quoteItemTransfer->getSumDiscountAmountAggregation()) {
            $documentCartItemTransfer->setSumDiscountAmount(
                $this->toAmount($quoteItemTransfer->getSumDiscountAmountAggregation(), $documentCartItemTransfer->getCurrency())
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
        $productAbstractStorageData = $this->productStorageClient->getProductAbstractStorageData(
            $quoteItemTransfer->getIdProductAbstract(), $this->currentLocale
        );
    
        $productDescription = $this->limitDescription(trim($productAbstractStorageData['description']));
        $documentCartItemTransfer->setDescription(trim($productDescription));
        
        $childrenDescriptions = [];
        if ($quoteItemTransfer->getChildBundleItems()->count()
            && $this->getBundleMode() == PunchoutConnectionConstsInterface::BUNDLE_MODE_SINGLE
        ) {
            foreach ($quoteItemTransfer->getChildBundleItems() as $childCartItem) {
                $childrenDescriptions[] = $this->prepareChildDescription($childCartItem);
            }
        }
        
        $longDescriptionParts = [
            trim($productAbstractStorageData['description'])
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
        $longDescriptionParts = implode("\n", array_filter($longDescriptionParts));
        
        $productLongDescription = $this->limitDescription(trim($longDescriptionParts));
        $documentCartItemTransfer->setLongDescription(trim($longDescriptionParts));
        
        return $documentCartItemTransfer;
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
        $documentCartItemTransfer->setUom('EA');//@todo: get a real value
        return $documentCartItemTransfer;
    }
    
    /**
     * @param QuoteItemTransfer $quoteItemTransfer
     *
     * @return string
     */
    protected function prepareChildDescription(QuoteItemTransfer $quoteItemTransfer)
    {
        $desc = [];
        $desc[] = sprintf('%s x %s', $quoteItemTransfer->getQuantity(), $quoteItemTransfer->getName());
    
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
        $productAbstractStorageData = $this->productStorageClient->getProductAbstractStorageData(
            $quoteItemTransfer->getIdProductAbstract(), $this->currentLocale
        );
        
        if ($quoteItemTransfer->getImages() && $quoteItemTransfer->getImages()->count()) {
            /** @var \Generated\Shared\Transfer\ProductImageTransfer $image */
            foreach($quoteItemTransfer->getImages() as $key => $image) {
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
     * @return string
     */
    protected function getDefaultSupplierId(): string
    {
        $cartDetails = $this->getPunchoutCartDetails();
        return $cartDetails['default_supplier_id'] ?? null;
    }
    
    /**
     * @return string
     */
    protected function getBundleMode(): string
    {
        $cartDetails = $this->getPunchoutCartDetails();
        return $cartDetails['bundle_mode'] ?? PunchoutConnectionConstsInterface::BUNDLE_MODE_SINGLE;
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
     * @param $description
     * @return mixed
     */
    protected function limitDescription($description)
    {
        $cartDetails = $this->getPunchoutCartDetails();
        if (!empty($cartDetails['max_description_length'])) {
            $length = intval($cartDetails['max_description_length']);
            return mb_substr($description, 0, $length);
        }
        return $description;
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
     * @param \Generated\Shared\Transfer\ItemTransfer $quoteItemTransfer
     *
     * @return string
     */
    protected function getQuoteItemInternalId(QuoteItemTransfer $quoteItemTransfer): string
    {
        $internalId = md5(
            json_encode($quoteItemTransfer->toArray())
            . '_' . microtime(true)
            . '_' . uniqid('', true)
        );
        return $this->getFactory()
            ->getUtilUuidGeneratorService()
            ->generateUuid5FromObjectId($internalId);
    }

    /**
     * Convert underscore_text to CamelCase
     * @param $string
     * @param string $separator
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
     * @param QuoteTransfer | QuoteItemTransfer $inputTransfer
     * @param PunchoutCatalogCartRequestTransfer|PunchoutCatalogDocumentCartItemTransfer $outputTransfer
     * @param array $mapping
     * @param array $getterArgs
     * @return PunchoutCatalogCartRequestTransfer|PunchoutCatalogDocumentCartItemTransfer|mixed|string
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
}

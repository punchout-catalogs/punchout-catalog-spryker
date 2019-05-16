<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Yves\PunchoutCatalog\Mapper;

use ArrayObject;
use Generated\Shared\Transfer\ItemTransfer as QuoteItemTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCustomAttributeTransfer;
use Generated\Shared\Transfer\PunchoutCatalogDocumentCartCustomerTransfer;
use Generated\Shared\Transfer\PunchoutCatalogDocumentCartItemTransfer;
use Generated\Shared\Transfer\PunchoutCatalogDocumentCartTransfer;
use Generated\Shared\Transfer\PunchoutCatalogDocumentCustomAttributeTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToCustomerClientInterface;
use PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToGlossaryStorageClientInterface;
use PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToMoneyClientInterface;
use PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToProductStorageClientInterface;
use Spryker\Service\UtilUuidGenerator\UtilUuidGeneratorService;
use Spryker\Service\UtilUuidGenerator\UtilUuidGeneratorServiceInterface;

class CartTransferMapper implements CartTransferMapperInterface
{
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
     * @param PunchoutCatalogToGlossaryStorageClientInterface $glossaryStorageClient
     * @param PunchoutCatalogToMoneyClientInterface $moneyClient
     * @param PunchoutCatalogToProductStorageClientInterface $productStorageClient
     * @param PunchoutCatalogToCustomerClientInterface $customerClient
     * @param string $currentLocale
     */
    public function __construct(
        PunchoutCatalogToGlossaryStorageClientInterface $glossaryStorageClient,
        PunchoutCatalogToMoneyClientInterface $moneyClient,
        PunchoutCatalogToProductStorageClientInterface $productStorageClient,
        PunchoutCatalogToCustomerClientInterface $customerClient,
        string $currentLocale
    ) {
        $this->glossaryStorageClient = $glossaryStorageClient;
        $this->moneyClient = $moneyClient;
        $this->currentLocale = $currentLocale;
        $this->productStorageClient = $productStorageClient;
        $this->customerClient = $customerClient;
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
    ): PunchoutCatalogCartRequestTransfer {
        //$cartRequestTransfer->setLocale($this->toLang($this->currentLocale));
        //Empty Cart Transfer
        if (!$quoteTransfer || !$quoteTransfer->getItems()) {
            return $cartRequestTransfer;
        }

        $documentCartTransfer = new PunchoutCatalogDocumentCartTransfer();
        $cartRequestTransfer->setCart($documentCartTransfer);

        $this->prepareHeader($quoteTransfer, $cartRequestTransfer);
        $this->prepareCustomer($quoteTransfer, $cartRequestTransfer);
        $this->prepareLineItems($quoteTransfer, $cartRequestTransfer);

        return $cartRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $quoteItemTransfer
     * @param \Generated\Shared\Transfer\PunchoutCatalogDocumentCartItemTransfer $documentCartItemTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogDocumentCartItemTransfer
     */
    public function mapQuoteItemTransferToPunchoutCatalogDocumentCartItemTransfer(
        QuoteItemTransfer $quoteItemTransfer,
        PunchoutCatalogDocumentCartItemTransfer $documentCartItemTransfer
    ): PunchoutCatalogDocumentCartItemTransfer {
        $internalId = md5(json_encode($quoteItemTransfer->toArray()) . '_' . rand(0, 100000) . '_'  . microtime());
        $internalId = $this->getUtilUuidGeneratorService()->generateUuid5FromObjectId($internalId);

        //@todo: fix UNSPSC/EAN/UPC

        $supplierId = $this->getDefaultSupplierId();

        $productAbstractStorageData = $this->productStorageClient->getProductAbstractStorageData($quoteItemTransfer->getIdProductAbstract(), $this->currentLocale);

        $longDescriptionParts = [$productAbstractStorageData['description']];
        foreach ($quoteItemTransfer->getProductOptions() as $option) {
            $value = $this->glossaryStorageClient->translate($option->getValue(), $this->currentLocale);
            $name = $this->glossaryStorageClient->translate($option->getGroupName(), $this->currentLocale);
            if ($value || $name){
                $longDescriptionParts[] = sprintf($name . '=' . $value);
            }
        }
        $imageUrl = '';
        if (isset($quoteItemTransfer->getImages()[0])) {
            /** @var \Generated\Shared\Transfer\ProductImageTransfer $image */
            $image = $quoteItemTransfer->getImages()[0];
            $imageUrl = $image->getExternalUrlSmall();
        }

        $productDescription = $this->limitDescription($productAbstractStorageData['description']);
        $productLongDescription = $this->limitDescription(join('\n', array_filter($longDescriptionParts)));

        $documentCartItemTransfer->setInternalId($internalId);//@todo: generate spaid
        $documentCartItemTransfer->setSupplierId($supplierId);
        $documentCartItemTransfer->setLocale($this->toLang($this->currentLocale));
        
        $documentCartItemTransfer->setQuantity($quoteItemTransfer->getQuantity());
        $documentCartItemTransfer->setProductPackagingUnit($quoteItemTransfer->getProductPackagingUnit());
        $documentCartItemTransfer->setSku($quoteItemTransfer->getSku());
        $documentCartItemTransfer->setGroupKey($quoteItemTransfer->getGroupKey());
        $documentCartItemTransfer->setAbstractSku($quoteItemTransfer->getAbstractSku());
        
        $documentCartItemTransfer->setName(trim($quoteItemTransfer->getName()));
        $documentCartItemTransfer->setDescription(trim($productDescription));
        $documentCartItemTransfer->setLongDescription(trim($productLongDescription));
        $documentCartItemTransfer->setCartNote($quoteItemTransfer->getCartNote());
        $documentCartItemTransfer->setImageUrl($imageUrl);
        
        //
        //PRICING & RATES
        $documentCartItemTransfer->setTaxRate($quoteItemTransfer->getTaxRate());

        $documentCartItemTransfer->setUnitPrice(
            $this->toAmount($quoteItemTransfer->getUnitPrice(), $documentCartItemTransfer->getCurrency())
        );
        
        $documentCartItemTransfer->setSumPrice(
            $this->toAmount($quoteItemTransfer->getSumPrice(), $documentCartItemTransfer->getCurrency())
        );
        
        $documentCartItemTransfer->setSumTaxAmount(
            $this->toAmount($quoteItemTransfer->getSumTaxAmount(), $documentCartItemTransfer->getCurrency())
        );
        
        $documentCartItemTransfer->setSumDiscountAmount(
            $this->toAmount($quoteItemTransfer->getSumDiscountAmountAggregation(), $documentCartItemTransfer->getCurrency())
        );

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

        $customAttributes = new ArrayObject();
        $documentCartItemTransfer->setCustomAttributes($customAttributes);

        return $documentCartItemTransfer;
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
        return $cartRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer $cartRequestTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer
     */
    protected function prepareLineItems(
        QuoteTransfer $quoteTransfer,
        PunchoutCatalogCartRequestTransfer $cartRequestTransfer
    ): PunchoutCatalogCartRequestTransfer {
        $totalQty = 0;
        if ($quoteTransfer->getItems()) {
            foreach ($quoteTransfer->getItems() as $idx => $quoteItemTransfer) {
                $documentCartItemTransfer = new PunchoutCatalogDocumentCartItemTransfer();
                $documentCartItemTransfer->setLineNumber($idx + 1);
                $documentCartItemTransfer->setCurrency($quoteTransfer->getCurrency()->getCode());

                $documentCartItemTransfer = $this->mapQuoteItemTransferToPunchoutCatalogDocumentCartItemTransfer(
                    $quoteItemTransfer,
                    $documentCartItemTransfer
                );

                $cartRequestTransfer->addCartItem($documentCartItemTransfer);

                $totalQty += (int)$quoteItemTransfer->getQuantity();
            }
        }

        if ($totalQty > 0) {
            $cartRequestTransfer->getCart()->setTotalQty($totalQty);
        }

        return $cartRequestTransfer;
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
     * @return \Spryker\Service\UtilUuidGenerator\UtilUuidGeneratorServiceInterface
     */
    protected function getUtilUuidGeneratorService(): UtilUuidGeneratorServiceInterface
    {
        return new UtilUuidGeneratorService();
    }

    /**
     * @param $description
     * @return mixed
     */
    protected function limitDescription($description)
    {
        $cartDetails = $this->getPunchoutCartDetails();
        if (!empty(['max_description_length'])) {
            $length = intval($cartDetails['max_description_length']);
            return mb_substr($description, 0, $length);
        }
        return $description;
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
}

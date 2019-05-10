<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalog\Yves\PunchoutCatalog\Mapper;

use ArrayObject;
use Generated\Shared\Transfer\ItemTransfer as QuoteItemTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogDocumentCartCustomerTransfer;
use Generated\Shared\Transfer\PunchoutCatalogDocumentCartItemTransfer;
use Generated\Shared\Transfer\PunchoutCatalogDocumentCartTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCustomAttributeTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToGlossaryStorageClientInterface;
use PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToMoneyClientInterface;

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
     *
     * @param \PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToGlossaryStorageClientInterface $glossaryStorageClient
     * @param \PunchoutCatalog\Yves\PunchoutCatalog\Dependency\Client\PunchoutCatalogToMoneyClientInterface $moneyClient
     * @param string $currentLocale
     */
    public function __construct(
        PunchoutCatalogToGlossaryStorageClientInterface $glossaryStorageClient,
        PunchoutCatalogToMoneyClientInterface $moneyClient,
        string $currentLocale
    ) {
        $this->glossaryStorageClient = $glossaryStorageClient;
        $this->moneyClient = $moneyClient;
        $this->currentLocale = $currentLocale;
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
        
        $cartRequestTransfer->setLocale($this->toLang($this->currentLocale));
        
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
        $internalId = md5($quoteItemTransfer->getId());
        $documentCartItemTransfer->setInternalId($internalId);//@todo: generate spaid
        $documentCartItemTransfer->setSupplierId('@todo: replace it');
        
        //@todo: update fields:
        //`uom` to `productPackagingUnit`
        //`setQuantity` to `setQuantity`
        //`setComment` to `setCartNote`
        //`setPriceAmount` to `setUnitPrice`
        //`setPriceAmount` to `setSumPrice`
        //`setPriceAmount` to `setSumTaxAmount`
        //`setPriceAmount` to `setSumDiscountAmount`
        // to `setAbstractSku`
        // to `setGroupKey`
        
        $documentCartItemTransfer->setQuantity($quoteItemTransfer->getQuantity());
        $documentCartItemTransfer->setProductPackagingUnit($quoteItemTransfer->getProductPackagingUnit());
        $documentCartItemTransfer->setSku($quoteItemTransfer->getSku());
        $documentCartItemTransfer->setGroupKey($quoteItemTransfer->getGroupKey());
        $documentCartItemTransfer->setAbstractSku($quoteItemTransfer->getAbstractSku());
        
        $documentCartItemTransfer->setName($quoteItemTransfer->getName());
        $documentCartItemTransfer->setDescription($quoteItemTransfer->getName());
        $documentCartItemTransfer->setCartNote($quoteItemTransfer->getCartNote());
        $documentCartItemTransfer->setLongDescription($quoteItemTransfer->getName());
        $documentCartItemTransfer->setLocale($this->toLang($this->currentLocale));
        
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
                $option = (new PunchoutCatalogCustomAttributeTransfer())
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
    ): PunchoutCatalogCartRequestTransfer {
        $documentCartTransfer = $cartRequestTransfer->getCart();
        
        $documentCartTransfer->setCurrency($quoteTransfer->getCurrency()->getCode());
        $documentCartTransfer->setCartNote($quoteTransfer->getCartNote());
        $documentCartTransfer->setLocale($this->toLang($this->currentLocale));

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
    ): PunchoutCatalogCartRequestTransfer {
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
     * @param \Generated\Shared\Transfer\PunchoutCatalogCustomAttributeTransfer $attributeTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogCustomAttributeTransfer
     */
    protected function translateCustomAttribute(PunchoutCatalogCustomAttributeTransfer $attributeTransfer)
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
     * @todo: fix toAmount method, it does not return float
     *
     * @param int $amount
     * @param string|null $isoCode
     *
     * @return int
     */
    protected function toAmount(int $amount, ?string $isoCode)
    {
        return $this->moneyClient->fromInteger($amount, $isoCode)->getAmount();
    }
}

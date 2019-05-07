<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace PunchoutCatalogs\Yves\PunchoutCatalogPage\Mapper;

use ArrayObject;
use Generated\Shared\Transfer\ItemTransfer as QuoteItemTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartCustomerTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartItemTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartRequestTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCartTransfer;
use Generated\Shared\Transfer\PunchoutCatalogCustomAttributeTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use PunchoutCatalogs\Yves\PunchoutCatalogPage\Dependency\Client\PunchoutCatalogPageToGlossaryStorageClientInterface;
use PunchoutCatalogs\Yves\PunchoutCatalogPage\Dependency\Client\PunchoutCatalogPageToMoneyClientInterface;

class CartTransferMapper implements CartTransferMapperInterface
{
    /**
     * @var string
     */
    protected $currentLocale;

    /**
     * @var \PunchoutCatalogs\Yves\PunchoutCatalogPage\Dependency\Client\PunchoutCatalogPageToGlossaryStorageClientInterface
     */
    protected $glossaryStorageClient;

    /**
     * @var \PunchoutCatalogs\Yves\PunchoutCatalogPage\Dependency\Client\PunchoutCatalogPageToMoneyClientInterface
     */
    protected $moneyClient;

    /**
     *
     * @param \PunchoutCatalogs\Yves\PunchoutCatalogPage\Dependency\Client\PunchoutCatalogPageToGlossaryStorageClientInterface $glossaryStorageClient
     * @param \PunchoutCatalogs\Yves\PunchoutCatalogPage\Dependency\Client\PunchoutCatalogPageToMoneyClientInterface $moneyClient
     * @param string $currentLocale
     */
    public function __construct(
        PunchoutCatalogPageToGlossaryStorageClientInterface $glossaryStorageClient,
        PunchoutCatalogPageToMoneyClientInterface $moneyClient,
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
        if (!$quoteTransfer) {
            return $cartRequestTransfer;
        }

        $cartTransfer = new PunchoutCatalogCartTransfer();
        $cartRequestTransfer->setCart($cartTransfer);

        $this->prepareHeader($quoteTransfer, $cartRequestTransfer);
        $this->prepareCustomer($quoteTransfer, $cartRequestTransfer);
        $this->prepareLineItems($quoteTransfer, $cartRequestTransfer);

        return $cartRequestTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $quoteItemTransfer
     * @param \Generated\Shared\Transfer\PunchoutCatalogCartItemTransfer $cartItemTransfer
     *
     * @return \Generated\Shared\Transfer\PunchoutCatalogCartItemTransfer
     */
    public function mapQuoteItemTransferToPunchoutCatalogCartItemTransfer(
        QuoteItemTransfer $quoteItemTransfer,
        PunchoutCatalogCartItemTransfer $cartItemTransfer
    ): PunchoutCatalogCartItemTransfer {
        $cartItemTransfer->setInternalId($quoteItemTransfer->getId());//@todo: generate spaid

        $cartItemTransfer->setQty($quoteItemTransfer->getQuantity());
        $cartItemTransfer->setSku($quoteItemTransfer->getSku());
        $cartItemTransfer->setName($quoteItemTransfer->getName());
        $cartItemTransfer->setDescription($quoteItemTransfer->getName());
        $cartItemTransfer->setComment($quoteItemTransfer->getCartNote());
        $cartItemTransfer->setUom($quoteItemTransfer->getProductPackagingUnit());
        $cartItemTransfer->setLang($this->toLang($this->currentLocale));

        $cartItemTransfer->setTaxRate($quoteItemTransfer->getTaxRate());

        $cartItemTransfer->setPriceAmount(
            $this->toAmount($quoteItemTransfer->getUnitPrice(), $cartItemTransfer->getCurrency())
        );
        $cartItemTransfer->setTotalAmount(
            $this->toAmount($quoteItemTransfer->getSumPrice(), $cartItemTransfer->getCurrency())
        );
        $cartItemTransfer->setTaxAmount(
            $this->toAmount($quoteItemTransfer->getSumTaxAmount(), $cartItemTransfer->getCurrency())
        );
        $cartItemTransfer->setDiscountAmount(
            $this->toAmount($quoteItemTransfer->getSumDiscountAmountAggregation(), $cartItemTransfer->getCurrency())
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
            $cartItemTransfer->setOptions($options);
        }

        $customAttributes = new ArrayObject();

        if ($quoteItemTransfer->getAbstractSku()) {
            $customAttribute = (new PunchoutCatalogCustomAttributeTransfer())
                ->setCode('abstract_sku')
                ->setValue($quoteItemTransfer->getAbstractSku());
            $customAttributes->append($customAttribute);
        }

        if ($quoteItemTransfer->getGroupKey()) {
            $customAttribute = (new PunchoutCatalogCustomAttributeTransfer())
                ->setCode('group_key')
                ->setValue($quoteItemTransfer->getGroupKey());
            $customAttributes->append($customAttribute);
        }

        $cartItemTransfer->setCustomAttributes($customAttributes);

        return $cartItemTransfer;
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
        $cartTransfer = $cartRequestTransfer->getCart();

        $cartTransfer->setInternalId($quoteTransfer->getIdQuote());
        $cartTransfer->setCurrency($quoteTransfer->getCurrency()->getCode());
        $cartTransfer->setComment($quoteTransfer->getCartNote());
        $cartTransfer->setLang($this->toLang($this->currentLocale));

        if ($quoteTransfer->getTotals()) {
            $cartTransfer->setSubtotal(
                $this->toAmount($quoteTransfer->getTotals()->getSubtotal(), $cartTransfer->getCurrency())
            );
            $cartTransfer->setGrandTotal(
                $this->toAmount($quoteTransfer->getTotals()->getGrandTotal(), $cartTransfer->getCurrency())
            );
            $cartTransfer->setNetTotal(
                $this->toAmount($quoteTransfer->getTotals()->getNetTotal(), $cartTransfer->getCurrency())
            );
            $cartTransfer->setTaxTotal(
                $this->toAmount($quoteTransfer->getTotals()->getTaxTotal()->getAmount(), $cartTransfer->getCurrency())
            );
            $cartTransfer->setDiscountTotal(
                $this->toAmount($quoteTransfer->getTotals()->getDiscountTotal(), $cartTransfer->getCurrency())
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
            foreach ($quoteTransfer->getCartRuleDiscounts() as $cartTransferRuleDiscount) {
                $discountDescription[] = $cartTransferRuleDiscount->getDisplayName();
            }
        }

        $coupons = array_filter($coupons);
        if ($coupons) {
            $cartTransfer->setCoupon(implode(',', $coupons));
        }

        $discountDescription = array_filter($discountDescription);
        if ($discountDescription) {
            $cartTransfer->setDiscountDescription(implode("\n", $discountDescription));
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
            $customer = new PunchoutCatalogCartCustomerTransfer();
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
                $cartItemTransfer = new PunchoutCatalogCartItemTransfer();
                $cartItemTransfer->setLineNumber($idx + 1);
                $cartItemTransfer->setCurrency($quoteTransfer->getCurrency()->getCode());

                $cartItemTransfer = $this->mapQuoteItemTransferToPunchoutCatalogCartItemTransfer(
                    $quoteItemTransfer,
                    $cartItemTransfer
                );

                $cartRequestTransfer->addCartItem($cartItemTransfer);

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

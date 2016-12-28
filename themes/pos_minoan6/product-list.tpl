{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{if isset($products) && $products}
	{*define number of products per line in other page for desktop*}
	{if $page_name !='index' && $page_name !='product'}
		{assign var='nbItemsPerLine' value=3}
		{assign var='nbItemsPerLineTablet' value=2}
		{assign var='nbItemsPerLineMobile' value=3}
	{else}
		{assign var='nbItemsPerLine' value=4}
		{assign var='nbItemsPerLineTablet' value=3}
		{assign var='nbItemsPerLineMobile' value=2}
	{/if}
	{*define numbers of product per line in other page for tablet*}
	{assign var='nbLi' value=$products|@count}
	{math equation="nbLi/nbItemsPerLine" nbLi=$nbLi nbItemsPerLine=$nbItemsPerLine assign=nbLines}
	{math equation="nbLi/nbItemsPerLineTablet" nbLi=$nbLi nbItemsPerLineTablet=$nbItemsPerLineTablet assign=nbLinesTablet}
	<!-- Products list -->
        <section class="page-product-box blockproductscategory">
            
        <div class="divTitleFeatured">
            <h1 class="titleFeatured pos-title">{l s="FEATURED"}</h1>
        </div>
        
	<ul{if isset($id) && $id} id="{$id}"{/if} class="product_list grid row{if isset($class) && $class} {$class}{/if}">
            <div id="product_categoryAll">
                {foreach from=$products item=product name=products}
                        {math equation="(total%perLine)" total=$smarty.foreach.products.total perLine=$nbItemsPerLine assign=totModulo}
                        {math equation="(total%perLineT)" total=$smarty.foreach.products.total perLineT=$nbItemsPerLineTablet assign=totModuloTablet}
                        {math equation="(total%perLineT)" total=$smarty.foreach.products.total perLineT=$nbItemsPerLineMobile assign=totModuloMobile}
                        {if $totModulo == 0}{assign var='totModulo' value=$nbItemsPerLine}{/if}
                        {if $totModuloTablet == 0}{assign var='totModuloTablet' value=$nbItemsPerLineTablet}{/if}
                        {if $totModuloMobile == 0}{assign var='totModuloMobile' value=$nbItemsPerLineMobile}{/if}
                        {assign var='save_price' value={math equation='round(((p - r) / p)*100)' r=$product.price p=$product.price_shop}}
                        <li class="ajax_block_product nopadding">
                                <div class="row title-block">
                                    <div class="col-lg-6 col-md-6 col-sm-6" style="padding-left: 0px;padding-right: 0px;"><img src="https://s3.amazonaws.com/imagenes-fluzfluz/m/{$product.id_manufacturer}.jpg" alt="{$product.manufacturer_name|escape:'htmlall':'UTF-8'}" title="{$product.manufacturer_name|escape:'htmlall':'UTF-8'}" class="img-responsive"/></div>
                                    {if $save_price <= 0}
                                    <div class="col-lg-6 col-md-6 col-sm-6 save-product"></div>
                                    {else}
                                    <div class="col-lg-6 col-md-6 col-sm-6 save-product">{l s="Save"} {$save_price}%</div>
                                    {/if}
                                        {if isset($product.is_virtual) && !$product.is_virtual}{hook h="displayProductDeliveryTime" product=$product}{/if}
                                        {hook h="displayProductPriceBlock" product=$product type="weight"}
                                </div>
                                <div>
                                        <a class="product_img_link" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url">
                                                <img class="img-responsive pruebaImgCategory" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'thickbox_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} itemprop="image" />
                                        </a>
                                        {if isset($quick_view) && $quick_view}
                                                <a class="quick-view" title="Quick view" href="{$product.link|escape:'html':'UTF-8'}" style="position: absolute;" >
                                                    <span class="quick"><i class="icon-search"></i></span>
                                                </a>
                                        {/if}
                                        {if isset($product.new) && $product.new == 1}
                                                <a class="new-box" href="{$product.link|escape:'html':'UTF-8'}">
                                                        <span class="new-label">{l s='New'}</span>
                                                </a>
                                        {/if}
                                        {if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
                                                <a class="sale-box" href="{$product.link|escape:'html':'UTF-8'}">
                                                        <span class="sale-label">{l s='Sale!'}</span>
                                                </a>
                                        {/if}
                                        {*if (!$PS_CATALOG_MODE && ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
                                                <!-- <div class="price-box" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                                                        {if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
                                                                <!--<span itemprop="price" class="price product-price">
                                                                        {hook h="displayProductPriceBlock" product=$product type="before_price"}
                                                                        {if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
                                                                </span>
                                                                <meta itemprop="priceCurrency" content="{$currency->iso_code}" />
                                                                {if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
                                                                        {hook h="displayProductPriceBlock" product=$product type="old_price"}
                                                                        <span class="old-price product-price">
                                                                                {displayWtPrice p=$product.price_without_reduction}
                                                                        </span>
                                                                        {if $product.specific_prices.reduction_type == 'percentage'}
                                                                                <span class="price-percent-reduction">-{$product.specific_prices.reduction * 100}%</span>
                                                                        {/if}
                                                                {/if}
                                                                {if $PS_STOCK_MANAGEMENT && isset($product.available_for_order) && $product.available_for_order && !isset($restricted_country_mode)}
                                                                        <span class="unvisible">
                                                                                {if ($product.allow_oosp || $product.quantity > 0)}
                                                                                        <link itemprop="availability" href="https://schema.org/InStock" />{if $product.quantity <= 0}{if $product.allow_oosp}{if isset($product.available_later) && $product.available_later}{$product.available_later}{else}{l s='In Stock'}{/if}{else}{l s='Out of stock'}{/if}{else}{if isset($product.available_now) && $product.available_now}{$product.available_now}{else}{l s='In Stock'}{/if}{/if}
                                                                                {elseif (isset($product.quantity_all_versions) && $product.quantity_all_versions > 0)}
                                                                                        <link itemprop="availability" href="https://schema.org/LimitedAvailability" />{l s='Product available with different options'}
                                                                                {else}
                                                                                        <link itemprop="availability" href="https://schema.org/OutOfStock" />{l s='Out of stock'}
                                                                                {/if}
                                                                        </span>
                                                                {/if}
                                                                {hook h="displayProductPriceBlock" product=$product type="price"}
                                                                {hook h="displayProductPriceBlock" product=$product type="unit_price"}
                                                        {/if}
                                                </div> -->
                                        {/if}
                                        <!--<div class="product-contents">
                                                <!--<div class="actions">
                                                        <div class="actions-inner">
                                                                <ul class="add-to-links">
                                                                        <li class="cart">
                                                                                {if ($product.id_product_attribute == 0 || (isset($add_prod_display) && ($add_prod_display == 1))) && $product.available_for_order && !isset($restricted_country_mode) && $product.minimal_quantity <= 1 && $product.customizable != 2 && !$PS_CATALOG_MODE}
                                                                                        {if ($product.allow_oosp || $product.quantity > 0)}
                                                                                                {if isset($static_token)}
                                                                                                        <a class="button ajax_add_to_cart_button btn btn-default" href="{$link->getPageLink('cart',false, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;token={$static_token}", false)|escape:'html':'UTF-8'}" rel="nofollow"  title="{l s='Add to cart' mod='posnewproduct'}" data-id-product="{$product.id_product|intval}">
                                                                                                                <span>{l s='Add to cart'}</span>
                                                                                                        </a>
                                                                                                {else}
                                                                                                        <a class="button ajax_add_to_cart_button btn btn-default" href="{$link->getPageLink('cart',false, NULL, 'add=1&amp;id_product={$product.id_product|intval}', false)|escape:'html':'UTF-8'}" rel="nofollow" title="{l s='Add to cart' mod='postabcateslider1'}" data-id-product="{$product.id_product|intval}">
                                                                                                                <span>{l s='Add to cart'}</span>
                                                                                                        </a>
                                                                                                {/if}      
                                                                                        {else}
                                                                                                <span class="button ajax_add_to_cart_button btn btn-default disabled" >
                                                                                                        <span>{l s='Add to cart'}</span>
                                                                                                </span>
                                                                                        {/if}
                                                                                {/if}
                                                                        </li>	
                                                                        <li>
                                                                                <a class="addToWishlist wishlistProd_{$product.id_product|intval}" data-toggle="tooltip" data-placement="top" data-original-title="Wishlist"  href="#" data-wishlist="{$product.id_product|intval}" onclick="WishlistCart('wishlist_block_list', 'add', '{$product.id_product|intval}', false, 1); return false;">
                                                                                        <span>{l s="Add to Wishlist"}</span>
                                                                                </a>
                                                                        </li>
                                                                        <li>
                                                                                {if isset($comparator_max_item) && $comparator_max_item}
                                                                                        <a class="add_to_compare" data-toggle="tooltip" data-placement="top" data-original-title="Compare"  href="{$product.link|escape:'html':'UTF-8'}" data-id-product="{$product.id_product}">{l s='Add to Compare'}

                                                                                        </a>
                                                                                {/if}
                                                                        </li>
                                                                        <li>
                                                                                {if isset($quick_view) && $quick_view}
                                                                                        <a class="quick-view" title="{l s='Quick view'}" href="{$product.link|escape:'html':'UTF-8'}">
                                                                                                <span>{l s='Quick view'}</span>
                                                                                        </a>
                                                                                {/if}
                                                                        </li>
                                                                </ul>
                                                        </div>
                                                </div> -->	
                                                <!-- <div class="button-container">
                                                        <a class="button lnk_view btn btn-default" href="{$product.link|escape:'html':'UTF-8'}" title="{l s='View'}">
                                                                <span>{if (isset($product.customization_required) && $product.customization_required)}{l s='Customize'}{else}{l s='More'}{/if}</span>
                                                        </a>
                                                </div> -->
                                                {if isset($product.color_list)}
                                                        <div class="color-list-container">{$product.color_list}</div>
                                                {/if}
                                                <!-- <div class="product-flags">
                                                        {if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
                                                                {if isset($product.online_only) && $product.online_only}
                                                                        <span class="online_only">{l s='Online only'}</span>
                                                                {/if}
                                                        {/if}
                                                        {if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}

                                                        {elseif isset($product.reduction) && $product.reduction && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
                                                                <span class="discount">{l s='Reduced price!'}</span>
                                                        {/if}
                                                </div> -->
                                                {*{if (!$PS_CATALOG_MODE && $PS_STOCK_MANAGEMENT && ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
                                                        {if isset($product.available_for_order) && $product.available_for_order && !isset($restricted_country_mode)}
                                                                <!--<span class="availability">
                                                                        {if ($product.allow_oosp || $product.quantity > 0)}
                                                                                <span class="
                                                                                        {if $product.quantity <= 0 && isset($product.allow_oosp) && !$product.allow_oosp} label-danger{elseif $product.quantity <= 0} label-warning{else} label-success{/if}">
                                                                                        {if $product.quantity <= 0}
                                                                                                {if $product.allow_oosp}{if isset($product.available_later) && $product.available_later}{$product.available_later}{else}{l s='In Stock'}{/if}{else}{l s='Out of stock'}{/if}
                                                                                        {else}
                                                                                                {if isset($product.available_now) && $product.available_now}{$product.available_now}{else}{l s='In Stock'}{/if}
                                                                                        {/if}
                                                                                </span>
                                                                        {elseif (isset($product.quantity_all_versions) && $product.quantity_all_versions > 0)}
                                                                                <span class="label-warning">
                                                                                        {l s='Product available with different options'}
                                                                                </span>
                                                                        {else}
                                                                                <span class="label-danger">
                                                                                        {l s='Out of stock'}
                                                                                </span>
                                                                        {/if}
                                                                </span>-->
                                                        {/if}
                                                {/if}
                                                <!-- {if $page_name != 'index'}
                                                    <div class="functional-buttons clearfix">
                                                            {hook h='displayProductListFunctionalButtons' product=$product}
                                                            {if isset($comparator_max_item) && $comparator_max_item}
                                                                    <div class="compare">
                                                                            <a class="add_to_compare" href="{$product.link|escape:'html':'UTF-8'}" data-id-product="{$product.id_product}">{l s='Add to Compare'}</a>
                                                                    </div>
                                                            {/if}
                                                    </div>
                                                {/if} 
                                        </div> -->*}
                                </div>
                                <div class="points-block">
                                        <div>
                                                {if isset($product.pack_quantity) && $product.pack_quantity}{$product.pack_quantity|intval|cat:' x '}{/if}
                                                {*<a class="product-name" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url" style="padding-left:0px;">*}
                                                        {$product.manufacturer_name|truncate:45:'...'|escape:'html':'UTF-8'|upper}
                                                {*</a>*}
                                        </div>
                                        <div>
                                            <span>{if $logged}{l s="You earn"}&nbsp;{$product.points}{else $logged}{l s="You earn"}&nbsp;{$product.pointsNl}{/if}</span><span style="font-size: 11px;"> {l s="points !"}</span>
                                        </div>
                                </div>
                                <div class="price-block">
                                        {*<div class="ratings">{hook h='displayProductListReviews' product=$product}</div>*}
                                        {if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
                                                {if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
                                                        {hook h="displayProductPriceBlock" product=$product type='before_price'}
                                                        <div>
                                                                <span style="text-align: left; margin-right: 1px;">{l s='Value: '}</span>
                                                                <span class="price product-price" style="text-align: left;">
                                                                        {convertPrice price=$product.price_shop|floatval}
                                                                </span>
                                                        </div>
                                                        <div>
                                                                <span style="text-align: left; margin-right: 1px; color:#ef4136;">{l s='Price: '}</span>
                                                                <span class="price product-price" style="color:#ef4136; text-align: left; font-weight: bold;">
                                                                        {if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
                                                                </span>
                                                        </div>
                                                        <div>
                                                                <span style="text-align: left; margin-right: 1px; color:#ef4136;">{l s='Precio en Fluz: '}</span>
                                                                <span class="price product-price" style="color:#ef4136; text-align: left;">
                                                                        {if !$priceDisplay}{(($product.price)/(int)Configuration::get('REWARDS_VIRTUAL_VALUE_1')|escape:'html':'UTF-8')}{else}{(($product.price_tax_exc)/(int)Configuration::get('REWARDS_VIRTUAL_VALUE_1')|escape:'html':'UTF-8')}{/if}
                                                                </span>
                                                        </div>        
                                                        {if isset($product.specific_prices) && $product.specific_prices && isset($product.specific_prices.reduction) && $product.specific_prices.reduction > 0}
                                                                {hook h="displayProductPriceBlock" product=$product type="old_price"}
                                                                <span class="old-price product-price">
                                                                        {displayWtPrice p=$product.price_without_reduction}
                                                                </span>
                                                                {hook h="displayProductPriceBlock" id_product=$product.id_product type="old_price"}
                                                                {if $product.specific_prices.reduction_type == 'percentage'}
                                                                        <span class="price-percent-reduction">-{$product.specific_prices.reduction * 100}%</span>
                                                                {/if}
                                                        {/if}
                                                        {hook h="displayProductPriceBlock" product=$product type="price"}
                                                        {hook h="displayProductPriceBlock" product=$product type="unit_price"}
                                                        {hook h="displayProductPriceBlock" product=$product type='after_price'}
                                                {/if}
                                        {/if}
                                </div>
                        </li>
                {/foreach}
            </div>
	</ul>
        <div class="boxprevnext2">
            <a class="prev prev-product"><i class="icon-chevron-left"></i></a>
            <a class="next next-product"><i class="icon-chevron-right"></i></a>
        </div>
        </section>   
                
{addJsDefL name=min_item}{l s='Please select at least one product' js=1}{/addJsDefL}
{addJsDefL name=max_item}{l s='You cannot add more than %d product(s) to the product comparison' sprintf=$comparator_max_item js=1}{/addJsDefL}
{addJsDef comparator_max_item=$comparator_max_item}
{addJsDef comparedProductsIds=$compared_products}
{/if}
{literal}
    <script type="text/javascript"> 
        $(document).ready(function() {
                    var owl = $("#product_categoryAll");
                    owl.owlCarousel({
                    items : 4,
                    pagination :false,
                    slideSpeed: 1000,
                    responsiveClass:true,
                    itemsDesktop : [1199,4],
                    itemsDesktopSmall : [1080,3], 
                    itemsTablet: [768,3], 
                    itemsMobile : [480,1],
                    });

                    // Custom Navigation Events
                    $(".next-product").click(function(){
                    owl.trigger('owl.next');
                    })
                    $(".prev-product").click(function(){
                    owl.trigger('owl.prev');
                    })     
        });
    </script>
{/literal}
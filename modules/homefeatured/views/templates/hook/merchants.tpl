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

<!-- New Merchants -->          
        
        {*foreach from=$merchants item=merchant name=merchants}
            
            <div>
                {$merchant.name}
                <div><img src="{$img_manu_dir}{$merchant.id_manufacturer}.jpg" alt="{$merchant.manufacturer_name|escape:'htmlall':'UTF-8'}" title="{$merchant.manufacturer_name|escape:'htmlall':'UTF-8'}" class="imgMini"/></div>
                <div>{l s="Save"} {math equation='round(((p - r) / r)*100)' p=$merchant.price_shop r=$merchant.price}%</div>
                                        
                                </div>
                                <div>
                                        <a class="product_img_link" href="{$merchant.link|escape:'html':'UTF-8'}" title="{$merchant.name|escape:'html':'UTF-8'}" itemprop="url">
                                                <img class="img-responsive pruebaImgCategory" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'thickbox_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} itemprop="image" />
                                        </a>
                                        {if isset($quick_view) && $quick_view}
                                                <a class="quick-view" title="Quick view" href="{$product.link|escape:'html':'UTF-8'}" style="position: absolute;" >
                                                    <span class="quick"><i class="icon-search"></i></span>
                                                </a>
                                        {/if}
                                        
            </div>
            
        {/foreach*}
        
        <section class="page-product-box blockproductscategory">
        <div class="divTitleFeatured">
            <h1 class="titleFeatured2">{l s="COMERCIOS"}</h1>
        </div>
        
        <div class="boxprevnext2">
            <a class="prev prev-product2"><i class="icon-chevron-left"></i></a>
            <a class="next next-product2"><i class="icon-chevron-right"></i></a>
        </div>  
          <ul{if isset($id) && $id} id="{$id}"{/if} class="product_list grid row{if isset($class) && $class} {$class}{/if}">
            <div id="product_categoryAll2">
                {foreach from=$products item=product name=products}
                        {math equation="(total%perLine)" total=$smarty.foreach.products.total perLine=$nbItemsPerLine assign=totModulo}
                        {math equation="(total%perLineT)" total=$smarty.foreach.products.total perLineT=$nbItemsPerLineTablet assign=totModuloTablet}
                        {math equation="(total%perLineT)" total=$smarty.foreach.products.total perLineT=$nbItemsPerLineMobile assign=totModuloMobile}
                        {if $totModulo == 0}{assign var='totModulo' value=$nbItemsPerLine}{/if}
                        {if $totModuloTablet == 0}{assign var='totModuloTablet' value=$nbItemsPerLineTablet}{/if}
                        {if $totModuloMobile == 0}{assign var='totModuloMobile' value=$nbItemsPerLineMobile}{/if}
                        <li class="ajax_block_product nopadding">
                                <div class="title-block">
                                        <div><img src="{$img_manu_dir}{$product.id_manufacturer}.jpg" alt="{$product.manufacturer_name|escape:'htmlall':'UTF-8'}" title="{$product.manufacturer_name|escape:'htmlall':'UTF-8'}" class="imgMini"/></div>
                                        <div>{l s="Save"} {math equation='round(((p - r) / p)*100)' p=$product.price_shop r=$product.price}%</div>
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
                                        
                                </div>
                                <div class="points-block">
                                        <div>
                                                {if isset($product.pack_quantity) && $product.pack_quantity}{$product.pack_quantity|intval|cat:' x '}{/if}
                                                {*<a class="product-name" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url" style="padding-left:0px;">*}
                                                        {$product.name|truncate:45:'...'|escape:'html':'UTF-8'|upper}
                                                {*</a>*}
                                        </div>
                                        <div>
                                                <span>{if $logged}{l s="Recibes"}&nbsp;{$product.points}{else $logged}{l s="Recibes"}&nbsp;{$product.pointsNl}{/if}</span><span style="font-size: 11px;"> {l s="Fluz !"}</span>
                                        </div>
                                </div>
                                <div class="price-block">
                                        {*<div class="ratings">{hook h='displayProductListReviews' product=$product}</div>*}
                                        {if (!$PS_CATALOG_MODE AND ((isset($product.show_price) && $product.show_price) || (isset($product.available_for_order) && $product.available_for_order)))}
                                                {if isset($product.show_price) && $product.show_price && !isset($restricted_country_mode)}
                                                        {hook h="displayProductPriceBlock" product=$product type='before_price'}
                                                        <div>
                                                                <span style="text-align: left; margin-right: 1px;">{l s='Valor: '}</span>
                                                                <span class="price product-price" style="text-align: left;">
                                                                        {convertPrice price=$product.price_shop|floatval}
                                                                </span>
                                                        </div>
                                                        <div>
                                                                <span style="text-align: left; margin-right: 1px; color:#ef4136;">{l s='Precio: '}</span>
                                                                <span class="price product-price" style="color:#ef4136; text-align: left;">
                                                                        {if !$priceDisplay}{convertPrice price=$product.price}{else}{convertPrice price=$product.price_tax_exc}{/if}
                                                                </span>
                                                        </div>
                                                        <div>
                                                                <span style="text-align: left; margin-right: 1px; color:#ef4136;">{l s='Precio en Puntos: '}</span>
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
        </section>   

<!-- New Merchants -->

<script type="text/javascript"> 
    $(document).ready(function() {
		var owl = $("#product_categoryAll2");
		owl.owlCarousel({
		items : 4,
		 pagination :false,
		slideSpeed: 1000,
		itemsDesktop : [1199,3],
		itemsDesktopSmall : [911,2], 
		itemsTablet: [767,2], 
		itemsMobile : [480,1],
		});
		 
		// Custom Navigation Events
		$(".next-product2").click(function(){
		owl.trigger('owl.next');
		})
		$(".prev-product2").click(function(){
		owl.trigger('owl.prev');
		})     
    });
</script>
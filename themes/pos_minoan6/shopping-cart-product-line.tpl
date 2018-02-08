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
<!--<tr class="accordion">
    <td colspan="8">Orden</td>
</tr>-->
{assign var="idprod" value=$product.reference}
{$product.id_product = $productsID.$idprod}
<div class="row table-product-cart" id="product_{$product.id_product}_{$product.id_product_attribute}_{if $quantityDisplayed > 0}nocustom{else}0{/if}_{$product.id_address_delivery|intval}{if !empty($product.gift)}_gift{/if}" class="cart_item{if isset($productLast) && $productLast && (!isset($ignoreProductLast) || !$ignoreProductLast)} last_item{/if}{if isset($productFirst) && $productFirst} first_item{/if}{if isset($customizedDatas.$productId.$productAttributeId) AND $quantityDisplayed == 0} alternate_item{/if} address_{$product.id_address_delivery|intval} {if $odd}odd{else}even{/if}" style="padding:0px;">
    <input type="hidden" value="{$productsPoints.$idprod}" id="pto_unit_fluz-{$product.id_product}" class="pto_unit_fluz-{$product.id_product}">
    <input type="hidden" value="{$product.id_product}" id="id_product_p" class="id_product_p">
    <input type="hidden" value="{$product.cart_quantity}" id="quantity_product_p" class="quantity_product_p">

    <div class="row" style="padding:0px;">
        {if !isset($noDeleteButton) || !$noDeleteButton}
            <div class="row cart_delete text-left" data-title="{l s='Delete'}" style="padding:0px;">
                {if (!isset($customizedDatas.$productId.$productAttributeId) OR $quantityDisplayed > 0) && empty($product.gift)}
                        <div class="remove-cart">
                                <a rel="nofollow" title="{l s='Delete'}" class="cart_quantity_delete" id="{$product.id_product}_{$product.id_product_attribute}_{if $quantityDisplayed > 0}nocustom{else}0{/if}_{$product.id_address_delivery|intval}" href="{$link->getPageLink('cart', true, NULL, "delete=1&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_address_delivery={$product.id_address_delivery|intval}&amp;token={$token_cart}")|escape:'html':'UTF-8'}"><i class="icon-remove" style="font-size: 12px; color: #d6d4d4;"></i></a>
                        </div>
                {else}

                {/if}
            </div>
        {/if}
        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12" style="padding:0px;">    
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-4 quantity-product cart_quantity">
                {if (isset($cannotModify) && $cannotModify == 1)}
                        <span>
                                {if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}
                                        {$product.customizationQuantityTotal}
                                {else}
                                        {$product.cart_quantity-$quantityDisplayed}
                                {/if}
                        </span>
                {else}
                        {if isset($customizedDatas.$productId.$productAttributeId) AND $quantityDisplayed == 0}
                                <span id="cart_quantity_custom_{$product.id_product}_{$product.id_product_attribute}_{$product.id_address_delivery|intval}" >{$product.customizationQuantityTotal}</span>
                        {/if}
                        {if !isset($customizedDatas.$productId.$productAttributeId) OR $quantityDisplayed > 0}

                                <input type="hidden" value="{if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}{$customizedDatas.$productId.$productAttributeId|@count}{else}{$product.cart_quantity-$quantityDisplayed}{/if}" name="quantity_{$product.id_product}_{$product.id_product_attribute}_{if $quantityDisplayed > 0}nocustom{else}0{/if}_{$product.id_address_delivery|intval}_hidden" />
                                <div class="row cart_quantity_button clearfix" style="padding:0px; margin-top: 20px;">
                                    {if $product.minimal_quantity < ($product.cart_quantity-$quantityDisplayed) OR $product.minimal_quantity <= 1}
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="padding:0px;">
                                        <a style="border:none;" rel="nofollow" class="cart_quantity_down btn btn-default button-minus" id="cart_quantity_down_{$product.id_product}_{$product.id_product_attribute}_{if $quantityDisplayed > 0}nocustom{else}0{/if}_{$product.id_address_delivery|intval}" href="{$link->getPageLink('cart', true, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_address_delivery={$product.id_address_delivery|intval}&amp;op=down&amp;token={$token_cart}")|escape:'html':'UTF-8'}" title="{l s='Subtract'}">
                                            <span><i class="icon-minus"></i></span>
                                        </a>
                                    </div>
                                    {else}
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="padding:0px;">    
                                        <a style="border:none;" class="cart_quantity_down btn btn-default button-minus disabled" href="#" id="cart_quantity_down_{$product.id_product}_{$product.id_product_attribute}_{if $quantityDisplayed > 0}nocustom{else}0{/if}_{$product.id_address_delivery|intval}" title="{l s='You must purchase a minimum of %d of this product.' sprintf=$product.minimal_quantity}">
                                            <span><i class="icon-minus"></i></span>
                                        </a>
                                    </div>        
                                    {/if}
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="padding:0px;">
                                        <input style="color:#000; box-shadow: inset 2px 2px 2px rgba(0, 0, 0, 0.075);" size="2" type="text" autocomplete="off" class="cart_quantity_input form-control grey" value="{if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}{$customizedDatas.$productId.$productAttributeId|@count}{else}{$product.cart_quantity-$quantityDisplayed}{/if}"  name="quantity_{$product.id_product}_{$product.id_product_attribute}_{if $quantityDisplayed > 0}nocustom{else}0{/if}_{$product.id_address_delivery|intval}" />
                                    </div> 
                                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="padding:0px;">
                                        <a style="border:none;" rel="nofollow" class="cart_quantity_up btn btn-default button-plus" id="cart_quantity_up_{$product.id_product}_{$product.id_product_attribute}_{if $quantityDisplayed > 0}nocustom{else}0{/if}_{$product.id_address_delivery|intval}" href="{$link->getPageLink('cart', true, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_address_delivery={$product.id_address_delivery|intval}&amp;token={$token_cart}")|escape:'html':'UTF-8'}" title="{l s='Add'}"><span><i class="icon-plus"></i></span></a>
                                    </div>    
                                </div>
                        {/if}
                {/if}
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-3 img-cart-product" style="padding: 0px;">
                    <a href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute, false, false, true)|escape:'html':'UTF-8'}">
                    {if $img_parent.$idprod == ''}
                        <img class="img-product-cart" src="{$s3}m/m/{$product.id_manufacturer}.jpg" alt="{$product.name|escape:'html':'UTF-8'}" {if isset($smallSize)}width="{$smallSize.width}" height="{$smallSize.height}" {/if} /></a>
                    {else}
                        <img class="img-product-cart" src="{$link->getImageLink($product.link_rewrite, $img_parent.$idprod, 'thickbox_default')|escape:'html':'UTF-8'}" alt="{$product.name|escape:'html':'UTF-8'}" {if isset($smallSize)} width="{$smallSize.width}" height="{$smallSize.height}" {/if} /></a>
                    {/if}
            </div>
        </div>
        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12" style="padding-right:0px;">
            <div class="col-lg-7 col-md-6 col-sm-6 col-xs-5 description-product-cart" style="margin-top:0px;">
                {capture name=sep} : {/capture}
                {capture}{l s=' : '}{/capture}
                <p class="product-name product-cart"><a href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute, false, false, true)|escape:'html':'UTF-8'}">{$product.name|escape:'html':'UTF-8'}</a></p>
                        {*if $product.reference}<small class="cart_ref">{l s='SKU'}{$smarty.capture.default}{$product.reference|escape:'html':'UTF-8'}</small>{/if*}
                {if isset($product.attributes) && $product.attributes}<small><a href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute, false, false, true)|escape:'html':'UTF-8'}">{$product.attributes|@replace: $smarty.capture.sep:$smarty.capture.default|escape:'html':'UTF-8'}</a></small>{/if}
            </div>
            <div class="col-lg-5 col-md-6 col-sm-6 col-xs-7 div-query-cart"style="padding:0px;">
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-3 price_value_product" style="font-size: 12px;color:#000; font-weight: bold;">Valor:</div>
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-9">
                    <ul class="price text-right div-query-cart style_cart_product" id="product_price_{$product.id_product}_{$product.id_product_attribute}{if $quantityDisplayed > 0}_nocustom{/if}_{$product.id_address_delivery|intval}{if !empty($product.gift)}_gift{/if}">
                {if !empty($product.gift)}
                        <li class="gift-icon">{l s='Gift!'}</li>
                {else}
                {if !$priceDisplay}
                <li class="price{if isset($product.is_discounted) && $product.is_discounted && isset($product.reduction_applies) && $product.reduction_applies} special-price{/if}" style="font-size: 12px;color:#000; font-weight: bold;">{convertPrice price=$product.price_wt}</li>
                                {else}
               	 	<li class="price{if isset($product.is_discounted) && $product.is_discounted && isset($product.reduction_applies) && $product.reduction_applies} special-price{/if}">{convertPrice price=$product.price}</li>
                                {/if}
                                {if isset($product.is_discounted) && $product.is_discounted && isset($product.reduction_applies) && $product.reduction_applies}
                        <li class="price-percent-reduction small">
                                {if !$priceDisplay}
                                        {if isset($product.reduction_type) && $product.reduction_type == 'amount'}
                                        {assign var='priceReduction' value=($product.price_wt - $product.price_without_specific_price)}
                                        {assign var='symbol' value=$currency->sign}
                                {else}
                                        {assign var='priceReduction' value=(($product.price_without_specific_price - $product.price_wt)/$product.price_without_specific_price) * 100 * -1}
                                        {assign var='symbol' value='%'}
                                {/if}
                                                {else}
                                                        {if isset($product.reduction_type) && $product.reduction_type == 'amount'}
                                                                {assign var='priceReduction' value=($product.price - $product.price_without_specific_price)}
                                                                {assign var='symbol' value=$currency->sign}
                                                        {else}
                                                                {assign var='priceReduction' value=(($product.price_without_specific_price - $product.price)/$product.price_without_specific_price) * -100}
                                                                {assign var='symbol' value='%'}
                                                        {/if}
                                                {/if}
                                                {if $symbol == '%'}
                                                        &nbsp;{$priceReduction|string_format:"%.2f"|regex_replace:"/[^\d]0+$/":""}{$symbol}&nbsp;
                                                {else}
                                                        &nbsp;{convertPrice price=$priceReduction}&nbsp;
                                                {/if}
                                        </li>
                                        <li class="old-price">{convertPrice price=$product.price_without_specific_price}</li>
                                {/if}
                                    {assign var="idprodshop" value=$product.reference}
                                    {assign var='save_price' value= {math equation='round(((p - r) / p)*100)' p=$shop.$idprodshop r=$product.price}}
                                    {if ($logged && !$save_price <= 0) && $type_currency.$idprod == 'COP'}
                                        <div style="color:#ef4136;">{l s="Ahorra: "} {$save_price}%</div>
                                    {else if ($logged && !$save_price <= 0) && $type_currency.$idprod == 'USD'} 
                                        <div style="color:#ef4136;">{l s="Ahorra: "} {$save_dolar.$idprod}%</div>
                                    {else if !$logged AND !$save_price <= 0 AND $type_currency.$idprod == 'COP'}
                                        <div style="color:#ef4136;">{l s="Ahorra: "} {$save_price}%</div>
                                    {else if !$logged AND !$save_price <= 0 AND $type_currency.$idprod == 'USD'}
                                        <div style="color:#ef4136;">{l s="Ahorra: "} {$save_dolar.$idprod}%</div>    
                                    {else if $logged AND $save_price <= 0}
                                        <div style="color:#ef4136;"></div>
                                    {else if !$logged AND $save_price <= 0}
                                        <div style="color:#ef4136;"></div>
                                    {/if}
                        {/if}
                </ul>
                </div>
                <div class="row div-price" style="padding:0px; margin-top: 20px;border-top:1px solid #d6d4d4; padding-top: 10px;">
                    <div class="col-lg-12 ptos_cart div-query-cart">
                        Precio en fluz: {$product.price/25}&nbsp;{l s="Fluz"}
                    </div>
                    <div class="col-lg-12 ptos_cart div-query-cart">
                        Fluz a obtener: <span id="div-query-cart">{$productsPoints.$idprod}</span>&nbsp;{l s="Fluz"}
                    </div>
                </div>
            </div>
        </div>
    </div>    
    <div class="row div-instruccion">
        <div class="col-lg-12 title-instru">
            <div id="button_{$product.id_product}" class="row btn-intru-cart" onclick="accordion_display({$product.id_product})">
                <i class="icon icon-plus" id="icon-plus-instru_{$product.id_product}"></i>
                <i class="icon icon-minus" id="icon-minus-instru_{$product.id_product}" style="display:none;"></i>
                Instrucciones
            </div>
        </div>
        <div id="container_{$product.id_product}" class="row container_{$product.id_product}" style="display:none;">
            <p class="description_product_cart">{$product.description_short|strip_tags}</p>
        </div>
    </div> 
    <div class="row div-terms">
        <div class="col-lg-12 title-instru">
            <div id="button_{$product.id_product}" class="row btn-intru-cart" onclick="accordion_display_terms({$product.id_product})">
                <i class="icon icon-plus" id="icon-plus-terms_{$product.id_product}"></i>
                <i class="icon icon-minus" id="icon-minus-terms_{$product.id_product}" style="display:none;"></i>
                T&eacute;rminos & Condiciones
            </div>
        </div>
        <div id="container_{$product.id_product}" class="row container_terms_{$product.id_product}" style="display:none;">
            <p class="description_product_cart">{$product.description|strip_tags}</p>
        </div>
    </div>     
</div>
{literal}
    <script>
        function accordion_display(id) {
            var esVisible = $('.container_'+id).is(":visible");
            if(esVisible){
                $('.container_'+id).slideToggle("slow");
                $('#button_'+id).removeClass('clicked');
                $('#icon-plus-instru_'+id).show();
                $('#icon-minus-instru_'+id).hide();
            }
            else {
                $('.container_history').css('display','none');
                $('.container_'+id).slideToggle("slow");
                $('#button_'+id).toggleClass('clicked');
                $('#icon-plus-instru_'+id).hide();
                $('#icon-minus-instru_'+id).show();
            }
        }
        
        function accordion_display_terms(id) {
            var esVisible = $('.container_terms_'+id).is(":visible");
            if(esVisible){
                $('.container_terms_'+id).slideToggle("slow");
                $('#button_'+id).removeClass('clicked');
                $('#icon-plus-terms_'+id).show();
                $('#icon-minus-terms_'+id).hide();
            }
            else {
                $('.container_history').css('display','none');
                $('.container_terms_'+id).slideToggle("slow");
                $('#button_'+id).toggleClass('clicked');
                $('#icon-plus-terms_'+id).hide();
                $('#icon-minus-terms_'+id).show();
            }
        }
    </script>
{/literal}      
<!--<tr id="product_{$product.id_product}_{$product.id_product_attribute}_{if $quantityDisplayed > 0}nocustom{else}0{/if}_{$product.id_address_delivery|intval}{if !empty($product.gift)}_gift{/if}" class="cart_item{if isset($productLast) && $productLast && (!isset($ignoreProductLast) || !$ignoreProductLast)} last_item{/if}{if isset($productFirst) && $productFirst} first_item{/if}{if isset($customizedDatas.$productId.$productAttributeId) AND $quantityDisplayed == 0} alternate_item{/if} address_{$product.id_address_delivery|intval} {if $odd}odd{else}even{/if}">
        <td class="cart_quantity text-center" data-title="{l s='Quantity'}">
		{if (isset($cannotModify) && $cannotModify == 1)}
			<span>
				{if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}
					{$product.customizationQuantityTotal}
				{else}
					{$product.cart_quantity-$quantityDisplayed}
				{/if}
			</span>
		{else}
			{if isset($customizedDatas.$productId.$productAttributeId) AND $quantityDisplayed == 0}
				<span id="cart_quantity_custom_{$product.id_product}_{$product.id_product_attribute}_{$product.id_address_delivery|intval}" >{$product.customizationQuantityTotal}</span>
			{/if}
			{if !isset($customizedDatas.$productId.$productAttributeId) OR $quantityDisplayed > 0}

				<input type="hidden" value="{if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}{$customizedDatas.$productId.$productAttributeId|@count}{else}{$product.cart_quantity-$quantityDisplayed}{/if}" name="quantity_{$product.id_product}_{$product.id_product_attribute}_{if $quantityDisplayed > 0}nocustom{else}0{/if}_{$product.id_address_delivery|intval}_hidden" />
				<input size="2" type="text" autocomplete="off" class="cart_quantity_input form-control grey" value="{if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}{$customizedDatas.$productId.$productAttributeId|@count}{else}{$product.cart_quantity-$quantityDisplayed}{/if}"  name="quantity_{$product.id_product}_{$product.id_product_attribute}_{if $quantityDisplayed > 0}nocustom{else}0{/if}_{$product.id_address_delivery|intval}" />
				<div class="cart_quantity_button clearfix">
				{if $product.minimal_quantity < ($product.cart_quantity-$quantityDisplayed) OR $product.minimal_quantity <= 1}
					<a rel="nofollow" class="cart_quantity_down btn btn-default button-minus" id="cart_quantity_down_{$product.id_product}_{$product.id_product_attribute}_{if $quantityDisplayed > 0}nocustom{else}0{/if}_{$product.id_address_delivery|intval}" href="{$link->getPageLink('cart', true, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_address_delivery={$product.id_address_delivery|intval}&amp;op=down&amp;token={$token_cart}")|escape:'html':'UTF-8'}" title="{l s='Subtract'}">
				<span><i class="icon-minus"></i></span>
				</a>
				{else}
					<a class="cart_quantity_down btn btn-default button-minus disabled" href="#" id="cart_quantity_down_{$product.id_product}_{$product.id_product_attribute}_{if $quantityDisplayed > 0}nocustom{else}0{/if}_{$product.id_address_delivery|intval}" title="{l s='You must purchase a minimum of %d of this product.' sprintf=$product.minimal_quantity}">
					<span><i class="icon-minus"></i></span>
				</a>
				{/if}
					<a rel="nofollow" class="cart_quantity_up btn btn-default button-plus" id="cart_quantity_up_{$product.id_product}_{$product.id_product_attribute}_{if $quantityDisplayed > 0}nocustom{else}0{/if}_{$product.id_address_delivery|intval}" href="{$link->getPageLink('cart', true, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_address_delivery={$product.id_address_delivery|intval}&amp;token={$token_cart}")|escape:'html':'UTF-8'}" title="{l s='Add'}"><span><i class="icon-plus"></i></span></a>
				</div>
			{/if}
		{/if}
	</td>
        <td class="cart_product">
		<a href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute, false, false, true)|escape:'html':'UTF-8'}">
                {if $img_parent.$idprod == ''}
                    <img src="{$s3}m/m/{$product.id_manufacturer}.jpg" alt="{$product.name|escape:'html':'UTF-8'}" {if isset($smallSize)}width="{$smallSize.width}" height="{$smallSize.height}" {/if} /></a>
                {else}
                    <img src="{$link->getImageLink($product.link_rewrite, $img_parent.$idprod, 'thickbox_default')|escape:'html':'UTF-8'}" alt="{$product.name|escape:'html':'UTF-8'}" {if isset($smallSize)} width="{$smallSize.width}" height="{$smallSize.height}" {/if} /></a>
                {/if}    
        </td>
	<td class="cart_description" data-title="{l s='Descripcion'}">
		{capture name=sep} : {/capture}
		{capture}{l s=' : '}{/capture}
		<p class="product-name product-cart"><a href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute, false, false, true)|escape:'html':'UTF-8'}">{$product.name|escape:'html':'UTF-8'}</a></p>
			{*if $product.reference}<small class="cart_ref">{l s='SKU'}{$smarty.capture.default}{$product.reference|escape:'html':'UTF-8'}</small>{/if*}
		{if isset($product.attributes) && $product.attributes}<small><a href="{$link->getProductLink($product.id_product, $product.link_rewrite, $product.category, null, null, $product.id_shop, $product.id_product_attribute, false, false, true)|escape:'html':'UTF-8'}">{$product.attributes|@replace: $smarty.capture.sep:$smarty.capture.default|escape:'html':'UTF-8'}</a></small>{/if}
	</td>
	{*if $PS_STOCK_MANAGEMENT}
		<td class="cart_avail"><span class="label{if $product.quantity_available <= 0 && isset($product.allow_oosp) && !$product.allow_oosp} label-danger{elseif $product.quantity_available <= 0} label-warning{else} label-success{/if}">{if $product.quantity_available <= 0}{if isset($product.allow_oosp) && $product.allow_oosp}{if isset($product.available_later) && $product.available_later}{$product.available_later}{else}{l s='In Stock'}{/if}{else}{l s='Out of stock'}{/if}{else}{if isset($product.available_now) && $product.available_now}{$product.available_now}{else}{l s='In Stock'}{/if}{/if}</span>{if !$product.is_virtual}{hook h="displayProductDeliveryTime" product=$product}{/if}</td>
	{/if*}
        
        <td colspan="1" style="text-align:right;" class="td-pto" data-title="{l s='Fluz A Obtener'}">
            <p class="ptoCart">{$productsPoints.$idprod}&nbsp;{l s="Fluz."}</p>
        </td>
        
        <td class="cart_unit" data-title="{l s='Unit price'}">
		<ul class="price text-right" id="product_price_{$product.id_product}_{$product.id_product_attribute}{if $quantityDisplayed > 0}_nocustom{/if}_{$product.id_address_delivery|intval}{if !empty($product.gift)}_gift{/if}">
			{if !empty($product.gift)}
				<li class="gift-icon">{l s='Gift!'}</li>
			{else}
            	{if !$priceDisplay}
					<li class="price{if isset($product.is_discounted) && $product.is_discounted && isset($product.reduction_applies) && $product.reduction_applies} special-price{/if}">{convertPrice price=$product.price_wt}</li>
				{else}
               	 	<li class="price{if isset($product.is_discounted) && $product.is_discounted && isset($product.reduction_applies) && $product.reduction_applies} special-price{/if}">{convertPrice price=$product.price}</li>
				{/if}
				{if isset($product.is_discounted) && $product.is_discounted && isset($product.reduction_applies) && $product.reduction_applies}
                	<li class="price-percent-reduction small">
            			{if !$priceDisplay}
            				{if isset($product.reduction_type) && $product.reduction_type == 'amount'}
                    			{assign var='priceReduction' value=($product.price_wt - $product.price_without_specific_price)}
                    			{assign var='symbol' value=$currency->sign}
                    		{else}
                    			{assign var='priceReduction' value=(($product.price_without_specific_price - $product.price_wt)/$product.price_without_specific_price) * 100 * -1}
                    			{assign var='symbol' value='%'}
                    		{/if}
						{else}
							{if isset($product.reduction_type) && $product.reduction_type == 'amount'}
								{assign var='priceReduction' value=($product.price - $product.price_without_specific_price)}
								{assign var='symbol' value=$currency->sign}
							{else}
								{assign var='priceReduction' value=(($product.price_without_specific_price - $product.price)/$product.price_without_specific_price) * -100}
								{assign var='symbol' value='%'}
							{/if}
						{/if}
						{if $symbol == '%'}
							&nbsp;{$priceReduction|string_format:"%.2f"|regex_replace:"/[^\d]0+$/":""}{$symbol}&nbsp;
						{else}
							&nbsp;{convertPrice price=$priceReduction}&nbsp;
						{/if}
					</li>
					<li class="old-price">{convertPrice price=$product.price_without_specific_price}</li>
				{/if}
                                    {assign var="idprodshop" value=$product.reference}
                                    {assign var='save_price' value= {math equation='round(((p - r) / p)*100)' p=$shop.$idprodshop r=$product.price}}
                                    {if ($logged && !$save_price <= 0) && $type_currency.$idprod == 'COP'}
                                        <div style="color:#ef4136;">{l s="Ahorra: "} {$save_price}%</div>
                                    {else if ($logged && !$save_price <= 0) && $type_currency.$idprod == 'USD'} 
                                        <div style="color:#ef4136;">{l s="Ahorra: "} {$save_dolar.$idprod}%</div>
                                    {else if !$logged AND !$save_price <= 0 AND $type_currency.$idprod == 'COP'}
                                        <div style="color:#ef4136;">{l s="Ahorra: "} {$save_price}%</div>
                                    {else if !$logged AND !$save_price <= 0 AND $type_currency.$idprod == 'USD'}
                                        <div style="color:#ef4136;">{l s="Ahorra: "} {$save_dolar.$idprod}%</div>    
                                    {else if $logged AND $save_price <= 0}
                                        <div style="color:#ef4136;"></div>
                                    {else if !$logged AND $save_price <= 0}
                                        <div style="color:#ef4136;"></div>
                                    {/if}
			{/if}
		</ul>
	</td>


	{if !isset($noDeleteButton) || !$noDeleteButton}
		<td class="cart_delete text-center" data-title="{l s='Delete'}">
		{if (!isset($customizedDatas.$productId.$productAttributeId) OR $quantityDisplayed > 0) && empty($product.gift)}
			<div>
				<a rel="nofollow" title="{l s='Delete'}" class="cart_quantity_delete" id="{$product.id_product}_{$product.id_product_attribute}_{if $quantityDisplayed > 0}nocustom{else}0{/if}_{$product.id_address_delivery|intval}" href="{$link->getPageLink('cart', true, NULL, "delete=1&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_address_delivery={$product.id_address_delivery|intval}&amp;token={$token_cart}")|escape:'html':'UTF-8'}"><i class="icon-trash"></i></a>
			</div>
		{else}

		{/if}
		</td>
	{/if}
	<td class="cart_total total-cart" data-title="{l s='Total'}">
		<span class="price" id="total_product_price_{$product.id_product}_{$product.id_product_attribute}{if $quantityDisplayed > 0}_nocustom{/if}_{$product.id_address_delivery|intval}{if !empty($product.gift)}_gift{/if}">
			{if !empty($product.gift)}
				<span class="gift-icon">{l s='Gift!'}</span>
			{else}
				{if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}
					{if !$priceDisplay}{displayPrice price=$product.total_customization_wt}{else}{displayPrice price=$product.total_customization}{/if}
				{else}
					{if !$priceDisplay}{displayPrice price=$product.total_wt}{else}{displayPrice price=$product.total}{/if}
				{/if}
			{/if}
		</span>
	</td>

</tr>-->
{literal}

    <style>
        @media (max-width:425px){
            .price_value_product{margin-top: 10px; padding-left: 0px;}
        }
    </style>
    
{/literal}
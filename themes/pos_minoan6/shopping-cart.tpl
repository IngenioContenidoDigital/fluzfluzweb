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

{capture name=path}{l s='Your shopping cart'}{/capture}

{if isset($account_created)}
	<p class="alert alert-success">
		{l s='Your account has been created.'}
	</p>
{/if}

<div class="col-lg-7 section_cart"></div>

{assign var='current_step' value='summary'}
{include file="$tpl_dir./order-steps.tpl"}
{include file="$tpl_dir./errors.tpl"}

{if isset($empty)}
{literal}    
    <style>    
        .menu-pay-disabled{display: block !important;}
        .breadcrumb{display: block !important;}
    </style>
{/literal}
    <p class="alert alert-warning">{l s='Your shopping cart is empty.'}</p>
    <div class="row continue_shop">
        <a href="/content/6-categorias">
            <i class="icon-chevron-left" style="color:#EF4136;"></i>
            Comprar
        </a>
    </div>
{elseif $PS_CATALOG_MODE}
    {literal}    
        <style>    
            .menu-pay-disabled{display: block !important;}
            .breadcrumb{display: block !important;}
        </style>
    {/literal}
    <p class="alert alert-warning">{l s='This store has not accepted your new order.'}</p>
    <div class="row continue_shop">
        <a href="/content/6-categorias">
            <i class="icon-chevron-left" style="color:#EF4136;"></i>
            Comprar
        </a>
    </div>
{else}
    <div class="row" style="padding:0px;">
        <div class="col-lg-11 col-sm-12 col-md-12 col-xs-12" style="padding:0px; text-align: center;">
            <img class="logo logo_cart" src="https://fluzfluz.co/img/fluzfluz-logo-1464806235.jpg" alt="FluzFluz">
        </div>
    </div>
    <div>
        <div class="container third-step">
                <div class="row bs-wizard" style="border-bottom:0;">

                    <div class="col-xs-4 bs-wizard-step complete">
                      <div class="progress"><div class="progress-bar" style=" background: #FFF;"></div></div>
                      <a href="{$link->getPageLink('order', true)}" class="bs-wizard-dot"></a>
                      <div class="bs-wizard-info text-center">Resumen Carrito</div>
                    </div>

                    <div class="col-xs-4 bs-wizard-step complete"><!-- complete -->
                        <div class="progress" style="left:50%;"><div class="progress-bar" style=" background: #FFF;"></div></div>
                      <a href="#" class="bs-wizard-dot bs-wizard-dot-second"></a>
                      <div class="bs-wizard-info text-center">Pago Seguro</div>
                    </div>

                    <div class="col-xs-4 bs-wizard-step active"><!-- complete -->
                      <a href="#" class="bs-wizard-dot bs-wizard-dot-third"></a>
                      <div class="bs-wizard-info text-center" style="margin-top: 47px;">Confirmaci&oacute;n</div>
                    </div>

                </div>
        </div>
    </div>
    <div class="row continue_shop">
        <a href="/content/6-categorias">
            <i class="icon-chevron-left" style="color:#EF4136;"></i>
            Continuar Comprando
        </a>
    </div>     
	<p id="emptyCartWarning" class="alert alert-warning unvisible">{l s='Your shopping cart is empty.'}</p>
	{if isset($lastProductAdded) AND $lastProductAdded}
		<div class="cart_last_product">
			<div class="cart_last_product_header">
				<div class="left">{l s='Last product added'}</div>
			</div>
			<a class="cart_last_product_img" href="{$link->getProductLink($lastProductAdded.id_product, $lastProductAdded.link_rewrite, $lastProductAdded.category, null, null, $lastProductAdded.id_shop)|escape:'html':'UTF-8'}">
				<img src="{$link->getImageLink($lastProductAdded.link_rewrite, $lastProductAdded.id_image, 'small_default')|escape:'html':'UTF-8'}" alt="{$lastProductAdded.name|escape:'html':'UTF-8'}"/>
			</a>
			<div class="cart_last_product_content">
				<p class="product-name">
					<a href="{$link->getProductLink($lastProductAdded.id_product, $lastProductAdded.link_rewrite, $lastProductAdded.category, null, null, null, $lastProductAdded.id_product_attribute)|escape:'html':'UTF-8'}">
						{$lastProductAdded.name|escape:'html':'UTF-8'}
					</a>
				</p>
				{if isset($lastProductAdded.attributes) && $lastProductAdded.attributes}
					<small>
						<a href="{$link->getProductLink($lastProductAdded.id_product, $lastProductAdded.link_rewrite, $lastProductAdded.category, null, null, null, $lastProductAdded.id_product_attribute)|escape:'html':'UTF-8'}">
							{$lastProductAdded.attributes|escape:'html':'UTF-8'}
						</a>
					</small>
				{/if}
			</div>
		</div>
	{/if}
	{assign var='total_discounts_num' value="{if $total_discounts != 0}1{else}0{/if}"}
	{assign var='use_show_taxes' value="{if $use_taxes && $show_taxes}2{else}0{/if}"}
	{assign var='total_wrapping_taxes_num' value="{if $total_wrapping != 0}1{else}0{/if}"}
	{* eu-legal *}
	{hook h="displayBeforeShoppingCartBlock"}
        <div id="order-detail-content" class="col-lg-7 col-md-7 col-sm-12 col-xs-12" style="padding:0px;">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="padding: 0px">
                <h1 id="cart_title" class="page-heading" style="font-weight: bold;margin-top:10px; margin-bottom: 0px; padding-bottom: 0px; border-bottom: none; ">
                    {l s='Shopping-cart summary'}
                </h1>
                <div class="border-title"></div>
            </div>
            {if !isset($empty) && !$PS_CATALOG_MODE}
                    <p class="heading-counter" style="letter-spacing: 1px;text-transform:uppercase; font-weight: bold; color: #000;">
                            <span id="summary_products_quantity">{$productNumber} {if $productNumber == 1}{l s='BONO'}{else}{l s='BONOS'}{/if}</span>
                    </p>
            {/if}   
            <div id="cart_summary" class="table {if $PS_STOCK_MANAGEMENT}stock-management-on{else}stock-management-off{/if}">
			<div>
				{assign var='odd' value=0}
				{assign var='have_non_virtual_products' value=false}
				{foreach $products as $product}
					{if $product.is_virtual == 0}
						{assign var='have_non_virtual_products' value=true}
					{/if}
					{assign var='productId' value=$product.id_product}
					{assign var='productAttributeId' value=$product.id_product_attribute}
					{assign var='quantityDisplayed' value=0}
					{assign var='odd' value=($odd+1)%2}
					{assign var='ignoreProductLast' value=isset($customizedDatas.$productId.$productAttributeId) || count($gift_products)}
					{* Display the product line *}
					{include file="$tpl_dir./shopping-cart-product-line.tpl" productLast=$product@last productFirst=$product@first}
					{* Then the customized datas ones*}
					{if isset($customizedDatas.$productId.$productAttributeId[$product.id_address_delivery])}
						{foreach $customizedDatas.$productId.$productAttributeId[$product.id_address_delivery] as $id_customization=>$customization}
							<div id="product_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}_{$product.id_address_delivery|intval}"
								class="product_customization_for_{$product.id_product}_{$product.id_product_attribute}_{$product.id_address_delivery|intval}{if $odd} odd{else} even{/if} customization alternate_item {if $product@last && $customization@last && !count($gift_products)}last_item{/if}">
								<div>
									{foreach $customization.datas as $type => $custom_data}
										{if $type == $CUSTOMIZE_FILE}
											<div class="customizationUploaded">
												<ul class="customizationUploaded">
													{foreach $custom_data as $picture}
														<li><img src="{$pic_dir}{$picture.value}_small" alt="" class="customizationUploaded" /></li>
													{/foreach}
												</ul>
											</div>
										{elseif $type == $CUSTOMIZE_TEXTFIELD}
											<ul class="typedText">
												{foreach $custom_data as $textField}
													<li>
														{if $textField.name}
															{$textField.name}
														{else}
															{l s='Text #'}{$textField@index+1}
														{/if}
														: {$textField.value}
													</li>
												{/foreach}
											</ul>
										{/if}
									{/foreach}
								</div>
                                                                <div class="cart_quantity">
									{if isset($cannotModify) AND $cannotModify == 1}
										<span>{if $quantityDisplayed == 0 AND isset($customizedDatas.$productId.$productAttributeId)}{$customizedDatas.$productId.$productAttributeId|@count}{else}{$product.cart_quantity-$quantityDisplayed}{/if}</span>
									{else}
										<input type="hidden" value="{$customization.quantity}" name="quantity_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}_{$product.id_address_delivery|intval}_hidden"/>
										<input type="text" value="{$customization.quantity}" class="cart_quantity_input form-control grey" name="quantity_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}_{$product.id_address_delivery|intval}"/>
										<div class="cart_quantity_button clearfix">
											{if $product.minimal_quantity < ($customization.quantity -$quantityDisplayed) OR $product.minimal_quantity <= 1}
												<a
													id="cart_quantity_down_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}_{$product.id_address_delivery|intval}"
													class="cart_quantity_down btn btn-default button-minus"
													href="{$link->getPageLink('cart', true, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_address_delivery={$product.id_address_delivery}&amp;id_customization={$id_customization}&amp;op=down&amp;token={$token_cart}")|escape:'html':'UTF-8'}"
													rel="nofollow"
													title="{l s='Subtract'}">
													<span><i class="icon-minus"></i></span>
												</a>
											{else}
												<a
													id="cart_quantity_down_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}"
													class="cart_quantity_down btn btn-default button-minus disabled"
													href="#"
													title="{l s='Subtract'}">
													<span><i class="icon-minus"></i></span>
												</a>
											{/if}
											<a
												id="cart_quantity_up_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}_{$product.id_address_delivery|intval}"
												class="cart_quantity_up btn btn-default button-plus"
												href="{$link->getPageLink('cart', true, NULL, "add=1&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_address_delivery={$product.id_address_delivery}&amp;id_customization={$id_customization}&amp;token={$token_cart}")|escape:'html':'UTF-8'}"
												rel="nofollow"
												title="{l s='Add'}">
												<span><i class="icon-plus"></i></span>
											</a>
										</div>
									{/if}
								</div>
								<div class="cart_delete text-center">
									{if isset($cannotModify) AND $cannotModify == 1}
									{else}
										<a
											id="{$product.id_product}_{$product.id_product_attribute}_{$id_customization}_{$product.id_address_delivery|intval}"
											class="cart_quantity_delete"
											href="{$link->getPageLink('cart', true, NULL, "delete=1&amp;id_product={$product.id_product|intval}&amp;ipa={$product.id_product_attribute|intval}&amp;id_customization={$id_customization}&amp;id_address_delivery={$product.id_address_delivery}&amp;token={$token_cart}")|escape:'html':'UTF-8'}"
											rel="nofollow"
											title="{l s='Delete'}">
											<i class="icon-trash"></i>
										</a>
									{/if}
								</div>
							</div>
							{assign var='quantityDisplayed' value=$quantityDisplayed+$customization.quantity}
						{/foreach}

						{* If it exists also some uncustomized products *}
						{if $product.quantity-$quantityDisplayed > 0}{include file="$tpl_dir./shopping-cart-product-line.tpl" productLast=$product@last productFirst=$product@first}{/if}
					{/if}
				{/foreach}
				{assign var='last_was_odd' value=$product@iteration%2}
				{foreach $gift_products as $product}
					{assign var='productId' value=$product.id_product}
					{assign var='productAttributeId' value=$product.id_product_attribute}
					{assign var='quantityDisplayed' value=0}
					{assign var='odd' value=($product@iteration+$last_was_odd)%2}
					{assign var='ignoreProductLast' value=isset($customizedDatas.$productId.$productAttributeId)}
					{assign var='cannotModify' value=1}
					{* Display the gift product line *}
					{include file="$tpl_dir./shopping-cart-product-line.tpl" productLast=$product@last productFirst=$product@first}
				{/foreach}
			</div>
		</div>
        <div class="cart_navigation_extra">
            <div id="HOOK_SHOPPING_CART_EXTRA">
                <div id="HOOK_SHOPPING_CART_EXTRA">{if isset($HOOK_SHOPPING_CART_EXTRA)}{$HOOK_SHOPPING_CART_EXTRA}{/if}</div>
            </div>
        </div>
	</div> <!-- end order-detail-content -->
        <div class="col-lg-4 col-md-5 col-sm-12 col-xs-12 cart_total_summary" id="cart_total_summary">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 title-summary">
                <h1 id="cart_title" class="page-heading" style="font-weight: bold;margin-top:10px; margin-bottom: 0px; padding-bottom: 0px; border-bottom: none; ">
                    {l s='Resumen'}
                </h1>
            <div class="border-title"></div>
            </div>
            <div class="row" style="text-align: right;">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text_left_padding title-queries-summary">{l s='Subtotal'}</div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 price text_left_padding" id="total_product">{displayPrice price=$total_products}</div>
            </div>
            <div class="row" style="text-align: right;">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text_left_padding title-queries-summary">{l s='Impuestos'}</div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 price text_left_padding" id="total_product">{displayPrice price=$total_tax}</div>
            </div>
                {if sizeof($discounts)}
                    <div class="row" style="padding:0px;">
                            {foreach $discounts as $discount}
                            {if ((float)$discount.value_real == 0 && $discount.free_shipping != 1) || ((float)$discount.value_real == 0 && $discount.code == '')}
                                    {continue}
                            {/if}
                                    <div class="row cart_discount {if $discount@last}last_item{elseif $discount@first}first_item{else}item{/if}" id="cart_discount_{$discount.id_discount}">
                                        {if $discount.description == 'Recompensa Fluz'}
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 cart_discount_name text_left_padding">{l s="Descuento mis Fluz"}</div>
                                        {else}
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 cart_discount_name text_left_padding">{l s="Descuento Codigo Fluz"}</div>
                                        {/if}
                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 cart_discount_price text_left_padding">
                                                    <span class="price-discount price">{if !$priceDisplay}{displayPrice price=$discount.value_real*-1}{else}{displayPrice price=$discount.value_tax_exc*-1}{/if}</span>
                                            </div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-3 price_discount_del text-center" style="padding:0px;">
                                                    {if strlen($discount.code)}
                                                            <a href="{if $opc}{$link->getPageLink('order-opc', true)}{else}{$link->getPageLink('order', true)}{/if}?deleteDiscount={$discount.id_discount}"
                                                                    class="price_discount_delete"
                                                                    title="{l s='Delete'}" style="font-size: 14px;color:#EF4136;font-family: 'Open-Sans';">
                                                                    Eliminar
                                                                    <!--<i class="icon-trash"></i>-->
                                                            </a>
                                                    {/if}
                                            </div>
                                    </div>
                            {/foreach}
                    </div>
                {/if}
                <div class="row cart_total_price">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 total_price_container text_left_padding title-queries-summary">
                                <span>{l s='Total'}</span>
                                <div class="hookDisplayProductPriceBlock-price">
                                    {hook h="displayCartTotalPriceLabel"}
                                </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text_left_padding">       
                            {if $use_taxes}
                                    <div colspan="5" class="price price_summary" id="total_price_container">
                                            <span id="total_price">{displayPrice price=$total_price}</span>
                                    </div>
                            {else}
                                    <div colspan="5" class="price price_summary" id="total_price_container">
                                            <span id="total_price">{displayPrice price=$total_price_without_tax}</span>
                                    </div>
                            {/if}
                        </div>
                </div>
                <div class="row price_in_fluz">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text_left_padding title-queries-summary">{l s='Precio Total en Fluz'}</div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 price text_left_padding" id="total_product_fluz">{$total_products/25|string_format:"%d"}</div>
                </div> 
                <div class="row fluz_receive">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text_receive_fluz text_left_padding title-queries-summary">{l s='Fluz Total a Obtener'}</div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 price price_fluz text_left_padding" id="total_fluz_earned">{$total_fluz}</div>
                </div>
                <div class="row">
                    
                            {if !$opc}
                                <a style="width:100%; text-align: center;padding: 15px 15px;margin-top: 20px;"  href="{if $back}{$link->getPageLink('order', true, NULL, 'step=1&amp;back={$back}')|escape:'html':'UTF-8'}{else}{$link->getPageLink('order', true, NULL, 'step=1')|escape:'html':'UTF-8'}{/if}" class="button btn btn-default standard-checkout button-medium" id="nextStep" title="{l s='Next Step'}">
                                            <span>{l s='NEXT STEP'}<i class="icon-chevron-right right"></i></span>
                                </a>   
                            {/if}
                            {if $total_price == 0}
                                    <a style="width:100%; text-align: center;padding: 15px 15px;margin-top: 20px;" href="{if $back}{$link->getPageLink('order', true, NULL, 'step=1&amp;back={$back}')|escape:'html':'UTF-8'}{else}{$link->getPageLink('order', true, NULL, 'step=1')|escape:'html':'UTF-8'}{/if}" class="button btn btn-default standard-checkout button-medium" title="{l s='Next Step'}">
                                            <span>{l s='Finalizar Compra'}<i class="icon-chevron-right right"></i></span>
                                    </a>
                                    {literal}
                                    <style>
                                        #nextStep{display: none !important;}
                                    </style>
                                    {/literal}
                            {/if}

                            <!--<a href="{if (isset($smarty.server.HTTP_REFERER) && ($smarty.server.HTTP_REFERER == $link->getPageLink('order', true) || $smarty.server.HTTP_REFERER == $link->getPageLink('order-opc', true) || strstr($smarty.server.HTTP_REFERER, 'step='))) || !isset($smarty.server.HTTP_REFERER)}{$link->getPageLink('index')}{else}{$smarty.server.HTTP_REFERER|escape:'html':'UTF-8'|secureReferrer}{/if}" class="button-exclusive btn btn-default" title="{l s='Continue shopping'}">
                                    <i class="icon-chevron-left"></i>{l s='Continue shopping'}
                            </a>-->
                            <p style="text-align:center;margin-top: 0px; color: #008000;"><i class="icon-lock"></i> Tu transacci&oacute;n es segura.</p>
                </div>    
                <div class="form-need-help">
                    <h4 class="title-help">Necesitas Ayuda?</h4>
                    <div class="p-help">
                        <p class="parragraph-help"><a href="http://reglas.fluzfluz.co" target="_blank"> - Qu&eacute; m&eacute;todos de pago puedo utilizar? </a></p>
                        <p class="parragraph-help"><a href="http://reglas.fluzfluz.co" target="_blank"> - Est&aacute; mi pedido seguro? </a></p>
                        <p class="parragraph-help"><a href="http://reglas.fluzfluz.co" target="_blank"> - C&oacute;mo se aplican mis recompensas? </a></p>
                    </div>
                </div>            
        </div>
	{if $show_option_allow_separate_package}
	<p>
		<label for="allow_seperated_package" class="checkbox inline">
			<input type="checkbox" name="allow_seperated_package" id="allow_seperated_package" {if $cart->allow_seperated_package}checked="checked"{/if} autocomplete="off"/>
			{l s='Send available products first'}
		</label>
	</p>
	{/if}

	{* Define the style if it doesn't exist in the PrestaShop version*}
	{* Will be deleted for 1.5 version and more *}
	{if !isset($addresses_style)}
		{$addresses_style.company = 'address_company'}
		{$addresses_style.vat_number = 'address_company'}
		{$addresses_style.firstname = 'address_name'}
		{$addresses_style.lastname = 'address_name'}
		{$addresses_style.address1 = 'address_address1'}
		{$addresses_style.address2 = 'address_address2'}
		{$addresses_style.city = 'address_city'}
		{$addresses_style.country = 'address_country'}
		{$addresses_style.phone = 'address_phone'}
		{$addresses_style.phone_mobile = 'address_phone_mobile'}
		{$addresses_style.alias = 'address_title'}
	{/if}
	{if !$advanced_payment_api && ((!empty($delivery_option) && (!isset($isVirtualCart) || !$isVirtualCart)) OR $delivery->id || $invoice->id) && !$opc}
		<div class="order_delivery clearfix row">
			{if !isset($formattedAddresses) || (count($formattedAddresses.invoice) == 0 && count($formattedAddresses.delivery) == 0) || (count($formattedAddresses.invoice.formated) == 0 && count($formattedAddresses.delivery.formated) == 0)}
				{if $delivery->id}
					<div class="col-xs-12 col-sm-6"{if !$have_non_virtual_products} style="display: none;"{/if}>
						<ul id="delivery_address" class="address item box">
							<li><h3 class="page-subheading">{l s='Delivery address'}&nbsp;<span class="address_alias">({$delivery->alias})</span></h3></li>
							{if $delivery->company}<li class="address_company">{$delivery->company|escape:'html':'UTF-8'}</li>{/if}
							<li class="address_name">{$delivery->firstname|escape:'html':'UTF-8'} {$delivery->lastname|escape:'html':'UTF-8'}</li>
							<li class="address_address1">{$delivery->address1|escape:'html':'UTF-8'}</li>
							{if $delivery->address2}<li class="address_address2">{$delivery->address2|escape:'html':'UTF-8'}</li>{/if}
							<li class="address_city">{$delivery->postcode|escape:'html':'UTF-8'} {$delivery->city|escape:'html':'UTF-8'}</li>
							<li class="address_country">{$delivery->country|escape:'html':'UTF-8'} {if $delivery_state}({$delivery_state|escape:'html':'UTF-8'}){/if}</li>
						</ul>
					</div>
				{/if}
				{if $invoice->id}
					<div class="col-xs-12 col-sm-6">
						<ul id="invoice_address" class="address alternate_item box">
							<li><h3 class="page-subheading">{l s='Invoice address'}&nbsp;<span class="address_alias">({$invoice->alias})</span></h3></li>
							{if $invoice->company}<li class="address_company">{$invoice->company|escape:'html':'UTF-8'}</li>{/if}
							<li class="address_name">{$invoice->firstname|escape:'html':'UTF-8'} {$invoice->lastname|escape:'html':'UTF-8'}</li>
							<li class="address_address1">{$invoice->address1|escape:'html':'UTF-8'}</li>
							{if $invoice->address2}<li class="address_address2">{$invoice->address2|escape:'html':'UTF-8'}</li>{/if}
							<li class="address_city">{$invoice->postcode|escape:'html':'UTF-8'} {$invoice->city|escape:'html':'UTF-8'}</li>
							<li class="address_country">{$invoice->country|escape:'html':'UTF-8'} {if $invoice_state}({$invoice_state|escape:'html':'UTF-8'}){/if}</li>
						</ul>
					</div>
				{/if}
			{else}
				{foreach from=$formattedAddresses key=k item=address}
					<div class="col-xs-12 col-sm-6"{if $k == 'delivery' && !$have_non_virtual_products} style="display: none;"{/if}>
						<ul class="address {if $address@last}last_item{elseif $address@first}first_item{/if} {if $address@index % 2}alternate_item{else}item{/if} box boxCheck">
							<li>
								<h3 class="page-subheading">
									{if $k eq 'invoice'}
										{l s='Invoice address'}
									{elseif $k eq 'delivery' && $delivery->id}
										{l s='Delivery address'}
									{/if}
									{if isset($address.object.alias)}
										<span class="address_alias">({$address.object.alias})</span>
									{/if}
								</h3>
							</li>
							{foreach $address.ordered as $pattern}
								{assign var=addressKey value=" "|explode:$pattern}
								{assign var=addedli value=false}
								{foreach from=$addressKey item=key name=foo}
								{$key_str = $key|regex_replace:AddressFormat::_CLEANING_REGEX_:""}
									{if isset($address.formated[$key_str]) && !empty($address.formated[$key_str])}
										{if (!$addedli)}
											{$addedli = true}
											<li><span class="{if isset($addresses_style[$key_str])}{$addresses_style[$key_str]}{/if}">
										{/if}
										{$address.formated[$key_str]|escape:'html':'UTF-8'}
									{/if}
									{if ($smarty.foreach.foo.last && $addedli)}
										</span></li>
									{/if}
								{/foreach}
							{/foreach}
						</ul>
					</div>
				{/foreach}
			{/if}
		</div>
	{/if}
        <br>
	<!--<p class="cart_navigation clearfix">
		{if !$opc}
                    <a  href="{if $back}{$link->getPageLink('order', true, NULL, 'step=1&amp;back={$back}')|escape:'html':'UTF-8'}{else}{$link->getPageLink('order', true, NULL, 'step=1')|escape:'html':'UTF-8'}{/if}" class="button btn btn-default standard-checkout button-medium" id="nextStep" title="{l s='Next Step'}">
				<span>{l s='NEXT STEP'}<i class="icon-chevron-right right"></i></span>
			</a>   
		{/if}
                {if $total_price == 0}
                        <a  href="{if $back}{$link->getPageLink('order', true, NULL, 'step=1&amp;back={$back}')|escape:'html':'UTF-8'}{else}{$link->getPageLink('order', true, NULL, 'step=1')|escape:'html':'UTF-8'}{/if}" class="button btn btn-default standard-checkout button-medium" title="{l s='Next Step'}">
				<span>{l s='Finalizar Compra'}<i class="icon-chevron-right right"></i></span>
			</a>
                        {literal}
                        <style>
                            #nextStep{display: none !important;}
                        </style>
                        {/literal}
                {/if}
                
		<!--<a href="{if (isset($smarty.server.HTTP_REFERER) && ($smarty.server.HTTP_REFERER == $link->getPageLink('order', true) || $smarty.server.HTTP_REFERER == $link->getPageLink('order-opc', true) || strstr($smarty.server.HTTP_REFERER, 'step='))) || !isset($smarty.server.HTTP_REFERER)}{$link->getPageLink('index')}{else}{$smarty.server.HTTP_REFERER|escape:'html':'UTF-8'|secureReferrer}{/if}" class="button-exclusive btn btn-default" title="{l s='Continue shopping'}">
			<i class="icon-chevron-left"></i>{l s='Continue shopping'}
		</a>
	</p>-->
	<div class="clear"></div>
                
{strip}
{addJsDef deliveryAddress=$cart->id_address_delivery|intval}
{addJsDefL name=txtProduct}{l s='product' js=1}{/addJsDefL}
{addJsDefL name=txtProducts}{l s='products' js=1}{/addJsDefL}
{/strip}
{/if}    
{literal}
    <style>
        /*Form Wizard*/
        .bs-wizard {border-bottom: solid 1px #e0e0e0; padding: 0 0 0px 0;}
        .bs-wizard > .bs-wizard-step {padding: 0; position: relative;}
        .bs-wizard > .bs-wizard-step + .bs-wizard-step {}
        .bs-wizard > .bs-wizard-step .bs-wizard-stepnum {color: #595959; font-size: 16px; margin-bottom: 5px;}
        .bs-wizard > .bs-wizard-step .bs-wizard-info {color: #fff; font-weight: bold; font-size: 12px; line-height: 0px;}
        .bs-wizard > .bs-wizard-step > .bs-wizard-dot {position: absolute; width: 16px; height: 16px; display: block; background: #fff; top: 30px; left: 50%; margin-top: -15px; margin-left: -10px; border-radius: 50%;} 
        .bs-wizard > .bs-wizard-step > .bs-wizard-dot:after {content: ' '; width: 12px; height: 12px; background: #C9B197; border-radius: 50px; position: absolute; top: 2px; left: 2px; } 
        .bs-wizard > .bs-wizard-step > .bs-wizard-dot-second {position: absolute; width: 16px; height: 16px; display: block; background: #fff; top: 30px; left: 50%; margin-top: -15px; margin-left: -10px; border-radius: 50%;} 
        .bs-wizard > .bs-wizard-step > .bs-wizard-dot-second:after {content: ' '; width: 12px; height: 12px; background: #fff; border-radius: 50px; position: absolute; top: 2px; left: 2px; } 
        .bs-wizard > .bs-wizard-step > .bs-wizard-dot-third {position: absolute; width: 16px; height: 16px; display: block; background: #fff; top: 30px; left: 50%; margin-top: -15px; margin-left: -10px; border-radius: 50%;} 
        .bs-wizard > .bs-wizard-step > .bs-wizard-dot-third:after {content: ' '; width: 12px; height: 12px; background: #fff; border-radius: 50px; position: absolute; top: 2px; left: 2px; } 
        .bs-wizard > .bs-wizard-step > .bs-wizard-dot-first:after {content: ' '; width: 14px; height: 14px; background: #fff; border-radius: 50px; position: absolute; top: 3px; left: 3px; } 
        .bs-wizard > .bs-wizard-step > .progress {position: relative; border-radius: 0px; height: 2px; box-shadow: none; margin: 22px 0;}
        .bs-wizard > .bs-wizard-step > .progress > .progress-bar {width:0px; box-shadow: none; background: #C9B197;}
        .bs-wizard > .bs-wizard-step.complete > .progress > .progress-bar {width:100%;}
        .bs-wizard > .bs-wizard-step.active > .progress > .progress-bar {width:50%;}
        .bs-wizard > .bs-wizard-step:first-child.active > .progress > .progress-bar {width:0%;}
        .bs-wizard > .bs-wizard-step:last-child.active > .progress > .progress-bar {width: 100%;}
        .bs-wizard > .bs-wizard-step.disabled > .bs-wizard-dot {background-color: #f5f5f5;}
        .bs-wizard > .bs-wizard-step.disabled > .bs-wizard-dot:after {opacity: 0;}
        .bs-wizard > .bs-wizard-step:first-child  > .progress {left: 50%; width: 100%;}
        .bs-wizard > .bs-wizard-step:last-child  > .progress {width: 50%;}
        .bs-wizard > .bs-wizard-step.disabled a.bs-wizard-dot{ pointer-events: none; }
        .menu-pay-disabled{display: none;}
        .footer-container{display: none;}
        .breadcrumb{display: none;}
    </style>
{/literal}
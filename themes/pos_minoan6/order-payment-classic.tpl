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
<!-- MODULE allinone_rewards -->

<div class="paiement_block">
    
    <div id="HOOK_TOP_PAYMENT">{$HOOK_TOP_PAYMENT}</div>
    {if $HOOK_PAYMENT}
        {if !$opc}
            <!--<div id="order-detail-content" class="table_block table-responsive">
                <table id="cart_summary" class="table table-bordered">
                    <!--<thead>
                        <tr>
                            <th class="cart_product first_item">{l s='Product'}</th>
                            <th class="cart_description item">{l s='Description'}</th>
                            {if $PS_STOCK_MANAGEMENT}
                                <th class="cart_availability item text-center">{l s='Availability'}</th>
                            {/if}
                            <th class="cart_unit item text-right" colspan="1">{l s='Fluz a Obtener'}</th>
                            <th class="cart_unit item text-right">{l s='Unit price'}</th>
                            <th class="cart_quantity item text-center">{l s='Qty'}</th>
                            <th class="cart_total last_item text-right">{l s='Total'}</th>
                        </tr>
                    </thead>
                    <tfoot>
                    {if $use_taxes}
                        {if $priceDisplay}
                            <tr class="cart_total_price">
                                <td colspan="4" class="text-right">{if $display_tax_label}{l s='Total products (tax excl.)'}{else}{l s='Total products'}{/if}</td>
                                <td colspan="3" class="price" id="total_product">{displayPrice price=$total_products}</td>
                            </tr>
                        {else}
                            <tr class="cart_total_price">
                                <td colspan="4" class="text-right">{if $display_tax_label}{l s='Total products (tax incl.)'}{else}{l s='Total products'}{/if}</td>
                                <td colspan="3" class="price" id="total_product">{displayPrice price=$total_products_wt}</td>
                            </tr>
                        {/if}
                    {else}
                        <tr class="cart_total_price">
                            <td colspan="4" class="text-right">{l s='Total products'}</td>
                            <td colspan="3" class="price" id="total_product">{displayPrice price=$total_products}</td>
                        </tr>
                    {/if}
                    <tr class="alternate_item" colspan="4">
                        <td  class="history_method bold pto-disponible" style="text-align:center; color: #ef4136; font-weight: bold;">
                            <input type="hidden" value="{$totalAvailable}" id="ptosTotalOculto"/>
                            <p class="pto-totaltext">{l s='Fluz Totales'}</p><p style="font-size:20px; margin-top: 0px;margin-bottom: 0px;" id="ptosTotal">{$totalAvailable}</p>
                        </td>
                        
                        <td colspan="2"> 
                            <input type="hidden" id="cavail_all" value="{$totalAvailableCurrency}" />
                            <input type="hidden" id="avail_all" value="{$totalAvailable}" />
                           {if $voucherAllowed}
                                    <div id="cart_voucher" class="table_block">
                                        {if $voucherAllowed}
                                            
                                            <form action="{if $opc}{$link->getPageLink('order-opc', true)}{/if}" method="post" id="voucher" name="voucher">
                                                <fieldset>
                                                    <input type="text" id="discount_name" class="form-control" style="display:none;" name="discount_name" value="{if isset($discount_name) && $discount_name}{$discount_name}{/if}"/>
                                                    <input type="hidden" name="submitDiscount" />
                                                    {if count($discounts)}
                                                        <p style="width:100%; font-size: 12px;" class="text-allpoint"> {l s='Usar todos los Fluz necesarios para cubrir el costo de la compra: '} &nbsp;&nbsp;<button type="button" name="submitAddDiscount" id="submitAddDiscount" class="btn-cart" disabled><span>{l s='Apply'}</span></button></p>
                                                    {else}
                                                    <p style="width:100%; font-size: 12px;" class="text-allpoint"> {l s='Usar todos los Fluz necesarios para cubrir el costo de la compra: '} &nbsp;&nbsp;<button type="button" name="submitAddDiscount" id="submitAddDiscount" class="btn-cart"><span>{l s='Apply'}</span></button></p>
                                                    {/if}    
                                                    {*if $displayVouchers}
                                                            <div id="display_cart_vouchers">
                                                                {foreach from=$displayVouchers item=voucher}
                                                                    <span onclick="$('#discount_name').val('{$voucher.name}');return false;" class="voucher_name">{$voucher.name}</span> - {$voucher.description} <br />
                                                                {/foreach}
                                                            </div>
                                                    {/if*}
                                                </fieldset>
                                            </form>
                                        {/if}
                                    </div>
                        </td>
                            {/if}
                         
                        <td style="font-size: 10px;" colspan="4"> 
                            <input type="hidden" id="cavail" value="{$totalAvailableCurrency}" />
                            <input type="hidden" id="avail" value="{$totalAvailable}" />
                            {if $voucherAllowed}
                                    <div id="cart_voucher" class="table_block">
                                        {if $voucherAllowed}
                                            
                                            <form action="{if $opc}{$link->getPageLink('order-opc', true)}{/if}" method="post" id="voucher" name="voucher">
                                                <fieldset>
                                                    <input type="text" id="discount_name" class="form-control" style="display:none;" name="discount_name" value="{if isset($discount_name) && $discount_name}{$discount_name}{/if}"/>
                                                    <input type="hidden" name="submitDiscount" />
                                                      <div style="text-align: left; font-size: 12px; width: 100%;" class="item use-text">{l s='Usar un monto especifico de Fluz:'}
                                                            {if count($discounts)}
                                                            <input type="number" min="1" max="{$totalAvailable}" oninput="if(value>{$totalAvailable})value={$totalAvailable}" id="toUse" style="text-align:right;" autocomplete="off" disabled/>
                                                            <button type="button" name="submitLabel" id="submitLabel" class="btn" style="background:#ef4136; color:#FFF;" disabled><span>{l s='ok'}</span></button>
                                                            {else}
                                                            <input type="number" min="1" max="{$totalAvailable}" oninput="if(value>{$totalAvailable})value={$totalAvailable}" id="toUse" style="text-align:right;" autocomplete="off"/>
                                                            <button type="button" name="submitLabel" id="submitLabel" class="btn" style="background:#ef4136; color:#FFF;"><span>{l s='ok'}</span></button>
                                                            {/if}
                                                      </div> 
                                                </fieldset>
                                            </form>
                                        {/if}
                                    </div>
                        </td>
                            {/if}
                    </tr>
                            
                    <tr class="cart_total_voucher" style="display:none;"{*if $total_wrapping == 0}style="display:none"{/if*}>
                        <td colspan="4" class="text-right">
                            {if $use_taxes}
                                {if $priceDisplay}
                                    {if $display_tax_label}{l s='Total gift wrapping (tax excl.):'}{else}{l s='Total gift wrapping cost:'}{/if}
                                {else}
                                    {if $display_tax_label}{l s='Total gift wrapping (tax incl.)'}{else}{l s='Total gift wrapping cost:'}{/if}
                                {/if}
                            {else}
                                {l s='Total gift wrapping cost:'}
                            {/if}
                        </td>
                        <td colspan="2" class="price-discount price" id="total_wrapping">
                            {if $use_taxes}
                                {if $priceDisplay}
                                    {displayPrice price=$total_wrapping_tax_exc}
                                {else}
                                    {displayPrice price=$total_wrapping}
                                {/if}
                            {else}
                                {displayPrice price=$total_wrapping_tax_exc}
                            {/if}
                        </td>
                    </tr>
                    {if $total_shipping_tax_exc <= 0 && (!isset($isVirtualCart) || !$isVirtualCart) && $free_ship}
                        <tr class="cart_total_delivery">
                            <td colspan="4" class="text-right">{l s='Total shipping'}</td>
                            <td colspan="2" class="price" id="total_shipping">{l s='Free Shipping!'}</td>
                        </tr>
                    {else}
                        {if $use_taxes && $total_shipping_tax_exc != $total_shipping}
                            {if $priceDisplay}
                                <tr class="cart_total_delivery" {if $shippingCost <= 0} style="display:none"{/if}>
                                    <td colspan="4" class="text-right">{if $display_tax_label}{l s='Total shipping (tax excl.)'}{else}{l s='Total shipping'}{/if}</td>
                                    <td colspan="2" class="price" id="total_shipping">{displayPrice price=$shippingCostTaxExc}</td>
                                </tr>
                            {else}
                                <tr class="cart_total_delivery"{if $shippingCost <= 0} style="display:none"{/if}>
                                    <td colspan="4" class="text-right">{if $display_tax_label}{l s='Total shipping (tax incl.)'}{else}{l s='Total shipping'}{/if}</td>
                                    <td colspan="2" class="price" id="total_shipping" >{displayPrice price=$shippingCost}</td>
                                </tr>
                            {/if}
                        {else}
                            <tr class="cart_total_delivery"{if $shippingCost <= 0} style="display:none"{/if}>
                                <td colspan="4" class="text-right">{l s='Total shipping'}</td>
                                <td colspan="2" class="price" id="total_shipping" >{displayPrice price=$shippingCostTaxExc}</td>
                            </tr>
                        {/if}
                    {/if}
                    <tr class="cart_total_voucher" style="display:none;"{*if $total_discounts == 0}style="display:none"{/if*}>
                        <td colspan="4" class="text-right">
                            {if $use_taxes}
                                {if $priceDisplay}
                                    {if $display_tax_label && $show_taxes}{l s='Total vouchers (tax excl.)'}{else}{l s='Total vouchers'}{/if}
                                {else}
                                    {if $display_tax_label && $show_taxes}{l s='Total vouchers (tax incl.)'}{else}{l s='Total vouchers'}{/if}
                                {/if}
                            {else}
                                {l s='Total vouchers'}
                            {/if}
                        </td>
                        <td colspan="3" class="price-discount price" id="total_discount">
                            {if $use_taxes}
                                {if $priceDisplay}
                                    {displayPrice price=$total_discounts_tax_exc*-1}
                                {else}
                                    {displayPrice price=$total_discounts*-1}
                                {/if}
                            {else}
                                {displayPrice price=$total_discounts_tax_exc*-1}
                            {/if}
                        </td>
                    </tr>
                    {if $use_taxes}
                        {if $total_tax != 0 && $show_taxes}
                            {if $priceDisplay != 0}
                                <tr class="cart_total_price">
                                    <td colspan="4" class="text-right">{if $display_tax_label}{l s='Total (tax excl.)'}{else}{l s='Total'}{/if}</td>
                                    <td colspan="2" class="price" id="total_price_without_tax">{displayPrice price=$total_price_without_tax}</td>
                                </tr>
                            {/if}
                            <tr class="cart_total_tax">
                                <td colspan="4" class="text-right">{l s='Tax'}</td>
                                <td colspan="2" class="price" id="total_tax" >{displayPrice price=$total_tax}</td>
                            </tr>
                        {/if}
                        <tr class="cart_total_price">
                            <td colspan="4" class="total_price_container text-right"><span>{l s='Total'}</span></td>
                            <td colspan="3" class="price" id="total_price_container">
                                <span id="total_price" class="tprice" data-selenium-total-price="{$total_price}">{displayPrice price=$total_price}</span>
                            </td>
                        </tr>
                    {else}
                       <tr class="cart_total_price">
                            {if $voucherAllowed}
                                <td colspan="2" id="cart_voucher" class="cart_voucher">
                                    <div id="cart_voucher" class="table_block">
                                        {if $voucherAllowed}
                                            <form action="{if $opc}{$link->getPageLink('order-opc', true)}{else}{$link->getPageLink('order', true)}{/if}" method="post" id="voucher">
                                                <fieldset>
                                                    <h4>{l s='Vouchers'}</h4>
                                                    <input type="text" id="discount_name" class="form-control" name="discount_name" value="{if isset($discount_name) && $discount_name}{$discount_name}{/if}" />
                                                    <input type="hidden" name="submitDiscount" />
                                                    <!--<button type="submit" name="submitAddDiscount" id="submitAddDiscount" class="button btn btn-default button-small"><span>{l s='ok'}</span></button>-->
                                                    {if $displayVouchers}
                                                        <p id="title" class="title_offers">{l s='Take advantage of our offers:'}</p>
                                                        <div id="display_cart_vouchers">
                                                            {foreach from=$displayVouchers item=voucher}
                                                                <span onclick="$('#discount_name').val('{$voucher.name}');return false;" class="voucher_name">{$voucher.name}</span> - {$voucher.description} <br />
                                                            {/foreach}
                                                        </div>
                                                    {/if}
                                                </fieldset>
                                            </form>
                                        {/if}
                                    </div>
                                </td>
                            {/if}
                            <td colspan="{if !$voucherAllowed}4{else}2{/if}" class="text-right total_price_container">
                                <span>{l s='Total'}</span>
                            </td>
                            <td colspan="3" class="price total_price_container" id="total_price_container">
                                <span id="total_price" data-selenium-total-price="{$total_price_without_tax}">{displayPrice price=$total_price_without_tax}</span>
                            </td>
                        </tr>
                    {/if}
                    </tfoot>
                    <div id="errors_void" style='display:none;'></div>
                    <tbody>
                    {foreach from=$products item=product name=productLoop}
                        {assign var='productId' value=$product.id_product}
                        {assign var='productAttributeId' value=$product.id_product_attribute}
                        {assign var='quantityDisplayed' value=0}
                        {assign var='cannotModify' value=1}
                        {assign var='odd' value=$product@iteration%2}
                        {assign var='noDeleteButton' value=1}

                        {* Display the product line *}
                        
                        {*include file="$tpl_dir./shopping-cart-product-line.tpl"*}
                        {* Then the customized datas ones*}
                        {if isset($customizedDatas.$productId.$productAttributeId)}
                            {foreach from=$customizedDatas.$productId.$productAttributeId[$product.id_address_delivery] key='id_customization' item='customization'}
                                <tr id="product_{$product.id_product}_{$product.id_product_attribute}_{$id_customization}" class="alternate_item cart_item">
                                    <td colspan="4">
                                        {foreach from=$customization.datas key='type' item='datas'}
                                            {if $type == $CUSTOMIZE_FILE}
                                                <div class="customizationUploaded">
                                                    <ul class="customizationUploaded">
                                                        {foreach from=$datas item='picture'}
                                                            <li>
                                                                <img src="{$pic_dir}{$picture.value}_small" alt="" class="customizationUploaded" />
                                                            </li>
                                                        {/foreach}
                                                    </ul>
                                                </div>
                                            {elseif $type == $CUSTOMIZE_TEXTFIELD}
                                                <ul class="typedText">
                                                    {foreach from=$datas item='textField' name='typedText'}
                                                        <li>
                                                            {if $textField.name}
                                                                {l s='%s:' sprintf=$textField.name}
                                                            {else}
                                                                {l s='Text #%s:' sprintf=$smarty.foreach.typedText.index+1}
                                                            {/if}
                                                            {$textField.value}
                                                        </li>
                                                    {/foreach}
                                                </ul>
                                            {/if}
                                        {/foreach}
                                    </td>
                                    <td class="cart_quantity text-center">
                                        {$customization.quantity}
                                    </td>
                                    <td class="cart_total"></td>
                                </tr>
                                {assign var='quantityDisplayed' value=$quantityDisplayed+$customization.quantity}
                            {/foreach}
                            {* If it exists also some uncustomized products *}
                            {if $product.quantity-$quantityDisplayed > 0}{include file="$tpl_dir./shopping-cart-product-line.tpl"}{/if}
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
                        {include file="./shopping-cart-product-line.tpl" productLast=$product@last productFirst=$product@first}
                    {/foreach}
                    </tbody>

                    {if count($discounts)}
                        <tbody>
                        {foreach from=$discounts item=discount name=discountLoop}
                            {if (float)$discount.value_real == 0}
                                {continue}
                            {/if}
                            <tr class="cart_discount {if $smarty.foreach.discountLoop.last}last_item{elseif $smarty.foreach.discountLoop.first}first_item{else}item{/if}" id="cart_discount_{$discount.id_discount}">
                                <td class="cart_discount_name" colspan="{if $PS_STOCK_MANAGEMENT}4{else}2{/if}">{$discount.name}</td>
                                <td class="cart_discount_price">
                                                            <span class="price-discount">
                                                        {if $discount.value_real > 0}
                                                            {if !$priceDisplay}
                                                                {displayPrice price=$discount.value_real*-1}
                                                            {else}
                                                                {displayPrice price=$discount.value_tax_exc*-1}
                                                            {/if}
                                                        {/if}
													</span>
                                </td>
                                <td class="cart_discount_delete">1</td>
                                <td class="cart_discount_price">
							<span class="price-discount">
                                                        {if $discount.value_real > 0}
                                                            {if !$priceDisplay}
                                                                {displayPrice price=$discount.value_real*-1}
                                                            {else}
                                                                {displayPrice price=$discount.value_tax_exc*-1}
                                                            {/if}
                                                        {/if}
							</span>
                                </td>
                            </tr>
                        {/foreach}
                        </tbody>
                    {/if}
                </table>
            </div> --><!-- end order-detail-content -->
        {/if}
        <div class="row" style="padding:0px;">
            <div class="col-lg-11 col-sm-12 col-md-12 col-xs-12" style="padding:0px; text-align: center;">
                <img class="logo logo_cart" src="https://fluzfluz.co/img/fluzfluz-logo-1464806235.jpg" alt="FluzFluz">
            </div>
        </div>
        <div>
            <div class="container third-step">
                    <div class="row bs-wizard" style="border-bottom:0;">

                        <div class="col-xs-4 bs-wizard-step complete">
                          <div class="progress"><div class="progress-bar"></div></div>
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
        <div class="row" style="padding: 0px">
            <div class="col-lg-7 col-md-7 col-sm-12 col-xs-12" style="padding: 0px">
                <div class="col-lg-12" style="padding: 0px">
                    <h1 id="cart_title" class="page-heading" style="font-weight: bold;margin-top:0px; margin-bottom: 0px; padding-bottom: 0px; border-bottom: none; ">
                        {l s='Pago Seguro'}
                    </h1>
                    <div class="border-title"></div>
                    <div class="row" style="padding: 0px;">
                        <div class="panel panel-default" style="margin-top:5px;margin-bottom: 0px;">
                            <div class="panel-heading" style="background:#fff;" id="paid_total_fluz">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseTree">Pago Total con Fluz</a>
                                </h4>
                            </div>
                            <div id="collapseTree" class="panel-collapse collapse">
                                <div class="panel-body" style="background: #F9F9F8;">
                                    <div class="alternate_item">
                                        <div  class="row history_method bold pto-disponible" style="text-align:center;">
                                            <input type="hidden" value="{$totalAvailable}" id="ptosTotalOculto"/>
                                            <p class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pto-totaltext-left">{l s='Tus Fluz Totales:'}</p>
                                            <p class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pto-total-left">{$totalAvailable}</p>
                                        </div>
                                        <div class="row" style="text-align: right;">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text_left_padding hook_fluz">{l s='Precio Total:'}</div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 price text_left_padding text_p_fluz" id="total_product" style="text-align:right;">{displayPrice price=$total_products}</div>
                                        </div>
                                        <div class="row price_in_fluz_left">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text_left_padding price_total_fluz">{l s='Precio Total en Fluz:'}</div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 price text_left_padding text_price_total" id="total_product_fluz" style="text-align:right;">- {$total_products/25|string_format:"%d"} Fluz.</div>
                                        </div> 
                                        <div class="row fluz_left">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text_left_padding">{l s='Fluz Restantes:'}</div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 price text_left_padding" id="total_product_fluz" style="text-align:right;padding-right: 0px;">{if ($totalAvailable < ($total_products/25))}0{else}{($totalAvailable - ($total_products/25))|string_format:"%d"}{/if} Fluz.</div>
                                        </div>
                                        <td colspan="2"> 
                                            <input type="hidden" id="cavail_all" value="{$totalAvailableCurrency}" />
                                            <input type="hidden" id="avail_all" value="{$totalAvailable}" />
                                            <div class="cart_total_price" style="display:none;">
                                                <div colspan="3" class="price" id="total_price_container">
                                                    <input type="hidden" id="total_price" class="tprice" data-selenium-total-price="{$total_price}">{displayPrice price=$total_price}</span>
                                                </div>
                                            </div>
                                            {if $voucherAllowed}
                                                    <div id="cart_voucher" class="table_block">
                                                        {if $voucherAllowed}

                                                            <form action="{if $opc}{$link->getPageLink('order-opc', true)}{/if}" method="post" id="voucher" name="voucher">
                                                                <fieldset>
                                                                    <input type="text" id="discount_name" class="form-control" style="display:none;" name="discount_name" value="{if isset($discount_name) && $discount_name}{$discount_name}{/if}"/>
                                                                    <input type="hidden" name="submitDiscount" />
                                                                    {if count($discounts)}
                                                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 div-btn-cart">
                                                                            <button type="button" name="submitAddDiscount" id="submitAddDiscount" class="btn-cart" disabled><span>{l s='Apply'}</span></button></p>
                                                                        </div>
                                                                    {else}
                                                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 div-btn-cart">
                                                                            <button type="button" name="submitAddDiscount" id="submitAddDiscount" class="btn-cart"><span>{l s='Apply'}</span></button></p>
                                                                        </div>
                                                                    {/if}    
                                                                </fieldset>
                                                            </form>
                                                        {/if}
                                                    </div>
                                            {/if}
                                    </div>
                                </div>
                            </div>
                        </div>
                         <div class="panel panel-default" id="paid_partial_fluz" style="margin-top:5px;margin-bottom: 0px;">
                            <div class="panel-heading" style="background:#fff;">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapseFour">Pago Parcial con Fluz</a>
                                </h4>
                            </div>
                            <div id="collapseFour" class="panel-collapse collapse">
                                <div class="panel-body" style="background: #F9F9F8;">
                                    <div class="alternate_item">
                                        <div class="row">
                                            <input class="slider-cash col-lg-6 col-md-5 col-sm-5 col-xs-5" type="range" id="rangeSlider" value="40" min="40" max="{$totalAvailable}" step="100" data-rangeslider>
                                            <div class="info-cash col-lg-5 col-md-6 col-sm-6 col-xs-6">
                                                    <span class="money-cash col-lg-2 col-md-1 col-sm-2 col-xs-2">$</span>
                                                    <input class="output-cash col-lg-6 col-md-6 col-sm-6 col-xs-5" type="number" name="valorSlider" id="valorSlider" value="" style="padding:0px; padding-left: 10px;height: 35px;margin-top: -5px;"/>
                                                    <span class="col-lg-3 cash-point col-md-3 col-sm-3 col-xs-4" style="padding: 0px;padding-left: 36px;"> &nbsp;{l s="de"}&nbsp;{$totalAvailable}&nbsp;{l s="Pts."}</span>
                                            </div>
                                                    <span class="cashout-money col-lg-12 col-md-12 col-sm-12 col-xs-12" style="display:none;> {l s ="COP"}&nbsp;<span id="value-cash"></span></span>
                                                    <span class="cashout-money col-lg-12" style="display:none;"> {l s ="COP"}&nbsp;<span id="value-money">{(int)Configuration::get('REWARDS_VIRTUAL_VALUE_1')}</span></span>
                                        </div>
                                        <div  class="row history_method bold pto-disponible" style="text-align:center;">
                                            <input type="hidden" value="{$totalAvailable}" id="ptosTotalOculto"/>
                                            <p class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pto-totaltext-left">{l s='Tus Fluz Totales:'}</p>
                                            <p class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pto-total-left">{$totalAvailable}</p>
                                        </div>
                                        <div class="row" style="text-align: right;">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text_left_padding hook_fluz">{l s='Precio Total:'}</div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 price text_left_padding text_p_fluz" id="total_product" style="text-align:right;">{displayPrice price=$total_products}</div>
                                        </div>
                                        <div class="row" style="text-align: right;">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text_left_padding hook_fluz">{l s='Precio en Puntos:'}</div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 price text_left_padding text_p_fluz" id="total_product_fluz_p" style="text-align:right;">- {$total_products/25|string_format:"%d"} Fluz.</div>
                                        </div>
                                        <div class="row price_in_fluz_left">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text_left_padding price_total_fluz">{l s='Puntos Aplicados:'}</div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6  price text_left_padding text_price_total" id="total_product_fluz_partial_f" style="text-align:right;">- {$total_products/25|string_format:"%d"} Fluz.</div>
                                        </div> 
                                        <div class="row fluz_left">
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text_left_padding">{l s='Fluz Restantes:'}</div>
                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 price text_left_padding" id="total_product_fluz_partial" style="text-align:right;padding-right: 0px;"></div>
                                        </div>
                                        <td colspan="2"> 
                                            <input type="hidden" id="cavail_all" value="{$totalAvailableCurrency}" />
                                            <input type="hidden" id="avail_all" value="{$totalAvailable}" />
                                            <div class="cart_total_price" style="display:none;">
                                                <div colspan="3" class="price" id="total_price_container">
                                                    <input type="hidden" id="total_price" class="tprice" data-selenium-total-price="{$total_price}">{displayPrice price=$total_price}</span>
                                                </div>
                                            </div>
                                            <div style="font-size: 10px;"> 
                                            <input type="hidden" id="cavail" value="{$totalAvailableCurrency}" />
                                            <input type="hidden" id="avail" value="{$totalAvailable}" />
                                            {if $voucherAllowed}
                                                    <div id="cart_voucher" class="table_block">
                                                        {if $voucherAllowed}

                                                            <form action="{if $opc}{$link->getPageLink('order-opc', true)}{/if}" method="post" id="voucher" name="voucher">
                                                                <fieldset>
                                                                    <input type="text" id="discount_name" class="form-control" style="display:none;" name="discount_name" value="{if isset($discount_name) && $discount_name}{$discount_name}{/if}"/>
                                                                    <input type="hidden" name="submitDiscount" />
                                                                            {if count($discounts)}
                                                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 div-btn-cart">    
                                                                                <input type="hidden" min="1" max="{$totalAvailable}" oninput="if(value>{$totalAvailable})value={$totalAvailable}" id="toUse" style="text-align:right;" autocomplete="off" disabled/>
                                                                                <button type="button" name="submitLabel" id="submitLabel" class="btn-cart" disabled><span>{l s='Aplicar'}</span></button>
                                                                            </div>
                                                                            {else}
                                                                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 div-btn-cart">    
                                                                                <input type="hidden" min="1" max="{$totalAvailable}" oninput="if(value>{$totalAvailable})value={$totalAvailable}" id="toUse" style="text-align:right;" autocomplete="off"/>
                                                                                <button type="button" name="submitLabel" id="submitLabel" class="btn-cart"><span>{l s='Aplicar'}</span></button>
                                                                            </div>
                                                                            {/if}
                                                                </fieldset>
                                                            </form>
                                                        {/if}
                                                    </div>
                                            {/if}
                                    </div>       
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="HOOK_PAYMENT">
                            {$HOOK_PAYMENT}
                        </div>            
                    </div>
                </div>
            </div>
            <div class="col-lg-5 col-md-5 col-sm-12 col-xs-12 cart_total_summary" id="cart_total_summary">
            <div class="col-lg-12 col-ms-12 col-sm-12 col-xs-12 title-summary">
                <h1 id="cart_title" class="page-heading" style="font-weight: bold;margin-top:0px; margin-bottom: 0px; padding-bottom: 0px; border-bottom: none; ">
                    {l s='Resumen de Orden'}
                </h1>
            <div class="border-title"></div>
            <div class="row div_products" style="padding:0px;margin-bottom: 25px;" id="div_products">
                <div class="row r-summary">
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="padding: 0px; text-align: left;"> Comerciante </div>
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="padding: 0px; text-align: center;"> Valor </div>
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="padding: 0px; text-align: center;"> Cantidad </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="padding: 0px; text-align: right;"> Precio en Fluz </div>
                </div>
                
                {foreach from=$products item=product}
                {assign var="idprod" value=$product.reference}
                <div class="row r-content-summary" id="r-content-summary">    
                    <input type="hidden" value="{$productsPoints.$idprod}" id="pto_unit_fluz-{$product.id_product}" class="pto_unit_fluz-{$product.id_product}">
                    <input type="hidden" value="{$product.id_product}" id="id_product_p" class="id_product_p">
                    <input type="hidden" value="{$product.cart_quantity}" id="quantity_product_p" class="quantity_product_p">
                    <input type="hidden" class="validation_fluz" id="validation_fluz" name="validation_fluz" value="{$product.reference}">
                    
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 font-merchant" style="padding: 0px; text-align: left; padding-left: 5px; text-transform: uppercase;">{$product.manufacturer_name}</div>
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="padding: 0px; text-align: center;">{$product.price}</div>
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="padding: 0px; text-align: center;">{$product.quantity}</div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="padding: 0px; text-align: right; color: #ef4136; font-weight: bold;">{$product.price / 25}</div>
                </div>
                {/foreach}
            </div>
            <div class="row" style="text-align: right;">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text_left_padding">{l s='Subtotal'}</div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 price text_left_padding" id="total_product" style="text-align:right;">{displayPrice price=$total_products}</div>
            </div>
            <div class="row" style="text-align: right;">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text_left_padding">{l s='Impuestos'}</div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 price text_left_padding" id="total_product" style="text-align:right;">{displayPrice price=$total_tax}</div>
            </div>
                {if sizeof($discounts)}
                    <div class="row" style="padding:0px;">
                            {foreach $discounts as $discount}
                            {if ((float)$discount.value_real == 0 && $discount.free_shipping != 1) || ((float)$discount.value_real == 0 && $discount.code == '')}
                                    {continue}
                            {/if}
                                    <div class="row cart_discount {if $discount@last}last_item{elseif $discount@first}first_item{else}item{/if}" id="cart_discount_{$discount.id_discount}">
                                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 cart_discount_name text_left_padding">{l s="Descuento en Fluz"}</div>
                                            <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 cart_discount_price text_left_padding">
                                                    <span class="price-discount price">{if !$priceDisplay}{displayPrice price=$discount.value_real*-1}{else}{displayPrice price=$discount.value_tax_exc*-1}{/if}</span>
                                            </div>
                                            <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2 price_discount_del text-center" style="padding:0px;">
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
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 total_price_container text_left_padding">
                                <span>{l s='Total'}</span>
                                <div class="hookDisplayProductPriceBlock-price">
                                    {hook h="displayCartTotalPriceLabel"}
                                </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text_left_padding" style="text-align:right;">  
                            <input type='hidden' id="total_cart_bit" name="total_cart_bit" value="{$total_price}">
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
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text_left_padding">{l s='Precio Total en Fluz'}</div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 price text_left_padding" id="total_product_fluz" style="text-align:right;">{$total_products/25|string_format:"%d"}</div>
                </div> 
                <div class="row fluz_receive">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text_receive_fluz text_left_padding">{l s='Fluz Total a Obtener'}</div>
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 price price_fluz text_left_padding" id="total_fluz_earned" style="text-align:right;">{$total_fluz}</div>
                </div>
                <div class="row" style="display:none;">
                    
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
            </div>
            </div>                
        </div>
        <!--<div class="row bitpay-info"> BitPay solo est&aacute; disponible en compras superiores a $ 300.000 &oacute; $ 100 USD </div>-->
        <div id="prueba" style="display:none;">{$base_dir_ssl}</div>
        
        {*if $opc}
            <div id="opc_payment_methods-content">
        {/if*}
        <!--<div id="HOOK_PAYMENT">
            {*$HOOK_PAYMENT*}
        </div>
        {*if $opc}
            </div> <!-- end opc_payment_methods-content -->
        {/if*}
    {else}
        <p class="alert alert-warning">{l s='No payment modules have been installed.'}</p>
    {/if}
    <br/>
    {if !$opc}
    {*<p class="cart_navigation clearfix">
        <a  href="{if $back}{$link->getPageLink('order', true, NULL, 'step=2&amp;back={$back}')|escape:'html':'UTF-8'}{else}{$link->getPageLink('order', true, NULL, 'step=3')|escape:'html':'UTF-8'}{/if}" class="btnPayment button btn btn-default standard-checkout button-medium" title="{l s='Confirm Purchase'}">
				<span>{l s='Confirm Purchase'}</span>
        </a>
    </p>*}
    
    {else} 
</div> <!-- end opc_payment_methods -->
{/if}
</div> <!-- end HOOK_TOP_PAYMENT -->
{literal}
    <script>
        $(document).ready(function(){
            pointsCurrent();
            $('.div_products').children('.r-content-summary').each(function (){
                var reference = $('.validation_fluz').val();
                var r_st = reference.substring(0,5);
                var a = $('.r-content-summary').find(".validation_fluz").length;

                if(r_st === 'MFLUZ' && a < 2){
                    $('#paid_total_fluz').css('pointer-events', 'none');
                    $('#paid_total_fluz').css('opacity', '0.4');
                    $('#paid_partial_fluz').css('pointer-events', 'none');
                    $('#paid_partial_fluz').css('opacity', '0.4');
                }
            });
            
            var price_bitcoin = $('#total_cart_bit').val();
            if(price_bitcoin <= 300000){
                $('#panel_bitpay').css('pointer-events', 'none');
                $('#panel_bitpay').css('opacity', '0.4');
            }
        });
        
        function pointsCurrent(){
    
            var pts=0;
            var total_p=0;
            var quantity = 0;

            $('.r-content-summary').each(function(){

               var id_product = $(this).find(".id_product_p").val(); 
               quantity = $(this).find(".quantity_product_p").val();
               pts = $('.pto_unit_fluz-'+id_product).val();
               total_p += parseInt(pts*quantity);
               
            });
            $('#total_fluz_earned').html(total_p);
        }
    </script>
{/literal}
{literal}
    <script type="text/javascript">
        $(document).ready(function(){
            var pto_inicial_slider = document.getElementById( 'valorSlider' ).value=40 ;
            var value_money = $('#value-money').text();
            var total_p = $('#ptosTotalOculto').val();
            $('#total_product_fluz_partial_f').html(pto_inicial_slider);
            
            $('#rangeSlider').change(function() 
            {
              var value = $(this).val();
              var total_partial = (parseInt(total_p) - parseInt(value));
              $('#valorSlider').val($(this).val());
              $('#pt_parciales').val(value);
              var mult = (value * value_money); 
              $("#value-cash").html(mult);
              $("#value-confirmation").html(mult);
              var total = mult - 7000;
              $("#total-valor").html(total);
              $('#toUse').val(value);
              $('#total_product_fluz_partial_f').html(value);
              
              if(value > total_p){
                  $('#total_product_fluz_partial').html(0+' Fluz.');
              }
              else{
                  $('#total_product_fluz_partial').html(total_partial+' Fluz.');
              }
              
            });

        });
    </script>
    <script>
           $('#submitAddDiscount').click(function(){
               $(this).attr("disabled","disabled");
               var totalCart=$('.tprice').attr("data-selenium-total-price");
               var credits=$('#cavail_all').val();
               var points=$('#avail_all').val();
               var use = $('#toUse').val();
               var prueba = document.getElementById("prueba").innerHTML;
               
               $.ajax({
                    method:"GET",
                    url: ''+prueba+'module/allinone_rewards/rewards?transform-credits=true&ajax=true&credits='+credits+'&price='+totalCart+'&points='+points+'&use='+use,
                    success:function(response){
                      $('#discount_name').val(response);
                      $('input[name="submitDiscount"]').val(response);
                      $('#voucher').submit();
                    }
                  });  
           });
    </script>
{/literal}
{literal}
    <script>
         $('#submitLabel').click(function(){
              
               $(this).attr("disabled","disabled");
               var totalCart=$('.tprice').attr("data-selenium-total-price");
               var credits=$('#cavail').val();
               var points=$('#avail').val();
               var use = $('#toUse').val();
               var prueba = document.getElementById("prueba").innerHTML;
               console.log(use);
               if(use != ""){
                    $.ajax({
                         method:"GET",
                         url: ''+prueba+'module/allinone_rewards/rewards?transform-credits=true&ajax=true&credits='+credits+'&price='+totalCart+'&points='+points+'&use='+use,
                         success:function(response){
                           console.log('entra a respuesta');
                           $('#discount_name').val(response);
                           $('input[name="submitDiscount"]').val(response);
                           $('#voucher').submit();
                          }
                    });  
                }
                else{
                    $('#submitLabel').prop('disabled',false);
                    $('#errors_void').show();
                    $('#errors_void').html('Debes Ingresar un valor para aplicar un descuento en fluz.')
                    $('#order_step').css('margin-bottom','10px');
                }
            });
           
    </script>
{/literal}
{literal}
    <script>       
        $("#valorSlider").on("keyup",function(event){
            var valor1=$('#ptosTotalOculto').val();
            var valor2=$('#valorSlider').val();
            console.log(valor2);
            if(valor2>=0){
                var resultado = calcular(valor1,valor2);
                $('#ptosTotal').html(resultado);
                $('#toUse').val(valor2);
            }else{
                valor2*=-1;
                $('#valorSlider').val(valor2);
                var resultado = calcular(valor1,valor2);
                $('#ptosTotal').html(resultado);
                $('#toUse').val(valor2);
            }
                
        }).keydown(function( event ) {
              if ( event.which == 13) {
                event.preventDefault();
              }
            });
        function calcular(valor1,valor2)
        {   
            return (valor1-valor2);
        }
    </script>
{/literal}
{literal}
    <style>
        #errors_void{
            padding: 20px;
            background-color: #f3515c;
            border-color: #d4323d;
            color: #fff;
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px;
        }
        .bitpay-info{color:#000; padding: 5px; font-weight: bold;}
        
        @media (max-width: 425px){
            .bitpay-info{color:#000; padding: 0px; font-weight: bold; text-align: center;}
        }
        
    </style>
{/literal}
{literal}
    <style>
        /*Form Wizard*/
        .bs-wizard {border-bottom: solid 1px #e0e0e0; padding: 0 0 0px 0;}
        .bs-wizard > .bs-wizard-step {padding: 0; position: relative;}
        .bs-wizard > .bs-wizard-step + .bs-wizard-step {}
        .bs-wizard > .bs-wizard-step .bs-wizard-stepnum {color: #595959; font-size: 16px; margin-bottom: 5px;}
        .bs-wizard > .bs-wizard-step .bs-wizard-info {color: #fff; font-weight: bold; font-size: 12px; line-height: 0px;}
        .bs-wizard > .bs-wizard-step > .bs-wizard-dot {position: absolute; width: 16px; height: 16px; display: block; background: #C9B197; top: 30px; left: 50%; margin-top: -15px; margin-left: -10px; border-radius: 50%;} 
        .bs-wizard > .bs-wizard-step > .bs-wizard-dot:after {content: ' '; width: 12px; height: 12px; background: #C9B197; border-radius: 50px; position: absolute; top: 2px; left: 2px; } 
        .bs-wizard > .bs-wizard-step > .bs-wizard-dot-second {position: absolute; width: 16px; height: 16px; display: block; background: #C9B197; top: 30px; left: 50%; margin-top: -15px; margin-left: -10px; border-radius: 50%;} 
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

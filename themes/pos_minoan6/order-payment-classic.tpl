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
            <div id="order-detail-content" class="table_block table-responsive">
                <table id="cart_summary" class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="cart_product first_item">{l s='Product'}</th>
                            <th class="cart_description item">{l s='Description'}</th>
                            {if $PS_STOCK_MANAGEMENT}
                                <th class="cart_availability item text-center">{l s='Availability'}</th>
                            {/if}
                            <th class="cart_unit item text-right" colspan="1">{l s='Puntos a Obtener'}</th>
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
                        <td  class="history_method bold" style="text-align:center; color: #ef4136; width: 20%; font-weight: bold;">
                            <input type="hidden" value="{$totalAvailable}" id="ptosTotalOculto"/>
                            {l s='Puntos Totales'}<br/><p style="font-size:20px; margin-top: 0px;margin-bottom: 0px;" id="ptosTotal">{$totalAvailable}</p>
                        </td>
                        
                        <td colspan="3"> 
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
                                                        <p style="width:100%; font-size: 12px;"> {l s='use all points necessary to conver the cost of purchase:'} &nbsp;&nbsp;<button name="submitAddDiscount" id="submitAddDiscount" class="btn-cart" disabled><span>{l s='Apply'}</span></button></p>
                                                    {else}
                                                    <p style="width:100%; font-size: 12px;"> {l s='use all points necessary to conver the cost of purchase:'} &nbsp;&nbsp;<button name="submitAddDiscount" id="submitAddDiscount" class="btn-cart"><span>{l s='Apply'}</span></button></p>
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
                         
                        <td style="font-size: 10px;" colspan="3"> 
                            <input type="hidden" id="cavail" value="{$totalAvailableCurrency}" />
                            <input type="hidden" id="avail" value="{$totalAvailable}" />
                            {if $voucherAllowed}
                                    <div id="cart_voucher" class="table_block">
                                        {if $voucherAllowed}
                                            
                                            <form action="{if $opc}{$link->getPageLink('order-opc', true)}{/if}" method="post" id="voucher" name="voucher">
                                                <fieldset>
                                                    <input type="text" id="discount_name" class="form-control" style="display:none;" name="discount_name" value="{if isset($discount_name) && $discount_name}{$discount_name}{/if}"/>
                                                    <input type="hidden" name="submitDiscount" />
                                                      <div style="text-align: left; font-size: 12px; width: 100%;" class="item">{l s='Use specific amount of points:'}
                                                            {if count($discounts)}
                                                            <input type="number" min="1" max="{$totalAvailable}" oninput="if(value>{$totalAvailable})value={$totalAvailable}" id="toUse" style="text-align:right; width: 20%;" autocomplete="off" disabled/>
                                                            <button name="submitLabel" id="submitLabel" class="btn" style="background:#ef4136; color:#FFF;" disabled><span>{l s='ok'}</span></button>
                                                            {else}
                                                            <input type="number" min="1" max="{$totalAvailable}" oninput="if(value>{$totalAvailable})value={$totalAvailable}" id="toUse" style="text-align:right; width: 20%;" autocomplete="off"/>
                                                            <button name="submitLabel" id="submitLabel" class="btn" style="background:#ef4136; color:#FFF;"><span>{l s='ok'}</span></button>
                                                            {/if}
                                                      </div> 
                                                </fieldset>
                                            </form>
                                        {/if}
                                    </div>
                        </td>
                            {/if}
                    </tr>
                            
                    <tr class="cart_total_voucher" {if $total_wrapping == 0}style="display:none"{/if}>
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
                    <tr class="cart_total_voucher" {if $total_discounts == 0}style="display:none"{/if}>
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

                    <tbody>
                    {foreach from=$products item=product name=productLoop}
                        {assign var='productId' value=$product.id_product}
                        {assign var='productAttributeId' value=$product.id_product_attribute}
                        {assign var='quantityDisplayed' value=0}
                        {assign var='cannotModify' value=1}
                        {assign var='odd' value=$product@iteration%2}
                        {assign var='noDeleteButton' value=1}

                        {* Display the product line *}
                        {include file="$tpl_dir./shopping-cart-product-line.tpl"}

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
            </div> <!-- end order-detail-content -->
        {/if}
        
        {if $base_dir}
        <div id="prueba">{$base_dir}</div>
        {elseif $base_dir_ssl}
        <div id="prueba">{$base_dir_ssl}</div>    
        {/if}
        
        {if $opc}
            <div id="opc_payment_methods-content">
        {/if}
        <div id="HOOK_PAYMENT">
            {$HOOK_PAYMENT}
        </div>
        {if $opc}
            </div> <!-- end opc_payment_methods-content -->
        {/if}
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
           $('#submitAddDiscount').click(function(){
               
               var totalCart=$('.tprice').attr("data-selenium-total-price");
               var credits=$('#cavail').val();
               var points=$('#avail').val();
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
    
    <script>
         $('#submitLabel').click(function(){
               
               var totalCart=$('.tprice').attr("data-selenium-total-price");
               var credits=$('#cavail').val();
               var points=$('#avail').val();
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
        $("#toUse").on("keyup",function(event){
            var valor1=$('#ptosTotalOculto').val();
            var valor2=$('#toUse').val();
            if(valor2>=0){
                var resultado = calcular(valor1,valor2);
                $('#ptosTotal').html(resultado);
            }else{
                valor2*=-1;
                $('#toUse').val(valor2);
                var resultado = calcular(valor1,valor2);
                $('#ptosTotal').html(resultado);
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

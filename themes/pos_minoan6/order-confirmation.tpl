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

{capture name=path}{l s='Order confirmation'}{/capture}
{assign var='current_step' value='payment'}
{*include file="$tpl_dir./order-steps.tpl"*}
{include file="$tpl_dir./errors.tpl"}

{$HOOK_ORDER_CONFIRMATION}
{$HOOK_PAYMENT_RETURN}

<link rel="stylesheet" type="text/css" href="{$css_dir}/order-confirmation.css">

{if $pse}
    {assign var="date" value=$smarty.now|date_format:"%d/%m/%Y, %H:%M"}
    {assign var="status" value=$message_payu}
{else if $state_payment.message != ''}
    {assign var="date" value=$state_payment.fecha|date_format:"%d/%m/%Y, %H:%M"}
    {assign var="status" value=$state_payment.message}
{else}
    {assign var="status" value='APROBADO'}
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
                  <a href="#" class="bs-wizard-dot"></a>
                  <div class="bs-wizard-info text-center">Resumen Carrito</div>
                </div>
                
                <div class="col-xs-4 bs-wizard-step complete"><!-- complete -->
                  <div class="progress"><div class="progress-bar"></div></div>
                  <a href="#" class="bs-wizard-dot"></a>
                  <div class="bs-wizard-info text-center">Pago Seguro</div>
                </div>
                
                <div class="col-xs-4 bs-wizard-step active"><!-- complete -->
                  <div class="progress"><div class="progress-bar"></div></div>
                  <a href="#" class="bs-wizard-dot bs-wizard-dot-third"></a>
                  <div class="bs-wizard-info text-center">Confirmaci&oacute;n</div>
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
    <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12" style="padding: 0px">
        <div class="row title-summary">
            <h1 id="cart_title" class="page-heading" style="font-weight: bold;margin-top:0px; margin-bottom: 0px; padding-bottom: 0px; border-bottom: none; ">
                Confirmaci&oacute;n de Compra
            </h1>
            <div class="border-title"></div>
            {foreach $order_products as $product}
            <div class="row">
                <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8" style="padding: 0px;">
                    
                        <div class="row" style="padding:0px;">
                            <div class="col-sm-2 col-md-2 col-lg-4 col-xs-4 img-none-querie" style="padding:0px;width: 100px;">
                                {if $product.image->id_image == ''}
                                    <img style=" border-radius: 10px;" src="{$s3}m/m/{$product.id_manufacturer}.jpg" alt="{$product.name|escape:'html':'UTF-8'}" {if isset($mediumSize)}width="" height="{$mediumSize.height}" {/if} /></a>
                                {else}
                                    <img style=" border-radius: 10px;" class="img_product" src="{$link->getImageLink($product.id_product, $product.image->id_image, 'thickbox_default')|escape:'html':'UTF-8'}" alt="{$product.product_name|escape:'html':'UTF-8'}" title="{$product.product_name|escape:'html':'UTF-8'}" />
                                {/if}
                            </div>
                            <div class="col-xs-7 col-sm-7 col-md-5 col-lg-8 p-name-payment">
                                <p class="name_product">{$product.product_name}</p>
                            </div>
                        </div>
                    
                </div>
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 value-payment-container">
                    <div class="row" style="padding:0px;text-align: right;font-weight: bold;color:#000;">
                        Valor: {$product.price|string_format:"%d"}
                    </div>
                    <div class="row" style="padding:0px; margin-top: 20px;border-top:1px solid #d6d4d4; padding-top: 10px;">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ptos_cart">
                            Precio en fluz: {$product.price/25}&nbsp;{l s="Fluz"}
                        </div>
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ptos_cart">
                            Fluz a obtener: {$product.fluzpoints}&nbsp;{l s="Fluz"}
                        </div>
                        <input type="hidden" value="{$product.fluzpoints}" id="pto_unit_fluz" class="pto_unit_fluz">
                    </div>
                </div>
            </div>
            <div class="row" style="margin-bottom:30px;">
                <div class="row div-instruccion">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 title-instru">
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
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 title-instru">
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
            {/foreach}
        </div>    
    </div>
    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12 cart_total_summary" id="cart_total_summary" style="padding: 0px">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 title-summary title-payment-summary">
            <h1 id="cart_title" class="page-heading" style="font-weight: bold;margin-top:0px; margin-bottom: 0px; padding-bottom: 0px; border-bottom: none; ">
                {l s='Resumen de Orden'}
            </h1>
        <div class="border-title"></div>
        {if $order->current_state == 15}
            <div class="row information-pending">Hemos recibido tu solicitud de compra. Te informamos que tu transacci&oacute;n est&aacute; siendo procesada por nuestra pasarela de pagos y tu banco. La aprobaci&oacute;n o rechazo depende de tu entidad financiera y ser&aacute; recibida m&aacute;ximo en las 4 horas siguientes. Si tienes alguna inquietud adicional, por favor comun&iacute;cate con tu entidad financiera.</div>
        {/if}
        <div class="row" style="padding:0px;margin-bottom: 25px;">
            <div class="row r-summary">
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="padding: 0px; text-align: left;"> Comerciante </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="padding: 0px; text-align: center;"> Valor </div>
                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="padding: 0px; text-align: center;"> Cantidad </div>
                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="padding: 0px; text-align: right;"> Precio en Fluz </div>
            </div>

            {foreach from=$order_products item=product}
                <div class="row r-content-summary">    
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="padding: 0px; text-align: left; padding-left: 5px; text-transform: uppercase;">{$product.manufacturer_name}</div>
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="padding: 0px; text-align: center;">{$product.price|string_format:"%d"}</div>
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2" style="padding: 0px; text-align: center;">{$product.product_quantity}</div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="padding: 0px; text-align: right; color: #ef4136; font-weight: bold;">{$product.price / 25}</div>
                </div>
            {/foreach}
        </div>
        <div class="row" style="text-align: right;">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text_left_padding">{l s='Subtotal'}</div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 price text_left_padding" id="total_product" style="text-align:right;">{displayPrice price=$order->total_paid}</div>
        </div>
        <div class="row" style="text-align: right;">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text_left_padding">{l s='Impuestos'}</div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 price text_left_padding" id="total_product" style="text-align:right;">{displayPrice price=0}</div>
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
                        {if $use_taxes}
                                <div colspan="5" class="price price_summary" id="total_price_container">
                                    <span id="total_price" style="color:#000;font-size: 16px;">{displayPrice price=$order->total_paid}</span>
                                </div>
                        {else}
                                <div colspan="5" class="price price_summary" id="total_price_container">
                                        <span id="total_price" style="color:#000;font-size: 16px;">{displayPrice price=$order->total_paid}</span>
                                </div>
                        {/if}
                    </div>
            </div>
            <div class="row price_in_fluz">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text_left_padding">{l s='Precio Total en Fluz'}</div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 price text_left_padding" id="total_product_fluz" style="text-align:right;">{$order->total_paid/25|string_format:"%d"}</div>
            </div> 
            <div class="row fluz_receive">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 text_receive_fluz text_left_padding">{l s='Fluz Total a Obtener'}</div>
                {foreach $order_products as $product}
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 price price_fluz text_left_padding" id="total_fluz_earned" style="text-align:right;">{$product.fluzpoints_sum}</div>
                {/foreach}
            </div>
            <div class="row">
                    <a class="button btn btn-default standard-checkout button-medium" style="width:100%; text-align: center;padding: 15px 15px;margin-top: 20px;" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}" title="{l s='My account'}">{l s='My account'} 
                        <i class="icon-chevron-right"></i>
                    </a>
            </div>
            <div class="information" style=" line-height: 15px; margin-top: 10px;">Los c&oacute;digos ser&aacute;n enviados a tu correo electr&oacute;nico 
                             y tambi&eacute;n los encuentras en tu b&oacute;veda de c&oacute;digos</div>
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
                        <p style="text-align:center;margin-top: 0px; color: #BDBDBD;"><i class="icon-lock"></i> Tu transacci&oacute;n es segura.</p>
            </div>
        </div>
        </div>  
</div>                    
    <!--<h1 class="page-heading title">{l s='Confirmation'}</h1>
    <div class="transaction">
        <div class="row">
            <div>
                <div>
                    <div class="transaction-title col-xs-12 col-sm-12 col-md-12 col-lg-12">{l s='Transaction'}</div>
                </div>
            </div>
            <div>
                <div class="row border-trans">
                    <div class="title-right col-xs-6 col-sm-6 col-md-6 col-lg-6">{l s='Status'}</div>
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">{$status|utf8_encode}</div>
                </div>
                <div class="row border-trans">
                    <div class="title-right col-xs-6 col-sm-6 col-md-6 col-lg-6">{l s='Value'}</div>
                    {if $state_payment.valor != ''}
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">{convertPrice price=$state_payment.valor}</div>
                    {else}
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">{l s='Pedido Gratuito'}</div>
                    {/if}
                </div>
                <div class="row border-trans">
                    <div class="title-right col-xs-6 col-sm-6 col-md-6 col-lg-6">{l s='Date'}</div>
                    {if $date!=''}
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">{$date}</div>
                    {else}
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">{$date_free_order}</div>
                    {/if}    
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="information">Los c&oacute;digos ser&aacute;n enviados a tu correo electr&oacute;nico 
                             y tambi&eacute;n los encuentras en tu b&oacute;veda de c&oacute;digos</div>
    <br>
    <div class="products table row">
        <div class="row title-margin">
            <div class="col-sm-2 col-md-2 col-lg-2 padding-title img-none-querie">{l s='Aliado Fluz Fluz'}</div>
            <div class="col-xs-4 col-sm-5 col-md-5 col-lg-5 padding-title">{l s='Descripton'}</div>
            <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 padding-title">{l s='Precio Tienda'}</div>
            <div class="col-xs-3 col-sm-1 col-md-1 col-lg-1 padding-title-querie">{l s='Fluz Ganados'}</div>
            <div class="col-xs-3 col-sm-2 col-md-2 col-lg-2 padding-title left-title">{l s='Total'}</div>
        </div>
        {foreach $order_products as $product}
            <div class="row">
                <div class="col-sm-2 col-md-2 col-lg-2 img-none-querie">
                    {if $product.image->id_image == ''}
                        <img src="{$s3}m/m/{$product.id_manufacturer}.jpg" alt="{$product.name|escape:'html':'UTF-8'}" {if isset($mediumSize)}width="" height="{$mediumSize.height}" {/if} /></a>
                    {else}
                        <img class="img_product" src="{$link->getImageLink($product.id_product, $product.image->id_image, 'thickbox_default')|escape:'html':'UTF-8'}" alt="{$product.product_name|escape:'html':'UTF-8'}" title="{$product.product_name|escape:'html':'UTF-8'}" />
                    {/if}
                </div>
                <div class="col-xs-4 col-sm-5 col-md-5 col-lg-5">
                    <span class="name_product">{$product.product_name}</span><br>
                </div>
                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 padding-out">{$product.type_currency}&nbsp;${$product.price_shop|number_format:0:".":","}</div>
                <div class="fluz_style col-xs-3 col-sm-1 col-md-1 col-lg-1 padding-out">{$product.fluzpoints}</div>
                <div class="col-xs-3 col-sm-2 col-md-2 col-lg-2 left-title">{convertPrice price=$product.total_price_tax_incl}</div>
            </div>
        {/foreach}
        <!--<div class="row paid-row">
            <div class="col-lg-10"></div>
            <div class="col-lg-2 left-title">{convertPrice price=$order->total_paid}</div>
        </div>
        <div class="row">
            <div class="col-lg-10"></div>
            <div class="col-lg-2 save left-title">{l s='Total'}: {convertPrice price=$order->total_paid}</div>
        </div>
    </div>

    <p class="cart_navigation exclusive btnaccount">
        <a class="button-exclusive btn btn-default" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}" title="{l s='My account'}">{l s='My account'} <i class="icon-chevron-right"></i></a>
    </p>
</div>-->

{*if $is_guest}
    <p>{l s='Your order ID is:'} <span class="bold">{$id_order_formatted}</span> . {l s='Your order ID has been sent via email.'}</p>
    <p class="cart_navigation exclusive">
        <a class="button-exclusive btn btn-default" href="{$link->getPageLink('guest-tracking', true, NULL, "id_order={$reference_order|urlencode}&email={$email|urlencode}")|escape:'html':'UTF-8'}" title="{l s='Follow my order'}"><i class="icon-chevron-left"></i>{l s='Follow my order'}</a>
    </p>
{else}
    <p class="cart_navigation exclusive">
        <a class="button-exclusive btn btn-default" href="{$link->getPageLink('history', true)|escape:'html':'UTF-8'}" title="{l s='Go to your order history page'}"><i class="icon-chevron-left"></i>{l s='View your order history'}</a>
    </p>
{/if*}
{literal}
    <style>
        .fancybox-lock {overflow: auto !important;width: auto;}
        .fancybox-overlay-fixed{display: none !important;}
        .fancybox-skin{display:none;}
        .fluz_style{color: #ef4136;font-size: 25px !important;font-weight: bold;text-align: center;
                    padding: 0px !important;}
        .menu-pay-disabled{display: none;}
        .footer-container{display: none;}
        .breadcrumb{display: none;}
    </style>
{/literal}
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

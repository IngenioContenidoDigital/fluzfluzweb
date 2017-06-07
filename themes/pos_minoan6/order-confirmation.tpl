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
{else}
    {assign var="date" value=$state_payment.fecha|date_format:"%d/%m/%Y, %H:%M"}
    {assign var="status" value=$state_payment.message}
{/if}

<div>
    <h1 class="page-heading title">{l s='Confirmation'}</h1>
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
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">{convertPrice price=$state_payment.valor}</div>
                </div>
                <div class="row border-trans">
                    <div class="title-right col-xs-6 col-sm-6 col-md-6 col-lg-6">{l s='Date'}</div>
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">{$date}</div>
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
                    <!--<span class="number_product">{l s='Product'} #:</span><br>-->
                    <!--<span class="number_product">{$product.product_reference}</span>-->
                </div>
                <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 padding-out">{$product.type_currency}&nbsp;${$product.price_shop|number_format:0:".":","}</div>
                <div class="fluz_style col-xs-3 col-sm-1 col-md-1 col-lg-1 padding-out">{$product.fluzpoints}</div>
                <div class="col-xs-3 col-sm-2 col-md-2 col-lg-2 left-title">{convertPrice price=$product.total_price_tax_incl}</div>
            </div>
        {/foreach}
        <div class="row paid-row">
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
</div>

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
    </style>
{/literal}

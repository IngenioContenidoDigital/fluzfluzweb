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

<div>
    <h1 class="page-heading title">{l s='Confirmation'}</h1>
    
    <div class="transaction">
        <table class="table row">
            <thead>
                <tr>
                    <th colspan="2" class="col-xs-12 col-sm-12 col-md-12 col-lg-12">{l s='Transaction'}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="col-xs-6 col-sm-6 col-md-6 col-lg-6">{l s='Status'}</th>
                    <td class="col-xs-6 col-sm-6 col-md-6 col-lg-6">{$state_payment.message}</th>
                </tr>
                <tr class="table-warning">
                    <td>{l s='Reference'}</th>
                    <td>{$state_payment.orderIdPayu}</th>
                </tr>
                <tr>
                    <td>{l s='Value'}</th>
                    <td>{convertPrice price=$state_payment.valor}</th>
                </tr>
                <tr class="table-warning">
                    <td>{l s='Date'}</th>
                    <td>{$state_payment.fecha|date_format:"%d/%m/%Y, %H:%M"}</th>
                </tr>
            </tbody>
        </table>
    </div>
    <br>
    <table class="products table row">
        <tr class="head">
            <th class="col-xs-2 col-sm-2 col-md-2 col-lg-2">{l s='Product'}</th>
            <th class="col-xs-8 col-sm-8 col-md-8 col-lg-8">{l s='Descripton'}</th>
            <th class="col-xs-2 col-sm-2 col-md-3 col-lg-2">{l s='Total'}</th>
        </tr>
        {foreach $order_products as $product}
            <tr>
                <td><img class="img_product" src="{$link->getImageLink($product.product_name, $product.image->id_image, 'thickbox_default')|escape:'html':'UTF-8'}" alt="{$product.product_name|escape:'html':'UTF-8'}" title="{$product.product_name|escape:'html':'UTF-8'}" /></td>
                <td>
                    <span class="name_product">{$product.product_name}</span><br>
                    <span class="number_product">{l s='Product'} #:</span><br>
                    <span class="number_product">{$product.product_reference}</span>
                </td>
                <td>{convertPrice price=$product.total_price_tax_incl}</td>
            </tr>
        {/foreach}
        <tr>
            <td colspan="2" rowspan="2" class="empty"></td>
            <td class="empty">{convertPrice price=$order->total_paid}</td>
        </tr>
        <tr>
            <td class="save">{l s='Total'}: {convertPrice price=$order->total_paid}</td>
        </tr>
        {*<tr>
            <td colspan="2" rowspan="2" class="not_view"></td>
            <td class="card_value">{l s='Card Value'}: {convertPrice price=$order->total_paid}</td>
        </tr>
        <tr>
            <td class="save">{l s='Save'}: 2%</td>
        </tr>*}
    </table>

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

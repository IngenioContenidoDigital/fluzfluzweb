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
{capture name=path}
	<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
		{l s='My account'}
	</a>
	<span class="navigation-pipe">{$navigationPipe}</span>
	<span class="navigation_page">{l s='Order history'}</span>
{/capture}
{include file="$tpl_dir./errors.tpl"}
<h1 class="page-heading bottom-indent">{l s='Order history'}</h1>
<p class="info-title">{l s='Here are the orders you\'ve placed since your account was created.'}</p>
{if $slowValidation}
	<p class="alert alert-warning">{l s='If you have just placed an order, it may take a few minutes for it to be validated. Please refresh this page if your order is missing.'}</p>
{/if}
<div class="block-center" id="block-history">
	{if $orders && count($orders)}
		<table id="order-list" class="table table-bordered footab">
			
			<tbody>
				{foreach from=$products item=product name=myLoop}
                                    <thead>
                                        <tr>
                                                <!--<th class="first_item" data-sort-ignore="true">{l s='Order reference'}</th>-->
                                                <th class="first_item">{l s='Product'}</th>
                                                <th class="item">{l s='Description'}</th>
                                                <th class="item">{l s='Date'}</th>
                                                <th class="item" style="text-align:right; padding-right: 2%;">{l s='Unit Price'}</th>
                                                <th class="item" style="text-align:left; padding-right: 2%;">{l s='Qty'}</th>
                                                <!--<th data-sort-ignore="true" data-hide="phone,tablet" class="item">{l s='Payment'}</th>-->
                                                <!--<th class="item">{l s='Status'}</th>-->
                                                <!--<th data-sort-ignore="true" data-hide="phone,tablet" class="item">{l s='Invoice'}</th>-->
                                                <!--<th data-sort-ignore="true" data-hide="phone,tablet" class="last_item">&nbsp;</th>-->
                                                <th data-hide="phone" class="item" style="text-align:right; padding-right: 2%;">{l s='Total'}</th>
                                        </tr>
                                    </thead>
                                    	<tr class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if} {if $smarty.foreach.myLoop.index % 2}alternate_item{/if}">
						<!--<td class="history_link bold">
							{if isset($order.invoice) && $order.invoice && isset($order.virtual) && $order.virtual}
								<img class="icon" src="{$img_dir}icon/download_product.gif"	alt="{l s='Products to download'}" title="{l s='Products to download'}" />
							{/if}
							<a class="color-myaccount" href="javascript:showOrder(1, {$order.id_order|intval}, '{$link->getPageLink('order-detail', true)|escape:'html':'UTF-8'}');">
								{Order::getUniqReferenceOf($order.id_order)}
							</a>
						</td>-->
                                                        
                                                <td>
                                                    <div style="text-align:center;">
                                                        <span><img src="{$link->getImageLink($product.link_rewrite, $product.image, 'small_default')}"/></span>
                                                    </div>
                                                        
                                                </td>                
                                                <td class="history_invoice">
                                                    <span style="color:#231f20; font-size: 18px; font-family: 'Open Sans'; font-weight: bold; line-height: 35px;">{$product.purchase}</span>
                                                    <div>
                                                    <span>{l s="Product #: "}</span><br/>    
                                                    <span>{$product.referencia}</span>
                                                    </div>
						</td>
                                                <td data-value="{$order.date_add|regex_replace:"/[\-\:\ ]/":""}" class="history_date bold" style="width:15%;">
                                                    <span>{$product.time}</span>
						</td>
                                                <td class="history_price" data-value="{$product.precio}">
							<div style="display:block;">
                                                            <p>{l s="Value: "}{displayPrice price=$product.price_shop no_utf8=false convert=false}</p>
                                                            <p>{l s="You Save: "}%{math equation='round(((p - r) / p)*100)' p=$product.price_shop r=$product.precio}</p>
                                                            <p>{l s="Unit Price: "}{displayPrice price=$product.precio no_utf8=false convert=false}</p>
                                                        </div>
						</td>
                                                <td class="history_invoice" style="text-align:center !important; width: 13%; font-size: 22px;">
							{$product.cantidad}
						</td>
						
						<!--<td class="history_method">{$order.payment|escape:'html':'UTF-8'}</td>-->
						<!--<td{if isset($order.order_state)} data-value="{$order.id_order_state}"{/if} class="history_state">
							{if isset($order.order_state)}
								<span class="label{if isset($order.order_state_color) && Tools::getBrightness($order.order_state_color) > 128} dark{/if}"{if isset($order.order_state_color) && $order.order_state_color} style="background-color:{$order.order_state_color|escape:'html':'UTF-8'}; border-color:{$order.order_state_color|escape:'html':'UTF-8'};"{/if}>
									{$order.order_state|escape:'html':'UTF-8'}
								</span>
							{/if}
						</td>-->
						
						<!--<td class="history_detail">
							<a class="btn btn-default button button-small" href="javascript:showOrder(1, {$order.id_order|intval}, '{$link->getPageLink('order-detail', true)|escape:'html':'UTF-8'}');">
								<span>
									{l s='Details'}<i class="icon-chevron-right right"></i>
								</span>
							</a>
							{if isset($opc) && $opc}
								<a class="link-button" href="{$link->getPageLink('order-opc', true, NULL, "submitReorder&id_order={$order.id_order|intval}")|escape:'html':'UTF-8'}" title="{l s='Reorder'}">
							{else}
								<a class="link-button" href="{$link->getPageLink('order', true, NULL, "submitReorder&id_order={$order.id_order|intval}")|escape:'html':'UTF-8'}" title="{l s='Reorder'}">
							{/if}
								{if isset($reorderingAllowed) && $reorderingAllowed}
									<i class="icon-refresh"></i>{l s='Reorder'}
								{/if}
							</a>
						</td>-->
                                                                <td class="history_price" data-value="{$product.total}" style="text-align:right;">
							<p class="price">{l s="Price: "}{displayPrice price=$product.total no_utf8=false convert=false}</p>
                                                        <p style="color:#231f20; font-weight: bold;">{l s="Card Value: "}{displayPrice price=$product.price_shop no_utf8=false convert=false}</p>
						</td>
                                                 
					</tr>
                                        <tr>
                                            
                                            <td class="history_point">
                                                    <p style="font-size:30px; margin-right: 5%; padding-top: 5%; ">{$product.points}</p>
                                                    <p>{l s="Points"}</p>
                                            </td>
                                          
                                            <td colspan="4" style="text-align:right; padding: 2%;">
                                                <a class="btn_history" href="{$link->getPageLink('cardsview', true, NULL, "manufacturer={$manufacturer.id_manufacturer|intval}")|escape:'html':'UTF-8'}" title="{l s='Card View'}">{l s="Card View >"}</a>
                                            </td>
                                            <td>
                                                <p style="color:#ef4136; margin: 0px; text-align: right;">{l s="Save: "}%{math equation='round(((p - r) / r)*100)' p=$product.price_shop r=$product.precio}</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="6" style="height: 50px;">
                                                
                                            </td>
                                        </tr>
				{/foreach}
			</tbody>
		</table>
		<div id="block-order-detail" class="unvisible">&nbsp;</div>
	{else}
		<p class="alert alert-warning">{l s='You have not placed any orders.'}</p>
	{/if}
</div>
<ul class="footer_links clearfix">
	<li>
		<a class="btn btn-history" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
			<span>
				<i class="icon-chevron-left"></i> {l s='Back to Your Account'}
			</span>
		</a>
	</li>
	<li>
		<a class="btn btn-history-shop" href="{$base_dir}">
			<span>{l s='Shop Now '}<i class="icon-chevron-right"></i> </span>
		</a>
	</li>
</ul>
{literal}
    <style>
        .table > thead > tr > th{color: #414042; font-family:'Open Sans'; background: #f7f7fb;}
        .info-title{display: none;}
        .page-heading{margin-bottom: 7% !important; padding: 0px !important; letter-spacing: 0px;}
        .breadcrumb{font-size:12px; margin-bottom: 3%;}
        .footable .footable-sortable .footable-sort-indicator:after{display: none;}
        .table tbody > tr > td{text-align: left;}
        ul.footer_links{margin-bottom: 5% !important; border-top: none !important;}
        p.info-account{margin: 11px 0 24px 0;}
        
    </style>
{/literal}
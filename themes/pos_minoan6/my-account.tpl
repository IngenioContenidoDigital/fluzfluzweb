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

{capture name=path}{l s='My account'}{/capture}
<h1 class="page-heading col-lg-12 col-md-12 col-sm-12 col-xs-12">{l s='My Gift Cards'}</h1>
<p class="info-account">{l s='View and Redeem your gift card purchases'}</p>
<div class="row">
{foreach from=$manufacturers item=manufacturer}
    <a href="{$link->getPageLink('cardsview', true, NULL, "manufacturer={$manufacturer.id_manufacturer|intval}")|escape:'html':'UTF-8'}">    
    <div class="col-lg-4 col-md-4 Cards">
        <div class="col-lg-6 col-md-6 col-xs-6 infoCard">
            <img src="{$img_manu_dir}{$manufacturer.id_manufacturer}.jpg" alt="{$manufacturer.manufacturer_name|escape:'htmlall':'UTF-8'}"/>
            <span class="nameCard">{$manufacturer.manufacturer_name}</span>
        </div>
        <div class="col-lg-6 col-md-6 col-xs-6 priceCard">
            <span class="num-Card">{$manufacturer.products}&nbsp;{l s=' Cards'}</span>
            <span class="priceTotalCard">{displayPrice price=$manufacturer.total}</span>
        </div>
    </div>
    </a>
{/foreach}
<div class="col-lg-3 col-md-3 col-sm-12 textAccount">
    <p class="titleFAQ">{l s='Have Question?'}</p>
    <div class="pFAQ">
        <p>{l s='Learn how to redeem digital cards.'}</p>
        <p>{l s='Learn how to transact with merchants.'}</p>
    </div>
</div>
</div>

<h1 class="page-heading col-lg-12 col-md-12 col-sm-12 col-xs-12 efectMargin">{l s='My account'}</h1>
{if isset($account_created)}
	<p class="alert alert-success">
		{l s='Your account has been created.'}
	</p>
{/if}
<p class="info-account">{l s='Welcome to your account. Here you can manage all of your personal information and orders.'}</p>
<div class="row addresses-lists modAccount">
	<div class="col-xs-12 col-md-4 col-sm-5 col-lg-4 account-responsive" style=" padding-left: 0px; padding-right: 0px; margin-right: 1%;">
            <ul class="myaccount-link-list">
            {if $has_customer_an_address}
            <!--<li><a href="{$link->getPageLink('address', true)|escape:'html':'UTF-8'}" title="{l s='Add my first address'}"><i class="icon-building"></i><span>{l s='Add my first address'}</span></a></li>-->
            {/if}
            {if $returnAllowed}
                <li><a href="{$link->getPageLink('order-follow', true)|escape:'html':'UTF-8'}" title="{l s='Merchandise returns'}"><i class="icon-refresh"></i><span>{l s='My merchandise returns'}</span></a></li>
            {/if}
            <!--<li><a href="{$link->getPageLink('order-slip', true)|escape:'html':'UTF-8'}" title="{l s='Credit slips'}"><i class="icon-file-o"></i><span>{l s='My credit slips'}</span></a></li>-->
            <li><a href="{$link->getPageLink('history', true)|escape:'html':'UTF-8'}" title="{l s='Orders'}"><img src="{$img_dir}icon/orderList.png" class="imgSponsor" /><span class="spanSponsor">{l s='Order history and details'}</span></a></li>
            <!--<li><a href="{$link->getPageLink('addresses', true)|escape:'html':'UTF-8'}" title="{l s='Addresses'}"><i class="icon-building"></i><span>{l s='My addresses'}</span></a></li>-->
            <li><a href="{$link->getPageLink('identity', true)|escape:'html':'UTF-8'}" title="{l s='Information'}"><img src="{$img_dir}icon/rewards.png" class="imgSponsor" /><span class="spanSponsor">{l s='My personal information'}</span></a></li>
            <li><a href="{$link->getPageLink('cashout', true)|escape:'html':'UTF-8'}" title="{l s='Cash Out'}"><img src="{$img_dir}icon/exchange.png" class="imgSponsor" /><span class="spanSponsor">{l s='Cash Out You Points'}</span></a></li>
            </ul>
	</div>
{if $voucherAllowed || isset($HOOK_CUSTOMER_ACCOUNT) && $HOOK_CUSTOMER_ACCOUNT !=''}
    <div class="col-xs-12 col-md-4 col-sm-5 col-lg-4" style="padding-left:0px; padding-right: 0px;">
        <ul class="myaccount-link-list">
            {if $voucherAllowed}
                <li><a href="{$link->getPageLink('discount', true)|escape:'html':'UTF-8'}" title="{l s='Vouchers'}"><img src="{$img_dir}icon/network.png" class="imgSponsor" /><span class="spanSponsor">{l s='My Network'}</span></a></li>
            {/if}
            {$HOOK_CUSTOMER_ACCOUNT}
            <li><a href="{$link->getPageLink('index', true, NULL, "mylogout")|escape:'html'}" title="{l s='Sign out' mod='blockmyaccountheader'}"><img src="{$img_dir}icon/signOut.png" class="imgSponsor" style="padding:0;"/><span class="spanSponsor">{l s='Sign out' mod='blockmyaccountheader'}</span></a></li>
        </ul>
        </div>
{/if}
        <div class="col-lg-3 col-sm-12 textAccount">
            <p class="titleFAQ">{l s='Need Support?'}</p>
            <div class="pFAQ">
                <p>{l s='Add a Credit or Debit Card'}</p>
                <p>{l s='Change Email or Password'}</p>
                <p>{l s='Learn About the Network'}</p>
            </div>    
        </div>
</div>
<ul class="footer_links clearfix" style="display: none;">
<li><a class="btn btn-default button button-small" href="{if isset($force_ssl) && $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}" title="{l s='Home'}"><span><i class="icon-chevron-left"></i> {l s='Home'}</span></a></li>
</ul>
{literal}
    <style>
        .page-heading{margin-bottom: 0px; padding: 0px;letter-spacing: 0px;font-family: 'Open Sans'; margin-top: 2%; font-size: 16px;}
        p.info-account{margin: 16px 0 24px 0;}
    </style>
{/literal}    
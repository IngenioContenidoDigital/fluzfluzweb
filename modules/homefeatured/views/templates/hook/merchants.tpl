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
        <section class="page-product-box blockproductscategory">
                <div class="divTitleFeatured">
                    <h1 class="titleFeatured2">{l s="NUEVOS COMERCIOS"}</h1>
                </div>

                <ul{if isset($id) && $id} id="{$id}"{/if} class="product_list grid row{if isset($class) && $class} {$class}{/if}">
                        <div class="row" style="margin-bottom:20px;">
                                {foreach from=$merchants item=merchant name=merchants}
                                                
                                    {if $merchant.count==1}
                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 div-productscms">
                                            <div class="col-lg-12 border-products">
                                                <a class="product_img_link" href="{if $merchant.category != "" && $merchant.category != 0}{$link->getProductLink($merchant.id_product, $merchant.link_rewrite)|escape:'htmlall':'UTF-8'}{else}#{/if}" title="{$merchant.name|lower|escape:'htmlall':'UTF-8'}" itemprop="url">
                                                    <div style="background: url('{$s3}m/m/{$merchant.id_manufacturer}.jpg') no-repeat;" class="img-logo">
                                                        <div class="img-center">
                                                            <div class="logo-manufacturer">
                                                                <img src="{$s3}m/{$merchant.id_manufacturer}.jpg" alt="{$merchant.name|lower|escape:'htmlall':'UTF-8'}" title="{$merchant.name|lower|escape:'htmlall':'UTF-8'}" class="img-responsive"/>
                                                            </div>    
                                                        </div>
                                                    </div>
                                                </a>
                                                <div class="name-merchant"> {$merchant.name|truncate:35:'...'|escape:'html':'UTF-8'|upper} </div>
                                                {if $logged}
                                                    <div class="name-merchant" style="color: #ef4136; margin-bottom: 40px;">{l s="GANA HASTA"}&nbsp;{($merchant.value/2)|string_format:"%d"}&nbsp;{l s="FLUZ"} </div>
                                                {else}
                                                    <div class="name-merchant" style="color: #ef4136; margin-bottom: 40px;">{l s="GANA HASTA"}&nbsp;{($merchant.value_no_logged/16)|string_format:"%d"}&nbsp;{l s="FLUZ"} </div>
                                                {/if}
                                            </div>
                                        </div>
                                    {else if $merchant.count==0}
                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 div-productscms">
                                            <div class="col-lg-12 border-products">
                                                <a class="product_img_link" href="#" title="{$merchant.name|lower|escape:'htmlall':'UTF-8'}" itemprop="url">
                                                    <div style="background: url('{$s3}m/m/{$merchant.id_manufacturer}.jpg') no-repeat;" class="img-logo">
                                                        <div class="img-center">
                                                            <div class="logo-manufacturer">
                                                                <img src="{$s3}m/{$merchant.id_manufacturer}.jpg" alt="{$merchant.name|lower|escape:'htmlall':'UTF-8'}" title="{$merchant.name|lower|escape:'htmlall':'UTF-8'}" class="img-responsive"/>
                                                            </div>    
                                                        </div>
                                                    </div>
                                                </a>    
                                                <div class="name-merchant"> {$merchant.name|truncate:35:'...'|escape:'html':'UTF-8'|upper} </div>
                                                {if $logged}
                                                    <div class="name-merchant" style="color: #ef4136; margin-bottom: 40px;">{l s="GANA HASTA"}&nbsp;{($merchant.value/2)|string_format:"%d"}&nbsp;{l s="FLUZ"} </div>
                                                {else}
                                                    <div class="name-merchant" style="color: #ef4136; margin-bottom: 40px;">{l s="GANA HASTA"}&nbsp;{($merchant.value_no_logged/16)|string_format:"%d"}&nbsp;{l s="FLUZ"} </div>
                                                {/if}
                                            </div> 
                                       </div>
                                    {else}
                                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 div-productscms">
                                            <div class="col-lg-12 border-products">
                                                <a class="product_img_link" href="{if $merchant.category != "" && $merchant.category != 0}{$link->getCategoryLink({$merchant.category})}{else}#{/if}" title="{$merchant.name|lower|escape:'htmlall':'UTF-8'}" itemprop="url">
                                                    <div style="background: url('{$s3}m/m/{$merchant.id_manufacturer}.jpg') no-repeat;" class="img-logo">
                                                        <div class="img-center">
                                                            <div class="logo-manufacturer">
                                                                <img src="{$s3}m/{$merchant.id_manufacturer}.jpg" alt="{$merchant.name|lower|escape:'htmlall':'UTF-8'}" title="{$merchant.name|lower|escape:'htmlall':'UTF-8'}" class="img-responsive"/>
                                                            </div>    
                                                        </div>
                                                    </div>
                                                </a>    
                                                <div class="name-merchant"> {$merchant.name|truncate:35:'...'|escape:'html':'UTF-8'|upper} </div>
                                                {if $logged}
                                                    <div class="name-merchant" style="color: #ef4136; margin-bottom: 40px;">{l s="GANA HASTA"}&nbsp;{($merchant.value/2)|string_format:"%d"}&nbsp;{l s="FLUZ"} </div>
                                                {else}
                                                    <div class="name-merchant" style="color: #ef4136; margin-bottom: 40px;">{l s="GANA HASTA"}&nbsp;{($merchant.value_no_logged/16)|string_format:"%d"}&nbsp;{l s="FLUZ"} </div>
                                                {/if}
                                            </div>    
                                       </div>
                                    {/if}
                                {/foreach}
                        </div>
                    </ul>
        </section>

<!-- Merchants -->

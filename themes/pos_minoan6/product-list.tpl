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

{if isset($products) && $products}
	{*define number of products per line in other page for desktop*}
	{if $page_name !='index' && $page_name !='product'}
		{assign var='nbItemsPerLine' value=3}
		{assign var='nbItemsPerLineTablet' value=2}
		{assign var='nbItemsPerLineMobile' value=3}
	{else}
		{assign var='nbItemsPerLine' value=4}
		{assign var='nbItemsPerLineTablet' value=3}
		{assign var='nbItemsPerLineMobile' value=2}
	{/if}
	{*define numbers of product per line in other page for tablet*}
	{assign var='nbLi' value=$products|@count}
	{math equation="nbLi/nbItemsPerLine" nbLi=$nbLi nbItemsPerLine=$nbItemsPerLine assign=nbLines}
	{math equation="nbLi/nbItemsPerLineTablet" nbLi=$nbLi nbItemsPerLineTablet=$nbItemsPerLineTablet assign=nbLinesTablet}
	<!-- Products list -->
        <section class="page-product-box blockproductscategory">
            
        <div class="divTitleFeatured">
            <h1 class="titleFeatured pos-title">{l s="Resultados de Busqueda"}</h1>
        </div>
        
	<ul{if isset($id) && $id} id="{$id}"{/if} class="product_list grid row{if isset($class) && $class} {$class}{/if}">
            <div class="row">
                {foreach from=$products item=product name=products}
                        {math equation="(total%perLine)" total=$smarty.foreach.products.total perLine=$nbItemsPerLine assign=totModulo}
                        {math equation="(total%perLineT)" total=$smarty.foreach.products.total perLineT=$nbItemsPerLineTablet assign=totModuloTablet}
                        {math equation="(total%perLineT)" total=$smarty.foreach.products.total perLineT=$nbItemsPerLineMobile assign=totModuloMobile}
                        {if $totModulo == 0}{assign var='totModulo' value=$nbItemsPerLine}{/if}
                        {if $totModuloTablet == 0}{assign var='totModuloTablet' value=$nbItemsPerLineTablet}{/if}
                        {if $totModuloMobile == 0}{assign var='totModuloMobile' value=$nbItemsPerLineMobile}{/if}
                        {assign var='save_price' value={math equation='round(((p - r) / p)*100)' r=$product.price p=$product.price_shop}}
                       
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 div-productscms">
                                    <div class="col-lg-12 border-products">
                                         <a class="product_img_link" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url">
                                            <div style="background: url('{$s3}p/{$product.id_image_parent}.jpg') no-repeat;" class="img-logo" alt="{$product.name|lower|escape:'htmlall':'UTF-8'}" title="{$product.name|lower|escape:'htmlall':'UTF-8'}">
                                                <div class="img-center">
                                                    <div class="logo-manufacturer">
                                                        <img src="{$s3}m/{$product.id_manufacturer}.jpg" alt="{$product.name|lower|escape:'htmlall':'UTF-8'}" title="{$product.name|lower|escape:'htmlall':'UTF-8'}" class="img-responsive"/>
                                                    </div>    
                                                </div>
                                            </div>    
                                        </a>
                                        {if isset($quick_view) && $quick_view}
                                                <a class="quick-view" title="Quick view" href="{$product.link|escape:'html':'UTF-8'}" style="position: absolute;" >
                                                    <span class="quick"><i class="icon-search"></i></span>
                                                </a>
                                        {/if}
                                        {if isset($product.new) && $product.new == 1}
                                                <a class="new-box" href="{$product.link|escape:'html':'UTF-8'}">
                                                        <span class="new-label">{l s='New'}</span>
                                                </a>
                                        {/if}
                                        {if isset($product.on_sale) && $product.on_sale && isset($product.show_price) && $product.show_price && !$PS_CATALOG_MODE}
                                                <a class="sale-box" href="{$product.link|escape:'html':'UTF-8'}">
                                                        <span class="sale-label">{l s='Sale!'}</span>
                                                </a>
                                        {/if}
                                        <div class="name-merchant">
                                                {if isset($product.pack_quantity) && $product.pack_quantity}{$product.pack_quantity|intval|cat:' x '}{/if}
                                                {*<a class="product-name" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url" style="padding-left:0px;">*}
                                                        {$product.manufacturer_name|truncate:45:'...'|escape:'html':'UTF-8'|upper}
                                                {*</a>*}
                                        </div>
                                        <div class="name-merchant" style="color:#EF4136;">
                                            {foreach from=$points_subcategories item=p}
                                                {if $product.id_product==$p.id_padre}
                                                    {if $logged}
                                                        <span>{l s="GANA HASTA"}&nbsp;{(($p.value)/$sponsor)|string_format:"%d"}&nbsp;{l s="FLUZ"}</span>
                                                    {else}                       
                                                        <span>{l s="GANA HASTA"}&nbsp;{(($p.value)/16)|string_format:"%d"}&nbsp;{l s="FLUZ"}</span>
                                                    {/if}
                                                {/if}
                                            {/foreach}    
                                        </div>
                                    </div>    
                                </div>
                {/foreach}
            </div>
	</ul>
        </section>   
                
{addJsDefL name=min_item}{l s='Please select at least one product' js=1}{/addJsDefL}
{addJsDefL name=max_item}{l s='You cannot add more than %d product(s) to the product comparison' sprintf=$comparator_max_item js=1}{/addJsDefL}
{addJsDef comparator_max_item=$comparator_max_item}
{addJsDef comparedProductsIds=$compared_products}
{/if}
{literal}
    <style>
        .bottom-pagination-content{padding-left: 15px;}
        .quick-view{display: none;}
        .img-logo{background-size:100% 100% !important; margin:10px;}
        .img-center{padding: 10px; width: 100%;display: table; margin-bottom: 5px; height: 200px; text-align: center;}
        .img-center img{max-width: 100% !important; margin: 0px auto !important;}
        .logo-manufacturer{display: table-cell;
             vertical-align: middle;
             position: relative;}
        .border-products {
            padding-left: 0px;
            padding-right: 0px;
            border: 1px solid #CBCBCB;
            margin-bottom: 15px;
        }
        
        @media (max-width:920px){
            .img-center {min-height: 150px;}
        }
        
         @media (min-width:426px) and (max-width:570px){
            .img-center {min-height: 280px;}
        }

        @media (min-width:571px) and (max-width:690px){
            .img-center {min-height: 350px;}
        }

        @media (min-width:690px) and (max-width:767px){
            .img-center {min-height: 420px;}
        }


        @media (min-width:800px) and (max-width:990px){
            .img-center {min-height: 165px;}
        }

        @media (min-width:991px) and (max-width:1024px){
            .img-center {min-height: 145px;}
            .name-merchant {font-size: 10px !important;}
    
        }

        @media (min-width:1025px) and (max-width:1225px){
            .img-center {min-height: 145px;}
        }

        @media (min-width:1226px) and (max-width:1330px){
            .img-center {min-height: 175px;}
        }

        @media (min-width:1331px) and (max-width:1439px){
            .img-center {min-height: 200px;}
        }

        @media (min-width:1701px) and (max-width:1919px){
            .img-center {min-height: 260px;}
        }
    </style>
{/literal}
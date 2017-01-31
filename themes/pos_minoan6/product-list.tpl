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
            <h1 class="titleFeatured pos-title">{l s="FEATURED"}</h1>
        </div>
        
	<ul{if isset($id) && $id} id="{$id}"{/if} class="product_list grid row{if isset($class) && $class} {$class}{/if}">
            <div id="product_categoryAll">
                {foreach from=$products item=product name=products}
                        {math equation="(total%perLine)" total=$smarty.foreach.products.total perLine=$nbItemsPerLine assign=totModulo}
                        {math equation="(total%perLineT)" total=$smarty.foreach.products.total perLineT=$nbItemsPerLineTablet assign=totModuloTablet}
                        {math equation="(total%perLineT)" total=$smarty.foreach.products.total perLineT=$nbItemsPerLineMobile assign=totModuloMobile}
                        {if $totModulo == 0}{assign var='totModulo' value=$nbItemsPerLine}{/if}
                        {if $totModuloTablet == 0}{assign var='totModuloTablet' value=$nbItemsPerLineTablet}{/if}
                        {if $totModuloMobile == 0}{assign var='totModuloMobile' value=$nbItemsPerLineMobile}{/if}
                        {assign var='save_price' value={math equation='round(((p - r) / p)*100)' r=$product.price p=$product.price_shop}}
                        <li class="ajax_block_product nopadding">
                                <div>
                                        <a class="product_img_link" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url">
                                                <div class="img-center"><img src="{$s3}m/{$product.id_manufacturer}.jpg" alt="{$product.name|lower|escape:'htmlall':'UTF-8'}" title="{$product.name|lower|escape:'htmlall':'UTF-8'}" class="img-responsive img-newmerchant"/></div>    
                                                <img class="img-responsive pruebaImgCategory" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'thickbox_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} itemprop="image" style="padding:10px;"/>
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
                                </div>
                                <div class="points-block">
                                        <div class="name-merchant">
                                                {if isset($product.pack_quantity) && $product.pack_quantity}{$product.pack_quantity|intval|cat:' x '}{/if}
                                                {*<a class="product-name" href="{$product.link|escape:'html':'UTF-8'}" title="{$product.name|escape:'html':'UTF-8'}" itemprop="url" style="padding-left:0px;">*}
                                                        {$product.manufacturer_name|truncate:45:'...'|escape:'html':'UTF-8'|upper}
                                                {*</a>*}
                                        </div>
                                        <div class="name-merchant" style="color:#EF4136;">
                                            <span>{l s="GANA HASTA"}&nbsp;{$product.max_puntos|string_format:"%d"}&nbsp;{l s="FLUZ"}</span>
                                            {*if $logged}{l s="You earn"}&nbsp;{$product.points}{else $logged}{l s="You earn"}&nbsp;{$product.pointsNl}{/if*}
                                        </div>
                                </div>
                        </li>
                {/foreach}
            </div>
	</ul>
        <div class="boxprevnext2">
            <a class="prev prev-product"><i class="icon-chevron-left"></i></a>
            <a class="next next-product"><i class="icon-chevron-right"></i></a>
        </div>
        </section>   
                
{addJsDefL name=min_item}{l s='Please select at least one product' js=1}{/addJsDefL}
{addJsDefL name=max_item}{l s='You cannot add more than %d product(s) to the product comparison' sprintf=$comparator_max_item js=1}{/addJsDefL}
{addJsDef comparator_max_item=$comparator_max_item}
{addJsDef comparedProductsIds=$compared_products}
{/if}
{literal}
    <script type="text/javascript"> 
        $(document).ready(function() {
                    var owl = $("#product_categoryAll");
                    owl.owlCarousel({
                    items : 4,
                    pagination :false,
                    slideSpeed: 1000,
                    responsiveClass:true,
                    itemsDesktop : [1199,4],
                    itemsDesktopSmall : [1080,3], 
                    itemsTablet: [768,3], 
                    itemsMobile : [480,1],
                    });

                    // Custom Navigation Events
                    $(".next-product").click(function(){
                    owl.trigger('owl.next');
                    })
                    $(".prev-product").click(function(){
                    owl.trigger('owl.prev');
                    })     
        });
    </script>
{/literal}
{literal}
    <style>
        .product-count{display: none;}
        .quick-view{display: none;}
    </style>
{/literal}
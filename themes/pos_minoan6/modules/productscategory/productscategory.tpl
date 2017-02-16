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
{if count($categoryProducts) > 0 && $categoryProducts !== false}
<section class="page-product-box blockproductscategory">
	<div class="pos-title">
		<h2>
                        <span class="title-related">
                                {*if $categoryProducts|@count == 1}
                                        {l s='%s other product in the same category:' sprintf=[$categoryProducts|@count] mod='productscategory'}
                                {else}
                                        {l s='%s other products in the same category:' sprintf=[$categoryProducts|@count] mod='productscategory'}
                                {/if*}
                                {l s='Recomendados'}
                        </span>
		</h2>
	</div>	
        <div class="border-title"></div>                

	<div id="productscategory_list" class="clearfix">
		<div class="row pos-content">
			<div class="product_category">
			{foreach from=$categoryProducts item='categoryProduct' name=categoryProduct}
				<div class="item-product row">
                                        <div class="col-lg-6 padding-img">
                                                <a href="{$link->getProductLink($categoryProduct.id_product, $categoryProduct.link_rewrite, $categoryProduct.category, $categoryProduct.ean13)}" class="lnk_img product-image" title="{$categoryProduct.name|htmlspecialchars}">
                                                    <div class="img-center"><img src="{$s3}m/{$categoryProduct.id_manufacturer}.jpg" alt="{$categoryProduct.manufacturer_name|lower|escape:'htmlall':'UTF-8'}" title="{$categoryProduct.manufacturer_name|lower|escape:'htmlall':'UTF-8'}" class="img-responsive img-newmerchant"/></div>
                                                    <img class="img-responsive pruebaImgCategory"  src="{$link->getImageLink($categoryProduct.link_rewrite, $categoryProduct.id_image, 'thickbox_default')|escape:'html':'UTF-8'}" alt="{$categoryProduct.name|htmlspecialchars}" />
                                                </a>
                                        </div>
                                        <div class="points-block col-lg-6">
                                        {assign var="idprodshop" value=$product.reference}
                                        {assign var='save_price' value= {math equation='round(((p - r) / p)*100)' p=$categoryProduct.price_shop r=$categoryProduct.price_tax_exc}}    
                                                <div style="font-size:12px; margin-left: 0px;">
                                                        {$categoryProduct.manufacturer_name|truncate:40:'...'|escape:'html':'UTF-8'}
                                                </div>
                                                {foreach from=$points_subcategories item=p}
                                                    {if $categoryProduct.id_product==$p.id_padre}
                                                        {if $logged}
                                                            <span class="Earn-product">{l s="GANA HASTA"}&nbsp;{((($p.price/(int)Configuration::get('REWARDS_VIRTUAL_VALUE_1'))*$p.value)/$sponsor)|string_format:"%d"}&nbsp;{l s="FLUZ"}</span>
                                                        {else}                       
                                                            <span class="Earn-product">{l s="GANA HASTA"}&nbsp;{((($p.price/(int)Configuration::get('REWARDS_VIRTUAL_VALUE_1'))*$p.value)/16)|string_format:"%d"}&nbsp;{l s="FLUZ"}</span>
                                                        {/if}
                                                    {/if}
                                                {/foreach}    
                                        </div>
				</div>
			{/foreach}
			</div>
                        <div class="product_category">
			{foreach from=$categoryProducts2 item='categoryProduct' name=categoryProduct}
				<div class="item-product">
                                        <div class="col-lg-6 padding-img">
                                                <a href="{$link->getProductLink($categoryProduct.id_product, $categoryProduct.link_rewrite, $categoryProduct.category, $categoryProduct.ean13)}" class="lnk_img product-image" title="{$categoryProduct.name|htmlspecialchars}">
                                                    <div class="img-center"><img src="{$s3}m/{$categoryProduct.id_manufacturer}.jpg" alt="{$categoryProduct.manufacturer_name|lower|escape:'htmlall':'UTF-8'}" title="{$categoryProduct.manufacturer_name|lower|escape:'htmlall':'UTF-8'}" class="img-responsive img-newmerchant"/></div>
                                                    <img class="img-responsive pruebaImgCategory"  src="{$link->getImageLink($categoryProduct.link_rewrite, $categoryProduct.id_image, 'thickbox_default')|escape:'html':'UTF-8'}" alt="{$categoryProduct.name|htmlspecialchars}" />
                                                </a>
                                        </div>
                                        <div class="points-block col-lg-6">
                                        {assign var="idprodshop" value=$product.reference}
                                        {assign var='save_price' value= {math equation='round(((p - r) / p)*100)' p=$categoryProduct.price_shop r=$categoryProduct.price_tax_exc}}    
                                                <div style="font-size:12px; margin-left: 0px;">
                                                        {$categoryProduct.manufacturer_name|truncate:40:'...'|escape:'html':'UTF-8'}
                                                </div>
                                                {foreach from=$points_subcategories item=p}
                                                    {if $categoryProduct.id_product==$p.id_padre}
                                                        {if $logged}
                                                            <span class="Earn-product">{l s="GANA HASTA"}&nbsp;{((($p.price/(int)Configuration::get('REWARDS_VIRTUAL_VALUE_1'))*$p.value)/$sponsor)|string_format:"%d"}&nbsp;{l s="FLUZ"}</span>
                                                        {else}                       
                                                            <span class="Earn-product">{l s="GANA HASTA"}&nbsp;{((($p.price/(int)Configuration::get('REWARDS_VIRTUAL_VALUE_1'))*$p.value)/16)|string_format:"%d"}&nbsp;{l s="FLUZ"}</span>
                                                        {/if}
                                                    {/if}
                                                {/foreach} 
                                        </div>
				</div>
			{/foreach}
			</div>
                        <div class="product_category">
			{foreach from=$categoryProducts3 item='categoryProduct' name=categoryProduct}
				<div class="item-product">
                                        <div class="col-lg-6 padding-img">
                                                <a href="{$link->getProductLink($categoryProduct.id_product, $categoryProduct.link_rewrite, $categoryProduct.category, $categoryProduct.ean13)}" class="lnk_img product-image" title="{$categoryProduct.name|htmlspecialchars}">
                                                    <div class="img-center"><img src="{$s3}m/{$categoryProduct.id_manufacturer}.jpg" alt="{$categoryProduct.manufacturer_name|lower|escape:'htmlall':'UTF-8'}" title="{$categoryProduct.manufacturer_name|lower|escape:'htmlall':'UTF-8'}" class="img-responsive img-newmerchant"/></div>
                                                    <img class="img-responsive pruebaImgCategory"  src="{$link->getImageLink($categoryProduct.link_rewrite, $categoryProduct.id_image, 'thickbox_default')|escape:'html':'UTF-8'}" alt="{$categoryProduct.name|htmlspecialchars}" />
                                                </a>
                                        </div>
                                        <div class="points-block col-lg-6">
                                        {assign var="idprodshop" value=$product.reference}
                                        {assign var='save_price' value= {math equation='round(((p - r) / p)*100)' p=$categoryProduct.price_shop r=$categoryProduct.price_tax_exc}}    
                                                <div style="font-size:12px; margin-left: 0px;">
                                                        {$categoryProduct.manufacturer_name|truncate:40:'...'|escape:'html':'UTF-8'}
                                                </div>
                                                {foreach from=$points_subcategories item=p}
                                                    {if $categoryProduct.id_product==$p.id_padre}
                                                        {if $logged}
                                                            <span class="Earn-product">{l s="GANA HASTA"}&nbsp;{((($p.price/(int)Configuration::get('REWARDS_VIRTUAL_VALUE_1'))*$p.value)/$sponsor)|string_format:"%d"}&nbsp;{l s="FLUZ"}</span>
                                                        {else}                       
                                                            <span class="Earn-product">{l s="GANA HASTA"}&nbsp;{((($p.price/(int)Configuration::get('REWARDS_VIRTUAL_VALUE_1'))*$p.value)/16)|string_format:"%d"}&nbsp;{l s="FLUZ"}</span>
                                                        {/if}
                                                    {/if}
                                                {/foreach} 
                                        </div>
				</div>
			{/foreach}
			</div>
                        <div class="product_category">
			{foreach from=$categoryProducts4 item='categoryProduct' name=categoryProduct}
				<div class="item-product">
                                        <div class="col-lg-6 padding-img">
                                                <a href="{$link->getProductLink($categoryProduct.id_product, $categoryProduct.link_rewrite, $categoryProduct.category, $categoryProduct.ean13)}" class="lnk_img product-image" title="{$categoryProduct.name|htmlspecialchars}">
                                                    <div class="img-center"><img src="{$s3}m/{$categoryProduct.id_manufacturer}.jpg" alt="{$categoryProduct.manufacturer_name|lower|escape:'htmlall':'UTF-8'}" title="{$categoryProduct.manufacturer_name|lower|escape:'htmlall':'UTF-8'}" class="img-responsive img-newmerchant"/></div>
                                                    <img class="img-responsive pruebaImgCategory"  src="{$link->getImageLink($categoryProduct.link_rewrite, $categoryProduct.id_image, 'thickbox_default')|escape:'html':'UTF-8'}" alt="{$categoryProduct.name|htmlspecialchars}" />
                                                </a>
                                        </div>
                                        <div class="points-block col-lg-6">
                                        {assign var="idprodshop" value=$product.reference}
                                        {assign var='save_price' value= {math equation='round(((p - r) / p)*100)' p=$categoryProduct.price_shop r=$categoryProduct.price_tax_exc}}    
                                                <div style="font-size:12px; margin-left: 0px;">
                                                        {$categoryProduct.manufacturer_name|truncate:40:'...'|escape:'html':'UTF-8'}
                                                </div>
                                                {foreach from=$points_subcategories item=p}
                                                    {if $categoryProduct.id_product==$p.id_padre}
                                                        {if $logged}
                                                            <span class="Earn-product">{l s="GANA HASTA"}&nbsp;{((($p.price/(int)Configuration::get('REWARDS_VIRTUAL_VALUE_1'))*$p.value)/$sponsor)|string_format:"%d"}&nbsp;{l s="FLUZ"}</span>
                                                        {else}                       
                                                            <span class="Earn-product">{l s="GANA HASTA"}&nbsp;{((($p.price/(int)Configuration::get('REWARDS_VIRTUAL_VALUE_1'))*$p.value)/16)|string_format:"%d"}&nbsp;{l s="FLUZ"}</span>
                                                        {/if}
                                                    {/if}
                                                {/foreach}
                                        </div>
				</div>
			{/foreach}
			</div>
                        <div class="product_movil">
                            {foreach from=$categoryMovil item='categoryProduct' name=categoryProduct}
                                    <div class="item-product">
                                            <div class="col-lg-6 padding-img">
                                                    <a href="{$link->getProductLink($categoryProduct.id_product, $categoryProduct.link_rewrite, $categoryProduct.category, $categoryProduct.ean13)}" class="lnk_img product-image" title="{$categoryProduct.name|htmlspecialchars}">
                                                        <div class="img-center"><img src="{$s3}m/{$categoryProduct.id_manufacturer}.jpg" alt="{$categoryProduct.manufacturer_name|lower|escape:'htmlall':'UTF-8'}" title="{$categoryProduct.manufacturer_name|lower|escape:'htmlall':'UTF-8'}" class="img-responsive img-newmerchant"/></div>
                                                        <img class="img-responsive pruebaImgCategory"  src="{$link->getImageLink($categoryProduct.link_rewrite, $categoryProduct.id_image, 'thickbox_default')|escape:'html':'UTF-8'}" alt="{$categoryProduct.name|htmlspecialchars}" />
                                                    </a>
                                            </div>
                                            <div class="points-block col-lg-6">
                                            {assign var="idprodshop" value=$product.reference}
                                            {assign var='save_price' value= {math equation='round(((p - r) / p)*100)' p=$categoryProduct.price_shop r=$categoryProduct.price_tax_exc}}    
                                                    <div style="font-size:12px; margin-left: 0px;">
                                                            {$categoryProduct.manufacturer_name|truncate:40:'...'|escape:'html':'UTF-8'}
                                                    </div>
                                                    {foreach from=$points_subcategories item=p}
                                                        {if $categoryProduct.id_product==$p.id_padre}
                                                            {if $logged}
                                                                <span class="Earn-product">{l s="GANA HASTA"}&nbsp;{((($p.price/(int)Configuration::get('REWARDS_VIRTUAL_VALUE_1'))*$p.value)/$sponsor)|string_format:"%d"}&nbsp;{l s="FLUZ"}</span>
                                                            {else}                       
                                                                <span class="Earn-product">{l s="GANA HASTA"}&nbsp;{((($p.price/(int)Configuration::get('REWARDS_VIRTUAL_VALUE_1'))*$p.value)/16)|string_format:"%d"}&nbsp;{l s="FLUZ"}</span>
                                                            {/if}
                                                        {/if}
                                                    {/foreach}
                                                    <!--<div class="imgmanu" style="float: left;"><img src="{$s3}m/{$categoryProduct.id_manufacturer}.jpg" alt="{$categoryProduct.manufacturer_name|escape:'htmlall':'UTF-8'}" title="{$categoryProduct.manufacturer_name|escape:'htmlall':'UTF-8'}" class="img-responsive"/></div>-->
                                            </div>
                                    </div>
                            {/foreach}
			</div>
			<div class="boxprevnext">
				<a class="prev prev-product"><i class="icon-chevron-left"></i></a>
				<a class="next next-product"><i class="icon-chevron-right"></i></a>
			</div>
		</div>	
	</div>
</section>
{/if}
<script type="text/javascript"> 
    $(document).ready(function() {
		var owl = $(".product_category");
		owl.owlCarousel({
		items : 1,
		 pagination :false,
		slideSpeed: 1000,
		itemsDesktop : [1199,2],
		itemsDesktopSmall : [911,2], 
		itemsTablet: [767,2], 
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
{literal}
    <style>
        .pos-title{margin-bottom: 0px;}
        .pos-title span { color: #838383; font-size: 17px; }
        .pos-title h2 { width: 40%; text-align: center }
        .pos-title h2:before { display: none; }
        .boxprevnext a i { display: block; line-height: 32px; background: #f4f4f4; }
        .boxprevnext a { font-size: 25px; border: 0; height: 32px;}
        .boxprevnext a.prev { right: 10.5%; }
        .item-product { color: #777777; width: 100%; margin: 0; }
        .redfl { color: #ef4136!important; font-weight: bold!important; }
        .valuefl { font-size: 13px; }
        .imgMini { width: 35px!important; }
        .imgmanu { width: auto!important; }
        .price-block { padding: 15px 0%; }
        .points-block div:first-child{margin-left: 10px; margin-top: 10px;}
        .more-info ul li a{font-size: 10px;}
        .Earn-product{color: #ef4136;font-size: 9px;}
        
        @media (max-width:1024px){
            .Earn-product{color: #ef4136;font-size: 11px !important;}
        }
        
    </style>    
{/literal}
{literal}
    <script type="text/javascript"> 
    $(document).ready(function() {
		var owl = $(".product_movil");
		owl.owlCarousel({
		items : 4,
		 pagination :false,
		slideSpeed: 1000,
		itemsDesktop : [1199,4],
		itemsDesktopSmall : [911,3], 
		itemsTablet: [767,3], 
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

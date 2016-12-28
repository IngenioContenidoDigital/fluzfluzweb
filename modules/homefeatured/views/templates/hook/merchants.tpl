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

<!-- New Merchants -->          
        
        {*foreach from=$merchants item=merchant name=merchants}
            
            <div>
                {$merchant.name}
                <div><img src="{$img_manu_dir}{$merchant.id_manufacturer}.jpg" alt="{$merchant.manufacturer_name|escape:'htmlall':'UTF-8'}" title="{$merchant.manufacturer_name|escape:'htmlall':'UTF-8'}" class="imgMini"/></div>
                <div>{l s="Save"} {math equation='round(((p - r) / r)*100)' p=$merchant.price_shop r=$merchant.price}%</div>
                                        
                                </div>
                                <div>
                                        <a class="product_img_link" href="{$merchant.link|escape:'html':'UTF-8'}" title="{$merchant.name|escape:'html':'UTF-8'}" itemprop="url">
                                                <img class="img-responsive pruebaImgCategory" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'thickbox_default')|escape:'html':'UTF-8'}" alt="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" title="{if !empty($product.legend)}{$product.legend|escape:'html':'UTF-8'}{else}{$product.name|escape:'html':'UTF-8'}{/if}" {if isset($homeSize)} width="{$homeSize.width}" height="{$homeSize.height}"{/if} itemprop="image" />
                                        </a>
                                        {if isset($quick_view) && $quick_view}
                                                <a class="quick-view" title="Quick view" href="{$product.link|escape:'html':'UTF-8'}" style="position: absolute;" >
                                                    <span class="quick"><i class="icon-search"></i></span>
                                                </a>
                                        {/if}
                                        
            </div>
            
        {/foreach*}
        
        <section class="page-product-box blockproductscategory">
                <div class="divTitleFeatured">
                    <h1 class="titleFeatured2">{l s="COMERCIOS"}</h1>
                </div>

                <div class="boxprevnext2">
                    <a class="prev prev-product2"><i class="icon-chevron-left"></i></a>
                    <a class="next next-product2"><i class="icon-chevron-right"></i></a>
                </div>

                <ul{if isset($id) && $id} id="{$id}"{/if} class="product_list grid row{if isset($class) && $class} {$class}{/if}">
                        <div id="product_categoryAll2">
                                {foreach from=$merchants item=merchant name=merchants}
                                        <li class="ajax_block_product nopadding">
                                                <div class="title-block title-merchant">
                                                        <div class="col-lg-6 col-md-6 col-sm-6" style="padding-left: 0px;padding-right: 0px;"><img src="{$s3}m/{$merchant.id_manufacturer}.jpg" alt="{$merchant.name|lower|escape:'htmlall':'UTF-8'}" title="{$merchant.name|lower|escape:'htmlall':'UTF-8'}" class="img-responsive"/></div>
                                                        <div class="col-lg-6 col-md-6 col-sm-6 save-product"></div>
                                                </div>
                                                <div>
                                                        <a class="product_img_link" href="{if $merchant.category != "" && $merchant.category != 0}{$link->getCategoryLink({$merchant.category})}{else}#{/if}" title="{$merchant.name|lower|escape:'htmlall':'UTF-8'}" itemprop="url">
                                                            <img class="img-responsive pruebaImgCategory" src="{$s3}m/m/{$merchant.id_manufacturer}.jpg" alt="{$merchant.name|lower|escape:'htmlall':'UTF-8'}" title="{$merchant.name|lower|escape:'htmlall':'UTF-8'}" itemprop="image" />
                                                        </a>
                                                </div>
                                                <div>&nbsp;</div>
                                        </li>
                                {/foreach}
                        </div>
                    </ul>
        </section>

<!-- New Merchants -->

<script type="text/javascript"> 
    $(document).ready(function() {
		var owl = $("#product_categoryAll2");
		owl.owlCarousel({
		items : 4,
		 pagination :false,
		slideSpeed: 1000,
		itemsDesktop : [1199,3],
		itemsDesktopSmall : [911,2], 
		itemsTablet: [767,2], 
		itemsMobile : [480,1],
		});
		 
		// Custom Navigation Events
		$(".next-product2").click(function(){
		owl.trigger('owl.next');
		})
		$(".prev-product2").click(function(){
		owl.trigger('owl.prev');
		})     
    });
</script>

<style>
    .title-merchant { text-transform: capitalize; color: #000; }
</style>
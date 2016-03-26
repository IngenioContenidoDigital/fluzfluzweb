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

<!-- MODULE Block best sellers -->
<div id="best-sellers_block_right" class="block products_block">
	<div class="pos-title">
		<h2>
			<a href="{$link->getPageLink('best-sales')|escape:'html'}" title="{l s='View a top sellers products' mod='blockbestsellers'}">{l s='Top sellers' mod='blockbestsellers'}</a>
		</h2>
	</div>
	<div class=" pos-content">
	{if $best_sellers && $best_sellers|@count > 0}
		<div class=" Topsellers">
			{foreach from=$best_sellers item=product name=myLoop}
			{if $smarty.foreach.myLoop.index % 5 == 0 || $smarty.foreach.myLoop.first }
			<div class="clearfix bestsellerproductslider">
			{/if}
				<div class="item-sellers">
					<div class="sellers-img">
						<a href="{$product.link|escape:'html'}" title="{$product.legend|escape:'html':'UTF-8'}" class="products-block-image content_img clearfix">
							<img class="replace-2x img-responsive" src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'small_default')|escape:'html'}" alt="{$product.legend|escape:'html':'UTF-8'}" />
						</a>
					</div>	
					<div class="product-content">
						<h5>
							<a class="product-name" href="{$product.link|escape:'html'}" title="{$product.legend|escape:'html':'UTF-8'}">
								{$product.name|strip_tags:'UTF-8'|escape:'html':'UTF-8'}
							</a>
						</h5>
						<p class="product-description">{$product.description_short|strip_tags:'UTF-8'|truncate:75:'...'}</p>
						{if !$PS_CATALOG_MODE}
							<div class="price-box">
								<span class="price">{$product.price}</span>
								{hook h="displayProductPriceBlock" product=$product type="price"}
							</div>
						{/if}
						<div class="ratings">{hook h='displayProductListReviews' product=$product}</div> 
					</div>
				</div>	
			{if $smarty.foreach.myLoop.iteration % 5 == 0 || $smarty.foreach.myLoop.last  }
			</div>
			{/if}
		{/foreach}
		</div>
		<div class="boxprevnext">
			<a class="prev prevsellers"><i class="icon-angle-left"></i></a>
			<a class="next nextsellers"><i class="icon-angle-right"></i></a>
		</div>
		<!-- <div class="lnk">
        	<a href="{$link->getPageLink('best-sales')|escape:'html'}" title="{l s='All best sellers' mod='blockbestsellers'}"  class="btn btn-default button button-small"><span>{l s='All best sellers' mod='blockbestsellers'}<i class="icon-chevron-right right"></i></span></a>
        </div> -->
	{else}
		<p>{l s='No best sellers at this time' mod='blockbestsellers'}</p>
	{/if}
	</div>
</div>
<script type="text/javascript"> 
	$(document).ready(function() {
		var owl = $(".Topsellers");
		owl.owlCarousel({
		items :1,
		slideSpeed: 1000,
		 pagination :false,
		itemsDesktop : [1200,1],
		itemsDesktopSmall : [1024,1],
		itemsTablet: [768,1],
		itemsMobile : [480,1]
		});
		$(".nextsellers").click(function(){
		owl.trigger('owl.next');
		})
		$(".prevsellers").click(function(){
		owl.trigger('owl.prev');
		})  
	});
</script>
<!-- /MODULE Block best sellers -->
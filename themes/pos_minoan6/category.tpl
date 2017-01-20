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
{include file="$tpl_dir./errors.tpl"}
{if isset($category)}
	{if $category->id AND $category->active}
    <!-- 	{if $scenes || $category->description || $category->id_image}
			<div class="content_scene_cat">
            	 {if $scenes}
                 	<div class="content_scene">
                        <!-- Scenes --
                        {include file="$tpl_dir./scenes.tpl" scenes=$scenes}
                        {if $category->description}
                            <div class="cat_desc rte">
                            {if Tools::strlen($category->description) > 350}
                                <div id="category_description_short">{$description_short}</div>
                                <div id="category_description_full" class="unvisible">{$category->description}</div>
                                <a href="{$link->getCategoryLink($category->id_category, $category->link_rewrite)|escape:'html':'UTF-8'}" class="lnk_more">{l s='More'}</a>
                            {else}
                                <div>{$category->description}</div>
                            {/if}
                            </div>
                        {/if}
                    </div>
				{else}
                    <!-- Category image --
                    <div class="content_scene_cat_bg"{if $category->id_image} style="background:url({$link->getCatImageLink($category->link_rewrite, $category->id_image, 'category_default')|escape:'html':'UTF-8'}) right center no-repeat; background-size:cover; min-height:{$categorySize.height}px;"{/if}>
                        {if $category->description}
                            <div class="cat_desc">
                            <span class="category-name">
                                {strip}
                                    {$category->name|escape:'html':'UTF-8'}
                                    {if isset($categoryNameComplement)}
                                        {$categoryNameComplement|escape:'html':'UTF-8'}
                                    {/if}
                                {/strip}
                            </span>
                            {if Tools::strlen($category->description) > 350}
                                <div id="category_description_short" class="rte">{$description_short}</div>
                                <div id="category_description_full" class="unvisible rte">{$category->description}</div>
                                <a href="{$link->getCategoryLink($category->id_category, $category->link_rewrite)|escape:'html':'UTF-8'}" class="lnk_more">{l s='More'}</a>
                            {else}
                                <div class="rte">{$category->description}</div>
                            {/if}
                            </div>
                        {/if}
                     </div>
                  {/if}
            </div>
		{/if} -->
	{*if isset($subcategories)}
        {if (isset($display_subcategories) && $display_subcategories eq 1) || !isset($display_subcategories) }
		<!-- Subcategories --
		<div id="subcategories">
			<p class="subcategory-heading">{l s='Subcategories'}</p>
			<ul class="clearfix">
			{foreach from=$subcategories item=subcategory}
				<li>
                	<div class="subcategory-image">
						<a href="{$link->getCategoryLink($subcategory.id_category, $subcategory.link_rewrite)|escape:'html':'UTF-8'}" title="{$subcategory.name|escape:'html':'UTF-8'}" class="img">
						{if $subcategory.id_image}
							<img class="replace-2x" src="{$link->getCatImageLink($subcategory.link_rewrite, $subcategory.id_image, 'medium_default')|escape:'html':'UTF-8'}" alt="{$subcategory.name|escape:'html':'UTF-8'}" width="{$mediumSize.width}" height="{$mediumSize.height}" />
						{else}
							<img class="replace-2x" src="{$img_cat_dir}{$lang_iso}-default-medium_default.jpg" alt="{$subcategory.name|escape:'html':'UTF-8'}" width="{$mediumSize.width}" height="{$mediumSize.height}" />
						{/if}
					</a>
                   	</div>
					<h5><a class="subcategory-name" href="{$link->getCategoryLink($subcategory.id_category, $subcategory.link_rewrite)|escape:'html':'UTF-8'}">{$subcategory.name|truncate:25:'...'|escape:'html':'UTF-8'}</a></h5>
					{if $subcategory.description}
						<div class="cat_desc">{$subcategory.description}</div>
					{/if}
				</li>
			{/foreach}
			</ul>
		</div>
        {/if}
		{/if*}
                <div id="left_column" class="menuSticky column col-lg-3 col-md-3 col-xs-12 col-sm-12">
                    {$HOOK_LEFT_COLUMN}
                </div>
		{if $products}
                <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 widthCategory" style="padding-left: 0px;">
                        <h1 class="page-heading{if (isset($subcategories) && !$products) || (isset($subcategories) && $products) || !isset($subcategories) && $products} product-listing{/if}"><span class="cat-name">{$category->name|escape:'html':'UTF-8'}{if isset($categoryNameComplement)}&nbsp;{$categoryNameComplement|escape:'html':'UTF-8'}{/if}</span>{include file="$tpl_dir./category-count.tpl"}</h1>
   
			<div class="content_sortPagiBar clearfix">
                            <div class="sortPagiBar clearfix">
                                {include file="./product-compare.tpl"}
                                {include file="./product-sort.tpl"}
                                {include file="./nbr-product-page.tpl"}
                            </div>

			</div>
                        {include file="./product-list.tpl" products=$products}
			<div class="content_sortPagiBar">
			<div class="bottom-pagination-content clearfix">
			{include file="./product-compare.tpl" paginationId='bottom'}
                        {include file="./pagination.tpl" paginationId='bottom'}
			</div>
			</div>
                </div> 
                {/if}
	{elseif $category->id}
		<p class="alert alert-warning">{l s='This category is currently unavailable.'}</p>
	{/if}
           
        {if $category->id AND $category->active}
             
            {literal}
                <style>
                    .divTitleFeatured{display: none;}
                    .boxprevnext2 a i{display: none;}
                    .page-heading.product-listing{margin: 0 auto; width: 90%;}
                    .content_sortPagiBar .sortPagiBar{margin-left: 5%;}
                    .boxprevnext2 a{display: none;}
                    .owl-wrapper{width: 100% !important; margin-left: 50px; transition:none !important; transform: none !important;}
                    .content_sortPagiBar .display li{display: none;}
                    .bottom-pagination-content ul.pagination{margin-top: 0 !important;}
                    .save-product{font-size: 16px;text-align: right;padding-right: 0px;}
                    .style-search{background:#ef4136;height: 75px;}
                    form#searchbox input#search_query_block{margin-bottom: 0px !important;padding: 18.5px !important; max-width: 380px; margin-right: 0px !important;}
                    #search_query_top{left: 0px; margin-top: 65px; z-index: 1;}
                    
                    @media (min-width:1025px) and (max-width:1120px){
                        .prueba{left: 186px;}
                        .owl-item {width: 235px;}
                        .owl-wrapper{margin-left: 12px;}
                        .content_sortPagiBar .sortPagiBar #productsSortForm{ margin: 0 14px 0 30px;}
                    }
                    
                    @media (max-width: 2600px) and (min-width: 1451px){
                        .owl-item {    width: 275px !important;}
                        .owl-wrapper{ margin-left:0px; transition:none !important; transform: none !important;}
                        .ct_img > img{display: block;width: 100% !important;max-width: 100% !important;height: auto;margin-left: 0 !important;}
                    }    
                </style>
            {/literal}
        {/if}    
{/if}

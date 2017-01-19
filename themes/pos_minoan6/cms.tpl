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
{if isset($cms) && !isset($cms_category)}
	{if !$cms->active}
		<br />
		<div id="admin-action-cms">
			<p>
				<span>{l s='This CMS page is not visible to your customers.'}</span>
				<input type="hidden" id="admin-action-cms-id" value="{$cms->id}" />
				<input type="submit" value="{l s='Publish'}" name="publish_button" class="button btn btn-default"/>
				<input type="submit" value="{l s='Back'}" name="lnk_view" class="button btn btn-default"/>
			</p>
			<div class="clear" ></div>
			<p id="admin-action-result"></p>
		</div>
                        
	{/if}
        <div class="rte">
            {*$cms->content*}
            {hook h='bannerSlide'}
            {*$slider*}
        </div>
        <div class="row style-search">
            <div id="search_block_left" class="block exclusive">
                    <form method="get" action="{$link->getPageLink('search', true, null, null, false, null, true)|escape:'html':'UTF-8'}" id="searchbox">
                            <p class="block_content clearfix">
                                    <input type="hidden" name="orderby" value="position" />
                                    <input type="hidden" name="controller" value="search" />
                                    <input type="hidden" name="orderway" value="desc" />
                                    <input class="search_query form-control grey" placeholder="Buscar" type="text" id="search_query_block" name="search_query" value="{$search_query|escape:'htmlall':'UTF-8'|stripslashes}" />
                                    <button type="submit" id="search_button" class="btn btn-default button button-small"><span><i class="icon-search"></i></span></button>
                            </p>
                    </form>
            </div>
        </div>
        {include file="$tpl_dir./breadcrumb.tpl"}                            
        <div class="row cont-category">
                <div id="left_column" class="menuSticky column col-lg-3 col-md-3 col-xs-12 col-sm-12">
                    {$HOOK_LEFT_COLUMN}
                    <form class="block"><input class="title_blockSale" type="button" value="Regresar" onclick="history.go(-1)" style="color: #ef4136;font-size: 16px;padding-left: 18px;border: none;background: transparent;"></form>
                </div>
            {if $cms->id==6}
                <div class="col-lg-9 col-md-9 containerFeatured"> 
                    {hook h='newMerchants'}
                </div>
                <div class="col-lg-9 col-md-9 containerFeatured">
                    {hook h='merchants'}
                </div>
            {/if}
            {if $cms->id==6}
                {capture name='blockPosition3'}{hook h='blockPosition3'}{/capture}
                {if $smarty.capture.blockPosition3}
                    {$smarty.capture.blockPosition3}
                {/if}
            {/if}
        </div>   
{elseif isset($cms_category)}
	<div class="block-cms">
		<h1><a href="{if $cms_category->id eq 1}{$base_dir}{else}{$link->getCMSCategoryLink($cms_category->id, $cms_category->link_rewrite)}{/if}">{$cms_category->name|escape:'html':'UTF-8'}</a></h1>
		{if $cms_category->description}
			<p>{$cms_category->description|escape:'html':'UTF-8'}</p>
		{/if}
		{if isset($sub_category) && !empty($sub_category)}	
			<p class="title_block">{l s='List of sub categories in %s:' sprintf=$cms_category->name}</p>
			<ul class="bullet list-group">
				{foreach from=$sub_category item=subcategory}
					<li>
						<a class="list-group-item" href="{$link->getCMSCategoryLink($subcategory.id_cms_category, $subcategory.link_rewrite)|escape:'html':'UTF-8'}">{$subcategory.name|escape:'html':'UTF-8'}</a>
					</li>
				{/foreach}
			</ul>
		{/if}
		{if isset($cms_pages) && !empty($cms_pages)}
		<p class="title_block">{l s='List of pages in %s:' sprintf=$cms_category->name}</p>
			<ul class="bullet list-group">
				{foreach from=$cms_pages item=cmspages}
					<li>
						<a class="list-group-item" href="{$link->getCMSLink($cmspages.id_cms, $cmspages.link_rewrite)|escape:'html':'UTF-8'}">{$cmspages.meta_title|escape:'html':'UTF-8'}</a>
					</li>
				{/foreach}
			</ul>
		{/if}
	</div>
{else}
	<div class="alert alert-danger">
		{l s='This page does not exist.'}
	</div>
{/if}
<br />
{strip}
{if isset($smarty.get.ad) && $smarty.get.ad}
{addJsDefL name=ad}{$base_dir|cat:$smarty.get.ad|escape:'html':'UTF-8'}{/addJsDefL}
{/if}
{if isset($smarty.get.adtoken) && $smarty.get.adtoken}
{addJsDefL name=adtoken}{$smarty.get.adtoken|escape:'html':'UTF-8'}{/addJsDefL}
{/if}
{/strip}

{literal}
    <style>
        .btn-shop{width: 50%;}
        .quick .icon-search:before{display: none;}
        form#searchbox{position: initial !important; top: 0px !important;padding-left: 490px;}
        form#searchbox input#search_query_block{margin-bottom: 0px !important;padding: 18.5px !important; max-width: 380px; margin-right: 0px !important;}
        .button.button-small{background: #c9b198;padding: 11px 26px !important;}
        .menuSticky{margin-top: 0px !important;}
        .block {margin-bottom: 18px;margin-top: 18px;}
        .breadcrumb{border-bottom:1px solid #E9E9E9; padding: 10px 36px;}
        .boxprevnext2 a i{ background: #fff;}
        #search_query_top{left: 0px; margin-top: 65px; z-index: 1;}
        @media (max-width:420px){
            article.sectionBanner{margin-right: 0px !important;}   }         
        @media (max-width:425px){article.sectionBanner{margin-right: 0px !important; padding-left: 0px !important;}
            .sectionFooter{text-align: center; padding-left: 0; margin-right: 0; }
        }
    </style>
{/literal}
{literal}
    <script>
        $('#someid').click(function(e){

            var sw    = $(this).find(".switch"),
                on    = parseInt(sw.css("left")) ? 1 : 0,
                sds   = ['.lside', '.rside'],
                mts   = ['-=18', '+=18'],
                s_on  = sds[on],
                s_off = sds[1-on],
                mt    = mts[on];

            $(this).find(s_off).css("opacity", 1);
            
            sw.stop().animate({left: mt}, 50, function(){
              $(this).find(s_on).css("opacity", 0);
             
              if(s_on=='.lside'){
                  $('.menuSticky').hide("slow");
                  $('.containerFeatured').addClass("containerwidth");
                   $('.containerFeatured').removeClass("containerwidth-column");
              }
              else{
                $('.menuSticky').show("slow");
                $('.containerFeatured').addClass("containerwidth-column");
                $('.containerFeatured').removeClass("containerwidth");
              }
             });
          });
    </script>
{/literal}
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

<!-- Breadcrumb -->
{if isset($smarty.capture.path)}{assign var='path' value=$smarty.capture.path}{/if}
<div class="breadcrumb clearfix">
    <div class="col-lg-5 col-md-5 col-sm-5 col-xs-6">
        <a class="home" href="{if isset($force_ssl) && $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}" title="{l s='Return to Home'}">{l s="INICIO"}</a>
	{if isset($path) AND $path}
		<!--<span class="navigation-pipe"{if isset($category) && isset($category->id_category) && $category->id_category == (int)Configuration::get('PS_ROOT_CATEGORY')} style="display:none;"{/if}>{$navigationPipe|escape:'html':'UTF-8'}</span>-->
		<span class="navigation-pipe"{if isset($category) && isset($category->id_category) && $category->id_category == (int)Configuration::get('PS_ROOT_CATEGORY')} style="display:none;"{/if} style="color:#ef4136;">/</span>
                {if $path|strpos:'span' !== false}
			<span class="navigation_page">{$path|@replace:'<a ': '<span itemscope itemtype="http://data-vocabulary.org/Breadcrumb"><a itemprop="url" '|@replace:'data-gg="">': '><span itemprop="title">'|@replace:'</a>': '</span></a></span>'}</span>
		{else}
			{$path}
		{/if}
	{/if}
    </div>
    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
        <div class="col-lg-6 col-md-6 col-sm-6" style="padding-right:0px;">
            <div class="iOS" id="someid">
                <div class="switch">
                  <span class="outter lside">
                    <span class="otxt"></span>
                  </span>
                  <span class="outter rside">
                    <span class="otxt"></span>
                  </span>
                  <span class="circle"></span>
                </div>
            </div>
        </div>    
        <div class="col-lg-6 col-md-6 col-sm-6 text-filter">{l s="Filtrar"}</div>
    </div>    
</div>
{if isset($smarty.get.search_query) && isset($smarty.get.results) && $smarty.get.results > 1 && isset($smarty.server.HTTP_REFERER)}
<div class="pull-right">
	<strong>
		{capture}{if isset($smarty.get.HTTP_REFERER) && $smarty.get.HTTP_REFERER}{$smarty.get.HTTP_REFERER}{elseif isset($smarty.server.HTTP_REFERER) && $smarty.server.HTTP_REFERER}{$smarty.server.HTTP_REFERER}{/if}{/capture}
		<a href="{$smarty.capture.default|escape:'html':'UTF-8'|secureReferrer|regex_replace:'/[\?|&]content_only=1/':''}" name="back">
			<i class="icon-chevron-left left"></i> {l s='Back to Search results for "%s" (%d other results)' sprintf=[$smarty.get.search_query,$smarty.get.results]}
		</a>
	</strong>
</div>
{/if}
<!-- /Breadcrumb -->

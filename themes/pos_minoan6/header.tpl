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
<!DOCTYPE HTML>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7"{if isset($language_code) && $language_code} lang="{$language_code|escape:'html':'UTF-8'}"{/if}><![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8 ie7"{if isset($language_code) && $language_code} lang="{$language_code|escape:'html':'UTF-8'}"{/if}><![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9 ie8"{if isset($language_code) && $language_code} lang="{$language_code|escape:'html':'UTF-8'}"{/if}><![endif]-->
<!--[if gt IE 8]> <html class="no-js ie9"{if isset($language_code) && $language_code} lang="{$language_code|escape:'html':'UTF-8'}"{/if}><![endif]-->
<html{if isset($language_code) && $language_code} lang="{$language_code|escape:'html':'UTF-8'}"{/if}>
	<head>
		<meta charset="utf-8" />
		<title>{$meta_title|escape:'html':'UTF-8'}</title>
		{if isset($meta_description) AND $meta_description}
			<meta name="description" content="{$meta_description|escape:'html':'UTF-8'}" />
		{/if}
		{if isset($meta_keywords) AND $meta_keywords}
			<meta name="keywords" content="{$meta_keywords|escape:'html':'UTF-8'}" />
		{/if}
		<meta name="generator" content="PrestaShop" />
		<meta name="robots" content="{if isset($nobots)}no{/if}index,{if isset($nofollow) && $nofollow}no{/if}follow" />
		<meta name="viewport" content="width=device-width, minimum-scale=0.25, maximum-scale=1.6, initial-scale=1.0" />
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<link rel="icon" type="image/vnd.microsoft.icon" href="{$favicon_url}?{$img_update_time}" />
		<link rel="shortcut icon" type="image/x-icon" href="{$favicon_url}?{$img_update_time}" />
                
		{if isset($css_files)}
			{foreach from=$css_files key=css_uri item=media}
				<link rel="stylesheet" href="{$css_uri|escape:'html':'UTF-8'}" type="text/css" media="{$media|escape:'html':'UTF-8'}" />
			{/foreach}
		{/if}
		{if isset($js_defer) && !$js_defer && isset($js_files) && isset($js_def)}
			{$js_def}
			{foreach from=$js_files item=js_uri}
			<script type="text/javascript" src="{$js_uri|escape:'html':'UTF-8'}"></script>
			{/foreach}
		{/if}
		<script src="{$js_dir}owl.carousel.js" type="text/javascript"></script>
		{$HOOK_HEADER}
		<link rel="stylesheet" href="http{if Tools::usingSecureMode()}s{/if}://fonts.googleapis.com/css?family=Open+Sans:300,600&amp;subset=latin,latin-ext" type="text/css" media="all" />
		<!--[if IE 8]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
		<![endif]-->
	</head>
	<body{if isset($page_name)} itemscope itemtype="http://schema.org/WebPage" id="{$page_name|escape:'html':'UTF-8'}"{/if} class="{if isset($page_name)}{$page_name|escape:'html':'UTF-8'}{/if}{if isset($body_classes) && $body_classes|@count} {implode value=$body_classes separator=' '}{/if}{if $hide_left_column} hide-left-column{else} show-left-column{/if}{if $hide_right_column} hide-right-column{else} show-right-column{/if}{if isset($content_only) && $content_only} content_only{/if} lang_{$lang_iso}">
            <form id="searchbox" style="display:none;" method="get" action="{$link->getPageLink('search', null, null, null, false, null, true)|escape:'html':'UTF-8'}" >
		<input type="hidden" name="controller" value="search" />
		<input type="hidden" name="orderby" value="position" />
		<input type="hidden" name="orderway" value="desc" />
                <i class="cerrar">X</i>
		<input class="search_query form-control" type="text" id="search_query_top" name="search_query" placeholder="{l s='Search' mod='blocksearch'}" value="{$search_query|escape:'htmlall':'UTF-8'|stripslashes}" />
            </form>
	{if !isset($content_only) || !$content_only}
		{if isset($restricted_country_mode) && $restricted_country_mode}
			<div id="restricted-country">
				<p>{l s='You cannot place a new order from your country.'}{if isset($geolocation_country) && $geolocation_country} <span class="bold">{$geolocation_country|escape:'html':'UTF-8'}</span>{/if}</p>
			</div>
		{/if}
		<div id="page" {if $page_name !="index"} class="sub-page"{/if}>
			<div class="header-container col-xs-12 col-sm-12 col-md-12 col-lg-12">
				<header id="header">
					{capture name='displayBanner'}{hook h='displayBanner'}{/capture}
					{if $smarty.capture.displayBanner}
                                            <div class="banner">
                                                <div class="container">
                                                    <div class="row">
                                                        {$smarty.capture.displayBanner}
                                                    </div>
                                                </div>
                                            </div>
					{/if}
					{capture name='displayNav'}{hook h='displayNav'}{/capture}
					{if $smarty.capture.displayNav}
                                            <div class="nav">
                                                <div class="container">
                                                    <div class="row">
                                                        <nav>{$smarty.capture.displayNav}</nav>
                                                    </div>
                                                </div>
                                            </div>
					{/if}
					
					<div class="header-middle">
                                            <div class="container-fluid">
                                                <div class="row">
                                                    <div class="pos_logo col-md-3 col-lg-3 col-sm-4 col-xs-4">
                                                        <a href="{if isset($force_ssl) && $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}" title="{$shop_name|escape:'html':'UTF-8'}">
                                                            <img class="logo img-responsive" src="{$logo_url}" alt="{$shop_name|escape:'html':'UTF-8'}"{if isset($logo_image_width) && $logo_image_width} width="{$logo_image_width}"{/if}{if isset($logo_image_height) && $logo_image_height} height="{$logo_image_height}"{/if}/>
                                                        </a>
                                                    </div>
                                                    <div class="block-megamenu col-md-6 col-lg-6 col-sm-12 col-xs-12">
                                                        {capture name='megamenu'}{hook h='megamenu'}{/capture}
                                                            {if $smarty.capture.megamenu}
                                                                {$smarty.capture.megamenu}
                                                            {/if}
                                                    </div>
                                                    <div class="col-md-3 col-sm-12 col-lg-3 col-xs-8 hookLeft">
                                                            {if isset($HOOK_TOP)}{$HOOK_TOP}{/if}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                </header>
                        </div>
                    </div>
                    <div class="columns-container">
                                {if $page_name=='my-account'}
                                    <div class="col-lg-12 profileCustomer">
                                        <div class="col-lg-12 contProfile">
                                        <img src="{$img_dir}icon/profile.png" class="imgSponsor2 col-lg-2" />
                                        <span  class="col-lg-2">{$customerProfile}</span>    
                                        <div class="col-lg-2">{l s='Total Points'}<br/>
                                            <span class="ptoCustomer">+{$totalAvailable}</span>
                                            <span style="color:#000;">{displayPrice price=$totalAvailable * (int)Configuration::get('REWARDS_VIRTUAL_VALUE_1')|escape:'html':'UTF-8'}</span>
                                        </div>
                                        <div class="col-lg-2">{l s='Pts. From Last 30 Days'}<br/>
                                            <span class="ptoCustomer">+{$lastPoint}</span>
                                            <span style="color:#000;">{displayPrice price=$lastPoint * (int)Configuration::get('REWARDS_VIRTUAL_VALUE_1')|escape:'html':'UTF-8'}</span>
                                        </div>
                                        </div>
                                    </div>
                                {/if}    
                                
                                {if isset($left_column_size) && !empty($left_column_size)}
                                    <div id="left_column" class="column col-lg-3 col-xs-12 col-sm-{$left_column_size|intval}">{$HOOK_LEFT_COLUMN}
                                                
                                        {if $cms->id==6}
                                            <div class="block"><h2 class="title_blockSale">{l s="Sale"}</h2>
                                            <div id="onSale"> {l s="On Sale"} </div>
                                            </div>
                                            
                                            <!--<div class="block"><h2 class="title_blockSale">{l s="Price"}</h2>
                                                <div data-role="rangeslider">
                                                    <input name="range-1a" id="range-1a" min="0" max="100" value="20" type="range" style="width:100%;"/>
                                                    <!--<input name="range-1b" id="range-1b" min="0" max="100" value="100" type="range" />
                                                </div>
                                            </div>-->
                                                
                                            <div class="redes_sociales">
                                                    <img src="{$img_dir}icon/facebook.png"/>
                                                    <img src="{$img_dir}icon/twitter.png"/>
                                                    <img src="{$img_dir}icon/instagram.png"/>
                                            </div>
                                        {/if}
                                        
                                        {if $cms->id==8 || $cms->id==9}
                                        
                                            {literal}
                                                <style>
                                                    #left_column{display: none !important;}
                                                    .breadcrumb{display: none !important;}
                                                    #center_column{min-width: 100% !important; margin: 0px;}
                                                    #columns{margin-bottom: 0px !important; min-width: 100%;}
                                                    .banner-home{margin: 0px;}
                                                </style>
                                            {/literal}
                                            
                                        {/if}
                                        
                                        
                                    </div>
				{/if}
                                {if isset($left_column_size) && isset($right_column_size)}{assign var='cols' value=(12 - $left_column_size - $right_column_size)}{else}{assign var='cols' value=12}{/if}
						<div  style="background:#fff; padding-left: 0%; padding-right: 0%;" id="center_column" class="center_column col-xs-12 col-sm-{$cols|intval}">
                                                {if $cms->id==6}
                                                <div  style="background:#fff; padding-left: 2%; padding-right: 2%;" id="center_column" class="center_column col-lg-12 col-xs-12 col-sm-{$cols|intval}">
                                                {/if}    
                                {if $page_name =="index"}
					{capture name='blockPosition1'}{hook h='blockPosition1'}{/capture}
					{if $smarty.capture.blockPosition1}
					{$smarty.capture.blockPosition1}
					{/if}

				{/if}
                                {if $cms->id==8}
                                    {literal}
                                        <style>
                                            .breadcrumb .clearfix{display: none;}
                                        </style>  
                                    {/literal}    
                                {/if}
                                <div id="columns" class="container">
					
                                        {if $page_name !='index' && $page_name !='pagenotfound'}
						{include file="$tpl_dir./breadcrumb.tpl"}
					{/if}
					{if $page_name =='category'}
					<div class="banner-category" >
						<div class="ct_img">
							<img class="category_img img-responsive" src="{$link->getCatImageLink($category->link_rewrite, $category->id_image, 'category_default')|escape:'html':'UTF-8'}"/>
							
						</div>
					</div>
					{/if}
					
					<div id="slider_row" class="row">
						{capture name='displayTopColumn'}{hook h='displayTopColumn'}{/capture}
						{if $smarty.capture.displayTopColumn}
							<div id="top_column" class="center_column col-xs-12 col-sm-12">{$smarty.capture.displayTopColumn}</div>
						{/if}
					</div>
							
								
	{/if}

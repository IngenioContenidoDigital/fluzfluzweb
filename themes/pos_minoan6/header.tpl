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
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js" type="text/javascript"></script>
		{$HOOK_HEADER}
		<link rel="stylesheet" href="http{if Tools::usingSecureMode()}s{/if}://fonts.googleapis.com/css?family=Open+Sans:300,600&amp;subset=latin,latin-ext" type="text/css" media="all" />
		<!--[if IE 8]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
		<![endif]-->
                {literal}
                <!– Facebook Pixel Code –>
                    <script>
                    !function(f,b,e,v,n,t,s)
                    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
                    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
                    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
                    n.queue=[];t=b.createElement(e);t.async=!0;
                    t.src=v;s=b.getElementsByTagName(e)[0];
                    s.parentNode.insertBefore(t,s)}(window,document,'script',
                    'https://connect.facebook.net/en_US/fbevents.js');
                     fbq('init', '224432058103743'); 
                    fbq('track', 'PageView');
                    </script>
                    <noscript>
                     <img height="1" width="1" 
                    src="https://www.facebook.com/tr?id=224432058103743&ev=PageView
                    &noscript=1"/>
                    </noscript>
                <!– End Facebook Pixel Code –>
                {/literal}
	</head>
	<body{if isset($page_name)} itemscope itemtype="http://schema.org/WebPage" id="{$page_name|escape:'html':'UTF-8'}"{/if} class="{if isset($page_name)}{$page_name|escape:'html':'UTF-8'}{/if}{if isset($body_classes) && $body_classes|@count} {implode value=$body_classes separator=' '}{/if}{if $hide_left_column} hide-left-column{else} show-left-column{/if}{if $hide_right_column} hide-right-column{else} show-right-column{/if}{if isset($content_only) && $content_only} content_only{/if} lang_{$lang_iso}">
            <form id="searchbox" style="display:none;" method="get" action="{$link->getPageLink('search', null, null, null, false, null, true)|escape:'html':'UTF-8'}" >
		<input type="hidden" name="controller" value="search" />
		<input type="hidden" name="orderby" value="position" />
		<input type="hidden" name="orderway" value="desc" />
                <span class="cerrar">X</span>
                <input class="search_query form-control" type="text" id="search_query_top" name="search_query" placeholder="{l s='Buscar'}..." value="{$search_query|escape:'htmlall':'UTF-8'|stripslashes}" />
            </form>
	{if !isset($content_only) || !$content_only}
		{if isset($restricted_country_mode) && $restricted_country_mode}
			<div id="restricted-country">
				<p>{l s='You cannot place a new order from your country.'}{if isset($geolocation_country) && $geolocation_country} <span class="bold">{$geolocation_country|escape:'html':'UTF-8'}</span>{/if}</p>
			</div>
		{/if}
		<div id="page" {if $page_name !="index"} class="sub-page"{/if}>
			<div class="header-container">
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
                                                <div class="row menu-pay-disabled">
                                                    <div class="pos_logo col-xs-4 col-sm-4 col-md-3 col-lg-3">
                                                            {if $logged}
                                                            <a href="{$link->getPageLink('my-account', true)|escape:'html'}" title="{$shop_name|escape:'html':'UTF-8'}">    
                                                                <img class="logo img-responsive" src="{$logo_url}" alt="{$shop_name|escape:'html':'UTF-8'}"{if isset($logo_image_width) && $logo_image_width} width="{$logo_image_width}"{/if}{if isset($logo_image_height) && $logo_image_height} height="{$logo_image_height}"{/if}/>
                                                            </a>
                                                            {else $logged}
                                                            <a href="{if isset($force_ssl) && $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}" title="{$shop_name|escape:'html':'UTF-8'}">    
                                                                <img class="logo img-responsive" src="{$logo_url}" alt="{$shop_name|escape:'html':'UTF-8'}"{if isset($logo_image_width) && $logo_image_width} width="{$logo_image_width}"{/if}{if isset($logo_image_height) && $logo_image_height} height="{$logo_image_height}"{/if}/>
                                                            </a>
                                                            {/if}
                                                    </div>
                                                    <div class="col-xs-12 col-sm-12 col-md-5 col-lg-6" style="padding-right:0px;">
                                                        {capture name='megamenu'}{hook h='megamenu'}{/capture}
                                                            {if $smarty.capture.megamenu}
                                                                {$smarty.capture.megamenu}
                                                            {/if}
                                                            <div>
                                                                <button class="block_content3 aprueba" type="button"></button>
                                                                <button class="x-close" type="button" style="display:none;"></button>
                                                                <ul class="bullet dropdown-menu menu-down">
                                                                        <li><a href="{$link->getPageLink('my-account', true)|escape:'html'}" title="">{l s='Fluzzers' mod='blockmyaccountheader'}</a></li>
                                                                        <li><a href="http://info.fluzfluz.co/comercios/">{l s='Para Comercios'}</a></li>
                                                                        {if $logged}
                                                                        <li><a href="{$link->getCMSLink('6','categorias')|escape:'html':'UTF-8'}">{l s='Shop Now'}</a></li>
                                                                        {else}
                                                                        <li><a href="{$link->getPageLink('my-account', true)|escape:'html'}" title="">{l s='Shop Now'}</a></li>
                                                                        {/if}
                                                                        <li><a class="account-m">{l s="MI CUENTA"}&nbsp;&nbsp;<i class="icon icon-chevron-down"></i></a></li>
                                                                        <li class="textbox">
                                                                            <form id="searchbox2" method="get" action="{$link->getPageLink('search', null, null, null, false, null, true)|escape:'html':'UTF-8'}" >
                                                                                <i class="icon-search icon-block-searchtoogle"></i></span><input type="text" id="search_query_top2" class="search_query2 form-control" name="search_query" placeholder="{l s='Buscar'}" value="{$search_query|escape:'htmlall':'UTF-8'|stripslashes}" />
                                                                            </form>    
                                                                        </li>
                                                                </ul>
                                                            </div>                
                                                    </div>
                                                    <div class="col-xs-4 col-sm-4 col-md-3 col-lg-3 hookLeft">
                                                            {if isset($HOOK_TOP)}{$HOOK_TOP}{/if}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                </header>
                        </div>
                    </div>               
                                {literal}
                                    <script>
                                        $(".aprueba").click(function(){
                                            $(".menu-down").slideToggle('slow');
                                            $('.block_content3').hide();
                                            $(".x-close").show();
                                        });
                                        
                                        $('.x-close').click(function(){
                                            $('.menu-down').fadeOut('slow');
                                            $('.block_content3').show();
                                            $(".x-close").hide();
                                        })
                                        
                                        $('.account-m').click(function(){
                                           $('.menu-account').show("slow");
                                        });
                                    </script>
                                {/literal}   
                                {if $page_name=='my-account'}
                                    {if $order_lastmonth != ''}
                                        {if $order_lastmonth['alert'] == 4}
                                            <div class="col-lg-12 banner-advertencia">
                                                <p class="p-advertencia">{l s="Tu cuenta ha sido desactivada temporalmente."}</p>
                                            </div>
                                        {/if}

                                        {if $order_lastmonth['alert'] == 3}
                                            <div class="col-lg-12 banner-advertencia">
                                                <p class="p-advertencia">{l s="Has hecho "}{$order_lastmonth['orden']}{" de "}{$order_lastmonth['quantity_max']}{" compras y te estas pasando de la fecha de vencimiento. Si no haces "}{$order_lastmonth['quantity']}{" compras más hasta el ("}{$order_lastmonth['dateCancel']}{") tu cuenta será cancelada!"}<a class="btn-advertencia" href="/content/6-categorias">{l s="Comprar"}</a><i class="icon-remove-circle icon-white close-advertencia"></i></p>
                                            </div>
                                        {/if}

                                        {*if $order_lastmonth['alert'] == 2}
                                            <div class="col-lg-12 banner-advertencia-awesome">
                                                <p class="p-advertencia">{l s="¡Increíble! Tu compra mensual mínima se ha cumplido. "}<a class="btn-advertencia" href="/content/6-categorias">{l s="Comprar"}</a><i class="icon-remove-circle icon-white close-advertencia-awesome"></i></p>
                                            </div>
                                        {/if*}

                                        {if $order_lastmonth['alert'] == 1}
                                            <div class="col-lg-12 banner-advertencia-month blink_me">
                                                <p class="p-advertencia">{l s="Has hecho "}{$order_lastmonth['orden']}{" de "} {$order_lastmonth['total']} {" compras. Necesitaras hacer "}{$order_lastmonth['quantity']}{" compras más hasta el ("}{$order_lastmonth['date']}{") para cubrir tu requisito mensual"}<a class="btn-advertencia" href="/content/6-categorias">{l s="Comprar"}</a><i class="icon-remove-circle icon-white close-advertencia-month"></i></p>
                                            </div>
                                        {/if}
                                    {/if}
                                    
                                    {if $profile_complete < 100 }
                                        <div class="col-lg-12 banner-profile-complete" id="profile-customer" onclick="location.href='{$link->getPageLink('identity', true)|escape:'html':'UTF-8'}';" style="{if $order_lastmonth == '' OR $order_lastmonth['alert'] == 2}margin-top:70px{/if}">
                                            <p class="p-advertencia">Tu perfil de usuario está completado en un {$profile_complete}% 
                                                <progress value="{$profile_complete}" max="100"></progress>
                                                <i class="icon-remove-circle icon-white close-porcentaje"></i>
                                            </p>
                                        </div>
                                    {/if}

                                    <div class="container-fluid profileCustomer" style="{if ($order_lastmonth['alert'] == 0 OR $order_lastmonth['alert'] == 2) AND $profile_complete > 99}margin-top:70px{elseif $profile_complete < 100}margin-top:0px{/if};">
                                        <div class="row" style="width:100%;">
                                                <div class="col-lg-1 col-md-2 col-sm-12 col-xs-12 contProfile" style="padding-left:0px;">
                                                    {if $imgprofile != ""}
                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><img src="{$imgprofile}" width="70" height="70" style="margin: 0 15px;"></div>
                                                    {else}
                                                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><img src="{$img_dir}icon/profile.png" class="imgSponsor2 col-lg-2"></div>
                                                    {/if}
                                                </div>
                                                <div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 nameProfile">
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><span>{$customerProfile}</span></div>   
                                                </div>   
                                                <div class="col-lg-3 col-md-3 col-sm-4 col-xs-6 barTop">
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><span class="totalPoint" style="vertical-align: middle;">{l s='FLUZ TOTALES'}</span><a class="btnCash" href="{$link->getPageLink('cashout', true)|escape:'html':'UTF-8'}" title="{l s='Cash Out'}">{l s='Cash Out'}</a></div>
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                        <span class="ptoCustomer">+{$totalAvailable}</span>
                                                        <span style="color:#000;">{displayPrice price=$totalAvailable * (int)Configuration::get('REWARDS_VIRTUAL_VALUE_1')|escape:'html':'UTF-8'}</span>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 barTop point-querie">
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                        <span style="vertical-align: middle;">{l s='Fluzzers'}</span>
                                                        <a class="btnCash" href="{$link->getPageLink('discount', true)|escape:'html'}" title="{l s='My network' mod='blockmyaccountheader'}" title="{l s='Explore'}">{l s='Explore'}</a>
                                                    </div>
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><span class="ptoCustomer">+{$membersCount}</span></div>
                                                </div>
                                                <div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 barTop">
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">{l s='Pts. From Last 30 Days'}</div>
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                        <span class="ptoCustomer">+{$lastPoint.points}</span>
                                                        <span style="color:#000;">{displayPrice price=$lastPoint.points * (int)Configuration::get('REWARDS_VIRTUAL_VALUE_1')|escape:'html':'UTF-8'}</span>
                                                    </div>    
                                                </div>
                                                 <!--<div class="col-lg-1 col-md-1 col-sm-4 col-xs-6 barTop">    
                                                {*foreach from=$topPoint item=top}
                                               
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                        {l s='Top Performer'}
                                                        <!--<a class="btnCash" href="#" title="{l s='Message'}">{l s='Message'}</a>-->
                                                    </div>
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                        <span class="ptoCustomer" style="text-transform: none;">{$top.points|number_format:0}{l s=" pts."}</span>
                                                        <span style="color:#000;">{$top.username}</span>
                                                    </div>
                                                {/foreach*}
                                                </div>-->
                                                <!--<div class="col-lg-1 col-md-1 col-sm-4 col-xs-6 barTop last-block">
                                                {*foreach from=$worstPoint item=worst}
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                        {l s='Top Worst'}
                                                        <!--<a class="btnCash" href="#" title="{l s='Message'}">{l s='Message'}</a></div>
                                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                                        <span class="ptoCustomer" style="text-transform: none;">{$worst.points|number_format:0}{l s=" pts. "}</span>
                                                        <span style="color:#000;">{$worst.username}</span>
                                                    </div>
                                                {/foreach*}
                                                </div>-->
                                        </div>
                                    </div>
                                {/if}    
                                
                                {if isset($left_column_size) && !empty($left_column_size)}
                                    <!--<div id="left_column" class="menuSticky column col-lg-3 col-md-3 col-xs-12 col-sm-12">-->
                                        {*$HOOK_LEFT_COLUMN*}
                                        <!--<form class="block"><input class="title_blockSale" type="button" value="Regresar" onclick="history.go(-1)" style="color: #ef4136;font-size: 16px;padding-left: 18px;border: none;background: transparent;"></form>-->
                                        <!--<div class="block"><h2 class="title_blockSale">{l s="Price"}</h2>
                                            <div data-role="rangeslider">
                                                <input name="range-1a" id="range-1a" min="0" max="100" value="20" type="range" style="width:100%;"/>
                                                <!--<input name="range-1b" id="range-1b" min="0" max="100" value="100" type="range" />
                                            </div>
                                        </div>-->
                                        {if $cms->id==8 || $cms->id==9}
                                            {literal}
                                                <style>
                                                    #left_column{display: none !important;}
                                                    .breadcrumb{display: none !important;}
                                                    /*#center_column{min-width: 100% !important; margin: 0px;}*/
                                                    #columns{margin-bottom: 0px !important; min-width: 100%;}
                                                    .banner-home{margin: 0px;}
                                                </style>
                                            {/literal}
                                        {/if}
                                    <!--</div>-->
				{/if}
                                {if isset($left_column_size) && isset($right_column_size)}{assign var='cols' value=(12 - $left_column_size - $right_column_size)}{else}{assign var='cols' value=12}{/if}
						<div  style="background:#fff; padding-left: 0%; padding-right: 0%;" id="center_column" class="center_column col-lg-12 col-xs-12 col-md-12 col-sm-12">
                                                    
                                                    {if $page_name =="index"}
					{capture name='blockPosition1'}{hook h='blockPosition1'}{/capture}
					{if $smarty.capture.blockPosition1}
					{$smarty.capture.blockPosition1}
					{/if}

                                                                            {/if}
                                <div id="columns" class="container">
					
                                        {if $page_name !='index' && $page_name !='pagenotfound' && $cms->id != 6 && $page_name != 'category'}
						{include file="$tpl_dir./breadcrumb.tpl"}
					{/if}
                                        {if $category->id_image == '' && $page_name =='category'}
                                            {literal}
                                                <style>
                                                    .ctgImg{display:none !important;}
                                                    .style-search{margin-top: 70px;}
                                                </style>
                                            {/literal}
                                        {/if}    
					{if $page_name =='category'}
					<div class="banner-category" >
						<div class="ct_img">
							<img class="category_img img-responsive ctgImg" src="{$link->getCatImageLink($category->link_rewrite, $category->id_image, 'category_default')|escape:'html':'UTF-8'}"/>
						</div>
					</div>
                                        <div class="row style-search">
                                            <div id="search_block_left" class="block exclusive search-test">
                                                <div class="div-form">
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
                                        </div>    
                                        {include file="$tpl_dir./breadcrumb.tpl"}                                
					{/if}
					
					<div id="slider_row" class="row">
						{capture name='displayTopColumn'}{hook h='displayTopColumn'}{/capture}
						{if $smarty.capture.displayTopColumn}
							<div id="top_column" class="center_column col-xs-12 col-sm-12">{$smarty.capture.displayTopColumn}</div>
						{/if}
					</div>
							
								
	{/if}
  
{literal}
     <script>
        $('.dropdown-menu').find('form').click(function (e) {
            e.stopPropagation();
        });
         //ORIGINAL var stickySidebar = $('.menuSticky');
         var stickySidebar = $('.menuStickyABC123');
                if (stickySidebar.length > 0) {	
                  var stickyHeight = stickySidebar.height(),
                      sidebarTop = stickySidebar.offset().top;
                }

                // on scroll move the sidebar
                $(window).scroll(function () {
                  if (stickySidebar.length > 0) {	
                    var scrollTop = $(window).scrollTop();

                    if (sidebarTop < scrollTop) {
                      stickySidebar.css('top', scrollTop - sidebarTop);

                      // stop the sticky sidebar at the footer to avoid overlapping
                      var sidebarBottom = stickySidebar.offset().top + stickyHeight,
                          stickyStop = $('#center_column').offset().top + $('#center_column').height();
                      if (stickyStop < sidebarBottom) {
                        var stopPosition = $('#center_column').height() - stickyHeight;
                        stickySidebar.css('top', stopPosition);
                      }
                    }
                    else {
                      stickySidebar.css('top', '0');
                    } 
                  }
                });

                $(window).resize(function () {
                  if (stickySidebar.length > 0) {	
                    stickyHeight = stickySidebar.height();
                  }
                });
    </script>
{/literal}
{literal}
    <script>
        $('.close-advertencia').click( function() {
            $('.banner-advertencia').toggle("slow");
            $('.profileCustomer').css('margin-top', '69px');
        });
    </script>
    <script>
        $('.close-advertencia-month').click( function() {
            $('.banner-advertencia-month').toggle("slow");
            $('.profileCustomer').css('margin-top', '69px');
        });
    </script>
    <script>
        $('.close-advertencia-awesome').click( function() {
            $('.banner-advertencia-awesome').toggle("slow");
            $('.profileCustomer').css('margin-top', '25px');
        });
    </script>
    <script>
        $('.close-porcentaje').click( function() {
            $("#profile-customer").prop('onclick',null).off('click');
            $('.banner-profile-complete').toggle("slow");
            $('.profileCustomer').css('margin-top', '69px');
        });
    </script>
{/literal}

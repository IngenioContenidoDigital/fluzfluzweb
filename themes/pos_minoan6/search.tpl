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

{capture name=path}{l s='Search'}{/capture}

<h1
{if isset($instant_search) && $instant_search}id="instant_search_results"{/if}
class="page-heading {if !isset($instant_search) || (isset($instant_search) && !$instant_search)} product-listing{/if}">
    {l s='Search'}&nbsp;
    {if $nbProducts > 0}
        <span class="lighter">
            "{if isset($search_query) && $search_query}{$search_query|escape:'html':'UTF-8'}{elseif $search_tag}{$search_tag|escape:'html':'UTF-8'}{elseif $ref}{$ref|escape:'html':'UTF-8'}{/if}"
        </span>
    {/if}
    {if isset($instant_search) && $instant_search}
        <a href="#" class="close">
            {l s='Return to the previous page'}
        </a>
    {else}
        <span class="heading-counter">
            {if $nbProducts == 1}{l s='%d result has been found.' sprintf=$nbProducts|intval}{else}{l s='%d results have been found.' sprintf=$nbProducts|intval}{/if}
        </span>
    {/if}
</h1>

{include file="$tpl_dir./errors.tpl"}
{if !$nbProducts}
	<p class="alert alert-warning">
		{if isset($search_query) && $search_query}
			{l s='No results were found for your search'}&nbsp;"{if isset($search_query)}{$search_query|escape:'html':'UTF-8'}{/if}"
		{elseif isset($search_tag) && $search_tag}
			{l s='No results were found for your search'}&nbsp;"{$search_tag|escape:'html':'UTF-8'}"
		{else}
			{l s='Please enter a search keyword'}
		{/if}
	</p>
{else}
	{if isset($instant_search) && $instant_search}
        <p class="alert alert-info">
            {if $nbProducts == 1}{l s='%d result has been found.' sprintf=$nbProducts|intval}{else}{l s='%d results have been found.' sprintf=$nbProducts|intval}{/if}
        </p>
    {/if}
    <div class="content_sortPagiBar">
        <div class="sortPagiBar clearfix {if isset($instant_search) && $instant_search} instant_search{/if}">
            {include file="$tpl_dir./product-sort.tpl"}
            {if !isset($instant_search) || (isset($instant_search) && !$instant_search)}
                {include file="./nbr-product-page.tpl"}
            {/if}
        </div>
    	<div class="top-pagination-content clearfix">
            {include file="./product-compare.tpl"}
            {if !isset($instant_search) || (isset($instant_search) && !$instant_search)}
                {include file="$tpl_dir./pagination.tpl" no_follow=1}
            {/if}
        </div>
	</div>
	{include file="$tpl_dir./product-list.tpl" products=$search_products}
    <div class="content_sortPagiBar">
    	<div class="bottom-pagination-content clearfix">
        	{include file="./product-compare.tpl"}
        	{if !isset($instant_search) || (isset($instant_search) && !$instant_search)}
                {include file="$tpl_dir./pagination.tpl" paginationId='bottom' no_follow=1}
            {/if}
        </div>
    </div>
{/if}

{literal}
    <style>
        .pruebaImgCategory{width: 100% !important;}
        .hidden-xs{display: none !important;}
        .divTitleFeatured{margin-top: 10px !important;}
        .top-pagination-content{padding-left: 0px !important;}
        .boxprevnext2 a{margin-top: 10px !important;}
        .owl-item{    width: 278px !important; margin-left: 0px !important; border: 1px solid #CBCBCB !important;}
        .product_list{margin-left: -5px !important;}
        .img-center {position: absolute;min-width: 100%;display: inline-block;margin-top: 35px;}
        .owl-carousel .owl-wrapper-outer {width: 98%;}
        .img-newmerchant {
            width: 210px;
            margin: 0 auto !important;
        }

        .name-merchant {
            text-align: center !important;
            font-size: 12px !important;
            letter-spacing: 1px;
            margin-bottom: 5px;
            text-transform: uppercase;
            font-family: 'Open Sans' !important;
        }
        
        .boxprevnext2{display:none;}
        
        @media (max-width:1024px){
            .owl-item { width: 309px !important;margin-left: 0px !important;}
            .img-center {margin-top: 47px;}
        }
        
        @media (max-width:768px){
            .owl-item {width: 237px !important;margin-left: 11px !important;}
            .title-block div:last-child{font-size: 14px;}
            .divTitleFeatured{margin-left: 10px; width: 97%;}
            .top-pagination-content{padding-left: 10px !important;}
            .img-center {margin-top: 23px;}
            .owl-carousel .owl-wrapper-outer {width: 98%;}
        }
        
        @media (max-width:425px){
            .owl-item {width: 392px !important;margin-left: 15px !important;}
            .divTitleFeatured{width: 95%;}
            .content_sortPagiBar .sortPagiBar{float: left;margin-left: 10px;}
            .img-center {margin-top: 48px;}
            .img-newmerchant {
                width: 310px;
                margin: 0 auto !important;}
            .owl-carousel .owl-wrapper-outer {width: 96%;}
        }
        
        @media (max-width:375px){
            .owl-item {width: 344px !important;margin-left:8px !important;}
            .owl-wrapper{margin-left:0px !important;}
            .img-center {margin-top: 48px;}
            .img-newmerchant {
                width: 270px;
                margin: 0 auto !important;}
        }
        
        @media (max-width:320px){
            .owl-item {width: 302px !important;margin-left: 0px !important;}
            .owl-wrapper{margin-left:5px !important;}
            .divTitleFeatured{width: 97%;margin-left:8px;}
            .title-block div:last-child{font-size: 10px; padding-left: 0px;}
            .img-center {margin-top: 40px;}
            .img-newmerchant {
                width: 230px !important;
                margin: 0 auto !important;}
        }
        
    </style>
{/literal}
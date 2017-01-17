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
{capture name=path}
    <a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
        {l s='My account'}
    </a>
    <span class="navigation-pipe">{$navigationPipe}</span>
    <span class="navigation_page">{l s='Order history'}</span>
{/capture}

{include file="$tpl_dir./errors.tpl"}

<h1 class="page-heading bottom-indent">{l s='Order history'}</h1>
<p class="info-title">{l s='Here are the orders you\'ve placed since your account was created.'}</p>
{if $slowValidation}
	<p class="alert alert-warning">{l s='If you have just placed an order, it may take a few minutes for it to be validated. Please refresh this page if your order is missing.'}</p>
{/if}
<div class="block-center" id="block-history">
    {if $orders && count($orders)}
        {foreach from=$products item=product name=myLoop}
            <div id="button_{$product.id_order}" class="row buttonAccordion" onclick="accordion_display({$product.id_order})"> 
                <div class="col-lg-6 col-md-6 col-sm-6"><span class="col-lg-12 col-md-12 col-sm-12">Numero de Orden: {$product.id_order}</span></div>
                <div class="col-lg-6 col-md-6 col-sm-6"><span class="col-lg-12 col-md-12 col-sm-12">Fecha: {$product.time|date_format:"%D"}</span></div>
            </div>
            <div id="container_{$product.id_order}" class="container_order" style="display:none;">
            <div class="row history_list">
                <div class="row">
                    <div class="col-xs-4 col-sm-4 col-md-2 col-lg-2">
                        <div class="row">
                            <div>{l s='Product'}</div>
                            <div><img id="bigpic" class="img-responsive image-resp" src="{$link->getImageLink($product.link_rewrite, $product.image, 'thickbox_default')}"/></div>
                        </div>
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-2 col-lg-2">
                        <div class="row">
                            <div>{l s='Description'}</div>
                            <div><p class="block-product-name">{$product.manufacturer}<p><br>{l s="Product #: "} {$product.referencia}</div>
                        </div>
                    </div>
                    <div class="col-xs-4 col-sm-4 col-md-2 col-lg-2">
                        <div class="row">
                            <div>{l s='Date'}</div>
                            <div><p>{$product.time}</p></div>
                        </div>
                    </div>
                         <div class="col-xs-4 col-sm-4 col-md-2 col-lg-2 block-right">
                            <div class="row">
                                <div>{l s='Unit Price'}</div>
                                <div>
                                    <p>{l s="Value: "}{displayPrice price=$product.price_shop no_utf8=false convert=false}</p>
                                    <p>{l s="You Save: "}%{math equation='round(((p - r) / p)*100)' p=$product.price_shop r=$product.precio}</p>
                                    <p>{l s="Unit Price: "}{displayPrice price=$product.precio no_utf8=false convert=false}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-2 col-sm-2 col-md-1 col-lg-1 block-center">
                            <div class="row">
                                <div>{l s='Qty'}</div>
                                <div class="block-qty"><p>{$product.cantidad}</p></div>
                            </div>
                        </div>
                        <div class="col-xs-2 col-sm-2 col-md-1 col-lg-1 block-center">
                            <div class="row">
                                <div>{l s='Factura'}</div>
                                <div>
                                    <p><a class="link-button" href="{$link->getPageLink('pdf-invoice', true, NULL, "id_order={$product.id_order}")|escape:'html':'UTF-8'}" title="{l s='Invoice'}" target="_blank">
                                        <i class="icon-file-text large"></i>{l s='PDF'}
                                    </a></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-4 col-sm-4 col-md-2 col-lg-2 block-right">
                            <div class="row">
                                <div>{l s='Total'}</div>
                                <div>
                                    <p class="price">{l s="Price: "}{displayPrice price=$product.total no_utf8=false convert=false}</p>
                                    <p class="block-price">{l s="Card Value: "}{displayPrice price=$product.price_shop no_utf8=false convert=false}</p>
                                </div>
                            </div>
                        </div>
                </div>
                <div class="row">
                    <div class="col-xs-4 col-sm-4 col-md-2 col-lg-2 block-points">{$product.points} {l s="Fluz"}</div>
                    {*<div class="col-xs-4 col-sm-4 col-md-8 col-lg-8 block-cards"><a class="btn_history" href="{$link->getPageLink('cardsview', true, NULL, "id_product={$product.idProduct}&id_order={$product.id_order}")|escape:'html':'UTF-8'}" title="{l s='Card View'}">{l s="Card View >"}</a></div>*}
                    <div class="col-xs-4 col-sm-4 col-md-8 col-lg-8 block-cards"><a  class="btn_history fancybox fancybox.iframe" href="{$link->getPageLink('cardsview', true, NULL, "id_product={$product.idProduct}&id_order={$product.id_order}")|escape:'html':'UTF-8'}" title="{l s='Card View'}">{l s="Card View >"}</a></div>
                    <div class="col-xs-4 col-sm-4 col-md-2 col-lg-2 block-save">{l s="Save: "}%{math equation='round(((p - r) / p)*100)' p=$product.price_shop r=$product.precio}</div>
                </div>
                <br><br>
            </div>
            </div>    
        {/foreach}
    {else}
        <p class="alert alert-warning">{l s='You have not placed any orders.'}</p>
    {/if}
</div>
<ul class="footer_links clearfix">
    <li>
        <a class="btn btn-history" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
            <span>
                <i class="icon-chevron-left"></i> {l s='Back to Your Account'}
            </span>
        </a>
    </li>
    <li>
        <a class="btn btn-history-shop" href="{$base_dir}">
            <span>{l s='Shop Now '}<i class="icon-chevron-right"></i> </span>
        </a>
    </li>
</ul>
{literal}
    <style>
        .table > thead > tr > th{color: #414042; font-family:'Open Sans'; background: #f7f7fb;}
        .info-title{display: none;}
        .page-heading{margin-bottom: 30px !important; padding: 0px !important; letter-spacing: 0px;}
        .breadcrumb{font-size:12px;}
        .footable .footable-sortable .footable-sort-indicator:after{display: none;}
        .table tbody > tr > td{text-align: left;}
        ul.footer_links{margin-bottom: 5% !important; border-top: none !important;}
        p.info-account{margin: 11px 0 24px 0;}
    </style>
{/literal}
{*literal}
    <script>
        function accordion_display(id) {
            
            var esVisible = $('#container_'+id).is(":visible");
                if(esVisible){
                    $('#container_'+id).toggle("slow");
                }
                else {
                    $('.container_order').css('display','none');
                    $('#container_'+id).toggle("slow");
                }
        }
    </script>
{/literal*}
{literal}
    <script>
        if (($(window).width()) <= 768)
        {
            function accordion_display(id) {

                    var esVisible = $('#container_'+id).is(":visible");
                    if(esVisible){
                        //$('#container_'+id).css('display','none');
                        $('#container_'+id).toggle("slow");
                    }
                    else {
                        $('.container_order').css('display','none');
                        $('#container_'+id).toggle("slow");
                    }
                }
        }
        else if (($(window).width()) > 768){
            $('.container_order').show();
            $('.buttonAccordion').hide();
        }
    </script>
{/literal}

<script>
    $(document).ready(function(){
        $(".btn_history").fancybox({
            'width' : '85%'
        });
    });
</script>
<style>
    .fancybox-inner { height: 35vw!important; }
    @media (max-width:1024px){
        .fancybox-inner { height: 50vw!important; }
    }
    @media (max-width:768px){
        .fancybox-inner { height: 80vw!important; }
    }
    @media (max-width:425px){
        .fancybox-inner { height: 120vw!important; }
    }
</style>

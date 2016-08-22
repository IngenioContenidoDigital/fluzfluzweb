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

{capture name=path}{l s='My account'}{/capture}
<h1 class="page-heading col-lg-12 col-md-12 col-sm-12 col-xs-12">{l s='My Gift Cards'}</h1>
<p class="info-account">{l s='View and Redeem your gift card purchases'}</p>
<div class="row">
{foreach from=$manufacturers item=manufacturer}
    <div class="algo myfancybox" href="#myspecialcontent">
    
    <!--<a href="{$link->getPageLink('cardsview', true, NULL, "manufacturer={$manufacturer.id_manufacturer|intval}")|escape:'html':'UTF-8'}">-->
    <div class="col-lg-4 col-md-4 Cards">
        <div class="col-lg-6 col-md-6 col-xs-6 infoCard">
            <img src="{$img_manu_dir}{$manufacturer.id_manufacturer}.jpg" alt="{$manufacturer.manufacturer_name|escape:'htmlall':'UTF-8'}"/>
            <span class="nameCard">{$manufacturer.manufacturer_name}</span>
        </div>
        <div class="col-lg-6 col-md-6 col-xs-6 priceCard">
            <span class="num-Card">{$manufacturer.products}&nbsp;{l s=' Cards'}</span>
            <span class="priceTotalCard">{displayPrice price=$manufacturer.total}</span>
        </div>
    </div>
            <div class="id_manufacturer" id="manufacturer" name="manufacturer">{$manufacturer.id_manufacturer}</div>
   
    </div>
{/foreach}
<div class="col-lg-3 col-md-3 col-sm-12 textAccount">
    <p class="titleFAQ">{l s='Have Question?'}</p>
    <div class="pFAQ">
        <p>{l s='Learn how to redeem digital cards.'}</p>
        <p>{l s='Learn how to transact with merchants.'}</p>
    </div>
</div>
</div>

<h1 class="page-heading col-lg-12 col-md-12 col-sm-12 col-xs-12 efectMargin">{l s='My account'}</h1>
{if isset($account_created)}
	<p class="alert alert-success">
		{l s='Your account has been created.'}
	</p>
{/if}
<p class="info-account">{l s='Welcome to your account. Here you can manage all of your personal information and orders.'}</p>
<div class="row addresses-lists modAccount">
	<div class="col-xs-12 col-md-4 col-sm-5 col-lg-4 account-responsive" style=" padding-left: 0px; padding-right: 0px; margin-right: 1%;">
            <ul class="myaccount-link-list">
            {if $has_customer_an_address}
            <!--<li><a href="{$link->getPageLink('address', true)|escape:'html':'UTF-8'}" title="{l s='Add my first address'}"><i class="icon-building"></i><span>{l s='Add my first address'}</span></a></li>-->
            {/if}
            {if $returnAllowed}
                <li><a href="{$link->getPageLink('order-follow', true)|escape:'html':'UTF-8'}" title="{l s='Merchandise returns'}"><i class="icon-refresh"></i><span>{l s='My merchandise returns'}</span></a></li>
            {/if}
            <!--<li><a href="{$link->getPageLink('order-slip', true)|escape:'html':'UTF-8'}" title="{l s='Credit slips'}"><i class="icon-file-o"></i><span>{l s='My credit slips'}</span></a></li>-->
            <li><a href="{$link->getPageLink('history', true)|escape:'html':'UTF-8'}" title="{l s='Orders'}"><img src="{$img_dir}icon/orderList.png" class="imgSponsor" /><span class="spanSponsor">{l s='Order history and details'}</span></a></li>
            <!--<li><a href="{$link->getPageLink('addresses', true)|escape:'html':'UTF-8'}" title="{l s='Addresses'}"><i class="icon-building"></i><span>{l s='My addresses'}</span></a></li>-->
            <li><a href="{$link->getPageLink('identity', true)|escape:'html':'UTF-8'}" title="{l s='Information'}"><img src="{$img_dir}icon/rewards.png" class="imgSponsor" /><span class="spanSponsor">{l s='My personal information'}</span></a></li>
            <li><a href="{$link->getPageLink('cashout', true)|escape:'html':'UTF-8'}" title="{l s='Cash Out'}"><img src="{$img_dir}icon/exchange.png" class="imgSponsor" /><span class="spanSponsor">{l s='Cash Out You Points'}</span></a></li>
            </ul>
	</div>
{if $voucherAllowed || isset($HOOK_CUSTOMER_ACCOUNT) && $HOOK_CUSTOMER_ACCOUNT !=''}
    <div class="col-xs-12 col-md-4 col-sm-5 col-lg-4" style="padding-left:0px; padding-right: 0px;">
        <ul class="myaccount-link-list">
            {if $voucherAllowed}
                <!--<li><a href="{$link->getPageLink('discount', true)|escape:'html':'UTF-8'}" title="{l s='Vouchers'}"><img src="{$img_dir}icon/network.png" class="imgSponsor" /><span class="spanSponsor">{l s='My Network'}</span></a></li>-->
            {/if}
            {$HOOK_CUSTOMER_ACCOUNT}
            <li><a href="{$link->getPageLink('index', true, NULL, "mylogout")|escape:'html'}" title="{l s='Sign out'}"><img src="{$img_dir}icon/signOut.png" class="imgSponsor" style="padding:0;"/><span class="spanSponsor">{l s='Sign out'}</span></a></li>
        </ul>
        </div>
{/if}
        <div class="col-lg-3 col-sm-12 textAccount">
            <p class="titleFAQ">{l s='Need Support?'}</p>
            <div class="pFAQ">
                <p>{l s='Add a Credit or Debit Card'}</p>
                <p>{l s='Change Email or Password'}</p>
                <p>{l s='Learn About the Network'}</p>
            </div>    
        </div>
</div>
        <div style="display:none;">
        <div id="myspecialcontent" class="infoPopUp">
           {if !$cards}
                <h1>{l s='No hay resultados'}</h1>
           {else}
                <div class='container c'>
                
                </div>
            <div id="pagination" class="pagination">
            {*if $nbpagination < $cards|@count || $cards|@count > 10}
                    <div id="pagination" class="pagination">
                                    {if true || $nbpagination < $cards|@count}
                            <ul class="pagination">
                                            {if $page != 1}
                                            {assign var='p_previous' value=$page-1}
                                    <li id="pagination_previous"><a href="{$pagination_link|escape:'html':'UTF-8'}p={$p_previous|escape:'html':'UTF-8'}&n={$nbpagination|escape:'html':'UTF-8'}">
                                            <img src="{$img_dir}icon/left-arrow.png" style="height:auto; width: 60%; padding: 0; padding-top: 2px; padding-right: 2px;"/></a></li>
                                            {else}
                                    <li id="pagination_previous" class="disabled"><span><img src="{$img_dir}icon/left-arrow.png" style="height:auto; width: 60%; padding: 0; padding-top: 2px; padding-right: 2px;"/></span></li>
                                            {/if}
                                            {if $page > 2}
                                    <li><a href="{$pagination_link|escape:'html':'UTF-8'}p=1&n={$nbpagination|escape:'html':'UTF-8'}">1</a></li>
                                                    {if $page > 3}
                                    <!--<li class="truncate">...</li>-->
                                                    {/if}
                                            {/if}
                                            {section name=pagination start=$page-1 loop=$page+2 step=1}
                                                    {if $page == $smarty.section.pagination.index}
                                    <li class="current"><span>{$page|escape:'html':'UTF-8'}</span></li>
                                                    {elseif $smarty.section.pagination.index > 0 && $cards|@count+$nbpagination > ($smarty.section.pagination.index)*($nbpagination)}
                                    <li><a href="{$pagination_link|escape:'html':'UTF-8'}p={$smarty.section.pagination.index|escape:'html':'UTF-8'}&n={$nbpagination|escape:'html':'UTF-8'}">{$smarty.section.pagination.index|escape:'html':'UTF-8'}</a></li>
                                                    {/if}
                                            {/section}
                                            {if $max_page-$page > 1}
                                                    {if $max_page-$page > 2}
                                    <!--<li class="truncate">...</li>-->
                                                    {/if}
                                    <li><a href="{$pagination_link|escape:'html':'UTF-8'}p={$max_page|escape:'html':'UTF-8'}&n={$nbpagination|escape:'html':'UTF-8'}">{$max_page|escape:'html':'UTF-8'}</a></li>
                                            {/if}
                                            {if $cards|@count > $page * $nbpagination}
                                                    {assign var='p_next' value=$page+1}
                                    <li id="pagination_next"><a href="{$pagination_link|escape:'html':'UTF-8'}p={$p_next|escape:'html':'UTF-8'}&n={$nbpagination|escape:'html':'UTF-8'}"><img src="{$img_dir}icon/right-arrow.png" style="height:auto; width: 60%; padding: 0; padding-top: 2px; padding-right: 2px;"/></a></li>
                                            {else}
                                    <li id="pagination_next" class="disabled"><img src="{$img_dir}icon/right-arrow.png" style="height:auto; width: 60%; padding: 0; padding-top: 2px; padding-right: 2px;"/></li>
                                            {/if}
                            </ul>
                                    {/if}
                    </div>
                {/if*}
            </div>
            <div class="col-lg-6 card-view">
                <div>

                </div>
                <div class="title-card">
                    <img id="img-prod" src="" height="" width="" alt="" class="imgCardView"/><span id="nameViewCard"></span><br/>
                </div>
                <div class="pointPrice">
                        <p class="col-lg-7 col-xs-8 col-md-8 pCode">{l s="Your Gift Card ID is: "}<br><span class="micode" style="font-size:20px; color: #ef4136;"> </span></p>
                        <p class="col-lg-5 col-xs-4 col-md-4 pPrice">{l s="Value: "}<br><span id="priceCard" style="font-size:20px; color: #ef4136;"></span></p>
                </div>
                <div>
                    <img id="bar-code" src=""/><br/>
                    <span class="micode popText" id="code-img"></span>
                </div>
            </div>
            <div class="CardInstru" data-toggle="collapse" data-target="#demo">
                <div><h4 class="insTitle">{l s='Gift Card Instructions'}</h4></div>
                <div class="pViewcard collapse" id="demo"></div>
            </div>
            <div class="CardInstru" data-toggle="collapse" data-target="#terms">
                <div><h4 class="insTitle">{l s='Terms'}</h4></div>
                <div class="terms-card collapse" id="terms"></div>
            </div>
            <div class="containerCard">
                <ul>
                    <li>
                      <input type="radio" id="f-option" name="selector" value="1">
                      <div class="check" id="used"></div>
                      <label id="labelCard" for="f-option">{l s='MARK AS USED'}</label>
                    </li>

                    <li>
                      <input type="radio" id="s-option" name="selector" value="0">
                      <div id="not-used" class="check"></div>
                      <label id="labelCard2" for="s-option">{l s='MARK AS FINISHED'}</label>
                    </li>
                </ul>
            </div>
   
    
            {/if}
        </div>
    </div>    
<ul class="footer_links clearfix" style="display: none;">
<li><a class="btn btn-default button button-small" href="{if isset($force_ssl) && $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}" title="{l s='Home'}"><span><i class="icon-chevron-left"></i> {l s='Home'}</span></a></li>
</ul>
{literal}
    <style>
        .page-heading{margin-bottom: 0px; padding: 0px;letter-spacing: 0px;font-family: 'Open Sans'; font-size: 16px; line-height: 20px;}
        p.info-account{margin: 16px 0 24px 0;}
        
        @media (max-width:420px){
            .imgSponsor2 {width: 33% !important; margin-bottom: 5%;}
            .barTop{margin-bottom: 4%;}
        }
    </style>
{/literal}    
{literal}
    <script>
        $('.algo').click(function() {
            var id_manu = $(this).find(".id_manufacturer").html();
            var id_cust = {/literal}{$profile}{literal};

            $.ajax({
                    method:"POST",
                    data: {'action': 'getCardsbySupplier','id_manu': id_manu, 'profile':id_cust},
                    url: '/cardsSupplier.php', 
                    success:function(response){
                        var x = jQuery.parseJSON(response);
                        var content = '';
                        for (var i=0;i<x.length;i++){
                          
            content += '<a class="myfanc" href="#myspecialcontent">'+
                    '<div class="card"><img class="col-lg-4 col-md-3 col-sm-3 col-xs-3" src="/img/m/'+x[i].id_manufacturer+'.jpg" width="40px" height="40px"/>'+
                    '<div class="col-lg-6 col-md-7 col-sm-5 col-xs-8 codigoCard"><span style="color: #000;">Tarjeta: </span><span class="codeImg">'+x[i].card_code+'</span></div>'+
                    '<div class="oculto">/img/m/'+x[i].id_manufacturer+'.jpg</div>'+
                '</div>'+
            '</a>'+
            '<div id="pOculto">'+x[i].price+'</div>'+
            '<div id="desc_oculto">'+x[i].description+'</div>'+
            '<div id="prodid_oculto">'+x[i].id_product+'</div>'+
            '<div id="nameOculto">'+x[i].product_name+'</div>';
                    if (i%2 != 0){
                        content+='<br /><br/>';
                    }
                        
                    }
                    $('.c').html(content)
                    $('#myspecialcontent').parent().show();
              }});
        });
        
        $('.c').on("click",".myfanc",function(){
            var codeImg2 = $(this).find(".codeImg").html();
            var price = document.getElementById("pOculto").innerHTML;
            var name = document.getElementById("nameOculto").innerHTML;
            var description = document.getElementById("desc_oculto").innerHTML;
            var idproduct = document.getElementById("prodid_oculto").innerHTML;
            var ruta = $(this).before().find(".oculto").html();
            $("#img-prod").attr("src",ruta)
            $.ajax({
                    method:"POST",
                    data: {'action': 'consultcodebar', 'codeImg2': codeImg2,'price':price,'idproduct':idproduct},
                    url: '/raizBarcode.php', 
                    success:function(response){
                        var response = jQuery.parseJSON(response);
                        if (response.used == 1) {
                           $('#labelCard').addClass('labelcard');
                           $('#used').addClass('checkConfirm');
                           $('#not-used').removeClass('checkConfirm');
                           $('#labelCard2').removeClass('labelcard');
                        } else {
                           $('#labelCard2').addClass('labelcard');
                           $('#labelCard').removeClass('labelcard');
                           $('#not-used').addClass('checkConfirm');
                           $('#used').removeClass('checkConfirm')
                        }
                        
                        if ( response.codetype == 0 ) {
                            $('#bar-code').attr('src','.'+response.code);
                            $('.pointPrice').css("float","left").css("width","50%").css("padding","10px 0 0 10px");
                            $('.pCode').addClass("col-lg-12").addClass("col-xs-12").addClass("col-md-12");
                            $('.pPrice').addClass("col-lg-12").addClass("col-xs-12").addClass("col-md-12");
                            $('#bar-code').parent().css("float","right");
                            $('#bar-code').parent().css("margin-right","10%");
                            $('.popText').css("font-size","14px");
                        }
                        if ( response.codetype == 1 ) {
                            $('#bar-code').attr('src','.'+response.code);
                        }
                        if ( response.codetype == 2 ) {
                            $('.popText').parent().css("margin-top","50px");
                            $('.popText').css("background","none");
                            $('.popText').css("color","#fff");
                        }
                        
                        $('.micode').html(codeImg2);
                        $('#priceCard').html(price);
                        $('#nameViewCard').html(name);
                        $('.pViewcard').html(description);
                    }
              });
        });
        
        
        $('#used').click(function(){
            $(this).addClass('checkConfirm');
            $('#labelCard').addClass('labelcard');
            $('#labelCard2').removeClass('labelcard');
            $('#not-used').removeClass('checkConfirm');
        });
        
        $('#labelCard').click(function(){
            $(this).addClass('labelcard');
            $('#used').addClass('checkConfirm');
            $('#not-used').removeClass('checkConfirm');
            $('#labelCard2').removeClass('labelcard');
        
        });
        
        $('#labelCard2').click(function(){
            $(this).addClass('labelcard');
            $('#labelCard').removeClass('labelcard');
            $('#not-used').addClass('checkConfirm');
            $('#used').removeClass('checkConfirm')
            
        });
        
        $('#not-used').click(function(){
            $(this).addClass('checkConfirm');
            $('#labelCard2').addClass('labelcard');
            $('#labelCard').removeClass('labelcard');
            $('#used').removeClass('checkConfirm');
        });
        
    </script>
{/literal}

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
            <li><a href="{$link->getPageLink('discount', true)|escape:'html':'UTF-8'}" title="{l s='Vouchers'}"><img src="{$img_dir}icon/network.png" class="imgSponsor" /><span class="spanSponsor">{l s='My Network'}</span></a></li>
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
<div>
    <h1 class="page-heading">
        {l s='My Messaging'}
    </h1>

    <form action="{$link->getPageLink('discount', true)|escape:'html':'UTF-8'}" method="post" id="formnetwork">
        <div class="blockcontainer">
            <div class="block-network">
                <table class="tablenetwork">
                    {foreach from=$members item=member}
                        <tr>
                            <td>
                                <table class="tablecontent">
                                    <tr>
                                        <td rowspan="2" class="img">
                                            {if $member.img != ""}
                                                <img src="{$member.img}" width="50" height="50" style="margin-left: 5px;">
                                            {else}
                                                <img src="{$img_dir}icon/profile.png" width="55" height="50">
                                            {/if}
                                        </td>
                                        <td colspan="3" class="line"><span class="name">{$member.name}</span></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td><span class="information">{l s='Points Contributed:'} </span><span class="data">{if $member.points != ""}{$member.points}{else}0{/if}</span></td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    {/foreach}
                </table>
            </div>
            {*<div class="block-messages">
                <h2>{l s='My Messages'}</h2>
                <input type="text" name="searchmessage" id="searchmessage" class="textsearch" placeholder="Search member"><img class="searchimg" src="/themes/pos_minoan6/css/modules/blocksearch/search.png" title="Search" alt="Search" height="15" width="15">
            </div>*}
        </div>
    </form>
</div>
<div id="not-shown" style="display:none;">
        <div id="myspecialcontent" class="infoPopUp">
            <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 available_cards">{l s='Tarjetas Disponibles: '}<span class="avail"></span></div>
            <div class="div-state col-xs-12 col-sm-12 col-md-6 col-lg-6">
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 v"><div class="la-verde"></div><div class="state-card">{l s="Disponible"}</div></div>
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 a"><div class="la-amarilla"></div><div class="state-card">{l s="Usada"}</div></div>
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 r"><div class="la-roja"></div><div class="state-card">{l s="Terminada"}</div></div>
            </div>
            <br>
            <div class='col-xs-12 col-sm-12 col-md-12 col-lg-12 c'></div>    
            <div class="row">
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 card-view">
                    <div class="row">
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                            <p class="pValuePrice">{l s="Valor Original: "}<span class="price_value_content"></span></p>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                            <p class="pDate">{l s="Compra: "}<span class="date_purchased"></span></p>
                        </div>
                    </div>
                    <div class="row title-card" style="display: flex;align-items: center;">
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6" >
                            <img id="img-prod" src="" alt="" class="imgCardView img-responsive"/>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                            <div id="nameViewCard"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                            <div class="pCode">{l s="Your Gift Card ID is: "}</div><div class="micode">></div>
                            <div class="pPrice">{l s="Value: "}</div><div id="priceCard"></div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                            <p style="text-align: center;"><img id="bar-code" class="img-responsive" src=""/></p>
                            <p style="text-align: center;" class="micode popText" id="code-img"></p>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
                    <div class="CardInstru" data-toggle="collapse" data-target="#demo">
                        <div><h4 class="insTitle">{l s='Gift Card Instructions'}</h4></div>
                        <div class="pViewcard collapse" id="demo"></div>
                    </div>
                    <div class="CardInstru" data-toggle="collapse" data-target="#terms">
                        <div><h4 class="insTitle">{l s='Terms'}</h4></div>
                        <div class="terms-card collapse" id="terms"></div>
                    </div>  
                </div>
            </div>
            <br>
            <div class="row">
                <div class="containerCard">
                <ul>
                    <li>
                      <input type="radio" id="f-option" name="selector" value="1">
                      <div class="check" id="used"></div>
                      <label id="labelCard" for="f-option">{l s='MARK AS USED'}</label>
                    </li>
                    <li>
                      <input type="radio" id="s-option" name="selector" value="2">
                      <div id="not-used" class="check"></div>
                      <label id="labelCard2" for="s-option">{l s='MARK AS FINISHED'}</label>
                    </li>
                </ul>
                </div>
            </div>
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
        
        
        function renderCard(codeImg21, price1,priceValue1, dateP1, name1, description1,terms1,idproduct1,ruta1){           
            var codeImg2 = codeImg21;
            var price = price1;
            var priceValue = priceValue1;
            var dateP = dateP1;
            var name = name1;
            var description = description1;
            var terms = terms1;
            var idproduct = idproduct1;
            var ruta = ruta1;
            $("#img-prod").attr("src",ruta);
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
                        } else if(response.used == 2){
                           $('#labelCard2').addClass('labelcard');
                           $('#labelCard').removeClass('labelcard');
                           $('#not-used').addClass('checkConfirm');
                           $('#used').removeClass('checkConfirm');
                        }
                        else if(response.used == 0){
                           $('#labelCard2').removeClass('labelcard');
                           $('#labelCard').removeClass('labelcard');
                           $('#not-used').removeClass('checkConfirm');
                           $('#used').removeClass('checkConfirm');
                        }
                        
                        if ( response.codetype == 0 ) {
                            $('#bar-code').attr('src','.'+response.code);
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
                        $('.terms-card').html(terms);
                        $('.price_value_content').html(priceValue);
                        $('.date_purchased').html(dateP);
                    }
        })}
        
        $('.v').on('click',function(){
            $('.myfanc').each(function(){
                var card= $(this).children('.card');
                var ocul= card.children('.used-oculto');
                if(ocul.children().attr('class')!='la-verde'){
                    $(this).fadeOut("slow");
                }else{
                    $(this).fadeIn("slow");
                }
            });
        });
        $('.a').on('click',function(){
            $('.myfanc').each(function(){
                var card= $(this).children('.card');
                var ocul= card.children('.used-oculto');
                if(ocul.children().attr('class')!='la-amarilla'){
                    $(this).fadeOut("slow");
                }else{
                    $(this).fadeIn("slow");
                }
            });
        });
        $('.r').on('click',function(){
           $('.myfanc').each(function(){
                var card= $(this).children('.card');
                var ocul= card.children('.used-oculto')
                if(ocul.children().attr('class')!='la-roja'){
                    $(this).fadeOut("slow");
                }else{
                    $(this).fadeIn("slow");
                }
            });
        });
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
                        //var fecha = x[0].date;
                        for (var i=0;i<x.length;i++){
                          var fecha = x[i].date;
            content += '<a class="col-xs-12 col-sm-12 col-md-6 col-lg-6 myfanc" href="#myspecialcontent">'+
                    '<div class="card">'+
                        '<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 used-oculto">'+x[i].used+'</div>'+
                        '<div class="col-xs-5 col-sm-5 col-md-5 col-lg-5"><img src="/img/m/'+x[i].id_manufacturer+'.jpg" height="37px"/></div>'+
                        '<div class="col-xs-5 col-sm-5 col-md-5 col-lg-5 codigoCard"><span style="color: #000;">Tarjeta: </span><span class="codeImg">'+x[i].card_code+'</span></div>'+
                    '<div class="oculto">/img/m/'+x[i].id_manufacturer+'.jpg</div>'+
                    '</div>'+
                    '<div id="pOculto">'+Math.round(x[i].price)+'</div>'+
                    '<div id="desc_oculto">'+x[i].description_short+'</div>'+
                    '<div id="terms_oculto">'+x[i].description+'</div>'+
                    '<div id="prodid_oculto">'+x[i].id_product+'</div>'+
                    '<div id="price_value">'+Math.round(x[i].price_value)+'</div>'+
                    '<div id="date">'+fecha.substring(0,10)+'</div>'+
                    '<div id="nameOculto">'+x[i].product_name+'</div>'+
                    '</a>';
                    }
                    $('.c').html(content)
                    var avail=0;
                    $('.used-oculto').each(function(){
                        var estado = $(this).html();
                        switch (estado){
                            case '0':
                                $(this).html('<div class="la-verde"></div>');
                                break;
                            case '1':
                                $(this).html('<div class="la-amarilla"></div>');
                                break;
                            case '2':
                                $(this).html('<div class="la-roja"></div>');
                                break;
                        }
                    });
                    $('.avail').html(avail);
                    renderCard(x[0].card_code, Math.round(x[0].price), Math.round(x[0].price_value), fecha.substring(0,10),x[0].product_name,x[0].description_short,x[0].description,x[0].id_product,'/img/m/'+x[0].id_manufacturer+'.jpg');
                    $('#myspecialcontent').parent().show();
              }});
        });
        
        $('.c').on("click",".myfanc",function(){
            var codeImg2 = $(this).find(".codeImg").html();
            var price = document.getElementById("pOculto").innerHTML;
            var priceValue = document.getElementById("price_value").innerHTML;
            var dateP = document.getElementById("date").innerHTML;
            var name = document.getElementById("nameOculto").innerHTML;
            var description = document.getElementById("desc_oculto").innerHTML;
            var terms = document.getElementById("terms_oculto").innerHTML;
            var idproduct = document.getElementById("prodid_oculto").innerHTML;
            var ruta = $(this).before().find(".oculto").html();
            renderCard(codeImg2,price,priceValue, dateP, name,description, terms, idproduct, ruta);
        });
        
        
        $('#used').click(function(){
            var code = $('.micode').html();
            $('.codeImg').each(function(){
                var compare = $(this).html();
                if(compare==code){
                    var algo = $(this).parent().parent().children('.used-oculto');
                    if($('#used').hasClass('checkConfirm')){
                        algo.html('<div class="la-verde"></div>');
                        $('#used').removeClass('checkConfirm');
                        $('#labelCard').removeClass('labelcard');
                        $('#labelCard2').removeClass('labelcard');
                        $('#not-used').removeClass('checkConfirm');
                    }else{
                        algo.html('<div class="la-amarilla"></div>');
                        $('#used').addClass('checkConfirm');
                        $('#labelCard').addClass('labelcard');
                        $('#labelCard2').removeClass('labelcard');
                        $('#not-used').removeClass('checkConfirm');
                    }
                }
            });
        });
        
        $('#not-used').click(function(){
            var code = $('.micode').html();
            $('.codeImg').each(function(){
                var compare = $(this).html();
                if(compare==code){
                    var algo = $(this).parent().parent().children('.used-oculto');
                    if($('#not-used').hasClass('checkConfirm')){
                        algo.html('<div class="la-verde"></div>');
                        $('#not-used').removeClass('checkConfirm');
                        $('#labelCard').removeClass('labelcard');
                        $('#labelCard2').removeClass('labelcard');
                        $('#used').removeClass('checkConfirm');
                    }else{
                        algo.html('<div class="la-roja"></div>');
                        $('#not-used').addClass('checkConfirm');
                        $('#labelCard').addClass('labelcard');
                        $('#labelCard2').removeClass('labelcard');
                        $('#used').removeClass('checkConfirm');
                    }
                }/*else{
                    $(this).parent().parent().parent().hide();
                }*/
            });
        });
        
        $('.containerCard').on("click",'input:radio[name=selector]',function()
        {
            var val = $('input:radio[name=selector]:checked').val();
            var idproduct = document.getElementById("prodid_oculto").innerHTML;
            var codeImg2 = document.getElementById("code-img").innerHTML;
            $.ajax({
                    method:"POST",
                    data: {'action': 'updateUsed','val': val, 'codeImg2': codeImg2,'idproduct':idproduct},
                    url: '/raizBarcode.php'
              });
        });
    </script>
{/literal}

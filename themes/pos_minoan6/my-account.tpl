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
<div class="row page-heading">
    <h1 class="page-heading-2 col-lg-6 col-md-6 col-sm-6 col-xs-6">mis c&oacute;digos</h1>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-min">
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 btn-design">
            <a class="btn btn-default btn-account" href="/content/6-categorias">Comprar C&oacute;digos</a>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 btn-reload">
            <a class="btn btn-default btn-account btn-design-r" href="/inicio/485-precarga-de-saldo-fluzfluz.html">Recargar Fluz</a>
        </div>
    </div>        
</div>
<div class="row">
    <p class="col-lg-12 col-md-12 col-sm-12 col-xs-12 info-account">{l s='View and Redeem your gift card purchases'}</p>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-10 col-md-10 col-sm-12 card-st" id="card-div">
            {foreach from=$manufacturers item=manufacturer}
                <div class="col-lg-5 col-md-5 col-sm-6 col-xs-12 Cards fancybox fancybox.iframe" title="{$manufacturer.manufacturer_name}" href="{$link->getPageLink('wallet', true, null, "manufacturer={$manufacturer.id_manufacturer}")|escape:'html':'UTF-8'}">
                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 infoCard">
                        <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style=" padding-right: 0px; padding-left: 0px;"><img src="{$img_manu_dir}{$manufacturer.id_manufacturer}.jpg" alt="{$manufacturer.manufacturer_name|escape:'htmlall':'UTF-8'}"/></div>
                        <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 nameCard"><span>{$manufacturer.manufacturer_name|truncate:20:"...":true}</span></div>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4 priceCard">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style=" padding-right: 0px; padding-left: 0px;"><span class="num-Card">{$manufacturer.products}&nbsp; C&oacute;digos</span></div>
                        <!--<div class="col-lg-6 col-md-12 col-sm-6 col-xs-7"  style=" padding-right: 0px; padding-left: 0px;"><span class="priceTotalCard">{displayPrice price=$manufacturer.total}</span></div>-->
                    </div>
                </div>
            {/foreach}
            <button id="loadMore" class="col-lg-10 col-md-10 col-sm-12 btn-more"><span class="pmore">{l s="Mostrar mas"}</span><i id="boton-carga-card" class="icon-refresh icon-white"></i></button>
        </div>
        <div class="col-lg-2 col-md-2 col-sm-11 col-xs-12 textAccount">
            <p class="titleFAQ">{l s='Have Question?'}</p>
            <div class="detail-support">
                <a href="http://reglas.fluzfluz.co"><p style="font-family: 'Capitalized';font-weight: bold;">Aprende c&oacute;mo redimir tus c&oacute;digos</p></a>
                <a class="btn btn-default btn-account" href="http://reglas.fluzfluz.co"><span style="cursor:pointer;font-size: 15px;color: #fff; font-family: 'Capitalized';font-weight: bold;">{l s="Preguntas Frecuentes"}</span></a>
            </div>
        </div>
    </div>
</div>

<h1 class="page-heading col-lg-12 col-md-12 col-sm-12 col-xs-12 efectMargin">{l s='My account'}</h1>
{if isset($account_created)}
	<p class="alert alert-success">
		{l s='Your account has been created.'}
	</p>
{/if}
<p class="col-lg-12 info-account">{l s='Welcome to your account. Here you can manage all of your personal information and orders.'}</p>
<div class="row addresses-lists modAccount">
    <div class="col-xs-12 col-md-4 col-sm-5 col-lg-4 account-responsive" style=" padding-left: 0px; padding-right: 0px;">
        <ul class="myaccount-link-list">
        {if $has_customer_an_address}
        <!--<li><a href="{$link->getPageLink('address', true)|escape:'html':'UTF-8'}" title="{l s='Add my first address'}"><i class="icon-building"></i><span>{l s='Add my first address'}</span></a></li>-->
        {/if}
        {if $returnAllowed}
            <li><a href="{$link->getPageLink('order-follow', true)|escape:'html':'UTF-8'}" title="{l s='Merchandise returns'}"><i id="boton-carga" class="icon-refresh"></i><span>{l s='My merchandise returns'}</span></a></li>
        {/if}
        <!--<li><a href="{$link->getPageLink('order-slip', true)|escape:'html':'UTF-8'}" title="{l s='Credit slips'}"><i class="icon-file-o"></i><span>{l s='My credit slips'}</span></a></li>-->
        <li><a href="{$link->getPageLink('history', true)|escape:'html':'UTF-8'}" title="{l s='Orders'}"><img src="{$img_dir}icon/orderList.png" class="imgSponsor" /><span class="spanSponsor">{l s='Historial de Compras'}</span></a></li>
        <!--<li><a href="{$link->getPageLink('addresses', true)|escape:'html':'UTF-8'}" title="{l s='Addresses'}"><i class="icon-building"></i><span>{l s='My addresses'}</span></a></li>-->
        <li><a href="{$link->getPageLink('identity', true)|escape:'html':'UTF-8'}" title="{l s='Information'}"><img src="{$img_dir}icon/rewards.png" class="imgSponsor" /><span class="spanSponsor">{l s='My personal information'}</span></a></li>
        {foreach from=$grupo item=group}
            {if $group.id_group == 4}
                <li><a href="{$link->getPageLink('cashout', true)|escape:'html':'UTF-8'}" title="{l s='Cash Out'}"><img src="{$img_dir}icon/exchange.png" class="imgSponsor" /><span class="spanSponsor">{l s=' Redimir tus Fluz en efectivo'}</span></a></li>
                <li><a href="{$link->getPageLink('stateaccount', true)|escape:'html':'UTF-8'}" title="{l s='Cash Out'}"><img src="{$img_dir}icon/statics.png" class="imgSponsor" /><span class="spanSponsor">{l s=' Estado de Cuenta'}</span></a></li>
                <li><a href="{$link->getPageLink('transferfluz', true)|escape:'html':'UTF-8'}" title="{l s='Cash Out'}"><img src="{$img_dir}icon/exchange.png" class="imgSponsor" /><span class="spanSponsor">{l s=' Transferencias Fluz a Fluzzer'}</span></a></li>
            {/if}
        {/foreach}    
        </ul>
    </div>
{if $voucherAllowed || isset($HOOK_CUSTOMER_ACCOUNT) && $HOOK_CUSTOMER_ACCOUNT !=''}
    <div class="col-xs-12 col-md-4 col-sm-6 col-lg-4 block_b" style="padding-left:0px; padding-right: 0px;">
        <ul class="myaccount-link-list">
            <li><a href="{$link->getPageLink('discount', true)|escape:'html':'UTF-8'}" title="{l s='Vouchers'}"><img src="{$img_dir}icon/network.png" class="imgSponsor" /><span class="spanSponsor">{l s='Mi Network Completo'}</span></a></li>
            {$HOOK_CUSTOMER_ACCOUNT}
            {foreach from=$grupo item=group}
                {if $group.id_group == 5}
                    <li><a href="{$link->getPageLink('business', true)|escape:'html':'UTF-8'}" title="{l s='Business'}"><img src="{$img_dir}icon/network.png" class="imgSponsor" /><span class="spanSponsor">{l s='Panel de la Empresa'}</span></a></li>
                {/if}
            {/foreach}    
            <li><a href="{$link->getPageLink('index', true, NULL, "mylogout")|escape:'html'}" title="{l s='Sign out'}"><img src="{$img_dir}icon/signOut.png" class="imgSponsor" style="padding:0;"/><span class="spanSponsor">{l s='Sign out'}</span></a></li>
        </ul>
    </div>
{/if}
    <div class="col-lg-3 col-md-3 col-sm-11 col-xs-12 textAccount2">
        <p class="titleFAQ">{l s='Need Support?'}</p>
        <div class="detail-support">
            <a href="http://reglas.fluzfluz.co"><p style="font-family: 'Capitalized';font-weight: bold;">{l s='Add a Credit or Debit Card'}</p></a>
            <a href="http://reglas.fluzfluz.co"><p style="font-family: 'Capitalized';font-weight: bold;">{l s='Change Email or Password'}</p></a>
            <a href="http://reglas.fluzfluz.co"><p style="font-family: 'Capitalized';font-weight: bold;">{l s='Learn About the Network'}</p></a>
        </div>    
    </div>
</div>
<div class="row networkfeed" style="margin-top: 70px;">
    <h1 class="page-heading">
        {l s='My Network Feed'}
    </h1>
    {if $last_shopping_products}
        {foreach from=$last_shopping_products item=last_shopping_product}
            <div class="col-xs-12 col-md-4 col-sm-6 col-lg-4 last_shop container1 account-responsive">
                <div class="row">
                    {assign var="link_rewrite" value=$last_shopping_product.link_rewrite}
                    {assign var="id_product" value=$last_shopping_product.id_product}
                    {assign var="name_product" value=$last_shopping_product.name_product}
                    {assign var="img" value=$last_shopping_product.img}
                    {assign var="credits" value=$last_shopping_product.credits}
                    {assign var="name_customer" value=$last_shopping_product.name_customer}
                    {assign var="price" value=$last_shopping_product.price}
                    {assign var="id_customer_sponsor" value=$last_shopping_product.id_customer}
                    {assign var="id_image" value=$last_shopping_product.id_image}
                    <div class="col-xs-4 col-md-4 col-sm-4 col-lg-4 containerimgprod">
                        <div class="img-center">
                            <div class="logo-manufacturer">
                                <a class="product_img_link" href="{$link->getProductLink($id_product, $link_rewrite)|escape:'html':'UTF-8'}" title="{$name_product|escape:'html':'UTF-8'}" itemprop="url">
                                    <img src="{$s3}m/{$last_shopping_product.id_manufacturer}.jpg" alt="{$last_shopping_product.name_product|lower|escape:'htmlall':'UTF-8'}" title="{$last_shopping_product.name_product|lower|escape:'htmlall':'UTF-8'}" class="img-responsive" style="max-width:100% !important;"/>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-8 col-md-8 col-sm-8 col-lg-8 containerinfor">
                        <div class="row">
                            <div class="col-xs-2 col-md-2 col-sm-2 col-lg-2 containerimgcustom">
                                {assign var="urlimgnetfeed" value=""}
                                {if $img != ""}
                                    <img src="{$img}" width="100%" height="100%">
                                    {$urlimgnetfeed = {$img}}
                                {else}
                                    <img src="{$img_dir}icon/profile.png" width="100%" height="100%">
                                    {$urlimgnetfeed = $img_dir|cat:"icon/profile.png"}
                                {/if}
                            </div>
                            <div class="col-xs-10 col-md-10 col-sm-10 col-lg-10 pointstitlemnf">
                                <span style="font-size:11px;">{l s='Fluz Ganados: '}</span><span class="pointsmnf">&nbsp;{$credits|number_format:0:".":","}</span>
                            </div>
                        </div>
                        <div class="row" style="padding: 5px;">
                            <div class="col-xs-8 col-md-9 col-sm-9 col-lg-9 containerpurchase">
                                {$name_customer} {l s='has purchased a'} {convertPrice price=$price} {l s='in'} 
                                <span class="pointsmnf">
                                 <a class="product_img_link" href="{if $merchant.category != "" && $merchant.category != 0}{$link->getCategoryLink({$merchant.category})}{else}#{/if}" title="{$merchant.name|lower|escape:'htmlall':'UTF-8'}" itemprop="url">
                                     {$name_product}</a>
                                </span>.
                            </div>
                            <div class="col-xs-3 col-md-3 col-sm-3 col-lg-3 message">
                                <span class="myfancybox" href="#myspecialcontent2" send="{$id_customer_sponsor}|{$name_customer}|{$urlimgnetfeed}|{$id_customer}">{l s='Mensaje'}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        {/foreach}
        <button id="loadMoreNet" class="col-lg-8 col-md-8 col-sm-8 btn-more2"><span class="pmore">{l s="Mostrar mas"}</span><i id="boton-carganet" class="icon-refresh icon-white"></i></button>
    {/if}
    <div class="col-lg-3 col-md-3 col-sm-11 col-xs-12 textAccount2">
        <p class="titleFAQ">{l s='Need Support?'}</p>
        <div class="detail-support">
            <a href="http://reglas.fluzfluz.co" target="_blank"><p>{l s='Learn about your network'}</p></a>
        </div>    
    </div>
</div>
<div class="row">
    <h1 class="page-heading">
        {l s='My Messaging'}
    </h1>
    <form action="{$link->getPageLink('discount', true)|escape:'html':'UTF-8'}" method="post" id="formnetwork">
            <div class="col-lg-4 col-md-5 col-sm-6 col-xs-11 block-r">
                <h2 class="title-msj">{l s='My Network'}</h2>
                <div class="containtertables">
                    <div class="tablenetwork">
                        {foreach from=$members item=member}
                            <div class="member">
                                <td>
                                    <table class="tablecontent">
                                        <tr>
                                            <td rowspan="2" class="img">
                                                {assign var="urlimgnet" value=""}
                                                {if $member.img != ""}
                                                    <img src="{$member.img}" width="50" height="50" style="margin-left: 5px;">
                                                    {$urlimgnet = $member.img}
                                                {else}
                                                    <img src="{$img_dir}icon/profile.png" width="55" height="50">
                                                    {$urlimgnet = $img_dir|cat:"icon/profile.png"}
                                                {/if}
                                            </td>
                                            <td colspan="2" class="line colname"><span class="name">{$member.username}</span></td>
                                            <td class="message line"><span class="myfancybox" href="#myspecialcontent2" send="{$member.id}|{$member.name}|{$urlimgnet}|{$id_customer}">{l s='Mensaje'}</span></td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="2"><span class="information">{l s='Fluz Ganados:'} </span><span class="data">{if $member.points != ""}{$member.points|number_format:0:".":","}{else}0{/if}</span></td>
                                            <td>
                                                {if $member.pendingsinvitation != 0}
                                                    <span class="data pendingsinvitation">{$member.pendingsinvitation} Invitacion(es) Pendiente(s)</span>
                                                {/if}
                                                &nbsp;
                                            </td>
                                            <td></td>
                                        </tr>
                                    </table>
                                </td>
                            </div>
                        {/foreach}
                    </div>
                </div>  
                    <button id="loadMoreMember" class="col-lg-11 btn-moreload"><span class="pmore">{l s="Mostrar mas"}</span><i id="boton-carga" class="icon-refresh icon-white"></i></button>
            </div>
            
            <div class="col-lg-4 col-md-5 col-sm-6 col-xs-11 block-red">
                <h2 class="title-msj">{l s='My Messages'}</h2>
                <div class="containtertables">
                    <div class="tablemessages">
                        {foreach from=$messages item=message}
                            <div class="t-messages">
                                    <table class="tablecontent tablecontentmessages">
                                        <tr>
                                            <td rowspan="2" class="img">
                                                {assign var="urlimgmes" value=""}
                                                {if $message.img != ""}
                                                    <img src="{$message.img}" width="50" height="50" style="margin-left: 5px;">
                                                    {$urlimgmes = $message.img}
                                                {else}
                                                    <img src="{$img_dir}icon/profile.png" width="55" height="50">
                                                    {$urlimgmes = $img_dir|cat:"icon/profile.png"}
                                                {/if}
                                            </td>
                                            <td colspan="2" class="line colname">{if $message.id_customer_send == $id_customer}<img src="/img/admin/enabled.gif">{/if} <span class="name">{$message.username}</span></td>
                                            <td class="message line">{if $message.id_customer_send != $id_customer}<span class="myfancybox" href="#myspecialcontent2" send="{$message.id_customer_send}|{$message.username}|{$urlimgmes}|{$id_customer}">{l s='Responder'}</span>{/if}</td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3"><span class="information">{$message.message}</span></td>
                                            <td></td>
                                        </tr>
                                    </table>
                            </div>
                        {/foreach}
                    </div>
                </div>
                <button id="loadMoreMsg" class="col-lg-11 btn-moreload"><span class="pmore">{l s="Mostrar mas"}</span><i id="boton-carga-msg" class="icon-refresh icon-white"></i></button>
            </div>
    </form>
</div>
<div id="not-shown" style="display:none;">
    <div id="myspecialcontent2" class="infoPopUp">
        <div class="titlesendmessage">
            <img id="imgsendmessage" src="" width="55" height="50"> <span id="namesendmessage"></span>
        </div>
        <div>
            <textarea rows="4" cols="50" placeholder="{l s='Escriba su mensaje aqui'}" id="messagesendmessage"></textarea>
            <input type="hidden" id="idsendmessage" value="">
            <input type="hidden" id="idreceivemessage" value="">
        </div>
        <div class="blockbutton">
            <a class="btn btn-default button button-small" id="buttonsendmessage">
                <span>
                    {l s='Enviar'}
                </span>
            </a>
        </div>
    </div>
</div>
<ul class="footer_links clearfix" style="display: none;">
    <li>
        <a class="btn btn-default button button-small" href="{if isset($force_ssl) && $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}" title="{l s='Home'}"><span><i class="icon-chevron-left"></i> {l s='Home'}</span></a>
    </li>
</ul>

{literal}
    <style>
        .page-heading{margin-bottom: 0px; padding: 0px;letter-spacing: 0px;font-family: 'Open Sans'; font-size: 16px; line-height: 20px;}
        .page-heading-2{margin-bottom: 0px;padding: 0px;letter-spacing: 0px;font-family: 'Open Sans';font-size: 16px;line-height: 20px;}
        p.info-account{margin: 16px 0 24px 0; padding-left: 0px;font-family: 'Capitalized';font-weight: bold;}
        .btn-account{font-size: 12px;font-family: 'Montserrat';font-weight: lighter;letter-spacing: 1px;
                        color: #fff;
                        border: none;
                        background: #ef4136;}
        .btn-design{text-align: right;}
        
        @media (max-width:1024px){
        }
        
        @media (max-width:768px){
            .btn-design{text-align: right;padding-left: 0px;}
        }
        
        @media(max-width:768px){
            .btn-design{margin-bottom: 10px;}
            .btn-account{padding: 10px 18px;}
        }
        
        @media (max-width:425px){
           .btn-reload{padding-left: 0px; width: 200px !important;}
           .btn-design-r{width: 166px;}
        }
        
        @media (max-width:420px){
            .imgSponsor2 {width: 33% !important; margin-bottom: 5%;}
            .barTop{margin-bottom: 4%;}
        }
        
        @media(max-width:414px){
            .padding-min{padding-left:0px;}
        }
    </style>
{/literal}

{literal}
    <script>
        $(".Cards").fancybox();
    </script>
    <style>
        @media (max-width:768px){
            .fancybox-iframe { height: 75vh!important; }
        }
        @media (max-width:580px){
            .btn-account{font-size: 10px;}
        }
    </style>
{/literal}

{literal}
    <script>
        // popup message
        $('.myfancybox').click( function() {
            $("#idsendmessage").val("");
            $("#idreceivemessage").val("");
            $("#messagesendmessage").val("");
            var data = $(this).attr('send').split('|');
            $("#idreceivemessage").val(data[0]);
            $("#namesendmessage").text(data[1]);
            $("#imgsendmessage").attr("src", data[2]);
            $("#idsendmessage").val(data[3]);
        });

        // send message
        $('#buttonsendmessage').click( function() {
            var idsend = $("#idsendmessage").val();
            var idreceive = $("#idreceivemessage").val();
            var message = $("#messagesendmessage").val();
            var jsSrcRegex = /([^\s])/;
            if ( idsend != "" && idreceive != "" && message != "" && jsSrcRegex.exec(message) ) {
                $.ajax({
                    method:"POST",
                    data: {
                        'action': 'sendmessage',
                        'idsend': idsend,
                        'idreceive': idreceive,
                        'message': message
                    },
                    url: '/messagesponsor.php', 
                    success:function(response){
                        alert("Mensaje enviado exitosamente.");
                        $("#idsendmessage").val("");
                        $("#messagesendmessage").val("");
                        $.fancybox.close();
                        location.reload();
                    }
                });
            }
        });
    </script>
{/literal}

{literal}
    <script>
        $(function(){
            $(".Cards").slice(0, 4).show();
            // select the first ten
            if($(".Cards").length <= 4){
                $("#loadMore").css('display','none');
            }
            else
            $("#loadMore").click(function(e){// click event for load more
                $(this).find('i').addClass('icon-refresh2');
                e.preventDefault();
                $(".Cards:hidden").slice(0, 8).show();
                if($(".Cards:hidden").length == 0){ // check if any hidden divs still exist
                    $("#loadMore").css('display','none'); // alert if there are none left
                }
                setTimeout(function() {
                    $('#boton-carga-card').removeClass('icon-refresh2');
                    setTimeout(function() {
                      $('#pmore').html("Cargar Mas");
                    }, 1);
                }, 1000);
            });
        });
    </script>
{/literal}

{literal}
    <script>
        $(function(){
            $(".last_shop").slice(0, 4).show(); // select the first ten
            if($(".last_shop").length <= 4){
                $("#loadMoreNet").css('display','none');
            }
            else
                $("#loadMoreNet").click(function(e){ // click event for load more
                    $(this).find('i').addClass('icon-refresh2');
                    e.preventDefault();
                    $(".last_shop:hidden").slice(0, 8).show(); 
                    if($(".last_shop:hidden").length == 0){ // check if any hidden divs still exist
                        $("#loadMoreNet").css('display','none');; // alert if there are none left
                    }
                    setTimeout(function() {
                    $('#boton-carganet').removeClass('icon-refresh2');
                    setTimeout(function() {
                      $('#pmore').html("Cargar Mas");
                    }, 1);
                }, 1000);
              });
           });
    </script>
{/literal}

{literal}
    <script>
        $(function(){
            $(".member").slice(0, 4).show(); // select the first ten
            if($(".member").length <= 4){
                $("#loadMoreMember").css('display','none');
            }
            else
                $("#loadMoreMember").click(function(e){
                    $(this).find('i').addClass('icon-refresh2');
                    e.preventDefault();
                    $(".member:hidden").slice(0, 8).show();
                    if($(".member:hidden").length == 0){ // check if any hidden divs still exist
                        $("#loadMoreMember").css('display','none'); // alert if there are none left
                    }
                    
                    setTimeout(function() {
                    $('#boton-carga').removeClass('icon-refresh2');
                    setTimeout(function() {
                      $('#pmore').html("Cargar Mas");
                    }, 1);
                }, 1000);
            });
        });
    </script>
{/literal}

{literal}
    <script>
        $(function(){
            $(".t-messages").slice(0, 4).show(); // select the first ten
            if($(".t-messages").length <= 4){
                $("#loadMoreMsg").css('display','none');
            }
            else
                $("#loadMoreMsg").click(function(e){ // click event for load more
                    $(this).find('i').addClass('icon-refresh2');
                    e.preventDefault();
                    $(".t-messages:hidden").slice(0, 8).show(); 
                    if($(".t-messages:hidden").length == 0){ // check if any hidden divs still exist
                        $("#loadMoreMsg").css('display','none');// alert if there are none left
                    }
                setTimeout(function() {
                        $('#boton-carga-msg').removeClass('icon-refresh2');
                        setTimeout(function() {
                          $('#pmore').html("Cargar Mas");
                        }, 1);
                    }, 1000);    
                });
        });
    </script>
{/literal}

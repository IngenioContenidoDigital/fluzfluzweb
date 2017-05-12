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
{capture name=path}<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">{l s='My account'}</a><span class="navigation-pipe">{$navigationPipe}</span><span class="navigation_page">{l s='Explore Network'}</span>{/capture}

<h1 class="page-heading">
    {l s='Explore Network'}
</h1>

<form action="{$link->getPageLink('discount', true)|escape:'html':'UTF-8'}" method="post" id="formnetwork">
    <div class="row blockcontainer">
        <div class="block-network col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <h2>{l s='Explore Network'}</h2>
            <input type="text" name="searchnetwork" id="searchnetwork" class="textsearch" placeholder="{l s='Search member'}" value="{$searchnetwork}"><img class="searchimg" src="/themes/pos_minoan6/css/modules/blocksearch/search.png" title="Search" alt="Search" height="15" width="15">
            <div class="containtertables">
                <div class="tablenetwork">
                    {foreach from=$members item=member}
                        <div class="member">
                            <div class="spacesavailable">
                                <table class="tablecontent">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="avalaible-invitation">
                                            {if $member.pendingsinvitation != 0}
                                                <span>{$member.pendingsinvitation} Espacio(s) Disponible(s)</span>
                                            {/if}
                                        </div>
                                    </div>
                                        
                                    <div class="col-xs-2 col-md-2 col-sm-2 col-lg-2 containerimguser">
                                        <div class="img-center">
                                            <div class="img">
                                                {assign var="urlimgnet" value=""}
                                                {if $member.img != ""}
                                                    <img src="{$member.img}" width="50" height="50" style="margin-left: 5px;">
                                                    {$urlimgnet = $member.img}
                                                {else}
                                                    <img src="{$img_dir}icon/profile.png" width="55" height="50">
                                                    {$urlimgnet = $img_dir|cat:"icon/profile.png"}
                                                {/if}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-10 col-md-10 col-sm-10 col-lg-10 containerinfor">        
                                        <div class="containerinforname row">
                                            <span class="col-lg-4 col-sm-4 col-md-4 col-xs-4 name">{$member.username}</span>
                                            <div class="col-lg-4 col-sm-4 col-md-4 col-xs-4 message"><span class="myfancybox" href="#myspecialcontent" send="{$member.id}|{$member.name}|{$urlimgnet}|{$id_customer}">{l s='Mensaje'}</span></div>
                                            <div class="col-lg-4 col-sm-4 col-md-4 col-xs-4 message">
                                                {if $member.pendingsinvitation != 0}
                                                    <span class="pendingsinvitation fancybox fancybox.iframe" title="Invitar Amigo" href="{$link->getPageLink('sponsorshipthird', true)}?user={$member.id}">Invitar Amigo</span>
                                                    {*span class="data pendingsinvitation fancybox fancybox.iframe" title="Invitar Amigo" href="{$link->getPageLink('sponsorshipthird', true)}?user={$member.id}">{$member.pendingsinvitation} Invitacion(es) Pendiente(s)</span>*}
                                                {/if}
                                            </div>
                                        </div>
                                        <div class="row info-account-fluz">
                                            <div class="info-net-fluz col-lg-4 col-sm-4 col-md-4 col-xs-4"><span class="information">{l s='Points Contributed:'} </span><span class="data">{if $member.points != ""}{$member.points}{else}0{/if}</span></div>
                                            <div class="col-lg-3 col-sm-3 col-md-3 col-xs-3"><span class="information">{l s='Network Level:'} </span><span class="data">{$member.level}</span></div>
                                            <div class="col-lg-5 col-sm-5 col-md-5 col-xs-5"><span class="information">{l s='Date Added:'} </span><span class="data">{$member.dateadd}</span></div>
                                        </div>
                                    </div>
                                </table>
                            </div>
                        </div>
                    {/foreach}
                </div>
            </div>
                    <button id="loadMoreMember" class="col-lg-11 btn-moreload"><span class="pmore">{l s="Mostrar mas"}</span><i id="boton-carga" class="icon-refresh icon-white"></i></button>
        </div>
        <div class="block-messages col-lg-6 col-md-6 col-sm-6">
            <h2>{l s='My Messages'}</h2>
            <input type="text" name="searchmessage" id="searchmessage" class="textsearch" placeholder="{l s='Search member'}" value="{$searchmessage}"><img class="searchimg" src="/themes/pos_minoan6/css/modules/blocksearch/search.png" title="Search" alt="Search" height="15" width="15">
            <div class="containtertables">
                <div class="tablemessages">
                    {foreach from=$messages item=message}
                        <div class="t-messages">
                            <td>
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
                                        <td class="message line">{if $message.id_customer_send != $id_customer}<span class="myfancybox" href="#myspecialcontent" send="{$message.id_customer_send}|{$message.username}|{$urlimgmes}|{$id_customer}">{l s='Responder'}</span>{/if}</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3"><span class="information">{$message.message}</span></td>
                                        <td></td>
                                    </tr>
                                </table>
                            </td>
                        </div>
                    {/foreach}
                </div>
            </div>
                    <button id="loadMoreMsg" class="col-lg-11 btn-moreload"><span class="pmore">{l s="Mostrar mas"}</span><i id="boton-carga-msg" class="icon-refresh icon-white"></i></button>
        </div>
    </div>
</form>

<div id="not-shown" style="display:none;">
    <div id="myspecialcontent" class="infoPopUp">
        <div>
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

<ul class="footer_links clearfix">
    
    <div>
        <input type="checkbox" name="autoaddnetwork" id="autoaddnetwork" value="1" {if isset($autoaddnetwork) && $autoaddnetwork == 1} checked="checked"{/if}/>
        <label for="autoaddnetwork" style="vertical-align: sub;">
            Impedir que nuevos usuarios se agreguen autom&aacute;ticamente a mi network
        </label>
    </div>
    <br>

    <li>
        <a class="btn btn-default button button-small" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">
            <span>
                <i class="icon-chevron-left"></i> {l s='Back to your account'}
            </span>
        </a>
    </li>
    <li>
        <a class="btn btn-default button button-small" href="{if isset($force_ssl) && $force_ssl}{$base_dir_ssl}{else}{$base_dir}{/if}">
            <span>
                <i class="icon-chevron-left"></i> {l s='Home'}
            </span>
        </a>
    </li>
</ul>
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

<script>
    var id_customer = {$id_customer};
</script>

{literal}
    <script>
        $('#autoaddnetwork').change(function() {
            var value = 0;
            if( $(this).is(":checked") ) {
                value = 1;
            }
            $.ajax({
                method:"POST",
                data: {'action': 'updateautoaddnetwork', 'id': id_customer, 'value': value},
                url: '/autoaddnetwork.php'
            });
        });
    </script>
{/literal}
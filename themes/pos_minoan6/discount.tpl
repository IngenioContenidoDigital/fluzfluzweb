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
        <div class="block-network col-lg-6 col-md-6 col-sm-6">
            <h2>{l s='Explore Network'}</h2>
            <input type="text" name="searchnetwork" id="searchnetwork" class="textsearch" placeholder="{l s='Search member'}" value="{$searchnetwork}"><img class="searchimg" src="/themes/pos_minoan6/css/modules/blocksearch/search.png" title="Search" alt="Search" height="15" width="15">
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
                                        <td class="message line"><span class="myfancybox" href="#myspecialcontent" send="{$member.id}|{$member.name}|{$urlimgnet}|{$id_customer}">{l s='Mensaje'}</span></td>
                                        <td>
                                            {if $member.pendingsinvitation != 0}
                                                <span class="data pendingsinvitation fancybox fancybox.iframe" title="Invitar Amigo" href="http://fluzfluzweb.localhost/modules/allinone_rewards/views/templates/front/sponsorship_third.tpl">{$member.pendingsinvitation} Invitacion(es) Pendiente(s)</span>
                                            {/if}
                                            &nbsp;
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td class="colpoints"><span class="information">{l s='Points Contributed:'} </span><span class="data">{if $member.points != ""}{$member.points}{else}0{/if}</span></td>
                                        <td><span class="information">{l s='Network Level:'} </span><span class="data">{$member.level}</span></td>
                                        <td colspan="2"><span class="information">{l s='Date Added:'} </span><span class="data">{$member.dateadd}</span></td>
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
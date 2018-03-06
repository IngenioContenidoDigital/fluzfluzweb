{*
* @package   	OneAll Social Login
* @copyright 	Copyright 2011-2017 http://www.oneall.com
* @license   	GNU/GPL 2 or later
*
* This program is free software; you can redistribute it and/or
* modify it under the terms of the GNU General Public License
* as published by the Free Software Foundation; either version 2
* of the License, or (at your option) any later version.
*
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this program; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307,USA.
*
* The "GNU General Public License" (GPL) is available at
* http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*
*}

{capture name=path}{l s='Create an account' mod='oneallsociallogin'}{/capture}

<h1 class="page-heading bottom-indent">Te has conectado con {l s='%s!' sprintf=$identity_provider mod='oneallsociallogin'}</h1>
<p>
    T&oacute;mate un minuto para revisar y completar la informaci&oacute;n de tu cuenta. Una vez que hayas revisado tus datos, tu cuenta estar&aacute; lista para usar.
</p>

{* ERRORS *}
{if $errorsform}
    {include file="$tpl_dir./errors.tpl"}
{/if}

{* SEND SMS *}
{if $sendSMS}
    <div class="block-form block-confirmsms row">
        <span>Se ha enviado un c&oacute;digo de confirmaci&oacute;n a tu n&uacute;mero c&eacute;lular</span>
        <br>
        <label>Ingresalo a continuaci&oacute;n para completar t&uacute; registro</label>
        <form>
            <div class="form-group">
                <label for="codesms" class="required">C&oacute;digo</label>
                <input type="text" placeholder="------" class="form-control" id="codesms" name="codesms" autocomplete="off">
                <input type="hidden" name="id_customer" id="id_customer" value="{$id_customer}">
                <input type="hidden" name="codesponsor" id="codesponsor" value="{$codesponsor}">
                <input type="hidden" name="id_sponsor" id="id_sponsor" value="{$id_sponsor}">
            </div>
            <div class="form-group" style="text-align: center;">
                <button type="submit" class="btn btn-primary" name="confirm" id="confirm">Confirmar Registro</button>
            </div>
            <div class="form-group" style="text-align: center;">
                <button type="submit" class="btn btn-primary" name="resendSMS" id="resendSMS">Reenviar Codigo</button>
                <br>
                <small class="form-text text-muted text-help">Si no has recibido un c&oacute;digo luego de 10 minutos, pulsa en el anterior bot&oacute;n</small>
            </div>
        </form>
    </div>

{* COMPLETE REGISTRATION *}
{elseif $successfulregistration}
    <div class="block-successfulregistration row">
        <br>
        Tu Registro Ha Sido Exitoso
        <br><br><br>
        <img src="{$img_dir}checked.png" />
        <br><br><br><br>
        <a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">Continuar</a>
        <br><br><br>
    </div>      
    {literal}    
        <style>
            #oneallsociallogin{display:none;}
        </style>
    {/literal}    
{else}
<div id="oneallsociallogin">
    <form id="account-creation_form" action="{$oasl_register}" method="post" class="box">
        <fieldset>
            <div class="form_content clearfix">
                <div class="form-group">
                    <label for="oasl_code_sponsor">C&oacute;digo de Patrocinio <sup>*</sup></label> 
                    <input type="text" class="form-control" id="oasl_code_sponsor" name="oasl_code_sponsor" value="" />
                </div>
                <div class="form-group">
                    <label for="oasl_firstname">Nombre <sup>*</sup></label> 
                    <input type="text" class="is_required form-control" id="oasl_firstname" name="oasl_firstname" value="{if isset($smarty.post.oasl_firstname)}{$smarty.post.oasl_firstname|stripslashes}{elseif $oasl_populate == '1'}{$oasl_first_name}{/if}" />
                </div>
                <div class="form-group">
                    <label for="oasl_lastname">Apellido <sup>*</sup></label>
                    <input type="text" class="is_required form-control" id="oasl_lastname" name="oasl_lastname" value="{if isset($smarty.post.oasl_lastname)}{$smarty.post.oasl_lastname|stripslashes}{elseif $oasl_populate == '1'}{$oasl_last_name}{/if}" />
                </div>
                <div class="form-group">
                    <label for="oasl_email">Correo electronico <sup>*</sup></label>
                    <input type="text" class="is_required form-control" id="oasl_email" name="oasl_email" value="{if isset($smarty.post.oasl_email)}{$smarty.post.oasl_email|stripslashes}{elseif $oasl_populate == '1'}{$oasl_email}{/if}" />
                </div>
                <div class="form-group">
                    <label for="oasl_username">Nombre de usuario <sup>*</sup></label>
                    <input type="text" class="is_required form-control" id="oasl_username" name="oasl_username" value="{if isset($smarty.post.oasl_username)}{$smarty.post.oasl_username|stripslashes}{elseif $oasl_populate == '1'}{$oasl_username}{/if}" />
                </div>
                <div class="form-group">
                    <label for="oasl_address">Direcci&oacute;n <sup>*</sup></label>
                    <input type="text" class="is_required form-control" id="oasl_address" name="oasl_address" value="{if isset($smarty.post.oasl_address)}{$smarty.post.oasl_address|stripslashes}{elseif $oasl_populate == '1'}{$oasl_address}{/if}" />
                </div>
                <div class="form-group">
                    <label for="oasl_phone">Tel&eacute;fono <sup>*</sup></label>
                    <input type="text" class="is_required form-control" id="oasl_phone" name="oasl_phone" value="{if isset($smarty.post.oasl_phone)}{$smarty.post.oasl_phone|stripslashes}{elseif $oasl_populate == '1'}{$oasl_phone}{/if}" />
                </div>
                <div class="form-group">
                    <label for="oasl_city">Ciudad <sup>*</sup></label>
                    <select id="oasl_city" name="oasl_city" class="is_required form-control">
                        <option value="Bogota, D.C.">Bogot&aacute;, D.C.</option>
                        <option value="Medellin">Medell&iacute;n</option>
                        <option value="Cali">{l s="Cali"}</option>
                        <option value="Barranquilla">{l s="Barranquilla"}</option>
                        <option value="Bucaramanga">{l s="Bucaramanga"}</option>
                        {foreach from=$cities item=city}
                            <option value="{$city.ciudad}">{$city.ciudad}</option>
                        {/foreach}
                    </select>
                </div>
                <div class="form-group">
                    <label for="oasl_typedni">Tipo identificaci&oacute;n <sup>*</sup></label>
                    <select class="is_required form-control" id="oasl_typedni" name="oasl_typedni">
                        <option value="0" selected="selected">Cedula de Ciudadan&iacute;a</option>
                        <option value="1">NIT</option>
                        <option value="2">Cedula de Extranjer&iacute;a</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="oasl_dni">Identificaci&oacute;n <sup>*</sup></label>
                    <input type="text" class="is_required form-control" id="oasl_dni" name="oasl_dni" value="{if isset($smarty.post.oasl_dni)}{$smarty.post.oasl_dni|stripslashes}{elseif $oasl_populate == '1'}{$oasl_dni}{/if}" />
                </div>
                <hr />
                <div class="submit">
                    {if isset($back)}<input type="hidden" class="hidden" name="back" value="{$back|escape:'htmlall':'UTF-8'}" />{/if}
                    <button name="submit" id="submit" type="submit" class="btn btn-default button button-medium"><span>Confirmar<i class="icon-chevron-right right"></i></span></button>
                </div>
            </div>
        </fieldset>
    </form>
</div>
{/if}
<style>
    #right_column { display: none; }
    
    /* CONFIRMACION SMS */
    .block-banner { font-family: 'Open Sans'; background: #F14E38; margin-top: 15px; padding: 30px 0; color: white; }
    .block-banner > .text { font-size: 15px; padding: 50px; }
    .block-banner > .text > span { font-size: 20px; line-height: 30px; font-weight: bold; }
    .block-banner > .img { text-align: center; }
    .block-banner > .img > img { width: 100%; }

    .block-successfulregistration { font-family: 'Open Sans'; text-align: center; font-weight: bold; font-size: 15px; margin-top: 60px; border: 1px solid #72C279; color: #72C279; background: #f7f7fb; padding: 15px 25px; }
    .block-successfulregistration > a { border: 1px solid #72C279; color: white; background: #72C279; padding: 13px 60px; }

    .block-errors { font-family: 'Open Sans'; margin-top: 60px; border: 1px solid #F14E38; color: #F14E38; padding: 15px 25px; }
    .block-errors > span { color: #F14E38; font-size: 15px; }

    .block-form { font-family: 'Open Sans'; margin-top: 60px; }
    .block-form > span { font-size: 20px ; font-weight: bold; color: #000;}
    .block-form > form { margin-top: 30px; font-size: 15px; }
    .block-form > form > div > input { font-size: 15px; }

    .block-confirmsms { text-align: center; }
    .block-confirmsms > form > div > input { text-align: center; }

    select, input { width: 100%!important; border: none!important; border-bottom: 1px solid #F14E38!important; }
    select { padding: 7px 15px!important; }
    input { padding: 17px 15px!important; }

    .form-check-label { margin-bottom: 0; margin-top: 4px; }

    #register, #confirm { margin-top: 15px; background: #F14E38; border: 1px solid #F14E38; font-size: 14px; font-weight: bold; letter-spacing: 2px; }
    #register:hover, #confirm:hover { background: #C9B197; border: 1px solid #C9B197; }
    #resendSMS { padding: 6px 24px; margin-top: 5px; margin-bottom: 5px; margin-left: 3px; background: #C9B197; border: 1px solid #C9B197; font-size: 14px; font-weight: bold; letter-spacing: 2px; }
    #resendSMS:hover { background: #F14E38; border: 1px solid #F14E38; }
    #error_novalidos{    
        padding: 0;
        background: #ef4136;
        color: #fff;
        font-weight: bold;
        text-align: center;
        margin-top: 2px; padding: 5px;}

    #confirm_validos{    
        padding: 0;
        background: #72C279;
        color: #fff;
        font-weight: bold;
        text-align: center;
        margin-top: 2px; padding: 5px;}
</style>
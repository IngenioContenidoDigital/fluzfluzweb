{*
* All-in-one Rewards Module
*
* @category  Prestashop
* @category  Module
* @author    Yann BONNAILLIE - ByWEB
* @copyright 2012-2015 Yann BONNAILLIE - ByWEB (http://www.prestaplugins.com)
* @license   Commercial license see license.txt
* Support by mail  : contact@prestaplugins.com
* Support on forum : Patanock
* Support on Skype : Patanock13
*}
<!-- MODULE allinone_rewards -->

{capture name=path}<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">{l s='My account' mod='allinone_rewards'}</a><span class="navigation-pipe">{$navigationPipe|escape:'html':'UTF-8'}</span>{l s='My rewards account' mod='allinone_rewards'}{/capture}

<div id="rewards_account" class="rewards">
{if version_compare($smarty.const._PS_VERSION_,'1.6','<')}
    {include file="$tpl_dir./breadcrumb.tpl"}
{/if}
<div class="row"> 
    <h3 class="title-myinfo"> MI INFORMACION PERSONAL </h3> 
</div>
<div class="row info-personal">
    <div class="row row-personal">
        <div class="col-lg-6">
                <div class="left-info">{l s='Username'}</div>
        </div>
        <div class="col-lg-6">
            <div class="text-infouser"> UsernameUsuarioFluz </div>
        </div>
    </div>
    <div class="row row-personal">
        <div class="col-lg-6">
                <div class="left-info">{l s='Mis Fluz'}</div>
        </div>
        <div class="col-lg-6">
            <div class="text-infouser"> 2500 </div>
        </div>
    </div>
    <div class="row row-personal">
        <div class="col-lg-6">
                <div class="left-info">{l s='Dinero en Fluz'}</div>
        </div>
        <div class="col-lg-6">
            <div class="text-infouser"> COP $62.500 </div>
        </div>
    </div>    
</div>
<form>
    <div class="row"> 
        <h3 class="title-myinfo"> TRANSFERENCIA FLUZ A FLUZZERS </h3> 
    </div>
    <div class="row info-personal">
        <div class="row row-personal">
            <div class="col-lg-6">
                    <div class="left-info">{l s='Fluzzer Destino'}</div>
            </div>
            <div class="col-lg-6">
                <div class="text-infouser">             
                    <input type="text" class="is_required validate form-control input-infopersonal" data-validate="isUsername" id="username" name="username" value="PruebaUsuarioFluz"/>
                </div>
            </div>
        </div>
        <div class="row row-personal">
            <div class="col-lg-6">
                    <div class="left-info">{l s='Cantidad de Fluz a enviar'}</div>
            </div>
            <div class="col-lg-6">
                <div class="text-infouser">             
                    <input type="text" class="is_required validate form-control input-infopersonal" data-validate="isUsername" id="username" name="username" value="Fluz a Enviar"/>
                </div>
            </div>
        </div>
        <div class="row row-personal">
            <div class="col-lg-6">
                    <div class="left-info">{l s='Dinero en Fluz'}</div>
            </div>
            <div class="col-lg-6">
                <div class="text-infouser"> COP $62.500 </div>
            </div>
        </div>    
    </div>
    <div class="row btn-sendfluz">
        <div class="col-lg-6">
            <a class="btn btn-default btn-account" href="#"><span style="cursor:pointer;font-size: 15px;color: #fff; font-family: 'Capitalized';font-weight: bold;">{l s="Enviar Fluz"}</span></a>
        </div>
    </div>
</form>
<!-- END TEMPLATE TRANSFER FLUZ -->

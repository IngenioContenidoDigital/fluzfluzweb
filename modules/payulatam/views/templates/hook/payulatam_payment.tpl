{*
* 2014 PAYU LATAM
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @author    PAYU LATAM <sac@payulatam.com>
*  @copyright 2014 PAYU LATAM
*  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*}

{*<link href="{$base_dir|escape:'htmlall':'UTF-8'}/modules/payulatam/css/payu.css" rel="stylesheet" type="text/css">*}
<style type="text/css">
    .panel-heading a:after {
        font-family: "FontAwesome";
       content: "\f078";
       float: right;
       color: grey;
    }
    .panel-heading a.collapsed:after {
        content:"\f054";
    }
    .control-label {
        text-align: left;
    }
</style>
<div class="row">
    <div class="col-xs-12">
        {if isset($errors_pay) && $errors_pay == 'true'}
            <div class="alert alert-danger">
                {foreach name=outer_p item=error_p from=$errors_msgs}
                    {foreach key=key_p item=item_p from=$error_p}
                        {$item_p}&nbsp;&nbsp; 
                    {/foreach} 
                {/foreach}
                <p>Verifica tus datos e intenta de nuevo o utiliza otro medio de pago.</p>
            </div>
        {/if} 
    </div>
    <div class="col-xs-12">
        <div class="panel-group" id="accordion">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Pagar con tarjeta de crédito</a>
                    </h4>
                </div>
                <div id="collapseOne" class="panel-collapse collapse">
                    <div class="panel-body">
                        {include file="$tpl_dir../../modules/payulatam/views/templates/hook/credit_card.tpl"}
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">Transacción bancaria PSE</a>
                    </h4>
                </div>
                <div id="collapseTwo" class="panel-collapse collapse">
                    <div class="panel-body">
                        {include file="$tpl_dir../../modules/payulatam/views/templates/hook/payuPse.tpl"}
                    </div>
                </div>
            </div>
            {*<div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseThree">Pagar con Baloto</a>
                    </h4>
                </div>
                <div id="collapseThree" class="panel-collapse collapse">
                    <div class="panel-body">
                        {include file="$tpl_dir../../modules/payulatam/views/templates/hook/payuBaloto.tpl"}
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseFour">Pagar con Efecty</a>
                    </h4>
                </div>
                <div id="collapseFour" class="panel-collapse collapse">
                    <div class="panel-body">
                        {include file="$tpl_dir../../modules/payulatam/views/templates/hook/payuEfecty.tpl"}
                    </div>
                </div>
            </div>*}
        </div>
    </div>
</div>
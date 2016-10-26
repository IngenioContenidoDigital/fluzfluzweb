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

<div class="tab-pane active" id="status">
    <h4>{l s='Estado de la Redencion'} <span class="badge">({$id})</span></h4>
    <div class="table-responsive cash-table">
	<table class="table history-status row-margin-bottom">
            {foreach from=$datos item=cashout}
                <div class='style-label row'>
                    <label class="datos-back col-lg-6">{l s="Nombre del Solicitante:"}</label>
                    <div class="datos-back col-lg-6">{$cashout.nombre}&nbsp;{$cashout.apellido}</div>
                </div>
                <div class='style-label row'>
                    <label class="datos-back col-lg-6">{l s="Numero de Cuenta:"}</label>
                    <div class="datos-back col-lg-6">{$cashout.numero_tarjeta}</div>
                </div>
                <div class='style-label row'>
                    <label class="datos-back col-lg-6">{l s="Nombre del Banco:"}</label>
                    <div class="datos-back col-lg-6">{$cashout.banco}</div>
                </div>
                <div class='style-label row'>
                    <label class="datos-back col-lg-6">{l s="Cantidad a Pagar:"}</label>
                    <div class="datos-back col-lg-6">{$cashout.credits}</div>
                </div>
                <div class='style-label row'>
                    <label class="datos-back col-lg-6">{l s="Estado del Pago:"}</label>
                    <div class="datos-back col-lg-6" id="select-name">{$cashout.name}</div>
                </div>    
            {/foreach}
        </table>
    </div>    
</div>    
<!-- Change status form -->
<form action="{$currentIndex|escape:'html':'UTF-8'}&amp;viewrewards_payment&amp;token={$smarty.get.token}" method="post" class="form-horizontal well hidden-print">
        <div class="row">
                <div class="col-lg-9">
                        <select id="id_status" class="chosen form-control" name="id_status">
                        {foreach from=$state item=status}
                            <option id='id_state' name='id_state' value="{$status['id_status']}" {if $status['id_status'] == $cashout.id_status}selected="selected"{/if}>{$status['name']|escape}</option>
                        {/foreach}
                        <input type="hidden" id="name-select" name="name-select" value="{$cashout.name}">
                        </select>
                        <input type="hidden" name="id_payment" value="{$id}" />
                        <input type="hidden" id="paid" name="paid" value="{$cashout.credits}">
                </div>
                <div class="col-lg-3">
                        <button type="submit" name="submitState" class="btn btn-primary">
                                {l s='Update status'}
                        </button>
                </div>
        </div>
</form>

<script>
    $(document).ready(function(){
        var selected = $('#select-name').html();
        if(selected == 'Solicitada'){
            $("#select-name").addClass('solicitada');
        }
        else if(selected == 'En Proceso'){
            $("#select-name").addClass('proceso');
        }
        else if(selected == 'Pagada'){
            $("#select-name").addClass('pagada');
        }
    });
</script>
{literal}
    <style>
        .bootstrap .page-head h2.page-title{display:none;}
        .bootstrap h4, .bootstrap .h4 {font-size: 16px;}
    </style>
{/literal}
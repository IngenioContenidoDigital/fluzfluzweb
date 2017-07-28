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
<!-- PAGE Historial Transferencias -->

{if version_compare($smarty.const._PS_VERSION_,'1.6','<')}
    {include file="$tpl_dir./breadcrumb.tpl"}
{/if}
<form method="post" id="history-business" name="history-business">
    <div class="row" style="text-align: right;">        
            <button class="btn btn-default btn-save-employee" type="submit" id="export-excel" name="export-excel">
                <span> EXPORTAR HISTORIAL </span>
            </button>
    </div>
</form>
<div class="row container-info-users" id="container-info-users">
    <div class="row bar-info-users">
        <div class="col-lg-2 item-users" id="firstname">Nro. Transferencia</div>
        <div class="col-lg-2 item-users" id="firstname">Nro. Fluzzers Destino</div>
        <div class="col-lg-2 item-users" id="firstname">Nombre</div>
        <div class="col-lg-2 item-users" id="date">Fecha de Transferencia</div>
        <div class="col-lg-2 item-users" id="amount">Monto Total en Fluz</div>
        <div class="col-lg-2 item-users" id="amount">Monto Total en Dinero</div>
    </div>
</div>  
<div class="row row-container-info" id="container-List-employees">
    
    {foreach from=$history_transfer item=transfer}
        <div class="row content-info" id="content-users">
            {if $id_customer == $transfer.id_cliente}
                <div id="button_{$transfer.id_transferencia}" class="row buttonAccordionHistory" onclick="accordion_display({$transfer.id_transferencia})">
                    <div class="col-lg-2 content-item-users">{$transfer.id_transferencia}</div>
                    <div class="col-lg-2 content-item-users">{($transfer.numero_empleados - 1)}</div>
                    <div class="col-lg-2 content-item-users">{$transfer.nombre}</div>
                    <div class="col-lg-2 content-item-users">{$transfer.fecha_transferencia}</div>
                    <div class="col-lg-2 content-item-users" id="amount_unit">{$transfer.fluz_transferidos} Fluz</div>
                    <div class="col-lg-2 content-item-users" id="amount_unit">COP $ {$transfer.fluz_transferidos * (int)Configuration::get('REWARDS_VIRTUAL_VALUE_1')}</div>
                </div>
            {else}
                <div id="container_{$transfer.id_transferencia}" class="row container_history container_{$transfer.id_transferencia}" style="display:none;">
                    <div class="col-lg-2 content-item-users">{$transfer.id_transferencia}</div>
                    <div class="col-lg-2 content-item-users"></div>
                    <div class="col-lg-2 content-item-users">{$transfer.nombre}</div>
                    <div class="col-lg-2 content-item-users">{$transfer.fecha_transferencia}</div>
                    <div class="col-lg-2 content-item-users" id="amount_unit">{$transfer.fluz_transferidos} Fluz</div>
                    <div class="col-lg-2 content-item-users" id="amount_unit">COP $ {$transfer.fluz_transferidos * (int)Configuration::get('REWARDS_VIRTUAL_VALUE_1')}</div>
                </div>
            {/if}
        </div>
    {/foreach}
</div>
{literal}
    <style>
        #left_column{display: none;}
    </style>
{/literal}
{literal}
    <script>
        function accordion_display(id) {
            var esVisible = $('.container_'+id).is(":visible");
            if(esVisible){
                $('.container_'+id).slideToggle("slow");
                $('#button_'+id).removeClass('clicked');
            }
            else {
                $('.container_history').css('display','none');
                $('.container_'+id).slideToggle("slow");
                $('#button_'+id).toggleClass('clicked');
            }
        }
    </script>
{/literal}

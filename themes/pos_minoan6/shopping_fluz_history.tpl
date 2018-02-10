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
<form method="post" id="history-shopping-fluz" name="history-shopping-fluz">
    <div class="row" style="text-align: right;">        
        <button class="btn btn-default btn-save-employee" type="submit" id="export-excel-shopping-fluz" name="export-excel-shopping-fluz">
            <span> EXPORTAR HISTORIAL </span>
        </button>
    </div>
</form>
<div class="row container-info-users" id="container-info-users">
    <div class="row bar-info-users">
        <div class="col-lg-2 item-users" id="id_transfer">Referencia</div>
        <div class="col-lg-2 item-users" id="lastname">Pago</div>
        <div class="col-lg-2 item-users" id="firstname">Estado</div>
        <div class="col-lg-2 item-users" id="date">Fecha</div>
        <div class="col-lg-2 item-users" id="amount">Total</div>
        <div class="col-lg-2 item-users" id="amount">Total Fluz</div>
    </div>
</div>  
<div class="row row-container-info" id="container-List-employees">
    {foreach from=$history_fluz item=order}
        <div class="row content-info" id="content-users">
            <div id="button_{$order.id_order}" class="row buttonAccordionHistory" onclick="accordion_display({$order.id_order})">
                <div class="col-lg-2 content-item-users">{$order.reference}</div>
                <div class="col-lg-2 content-item-users">{$order.payment}</div>
                <div class="col-lg-2 content-item-users">{$order.state}</div>
                <div class="col-lg-2 content-item-users">{$order.date}</div>
                <div class="col-lg-2 content-item-users">COP $ {$order.total|round:0}</div>
                <div class="col-lg-2 content-item-users">{$order.total_fluz}</div>
            </div>
            {foreach from=$order.products item=details}
                <div id="container_{$order.id_order}" class="row container_history container_{$order.id_order}" style="display:none;">
                    <div class="col-lg-3 content-item-users">{$details.product_name}</div>
                    <div class="col-lg-1 content-item-users">Cant. {$details.product_quantity}</div>
                    <div class="col-lg-2 content-item-users">Prec. Unit: $ {$details.product_price|round:0}</div>
                    <div class="col-lg-2 content-item-users" id="amount_unit">Fluz Unit: {$details.product_fluz}</div>
                    <div class="col-lg-2 content-item-users">COP $ {$details.product_price_total|round:0}</div>
                    <div class="col-lg-2 content-item-users" id="amount_unit">{$details.product_fluz_total}</div>
                </div>
            {/foreach}
        </div>
    {/foreach}
</div>

{literal}
    <style>
        #left_column{display: none;}
    </style>

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
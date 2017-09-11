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
<form method="post" id="history-purchase-employee" name="history-purchase-employee">
    <div class="row" style="text-align: right;">        
            <button class="btn btn-default btn-save-employee" type="submit" id="export-excel-purchase" name="export-excel-purchase">
                <span> EXPORTAR HISTORIAL </span>
            </button>
    </div>
</form>
<div class="row container-info-users" id="container-info-users">
    <div class="row bar-info-users">
        <div class="col-lg-1 item-users" id="id_transfer">Nombre</div>
        <div class="col-lg-2 item-users" id="lastname">Cedula</div>
        <div class="col-lg-3 item-users" id="firstname">Email</div>
        <div class="col-lg-2 item-users" id="date">Nombre del producto</div>
        <div class="col-lg-2 item-users" id="amount">Cantidad</div>
        <div class="col-lg-2 item-users" id="amount">Valor Total</div>
    </div>
</div>  
<div class="row row-container-info" id="container-List-employees">
    
    {foreach from=$history_purchase item=purchase}
        <div class="row content-info" id="content-users">
            <div id="button_{$purchase.id_customer}" class="row buttonAccordionHistory" onclick="accordion_display({$purchase.id_customer})">
                <div class="col-lg-1 content-item-users">{$purchase.firstname}</div>
                <div class="col-lg-2 content-item-users">{$purchase.dni}</div>
                <div class="col-lg-3 content-item-users">{$purchase.email}</div>
                <div class="col-lg-2 content-item-users"></div>
                <div class="col-lg-2 content-item-users" id="amount_unit">{$purchase.details.0.sum_quantity}</div>
                <div class="col-lg-2 content-item-users" id="amount_unit">COP $ {$purchase.details.0.sum_total|round:0}</div>
            </div>
            {foreach from=$purchase.details item=details}
                <div id="container_{$purchase.id_customer}" class="row container_history container_{$purchase.id_customer}" style="display:none;">
                    <div class="col-lg-1 content-item-users">{$purchase.firstname}</div>
                    <div class="col-lg-2 content-item-users">{$purchase.dni}</div>
                    <div class="col-lg-3 content-item-users">{$purchase.email}</div>
                    <div class="col-lg-2 content-item-users">{$details.product_name}</div>
                    <div class="col-lg-2 content-item-users" id="amount_unit">{$details.product_quantity}</div>
                    <div class="col-lg-2 content-item-users" id="amount_unit">COP $ {$details.total_paid|round:0}</div>
                </div>
            {/foreach}
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

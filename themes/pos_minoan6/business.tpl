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
<!-- MODULE Transfer Business -->
<script>
    var urlTransferController = "{$link->getPageLink('business', true)|escape:'html':'UTF-8'}";
</script>

<div id="rewards_account" class="rewards">
{if version_compare($smarty.const._PS_VERSION_,'1.6','<')}
    {include file="$tpl_dir./breadcrumb.tpl"}
{/if}

<div class="row title-row-business"> 
    <span class="title-business" id="title-container"></span>
    <div id="quantity-users"> Cantidad de Empleados</div>
    <div id="available-point" class="title-fluz">{l s="Fluz Totales: "}<span class="available-point">{$pointsAvailable}</span></div>
    <div class="title-fluz" id="title-fluz">{l s="Fluz en Dinero: "}
        <span class="available-point"> {displayPrice price=$pointsAvailable * (int)Configuration::get('REWARDS_VIRTUAL_VALUE_1')|escape:'html':'UTF-8'} </span>
    </div>
</div>
{if $error}
    <p class="error">
        {if $error == 'email invalid'}
            Direcci&oacute;n de email no es correcta.
        {elseif $error == 'name invalid'}
            Nombre o apellido no es correcto.
        {elseif $error == 'email exists'}
            Alguien con este email ya ha sido apadrinado
        {elseif $error == 'no sponsor'}
            {l s='No hay espacios disponibles en la red.'}
        {/if}
    </p>
{/if}
<div class="row panel-employee">
    <div class="col-lg-3 item-employee" id="toggle-add-employees">
        <button class="btn btn-default dropdown-toggle" type="button" id="menu1" data-toggle="dropdown">
            <span id="option-list"></span>
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu back-business" role="menu">
          <li>
              <a id="item-menu-principal" role="menuitem"  href="#">Modify an Employees</a>
          </li>
          <li>
              <a id="item-menu-add" role="menuitem"  href="#">Add an Employees</a>
          </li>
          <li>
              <a id="item-menu-upload" role="menuitem"  href="#">Upload Employees</a>
          </li>
        </ul>
    </div>
    <div class="col-lg-3 item-employee">Allocate Fluz</div>
    <div class="col-lg-3 item-employee">Allocation History</div>
    <div class="col-lg-3 item-search">Buscar</div>
</div>
<form method="post" id="trasnferbusiness" class="contenedorBusiness" name="trasnferbusiness">    
<div class="row container-info-users" id="container-info-users">
    <div class="row pagination-header">
        <div class="col-lg-2 pag-style"> Pagination </div>
        <div class="col-lg-10 btn-save-user">
            <div class="col-lg-8 div-toggle"> 
                <div class="col-lg-5 button dropdown"> 
                    <select id="select-distribute" name="select-distribute">
                        <option value="select-option">Distribute Select</option>
                       <option value="single-fluz">Distribute to Single</option>
                       <option value="all-fluz">Distribute to All</option>
                    </select>
                </div>
                <div class="col-lg-7" id="amount-use">
                    <input type="hidden" value="{$pointsAvailable}" id="ptosTotalOculto"/>
                    <input type="hidden" value="{$all_fluz}" id="total_users"/>
                    <input type="hidden" value="" id="ptosusedhidden"/>
                    <input type="hidden" value="" id="ptosdistributehidden"/>

                    <div class="col-lg-6">
                        <input class="col-lg-12" value="" type="number" min="1" max="{$pointsAvailable}" oninput="if(value>{$pointsAvailable})value={$pointsAvailable}" id="use_allfluz" autocomplete="off"/>
                    </div>
                    <div class="col-lg-4" id="ptosTotal">{l s=" Fluz "}</div>
                    <div class="col-lg-6" id="ptosused"></div>
                </div>
            </div>
            <div class="col-lg-4 div-btn">
                <button class="btn btn-default btn-save-table" type="submit" id="save-info" name="save-info">
                    <span> SAVE ALL </span>
                </button>
            </div>
        </div>
    </div>
    <div class="row bar-info-users">
        <div class="col-lg-1 item-users"></div>
        <div class="col-lg-2 item-users" id="firstname">First Name</div>
        <div class="col-lg-2 item-users" id="lastname">Last Name</div>
        <div class="col-lg-2 item-users" id="email">Email</div>
        <div class="col-lg-1 item-users" id="phone">Phone</div>
        <div class="col-lg-2 item-users" id="dni">Cedula</div>
        <div class="col-lg-2 item-users" id="amount">Amount</div>
    </div>
    <div class="row row-container-info" id="container-List-employees">
        {foreach from=$network item=net}
            <div class="row content-info-users">
                <input type="hidden" id="id_sponsor" value="{$net.id_customer}">
                <input type="hidden" id="partial_amount-{$net.id_customer}" value="">
                
                <div class="col-lg-1 content-item-users">
                    <input type="checkbox" id="check-user" value="">
                </div>
                <div class="col-lg-2 content-item-users">{$net.firstname}</div>
                <div class="col-lg-2 content-item-users">{$net.lastname}</div>
                <div class="col-lg-2 content-item-users">{$net.email}</div>
                <div class="col-lg-1 content-item-users">Phone</div>
                <div class="col-lg-2 content-item-users">{$net.dni}</div>
                <div class="col-lg-2 content-item-users" id="amount_unit">
                    <div class="row">
                        <input class="col-lg-5 r_clase amount_unit" oninput="" sponsor="{$net.id_customer}" id="single-{$net.id_customer}" value="0" type="text" min="1" max="" autocomplete="off"/>
                        <div class="col-lg-3 text_fluz">Fluz</div>   
                        <div class="col-lg-4 edit-btn" id="btn-edit" onclick="edit({$net.id_customer})">Editar</div>
                    </div>
                    <div class="col-lg-12 amount_unit_cash" id="amount_unit_cash-{$net.id_customer}">COP $0</div>
                </div>
            </div>
        {/foreach}
    </div>
</div>    
</form>    
<div id="panel-add-employee" style="display:none;">
    <div class="row">
        {include file="./addemployee.tpl"}
    </div>
</div>  
<div id="panel-upload-employee" style="display:none;">
    <div class="row">
        {include file="./adduploademployee.tpl"}
    </div>
</div>      
{literal}
    <style>
        #right_column{display: none;}
    </style>
{/literal}
{literal}
    <script>
        $(document).ready(function(){
            var add = $('#item-menu-principal').text();
            var title = 'EMPLOYER DASHBOARD';
            $('#save-info').hide();
            $('#option-list').html(add);
            $('#title-container').html(title);
            $('#amount-use').hide();
            
            var select = $('select[name=select-distribute]').val()
            if(select == 'select-option'){
                $('#container-List-employees').addClass("disabledbutton");
            }
            
            $("#select-distribute").change(function() {
                var select = $('select[name=select-distribute]').val()
                
                if(select == 'all-fluz'){
                    $('#container-List-employees').addClass("disabledbutton");
                    $('#amount-use').show();
                    $('#save-info').show();
                    
                    $('.r_clase').addClass('amount_unit');
                    $('.r_clase').removeClass('amount_edit');
                    $('.r_clase').val(0);
                    $('#use_allfluz').empty();
                    
                    $('#save-info').unbind("click");
                    $('#save-info').click(function(){
                       var ptoDistribute = $('#ptosdistributehidden').val();
                       var ptoUsed = $('#ptosusedhidden').val();        
                       
                       $.ajax({
                            url : urlTransferController,
                            type : 'POST',
                            data : 'action=allFLuz&ptoDistribute='+ptoDistribute+'&ptoUsed='+ptoUsed,
                            success : function() {
                                
                            }
                        });
                    });
                    
                }
                else if(select == 'single-fluz'){
                    $('#container-List-employees').removeClass("disabledbutton");
                    $('#amount-use').hide();
                    $('#save-info').show();
                    $(".amount_unit").prop('disabled', true);
                    $(".amount_unit").css('background', 'transparent');
                    $(".amount_unit").val(0);
                    
                    $('#check-user').click(function() {
                        if ($(this).is(':checked')) {
                            //codigo para eliminar usuario de la red
                        }
                    });   
                    $('#save-info').unbind("click");
                    $('#save-info').click(function(){
                    
                    var listEdit = [];  
                    var total_point = 0;
                    $( ".amount_edit" ).each(function( index ) {
                        var id_sponsor = $(this).attr("sponsor");
                        var amount_edit = $( this ).val();
                        total_point += Number($(this).val());
                        
                        var item = {}
                        item ["id_sponsor"] = id_sponsor;
                        item ["amount"] = amount_edit;
                        
                        listEdit.push(item);
                    });
                       
                       listEdit = JSON.stringify(listEdit);   
                       
                       $.ajax({
                            url : urlTransferController,
                            type : 'POST',
                            data : 'action=editFLuz&listEdit='+listEdit+'&ptosTotal='+total_point,
                            success : function() {
                                
                            }
                        });
                    });
                }
                else{
                    $('#container-List-employees').addClass("disabledbutton");
                    $('#amount-use').hide();
                    $('#save-info').hide();
                }
            });
            
            $('#item-menu-principal').click(function(){
                var add = $('#item-menu-add').text();
                var title = 'EMPLOYER DASHBOARD';
                $('#title-container').html(title);
                $('#option-list').html(add);
                $('#panel-add-employee').hide();
                $('#panel-upload-employee').hide();
                $('#container-info-users').show();
                $('#quantity-users').show();
            });
            
            $('#item-menu-add').click(function(){
                var add = $('#item-menu-add').text();
                var title = 'ADD AN EMPLOYEE';
                $('#title-container').html(title);
                $('#option-list').html(add);
                $('#panel-add-employee').show();
                $('#container-info-users').hide();
                $('#panel-upload-employee').hide();
                $('#quantity-users').hide();
            });
            
            $('#item-menu-upload').click(function(){
                var add = $('#item-menu-upload').text();
                var title = 'IMPORT EMPLOYEES';
                $('#title-container').html(title);
                $('#option-list').html(add);
                $('#panel-add-employee').hide();
                $('#container-info-users').hide();
                $('#quantity-users').hide();
                $('#panel-upload-employee').show();
            });
            
        });
        
        function edit(id){
            $('#single-'+id).prop('disabled', false);
            $('#single-'+id).removeClass('amount_unit');
            $('#single-'+id).addClass('amount_edit');
            var availablepoint = $('#available-point span').html();
            
            $("#single-"+id).on("keyup",function(event){
                
                var value_edit=$("#single-"+id).val();
                $("#partial_amount-"+id).val(value_edit);
                var valor2 = $("#partial_amount-"+id).val();
                var cashamount = (Math.round(valor2*25));
                var cashconvertion='COP'+' '+'$' + cashamount.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
                var calculo = availablepoint - valor2;
                var cashconvertion2='COP'+' '+'$' + (Math.round(calculo*25)).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
                
                $('#available-point span').html(calculo);
                $('.r_clase').attr('oninput', calculo);
                $("#amount_unit_cash-"+id).html(cashconvertion);
                $('#title-fluz span').html(cashconvertion2);
                
                if(calculo <= 0){
                    $('.r_clase').attr('oninput', availablepoint);
                    $("#single-"+id).val(availablepoint);
                    $('#available-point span').html(0);
                    $('#title-fluz span').html(0);
                }
                
            }).keydown(function( event ) {
              if ( event.which == 13) {
                event.preventDefault();
              }
            });
        }
        
    </script>
{/literal}
{literal}
    <script>       
        $("#use_allfluz").on("keyup",function(event){
            var valor1=$('#ptosTotalOculto').val();
            var valor2=$('#use_allfluz').val();
            var t_user = $('#total_users').val();
            if(valor2>=0){
                var resultado = calcular(valor1,valor2);
                var ptoUnit = (Math.round(valor2/t_user))+' '+' Fluz para Cada Fluzzer';
                var ptosingle = (Math.round(valor2/t_user));
                var cashamount = (Math.round((valor2/t_user)*25));
                var cashconvertion='COP'+' '+'$' + cashamount.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
                var ptoList = (Math.round(valor2/t_user));
                
                $('#ptosTotal').html(resultado);
                $('#ptosused').html(ptoUnit);
                $('#ptosusedhidden').val(valor2);
                $('#ptosdistributehidden').val(ptoList);
                $('.amount_unit').val(ptosingle);
                $('.amount_unit_cash').html(cashconvertion);
                $('.text_fluz').html('Fluz');
            }else{
                valor2*=-1;
                $('#use_allfluz').val(valor2);
                var resultado = calcular(valor1,valor2);
                var ptoUnit = (Math.round(valor2/t_user))+' '+' Fluz para Cada Fluzzer';
                var ptoList = (Math.round(valor2/t_user));
                $('#ptosTotal').html(resultado);
                $('#ptosused').html(ptoUnit);
                $('#ptosdistributehidden').val(ptoList);
                $('.amount_unit').val(ptosingle);
                $('.amount_unit_cash').html(cashconvertion);
                $('.text_fluz').html('Fluz');
            }
                
        }).keydown(function( event ) {
              if ( event.which == 13) {
                event.preventDefault();
              }
            });
        function calcular(valor1,valor2)
        {   
            return (valor1-valor2);
        }
    </script>
{/literal}
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
        <input type="hidden" value="{$pointsAvailable * (int)Configuration::get('REWARDS_VIRTUAL_VALUE_1')}" id="cash-available">
        <span class="available-point"> {displayPrice price=$pointsAvailable * (int)Configuration::get('REWARDS_VIRTUAL_VALUE_1')|escape:'html':'UTF-8'} </span>
    </div>
</div>
{if $error}
    <p class="error" style="margin-top: 10px;">
        {if $error == 'email invalid'}
            Direcci&oacute;n de email no es correcta.
        {elseif $error == 'name invalid'}
            El campo nombre o apellido no es correcto.
        {elseif $error == 'email exists'}
            Alguien con este email {$email} ya ha sido apadrinado
        {elseif $error == 'no sponsor'}
            {l s='No hay espacios disponibles en la red.'}
        {else if $error == 'already exists'}   
            El achivo {$csv} ya existe. Por favor cambiar el nombre del archivo CSV. 
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
              <a id="item-menu-principal" role="menuitem"  href="#">Panel Principal</a>
          </li>
          <li>
              <a id="item-menu-add" role="menuitem"  href="#">Agregar un Empleado</a>
          </li>
          <li>
              <a id="item-menu-upload" role="menuitem"  href="#">Agregar varios Empleados</a>
          </li>
        </ul>
    </div>
    <div class="col-lg-3 item-employee"><a href="/inicio/485-precarga-de-saldo-fluzfluz.html">Comprar Fluz</a></div>
    <div class="col-lg-3 item-employee" id="history-transfer">Historial de Transferencia</div>
    <div class="col-lg-3 item-search">
        <input type="hidden" value="{$id_customer}" id="id_customer"/>
        <div id="example_filter" class="dataTables_filter">
            <input type="text" name="busqueda" id="busqueda" class="is_required validate form-control input-infopersonal textsearch" autocomplete="off" placeholder="{l s='Buscar Fluzzer'}" required>
            <div id="resultados" class="result-find"></div>
        </div>
    </div>
</div>
<form method="post" id="trasnferbusiness" class="contenedorBusiness" name="trasnferbusiness">    
<div class="row container-info-users" id="container-info-users">
    <div class="row pagination-header">
        <div class="col-lg-2 pag-style"> Paginaci&oacute;n </div>
        <div class="col-lg-10 btn-save-user">
            <div class="col-lg-8 div-toggle"> 
                <div class="col-lg-6 button dropdown"> 
                    <select id="select-distribute" name="select-distribute">
                        <option value="select-option">Seleccione M&eacute;todo de Distribucci&oacute;n</option>
                       <option value="single-fluz">Distribucci&oacute;n Simple</option>
                       <option value="all-fluz">Distribucci&oacute;n a Todos</option>
                    </select>
                </div>
                <div class="col-lg-6" id="amount-use">
                    <input type="hidden" value="{$pointsAvailable}" id="ptosTotalOculto"/>
                    <input type="hidden" value="{$all_fluz}" id="total_users"/>
                    <input type="hidden" value="" id="ptosusedhidden"/>
                    <input type="hidden" value="" id="ptosdistributehidden"/>

                    <div class="col-lg-6">
                        <input class="col-lg-12" value="" type="number" min="25" max="{$pointsAvailable * (int)Configuration::get('REWARDS_VIRTUAL_VALUE_1')}" oninput="if(value>{$pointsAvailable * (int)Configuration::get('REWARDS_VIRTUAL_VALUE_1')})value={$pointsAvailable * (int)Configuration::get('REWARDS_VIRTUAL_VALUE_1')}" id="use_allfluz" autocomplete="off"/>
                    </div>
                    <div class="col-lg-4" id="ptosTotal">{l s=" Fluz "}</div>
                    <div class="col-lg-6" id="ptosused"></div>
                </div>
            </div>
            <div class="col-lg-4 div-btn">
                <button class="btn btn-default btn-save-table" type="submit" id="save-info" name="save-info">
                    <span> TRANSFERIR </span>
                </button>
            </div>
        </div>
    </div>
    <div class="row bar-info-users">
        <div class="col-lg-1 item-users"></div>
        <div class="col-lg-2 item-users" id="firstname">Nombre</div>
        <div class="col-lg-2 item-users" id="lastname">Apellido</div>
        <div class="col-lg-2 item-users" id="email">Email</div>
        <div class="col-lg-1 item-users" id="phone">Tel&eacute;fono</div>
        <div class="col-lg-2 item-users" id="dni">C&eacute;dula</div>
        <div class="col-lg-2 item-users" id="amount">Monto</div>
    </div>
    <div class="row row-container-info" id="container-List-employees">
        {foreach from=$network item=net}
            <div class="row content-info-users" id="content-users">
                <input type="hidden" id="id_sponsor" value="{$net.id_customer}">
                <input type="hidden" id="partial_amount-{$net.id_customer}" value="">
                <input type="hidden" id="email_id" value="{$net.email}">
                
                <div class="col-lg-1 content-item-users">
                    <input type="checkbox" id="check-user" value="">
                </div>
                <div class="col-lg-2 content-item-users">{$net.firstname}</div>
                <div class="col-lg-2 content-item-users">{$net.lastname}</div>
                <div class="col-lg-2 content-item-users email-id">{$net.email}</div>
                <div class="col-lg-1 content-item-users">Phone</div>
                <div class="col-lg-2 content-item-users">{$net.dni}</div>
                <div class="col-lg-2 content-item-users" id="amount_unit">
                    <div class="row">
                        <input class="col-lg-5 r_clase amount_unit" oninput="" sponsor="{$net.id_customer}" id="single-{$net.id_customer}" value="0" type="text" min="25" max="" autocomplete="off"/>
                        <div class="col-lg-3 text_fluz">$ COP</div>   
                        <div class="col-lg-4 edit-btn" id="btn-edit" onclick="edit({$net.id_customer})">Editar</div>
                    </div>
                    <div class="col-lg-12 amount_unit_cash" id="amount_unit_cash-{$net.id_customer}">Fluz</div>
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
<div id="panel-allocation-history" style="display:none;">
    <div class="row">
        {include file="./allocation_history.tpl"}
    </div>
</div>        
{literal}
    <style>
        #right_column{display: none;}
    </style>
{/literal}
{literal}
    <script>
        $(document).ready(function(e){
              
                $("#busqueda").keyup(function(e){
                    var username = $("#busqueda").val();
                    
                    $('#container-List-employees > div').each(function () {
                        $(this).show();
                        var email = $(this).find('.email-id').html().toLowerCase();
                        if (email.indexOf(username.toLowerCase()) != -1){

                        }
                        else {
                            $(this).hide();
                        }
                    });
                });
            });
    </script>
{/literal}
{literal}
    <script>
        $(document).ready(function(){
            var add = $('#item-menu-principal').text();
            var title = 'PANEL PRINCIPAL';
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
                            success : function(a) {
                                console.log(a);
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
                        var amount_edit = ($( this ).val())/25;
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
                var add = $('#item-menu-principal').text();
                var title = 'PANEL PRINCIPAL';
                $('#option-list').html(add);
                $('#title-container').html(title);
                $('#panel-add-employee').hide();
                $('#panel-upload-employee').hide();
                $('#container-info-users').show();
                $('#quantity-users').show();
                $('#panel-allocation-history').hide();
                $('#history-transfer').removeClass('active_btn');
            });
            
            $('#item-menu-add').click(function(){
                var add = $('#item-menu-add').text();
                var title = 'AGREGAR UN EMPLEADO';
                $('#title-container').html(title);
                $('#option-list').html(add);
                $('#panel-add-employee').show();
                $('#container-info-users').hide();
                $('#panel-upload-employee').hide();
                $('#quantity-users').hide();
                $('#panel-allocation-history').hide();
                $('#history-transfer').removeClass('active_btn');
            });
            
            $('#item-menu-upload').click(function(){
                var add = $('#item-menu-upload').text();
                var title = 'AGREGAR VARIOS EMPLEADOS';
                $('#title-container').html(title);
                $('#option-list').html(add);
                $('#panel-add-employee').hide();
                $('#container-info-users').hide();
                $('#quantity-users').hide();
                $('#panel-upload-employee').show();
                $('#panel-allocation-history').hide();
                $('#history-transfer').removeClass('active_btn');
            });
            
            $('#history-transfer').click(function(){
                var add = $('#item-menu-principal').text();
                var title = 'HISTORIAL DE TRANSFERENCIAS';
                $('#title-container').html(title);
                $('#option-list').html(add);
                $('#panel-add-employee').hide();
                $('#panel-upload-employee').hide();
                $('#container-info-users').hide();
                $('#panel-allocation-history').show();
                $('#quantity-users').hide();
                $('#history-transfer').addClass('active_btn');
            });
            
        });
        
        function edit(id){
            $('#single-'+id).prop('disabled', false);
            $('#single-'+id).removeClass('amount_unit');
            $('#single-'+id).addClass('amount_edit');
            var availablepoint = $('#available-point span').html();
            var availablecash = $('#cash-available').val();
            
            $("#single-"+id).on("keyup",function(event){
                
                var value_edit=$("#single-"+id).val();
                $("#partial_amount-"+id).val(value_edit);
                var valor2 = $("#partial_amount-"+id).val();
                var cashamount = (Math.round(valor2/25));
                var cashconvertion= cashamount +' '+'Fluz';
                var ptosMax = availablepoint+' '+'Fluz';
                var calculo = availablepoint - cashamount;
                var cashconvertion2='COP'+' '+'$' + (Math.round(calculo*25)).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
                
                $('#available-point span').html(calculo);
                $('.r_clase').attr('oninput', availablecash);
                $("#amount_unit_cash-"+id).html(cashconvertion);
                $('#title-fluz span').html(cashconvertion2);
                
                if(calculo <= 0){
                    
                    $("#amount_unit_cash-"+id).html(ptosMax);
                    $('.r_clase').attr('oninput', availablepoint);
                    $("#single-"+id).val(availablecash);
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
            var availablecash = $('#cash-available').val();
            
            if(valor2>=0){
                var resultado = calcular(valor1,valor2);
                var ptoUnit = (Math.round((valor2/25)/t_user))+' '+' Fluz para Cada Fluzzer';
                var ptosingle = (Math.round((valor2/25)/t_user));
                var cashamount = (Math.round((valor2/t_user)*25));
                var cashconvertion='COP'+' '+'$' + cashamount.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
                var ptoList = (Math.round((valor2/25)/t_user));
                var result2 = valor1 - (ptoList*t_user);
                var resultCop = availablecash - valor2;
                var cashconvertion2='COP'+' '+'$' + (Math.round(resultCop)).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");

                $('#ptosTotal').html(resultado);
                $('#ptosused').html(ptoUnit);
                $('#ptosusedhidden').val(valor2);
                $('#ptosdistributehidden').val(ptoList);
                $('.amount_unit').val(ptosingle);
                $('.amount_unit_cash').html(cashconvertion);
                $('.text_fluz').html('Fluz');
                $('#available-point span').html(result2);
                $('#title-fluz span').html(cashconvertion2);
                
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
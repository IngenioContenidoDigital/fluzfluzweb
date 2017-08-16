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
<div id="error_p" style="display: none;">
    <div class="row border-report">
        <div class="col-lg-6 error_page" id="error_page">
            <h2 class="title-error">Reporte de Errores</h2>
        </div>
        <div  class="col-lg-6 download-error">
            <div><a href="../csvcustomer/carga_customer_example.csv" class="link-down">DESCARGAR</a></div>
        </div>
    </div>
    <div class="row">
        <p style="margin: 20px 0px;"> Listado de usuarios con errores. Por Favor Verificar la informaci&oacute;n de los empleados.</p>
    </div>
    <div class="row">
        <div class="container-error" id="container-error">
            
        </div>
    </div>
    <div class="row div-btn-error">
        <a class="btn btn-default button button-small" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"><span>{l s='regresar a mi cuenta'}</span></a>
        <a class="btn btn-default btn-back-business" href="{$link->getPageLink('business', true)|escape:'html':'UTF-8'}"><span>{l s='regresar al panel principal'}</span></a>
    </div>
</div>
{if $error_csv}
<div id="error_p">
    <div class="row border-report">
        <div class="col-lg-6 error_page" id="error_page">
            <h2 class="title-error">Reporte de Errores</h2>
        </div>
        <div  class="col-lg-6 download-error">
            <div><a href="../csvcustomer/carga_customer_example.csv" class="link-down">DESCARGAR</a></div>
        </div>
    </div>
    <div class="row">
        <p style="margin: 20px 0px;"> Listado de usuarios con errores. Por Favor Verificar la informaci&oacute;n de los empleados.</p>
    </div>
    <div class="row">
        <div class="container-error">
            {foreach from=$error_csv item=errorc}
                <p class="error" style="margin-top: 10px;">
                    {if $errorc.email == 'email invalid'}
                        <span style="color: #EF4136;">&#33;</span> Direcci&oacute;n de email no es correcta.
                    {elseif $errorc.name == 'name invalid'}
                        <span style="color: #EF4136;">&#33;</span> El campo nombre o apellido <span style="color: #EF4136;">{$errorc.name_custom}</span> no es correcto.
                    {elseif $errorc.email_exists == 'email exists'}
                        <span style="color: #EF4136;">&#33;</span> Alguien con este email <span style="color: #EF4136;">{$errorc.email}</span> ya ha sido apadrinado
                    {elseif $errorc.dni_exists == 'dni exists'}
                        <span style="color: #EF4136;">&#33;</span> La Cedula <span style="color: #EF4136;">{$errorc.cedula}</span> ya se encuentra Registrada en Fluz Fluz. Por Favor Revisa tu CSV.
                    {elseif $errorc.valid_phone == 'valid phone'}
                        <span style="color: #EF4136;">&#33;</span> El Tel&eacute;fono <span style="color: #EF4136;">{$errorc.phone}</span> ya se encuentra Registrado en Fluz Fluz. Por Favor Revisa tu CSV.
                    {elseif $errorc.valid_username == 'valid username'}
                        <span style="color: #EF4136;">&#33;</span> El Usuario <span style="color: #EF4136;">{$errorc.username}</span> ya se encuentra Registrado en Fluz Fluz. Por Favor Revisa tu CSV.
                    {elseif $errorc.sponsor == 'no sponsor'}
                        {l s='No hay espacios disponibles en la red.'}
                    {else if $errorc.csv == 'already exists'}   
                        <span style="color: #EF4136;">&#33;</span> El achivo <span style="color: #EF4136;">{$errorc.csv_name}</span> ya existe. Por favor cambiar el nombre del archivo CSV. 
                    {else if $errorc.csv_number == 'registro'}
                        <span style="color: #EF4136;">&#33;</span> No es posible importar mas de 140 registros. Por favor validar y reducir la cantidad de registros.
                    {/if}
                </p>
            {/foreach}
        </div>
    </div>
    <div class="row div-btn-error">
        <a class="btn btn-default button button-small" href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}"><span>{l s='regresar a mi cuenta'}</span></a>
        <a class="btn btn-default btn-back-business" href="{$link->getPageLink('business', true)|escape:'html':'UTF-8'}"><span>{l s='regresar al panel principal'}</span></a>
    </div>        
{literal}
    <style>
        #rewards_account{display: none;}
    </style>
{/literal}
</div>
{/if}
<div id="rewards_account" class="rewards">
{if version_compare($smarty.const._PS_VERSION_,'1.6','<')}
    {include file="$tpl_dir./breadcrumb.tpl"}
{/if}

<div class="row title-row-business"> 
    <div class="col-lg-12"><span class="title-business" id="title-container"></span></div>
    <div class="col-lg-6 margin-info">
            <div id="quantity-users" class="col-lg-12"> Cantidad de Empleados: <span class="available-point"> {$all_fluz} </span></div>
            <div style="padding-left:0px;" id="available-point" class="col-lg-12 title-fluz">{l s="Fluz Totales: "}<span class="available-point">{$pointsAvailable}</span></div>
            <div style="padding-left:0px;" class="col-lg-12 title-fluz" id="title-fluz">{l s="Fluz en Dinero: "}
                <input type="hidden" value="{$pointsAvailable * (int)Configuration::get('REWARDS_VIRTUAL_VALUE_1')}" id="cash-available">
                <span class="available-point"> {displayPrice price=$pointsAvailable * (int)Configuration::get('REWARDS_VIRTUAL_VALUE_1')|escape:'html':'UTF-8'} </span>
            </div>
    </div>
    <div class="col-lg-6 div-img">
        <img src="/img/business/{$id_customer}.png" class="img-business" />
        <div class="text-business">{$username}</div>
    </div>
</div>
{if $error}
    {foreach from=$error item=errorc}
        <p class="error" style="margin-top: 10px;">
            {if $errorc.email == 'email invalid'}
                Direcci&oacute;n de email no es correcta.
            {elseif $errorc.name == 'name invalid'}
                El campo nombre o apellido no es correcto.
            {elseif $errorc.email_exists == 'email exists'}
                <span style="color: #EF4136;">&#33;</span> Alguien con este email <span style="color: #EF4136;">{$errorc.email}</span> ya ha sido apadrinado
            {elseif $errorc.dni_exists == 'dni exists'}
                <span style="color: #EF4136;">&#33;</span> La Cedula <span style="color: #EF4136;">{$errorc.cedula}</span> ya se encuentra Registrada en Fluz Fluz. Por Favor Revisa tu CSV.
            {elseif $errorc.valid_phone == 'valid phone'}
                <span style="color: #EF4136;">&#33;</span> El Tel&eacute;fono <span style="color: #EF4136;">{$errorc.phone}</span> ya se encuentra Registrado en Fluz Fluz. Por Favor Revisa tu CSV.
            {elseif $errorc.valid_username == 'valid username'}
                <span style="color: #EF4136;">&#33;</span> El Usuario <span style="color: #EF4136;">{$errorc.username}</span> ya se encuentra Registrado en Fluz Fluz. Por Favor Revisa tu CSV.
            {elseif $errorc.sponsor == 'no sponsor'}
                {l s='No hay espacios disponibles en la red.'}
            {else if $errorc.csv == 'already exists'}   
                El achivo {$csv} ya existe. Por favor cambiar el nombre del archivo CSV. 
            {/if}
        </p>
    {/foreach}
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
            <input type="text" name="busqueda" id="busqueda" class="is_required validate form-control input-infopersonal textsearch" autocomplete="off" placeholder="{l s='Buscar Empleado'}" required>
            <div id="resultados" class="result-find"></div>
        </div>
    </div>
</div>
<form method="post" id="trasnferbusiness" class="contenedorBusiness" name="trasnferbusiness">    
<div class="row container-info-users" id="container-info-users">
    <div class="row pagination-header">
        <div class="col-lg-1 pag-style"> Paginaci&oacute;n </div>
        <div class="col-lg-11 btn-save-user">
            <div class="col-lg-10 div-toggle"> 
                <div class="col-lg-5 button dropdown"> 
                    <select id="select-distribute" name="select-distribute">
                        <option value="select-option">Seleccione M&eacute;todo de Distribucci&oacute;n</option>
                       <option value="single-fluz">Distribucci&oacute;n Uno a Uno</option>
                       <option value="all-fluz">Distribucci&oacute;n Igualitaria</option>
                       <option value="all-group">Distribucci&oacute;n por Grupo (csv)</option>
                    </select>
                </div>
                <div class="col-lg-7" id="amount-use">
                    <input type="hidden" value="{$pointsAvailable}" id="ptosTotalOculto"/>
                    <input type="hidden" value="{$all_fluz}" id="total_users"/>
                    <input type="hidden" value="" id="ptosusedhidden"/>
                    <input type="hidden" value="" id="ptosdistributehidden"/>

                    <div class="col-lg-7">
                        <div class="col-lg-4" style="padding-left: 0;margin-top: 7px;">COP $</div>
                        <input class="col-lg-8" value="" type="number" min="25" max="{$pointsAvailable * (int)Configuration::get('REWARDS_VIRTUAL_VALUE_1')}" oninput="if(value>{$pointsAvailable * (int)Configuration::get('REWARDS_VIRTUAL_VALUE_1')})value={$pointsAvailable * (int)Configuration::get('REWARDS_VIRTUAL_VALUE_1')}" id="use_allfluz" autocomplete="off"/>
                    </div>
                    <div class="col-lg-5" id="ptosused"></div>
                </div>
                <div class="col-lg-7 row-upload-transfer" id="row-upload-transfer">
                    <div class="col-lg-5 title-browser">
                        <div class="col-lg-12 title-panel-upload-transfer"> Importar CSV para Transferencia</div>
                        <div class="col-lg-12" style="font-size: 10px;"> Descargar <a href="../csvcustomer/carga_transfer_example.csv" class="link-down">CSV de Ejemplo</a></div>
                    </div>
                    <div class="col-lg-7 browse-div">
                        <div class="col-lg-12 custom-file-upload">
                            <!--<label for="file">File: </label>--> 
                            <input type="file" name="file" id="file" />
                        </div>
                    </div>
                </div>
                <div class="col-lg-7 div-btn-delete">
                    <button class="myfancybox col-lg-6 btn btn-default btn-delete-employee" href="#confirmDelete" id="delete_employee">
                        <span> ELIMINAR EMPLEADO </span>
                    </button>
                </div>   
            </div>
            <div class="col-lg-2 div-btn">
                <button class="myfancybox btn btn-default btn-save-table" href="#confirmTransfer" id="save-info" name="save-info">
                    <span> TRANSFERIR </span>
                </button>
            </div>
        </div>
    </div>
    <div class="error" id="error" style="display:none;"></div>  
    <div class="success" id="success" style="display:none;"></div>  
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
                    <input type="checkbox" id="check-user-{$net.id_customer}" class="check_user" value="{$net.id_customer}">
                </div>
                <div class="col-lg-2 content-item-users" id="name_employee-{$net.id_customer}">{$net.firstname}</div>
                <div class="col-lg-2 content-item-users" id="lastname_employee-{$net.id_customer}">{$net.lastname}</div>
                <div class="col-lg-2 content-item-users email-id">{$net.email}</div>
                <div class="col-lg-1 content-item-users">{$net.phone}</div>
                <div class="col-lg-2 content-item-users dni-id">{$net.dni}</div>
                <div class="col-lg-2 content-item-users" id="amount_unit">
                    <div class="row">
                        <input class="col-lg-5 r_clase amount_unit" oninput="" sponsor="{$net.id_customer}" id="single-{$net.id_customer}" value="0" type="text" min="25" autocomplete="off"/>
                        <div class="col-lg-3 text_fluz">$ COP</div>   
                        <div class="col-lg-4 edit-btn" id="btn-edit" onclick="edit({$net.id_customer})">Editar</div>
                    </div>
                    <div class="col-lg-12 amount_unit_cash" id="amount_unit_cash-{$net.id_customer}">Fluz</div>
                </div>
            </div>
        {/foreach}
    </div>
</div>
<div style="display:none;">
    <div id="confirmTransfer" class="myfancybox">
        <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <br>
            <img class="logo img-responsive" src="https://fluzfluz.co/img/fluzfluz-logo-1464806235.jpg" alt="FluzFluz" width="356" height="94">
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 title_transfer"> Confirmaci&oacute;n Envio Fluz </div>
        <div class="row info-transfer">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 t-name">Fluz a Enviar: </div><div id="fluz_send" class="col-lg-6 col-md-6 col-sm-6 col-xs-6 name_sponsor"></div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 t-name">Fluz en Dinero: </div><div id="fluz_send_cash" class="col-lg-6 col-md-6 col-sm-6 col-xs-6 name_sponsor"></div>
        </div>
        <div class="row row-btn-modal">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 btn-confirm-modal">
                <button class="btn btn-default btn-account" type="submit" id="save-info-process" name="save-info-process" style="background:#c9b198;">
                    <span class="btn_modal_f"> Confirmar </span>
                </button>        
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 btn-cancel-modal">
                <button class="btn btn-default btn-account" id="cancel_modal_fluz" onclick="cancelSubmit()">
                    <span class="btn_modal_f">
                        {l s="Cancelar"}
                    </span>
                </button>
            </div>
        </div>
    </div>
</div> 
<div style="display:none;">
    <div id="confirmDelete" class="myfancybox">
        <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <br>
            <img class="logo img-responsive" src="https://fluzfluz.co/img/fluzfluz-logo-1464806235.jpg" alt="FluzFluz" width="356" height="94">
        </div>
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 title_transfer"> Eliminaci&oacute;n de Empleado </div>
        <div class="row info-transfer">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 name_sponsor_delete"> Seguro deseas Eliminar al Empleado de tu Red Empresarial ? </div>
            <div id="user_delete" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 name_sponsor"></div>
        </div>
        <div class="row row-btn-modal">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 btn-confirm-modal">
                <button class="btn btn-default btn-account" type="submit" id="delete-info-process" name="delete-info-process" style="background:#c9b198;">
                    <span class="btn_modal_f"> Confirmar </span>
                </button>        
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 btn-cancel-modal">
                <button class="btn btn-default btn-account" id="cancel_modal_fluz" onclick="cancelSubmit()">
                    <span class="btn_modal_f">
                        {l s="Cancelar"}
                    </span>
                </button>
            </div>
        </div>
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
<div id="url_fluz" style="display:none;">{$base_dir_ssl}</div>
{literal}
    <script>
        $(document).ready(function(e){
              
                $("#busqueda").keyup(function(e){
                    var username = $("#busqueda").val();
                    
                    $('#container-List-employees > div').each(function () {
                        $(this).show();
                        var email = $(this).find('.email-id').html().toLowerCase();
                        var dni = $(this).find('.dni-id').html().toLowerCase();
                        if (email.indexOf(username.toLowerCase()) != -1){

                        }
                        else if(dni.indexOf(username.toLowerCase()) != -1){

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
            $('#row-upload-transfer').hide();
            $('#delete_employee').hide();
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
                    $('#error').css('display','none');
                    $('#success').css('display','none');
                    $('#row-upload-transfer').hide();
                    $('#delete_employee').hide();
                    $('.r_clase').addClass('amount_unit');
                    $('.r_clase').removeClass('amount_edit');
                    $('.r_clase').val(0);
                    $('#use_allfluz').empty();
                    
                    $('#save-info').click(function(){
                        var ptoUsed = $('#ptosusedhidden').val();       
                        if(ptoUsed == '' || ptoUsed == 0){
                            alert('Seleccione Cantidad de Fluz a enviar.')
                            $('#save-info').removeClass('myfancybox');
                            location.reload();
                            e.preventDefault();
                        }
                    });
                    
                    $('#save-info-process').unbind("click");
                    $('#save-info-process').click(function(){
                       //var ptoDistribute = $('#ptosdistributehidden').val();
                       var ptoUsed = $('#ptosusedhidden').val();       
                       $(this).prop("disabled",true);
                       $('#cancel_modal_fluz').prop('disabled',true);
                       var listEdit = [];  
                       var total_point = 0;
                       var url = document.getElementById("url_fluz").innerHTML;
                       
                       $( ".r_clase" ).each(function( index ) {
                            var id_sponsor = $(this).attr("sponsor");
                            var amount_edit = ($( this ).val())/25;

                            if($('#partial_amount-'+id_sponsor).val() !== ''){
                                amount_edit = $('#partial_amount-'+id_sponsor).val();
                            }
                            total_point += Number($(this).val());

                            var item = {}
                            if(amount_edit != 0){
                                item ["id_sponsor"] = id_sponsor;
                                item ["amount"] = amount_edit;
                            }

                            listEdit.push(item);
                        });
                       
                        listEdit = JSON.stringify(listEdit);
                       
                       $.ajax({
                            url : urlTransferController,
                            type : 'POST',
                            data : 'action=allFLuz&listEdit='+listEdit+'&ptoUsed='+ptoUsed,
                            success : function() {
                                window.location.replace(""+url+"confirmtransferfluzbusiness");
                            }
                        });
                    });
                    
                }
                else if(select == 'single-fluz'){
                    
                    $('#container-List-employees').removeClass("disabledbutton");
                    $('#amount-use').hide();
                    $('#row-upload-transfer').hide();
                    $('#save-info').show();
                    $(".amount_unit").prop('disabled', true);
                    $(".amount_unit").css('background', 'transparent');
                    $(".amount_unit").val(0);
                    $('#delete_employee').hide();
                    $('#error').css('display','none');
                    $('#success').css('display','none');
                    $('.check_user').click(function() {
                        if ($(this).is(':checked')) {
                            //codigo para eliminar usuario de la red+
                            
                            var url = document.getElementById("url_fluz").innerHTML;
                            var check_delete = $(this).val();
                            var name = $('#name_employee-'+check_delete).html();
                            var lastname = $('#lastname_employee-'+check_delete).html();
                            
                            $('#delete_employee').show();
                            $('#user_delete').html(name+' '+lastname);
                            $('#delete-info-process').click(function(){
                                $.ajax({
                                    url : urlTransferController,
                                    type : 'POST',
                                    data : 'action=kickoutemployee&id_employee='+check_delete,
                                    success : function(id) {
                                         console.log(id);
                                         window.location.replace(""+url+"confirmdeleteusers");
                                    }
                                });
                            });
                        }
                        else{
                            check_delete = "";
                            $('#delete_employee').hide();
                        }
                    });   
                    
                    $('#save-info').click(function(){
            
                        var total_point = 0;            
                        $( ".amount_edit" ).each(function( index ) {

                            total_point += Number($(this).val());

                        });
                        var fluz = Math.round((total_point/25));
                        var cashconvertionfluz='COP'+' '+'$' + Math.round(total_point).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
                        $('#fluz_send').html(fluz);
                        $('#fluz_send_cash').html(cashconvertionfluz);
                        
                        if(total_point== '' || total_point == 0){
                            alert('Seleccione Cantidad de Fluz a enviar.')
                            $('#save-info').removeClass('myfancybox');
                            location.reload();
                            e.preventDefault();
                        }

                    });
                    
                    $('#save-info-process').unbind("click");
                    $('#save-info-process').click(function(){
                    
                    var listEdit = [];  
                    var total_point = 0;
                    var url = document.getElementById("url_fluz").innerHTML;
                    $(this).prop("disabled",true);
                    $('#cancel_modal_fluz').prop('disabled',true);
                    
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
                                 window.location.replace(""+url+"confirmtransferfluzbusiness");
                            }
                        });
                    });
                }
                else if(select == 'all-group'){
                    $('#container-List-employees').addClass("disabledbutton");
                    $('#amount-use').hide();
                    $('#save-info').show();
                    $('#row-upload-transfer').show();
                    $('#delete_employee').hide();
                    $('.r_clase').addClass('amount_unit');
                    $('.r_clase').removeClass('amount_edit');
                    $('.r_clase').val(0);
                    $('#use_allfluz').empty();
                    
                    $('#save-info').click(function(){
                        var ptoUsed = $('#ptosusedhidden').val();       
                        if(ptoUsed == '' || ptoUsed == 0){
                            alert('Seleccione Cantidad de Fluz a enviar.')
                            $('#save-info').removeClass('myfancybox');
                            location.reload();
                            e.preventDefault();
                        }
                    });
                    
                    $('#save-info-process').unbind("click");
                    $('#save-info-process').click(function(){
                       //var ptoDistribute = $('#ptosdistributehidden').val();
                       var ptoUsed = $('#ptosusedhidden').val();       
                       $(this).prop("disabled",true);
                       $('#cancel_modal_fluz').prop('disabled',true);
                       var listEdit = [];  
                       var total_point = 0;
                       var url = document.getElementById("url_fluz").innerHTML;
                       
                       $( ".r_clase" ).each(function( index ) {
                            var id_sponsor = $(this).attr("sponsor");
                            var amount_edit = ($( this ).val())/25;

                            if($('#partial_amount-'+id_sponsor).val() !== ''){
                                amount_edit = $('#partial_amount-'+id_sponsor).val();
                            }
                            total_point += Number($(this).val());

                            var item = {}
                            if(amount_edit != 0){
                                item ["id_sponsor"] = id_sponsor;
                                item ["amount"] = amount_edit;
                            }

                            listEdit.push(item);
                        });
                       
                        listEdit = JSON.stringify(listEdit);
                       
                       $.ajax({
                            url : urlTransferController,
                            type : 'POST',
                            data : 'action=allFLuz&listEdit='+listEdit+'&ptoUsed='+ptoUsed,
                            success : function() {
                                window.location.replace(""+url+"confirmtransferfluzbusiness");
                            }
                        });
                    });
                }
                else{
                    $('#error').css('display','none');
                    $('#success').css('display','none');
                    $('#delete_employee').hide();
                    $('#container-List-employees').addClass("disabledbutton");
                    $('#amount-use').hide();
                    $('#save-info').hide();
                    $('#row-upload-transfer').hide();
                    $('.r_clase').addClass('amount_unit');
                    $('.r_clase').removeClass('amount_edit');
                    $('.r_clase').val(0);
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
                var cashconvertion2='COP'+' '+'$' + (Math.round(calculo*25)).toFixed(0).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
                
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
            $('.custom-file-upload').prop('disabled',true);
            if(valor2>=0){
                var resultado = calcular(valor1,valor2);
                var ptoUnit = (Math.round((valor2/25)/t_user))+' '+' Fluz para Cada Fluzzer';
                var ptosingle = Math.round((valor2/25));
                var cashconvertionfluz='COP'+' '+'$' + Math.round(valor2).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
                var ptoListview = (Math.round((valor2/25)/t_user))+' '+'Fluz';
                var ptoList = (Math.round((valor2/25)/t_user));
                var cashamount = ptoList * 25;
                var cashconvertion= cashamount.toFixed(0).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
                var result2 = valor1 - (ptoList*t_user);
                var resultCop = availablecash - valor2;
                var cashconvertion2='COP'+' '+'$' + (Math.round(resultCop)).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");

                $('#ptosTotal').html(resultado);
                $('#ptosused').html(ptoUnit);
                $('#ptosusedhidden').val(valor2);
                $('#fluz_send').html(ptosingle);
                $('#fluz_send_cash').html(cashconvertionfluz);
                $('#ptosdistributehidden').val(ptoList);
                $('.amount_unit').val(cashconvertion);
                $('.amount_unit_cash').html(ptoListview);
                $('.text_fluz').html('$ COP');
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
                $('.amount_unit').val(ptoList);
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
{literal}
    <script>
        function cancelSubmit(){ 
            $.fancybox.close();
            location.reload();
        }
    </script>
    <script>
        $("#file").change(function(e) {
            //$('#use_allfluz').prop('disabled',true);
            $('#use_allfluz').css('opacity', '0.5');
            $('#error').css('display','none');
            $('#success').css('display','none');
            $('#save-info').prop('disabled',false);
            var file = document.getElementById('file').files[0],
            reader = new FileReader();
            reader.onload = function(event) {
                
                var array = event.target.result;
                var extractValidString = array.match(/[\w @.]+(?=,?)/g);
                var noOfCols = 3;
                var objFields = extractValidString.splice(0,noOfCols);
                var arr = [];
                var flag = true;
                while(extractValidString.length>0) {
                    var obj = {};
                    var row = extractValidString.splice(0,noOfCols)
                    if(row.length == 3){
                        for(var i=0;i<row.length;i++) {
                            obj[objFields[i]] = row[i].trim();
                            flag = true;
                        }
                        arr.push(obj)
                    }
                    else{
                        flag = false;
                    }   
                }
                
                var sum = 0;
                $.each(arr, function( index, value ) {
                            if(flag == true){
                                var monto = value.montotransferencia;
                                var convmonto = monto.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
                                var monto_fluz_view = value.montotransferencia/25+' '+'Fluz';
                                var monto_fluz = value.montotransferencia/25;
                                sum += parseInt(monto);
                                $('#container-List-employees > div').each(function () {
                                    var email = $(this).find('.email-id').html();
                                    var id_custom = $(this).find('#id_sponsor').val();
                                    if(email == value.email){
                                        $('#single-'+id_custom).val(convmonto);
                                        $('#amount_unit_cash-'+id_custom).html(monto_fluz_view);
                                        $("#partial_amount-"+id_custom).val(monto_fluz);
                                    }
                                });
                           }
                        });
                
                if(sum != '' && sum != 0){
                    $('#ptosusedhidden').val(sum);   
                    var fluz = Math.round((sum/25));
                    var cashconvertionfluz='COP'+' '+'$' + Math.round(sum).toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
                    $('#fluz_send').html(fluz);
                    $('#fluz_send_cash').html(cashconvertionfluz);
                }
                
                if(flag == false){
                    $('#error').css('display','block');
                    $('#success').css('display','none');
                    $('#error').append("<b>Tu Archivo CSV contiene errores o Campos Vacios. Por Favor Verificarlo.</b>");;
                    $('#file').attr({ value: '' });
                    $('#save-info').prop('disabled',true);
                }
                else{
                    list_transfer = JSON.stringify(arr);
                    $('#success').text(' ');
                    $('#error').text(' ');
                    $.ajax({
                    url : urlTransferController,
                    type : 'POST',
                    data : 'action=uploadtransfers&list_transfer='+list_transfer,
                        success : function(data) {
                           console.log(data); 
                           if(data != ''){ 
                                $('#error').css('display','block');
                                $('#success').css('display','none');
                                $('#error').text(data);
                                $('#file').attr({ value: '' });
                                $('#save-info').prop('disabled',true);
                            }
                            else{
                                $('#file').attr({ value: '' });
                                $('#error').css('display','none');
                                $('#success').css('display','block');
                                $('#success').append("<b>Tu Archivo CSV no contiene errores.</b>");;
                            }
                        }
                    });
                }
            };
            reader.readAsText(file);
        });
    </script>
{/literal}

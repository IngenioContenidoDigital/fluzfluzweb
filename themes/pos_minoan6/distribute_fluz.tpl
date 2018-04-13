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
                        <div class="col-lg-4 title-browser">
                            <div class="col-lg-12 title-panel-upload-transfer"> Importar CSV para Transferencia</div>
                            <div class="col-lg-12" style="font-size: 10px;"> Descargar <a href="../csvcustomer/carga_transfer_example.csv" class="link-down">CSV de Ejemplo</a></div>
                        </div>
                        <div class="col-lg-8 browse-div">
                            <div class="col-lg-12 custom-file-upload">
                                <!--<label for="file">File: </label>--> 
                                <input type="file" name="file" id="file" />
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 item-search">
                        <input type="hidden" value="{$id_customer}" id="id_customer"/>
                        <div id="example_filter" class="dataTables_filter">
                            <input type="text" name="busqueda" id="busqueda" class="is_required validate form-control input-infopersonal textsearch" autocomplete="off" placeholder="{l s='Buscar Empleado'}" required>
                            <div id="resultados" class="result-find"></div>
                        </div>
                    </div>
                    <div class="col-lg-3 div-btn-delete">
                        <button disabled class="myfancybox col-lg-6 btn btn-default btn-delete-employee" href="#confirmDelete" id="delete_employee">
                            <span> ELIMINAR EMPLEADO </span>
                        </button>
                        <span class="div-btn-delete-info">Ning&uacute;n usuario seleccionado</span>
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
                    <input type="hidden" class="ptos_all" id="partial_amount-{$net.id_customer}" value="">
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
            <div class="row progress-container" id="progress-bar" style="display:none;">
                <div class="progress">
                    <div class="progress-bar">
                            <div class="progress-shadow"></div>
                    </div>
                </div>
                <div class="text-loader">Estamos Procesando tu solicitud de Transferencia. Por Favor Espera</div>
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
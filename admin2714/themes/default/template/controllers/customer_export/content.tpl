{*
* 2007-2016 PrestaShop
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
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{if $content}
	{$content}
{else}
	<iframe class="clearfix" style="margin:0px;padding:0px;width:100%;height:920px;overflow:hidden;border:none" src="//addons.prestashop.com/iframe/search.php?isoLang={$iso_lang}&amp;isoCurrency={$iso_currency}&amp;isoCountry={$iso_country}&amp;parentUrl={$parent_domain}"></iframe>
{/if}
<div class="panel" style='height: 450px !important; overflow-y: auto !important;'>
        <div class="panel-heading">
                <i class="icon-group"></i>
                {l s='List Fluzzers network'}
        </div>
        <div class="col-lg-6 col-md-8 col-sm-8 col-xs-12" style="margin-bottom: 20px;">
            <input type="hidden" id="id_customer" name="id_customer" value="{if $id_member != ""}{$id_member}{else}{/if}"/>
            <input type="hidden" id="customer_name" name="customer_name" value="{if $name_member != ""}{$name_member}{else}{/if}"/>
            <div class="text-infouser"> 
                <div class="col-lg-6">
                    <input type="text" name="busqueda" id="busqueda" value="{if $name_member != ""}{$name_member}{else}{/if}" class="is_required validate form-control input-infopersonal textsearch" autocomplete="off" placeholder="{l s='Buscar Fluzzer'}" required>
                    <div id="resultados" class="result-find"></div>
                </div>
                <div class="col-lg-6">
                    <button onclick="searchFunc();" type="submit" id="submitFilterButtoncustomer" name="submitFilter" class="btn btn-default" data-list-id="customer">
                        <i class="icon-search"></i> Buscar
                    </button>                
                    <button onclick="cleanCustomer();" type="submit" id="cleanCustomer" name="cleanCustomer" class="btn btn-default">
                        <i class="icon-refresh"></i> Limpiar Busqueda
                    </button> 
                </div>
            </div>
        </div>
        <div class="row" id="table-customer-net"> 
            <table class="table">
                <thead>
                    <tr>
                        <th><span class="title_box">{l s='Nivel'}</span></th>
                        <th><span class="title_box">{l s='Metodo Add'}</span></th>
                        <th><span class="title_box">{l s='Mail Invitado'}</span></th>
                        <th><span class="title_box">{l s='Nombre Invitado'}</span></th>
                        <th><span class="title_box">{l s='Mail Invitador'}</span></th>
                        <th><span class="title_box">{l s='Nombre Invitador'}</span></th>
                        <th><span class="title_box">{l s='Dia Ingreso'}</span></th>
                    </tr>
                </thead>
                <tbody id="table-c" class="result-network">
                        
                </tbody>
            </table>
        </div>          
</div>
{literal}
    <script>
        $(document).ready(function(e){
            $('#table-customer-net').hide();
            $("#busqueda").keyup(function(e){
                var username = $("#busqueda").val();
                console.log(username);
                if(username.length >= 3){
                    $.ajax({
                        type:"post",
                        data:'action=searchUser&username='+username,
                        success: function(data){
                            if(data != ""){
                                $("#resultados").empty();
                                data = jQuery.parseJSON(data);
                                var content = '';
                                $.each(data, function (key, id) {
                                    content += '<div class="resultados" id="id_sponsor" onclick="myFunction(\''+data[key].username+'\',\''+data[key].id+'\')">'+data[key].email+' - '+data[key].username+' - '+data[key].dni+'</div>';
                                })

                                $("#resultados").html(content);
                            }
                            else{
                                $("#resultados").empty();
                            }
                        }
                    });
                }
                else{
                    $("#resultados").empty();
                }
            });
        });
        
        function myFunction(name, id_customer) {
                $('#busqueda').val(name);
                $('#id_customer').val(id_customer);
                $('#customer_name').val(name);
                //$('#name_sponsor').html(name);
                $('.resultados').hide();
        }
        
        function searchFunc() {
            var id_customer = $('#id_customer').val();
            $.ajax({
                    type : 'POST',
                    data:'action=clickSearch&id_customer='+id_customer,
                    success: function(data){
                    if(data != ""){
                        $("#table-c").empty();
                        data = jQuery.parseJSON(data);
                        var content = "";
                        $.each(data, function (key, id) {
                            content += "<tr>";
                            content += '<td>' +data[key].level+ '</td>';
                            content += '<td>' +data[key].method_add+ '</td>';
                            content += '<td>' +data[key].email+ '</td>';
                            content += '<td>' +data[key].firstname+ '</td>';
                            content += '<td>' +data[key].email_sponsor+ '</td>';
                            content += '<td>' +data[key].Nombre_sponsor+ '</td>';
                            content += '<td>' +data[key].date_add+ '</td>';
                            content += "</tr>";
                        })
                        
                        $('#table-customer-net').show();
                        $("#table-c").append(content);
                    }
                    else{
                        $('#table-customer-net').hide();
                        $("#table-c").empty();
                    }
                }
            });
        }
        
        function cleanCustomer(){
            location.reload();
        }
    </script>
    <style>
        .resultados{color: #000; border-bottom: 1px solid #CBCBCB;padding: 5px;text-align: left; cursor: pointer;}
    </style>
{/literal}
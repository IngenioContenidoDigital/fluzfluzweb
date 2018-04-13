<div class="panel" style='height: 450px !important; overflow-y: auto !important;'>
        <div class="panel-heading">
                <i class="icon-group"></i>
                {l s='Emails Codigo Referidos (Solo usuarios Activos)'}
        </div>
    <form method="post">
        <div class="col-lg-6 col-md-8 col-sm-8 col-xs-12" style="margin-bottom: 20px;">
            <input type="hidden" id="id_customer" name="id_customer" value="{if $id_member != ""}{$id_member}{else}{/if}"/>
            <input type="hidden" id="customer_name" name="customer_name" value="{if $name_member != ""}{$name_member}{else}{/if}"/>
            <div class="text-infouser"> 
                <div class="col-lg-6">
                    <input type="text" name="busqueda" id="busqueda" value="{if $name_member != ""}{$name_member}{else}{/if}" class="is_required validate form-control input-infopersonal textsearch" autocomplete="off" placeholder="{l s='Buscar Codigo'}" required>
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
        <div class="row" id="table-customer-net" style="margin-bottom: 10px;"> 
            <table class="table">
                <thead>
                    <tr>
                        <th><span class="title_box">{l s='Email Registrado'}</span></th>
                        <th><span class="title_box">{l s='Dia Ingreso'}</span></th>
                        <th><span class="title_box">{l s='Nivel Fluzzer'}</span></th>
                    </tr>
                </thead>
                <tbody id="table-c" class="result-network">
                        
                </tbody>
            </table>
        </div>
        <div class="btn btn-default export-csv">
            <a href='#' id="btnExport"><i class="icon-cloud-upload"></i> Exportar a excel</a>
        </div>  
    </form>
</div>
<script type="text/javascript">
        $(document).ready(function(e){
            $('#table-customer-net').hide();
            $("#busqueda").keyup(function(e){
                var username = $("#busqueda").val();
                if(username.length >= 3){
                    $.ajax({
                        type:"post",
                        url: "{$module_dir}ajax/reportReferralCode_admin.php",
                        data:'action=searchCode&username='+username,
                        success: function(data){
                            if(data != ""){
                                $("#resultados").empty();
                                data = jQuery.parseJSON(data);
                                var content = '';
                                $.each(data, function (key, id) {
                                    content += '<div class="resultados" id="id_sponsor" onclick="myFunction(\''+data[key].code+'\',\''+data[key].id_sponsor+'\')">'+data[key].code+' - '+data[key].email+' - '+data[key].id_sponsor+'</div>';
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
            var referral_code = $('#customer_name').val();
            var id_customer = $('#id_customer').val();
            $.ajax({
                    type : 'POST',
                    url: "{$module_dir}ajax/reportReferralCode_admin.php",
                    data:'action=clickSearch&referral_code='+referral_code+'&id_customer='+id_customer,
                    success: function(data){
                    if(data != ""){
                        $("#table-c").empty();
                        data = jQuery.parseJSON(data);
                        var content = "";
                        $.each(data, function (key, id) {
                            content += "<tr>";
                            content += '<td>' +data[key].email+ '</td>';
                            content += '<td>' +data[key].date_add+ '</td>';
                            content += '<td>' +data[key].level_sponsorship+ '</td>';
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
        
        $("#btnExport").click(function (e) {
            var result = "data:application/vnd.ms-excel,"+encodeURIComponent($('#table-customer-net').html());
            this.href = result;
            this.download = "EmailCodigoReferencia.xls";
            location.reload();
        });
        
        function cleanCustomer(){
            location.reload();
        }
</script>
{literal}
    <style>
        .resultados{color: #000; border-bottom: 1px solid #CBCBCB;padding: 5px;text-align: left; cursor: pointer;}
        .calendar{display: none !important;}
    </style>
{/literal}
    
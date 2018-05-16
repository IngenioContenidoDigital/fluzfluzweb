<div class="panel" style='height: 450px !important; overflow-y: auto !important;'>
        <div class="panel-heading">
                <i class="icon-group"></i>
                {l s='Reporte Grafico Fluzzer Registrados y Expulsados'}
        </div>
    <form method="post">
        <div>
            
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
        #calendar{display: none !important;}
    </style>
{/literal}
    
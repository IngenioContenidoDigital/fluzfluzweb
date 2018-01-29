<div class="panel">
    <h3>Codigos Producto</h3>
    {if !$codes}
        <div class="bootstrap">
            <div class="alert alert-warning">
                No codes related to this product.
            </div>
	</div>
    {else}
        <form method="post">
            <div class="row">
                <div class="col-lg-6">
                    <p>{l s = 'Total de codigos asignados al producto seleccionado' mod="fluzfluzcodes"}</p>
                </div>
                <div class="col-lg-6">
                    Elminar Codigos Disponibles<img style="cursor: pointer;" title="{l s='Delete'}" src="../img/admin/delete.gif" onclick="sendActionAll('deletecodeall', '{$id_product}', '{$code.id_product_code}', '{$code.code}');">
                </div>
            </div>
            {foreach $totals as $total}
                <span>{$total.estado}:&nbsp;</span><span>{$total.total}</span>
            {/foreach}
            <br><br>
            <div style="height: 400px; overflow-y: scroll; overflow-x: hidden;" id="codes">
                <table class="table">
                    <thead>
                        <tr>
                            <th><strong>Codigos</strong></th>
                            <th style="text-align: center;"><strong>Pin</strong></th>
                            <th style="text-align: center;"><strong>Estado</strong></th>
                            <th style="text-align: center;"><strong>Orden</strong></th>
                            <th style="text-align: center;"><strong>Fecha Creacion</strong></th>
                            <th style="text-align: center;"><strong>Fecha Vencimiento</strong></th>
                            <th style="text-align: center;"><strong>No Lote</strong></th>
                            <th style="text-align: center;" class="action"><strong>Accion</strong></th>
                        </tr>
                    </thead>
                    <tbody  id="table_code">
                        {foreach $codes as $code}
                            <tr class="tr_codes-{$code.valid_date}" id="tr_codes" name="tr_codes">
                                <td>{$code.code}</td>
                                <td style="text-align: center;">{$code.pin}</td>
                                <td style="text-align: center;">{$code.estado}</td>
                                <td style="text-align: center;">{$code.order}</td>
                                <td style="text-align: center;">{$code.date_add}</td>
                                <td style="text-align: center;">{$code.date_expiration}</td>
                                <td style="text-align: center;">{$code.no_lote}</td>
                                <td style="text-align: center;" class="action">
                                    {if $code.order == ""}
                                        <img style="cursor: pointer;" title="{l s='Delete'}" src="../img/admin/delete.gif" onclick="sendAction('deletecode', '{$id_product}', '{$code.id_product_code}', '{$code.code}');">
                                    {/if}
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            </div>
            <div class="panel-footer">
                <a href="#" id="btnExport">Exportar a excel</a>
            </div>
        </form>
    {/if}
</div>
{literal}

    <style>
        .tr_codes-bueno{background: #ddf0de;}
        .tr_codes-regular{background: #FDFF7C;}
        .tr_codes-malo{background: #F36267;}
        .bootstrap .table tbody>tr>td{background: transparent;}
    </style>
    
{/literal}
<script type="text/javascript">
    function sendAction(action, product, id_product_code, code) {
        var msgError = "Se ha generado un error ejecutando la accion porfavor intente de nuevo.";
        if ( action == "deletecode" && product != "" && id_product_code != "" ) {
            conf = confirm( 'Confirma que desea eliminar el codigo '+code );
            if ( conf == true ) {
                $.ajax({
                    method: "POST",
                    url: "{$module_dir}ajax/fluzfluzcodes_admin.php",
                    data: { action: action, product: product, id_product_code: id_product_code }
                }).done(function(response) {
                    if ( response != 0 ) {
                        alert("El codigo ha sido eliminado exitosamente");
                        location.reload();
                    } else {
                        alert( msgErrorr );
                    }
                }).fail(function() {
                    alert( msgError );
                });
            }
        }
    }
    
    function sendActionAll(action, product) {
        
        var msgError = "Se ha generado un error ejecutando la accion porfavor intente de nuevo.";
        if ( action === "deletecodeall" && product !== "" ) {
            conf = confirm( 'Confirma que desea eliminar todos los codigos disponibles de este producto.');
            if ( conf == true ) {
                $.ajax({
                    method: "POST",
                    url: "{$module_dir}ajax/fluzfluzcodes_admin.php",
                    data: { action: action, product: product }
                }).done(function(response) {
                    if ( response != 0 ) {
                        alert("Los codigos han sido eliminados exitosamente");
                        location.reload();
                    } else {
                        alert( msgErrorr );
                    }
                }).fail(function() {
                    alert( msgError );
                });
            }
        }
    }
    
    $("#btnExport").click(function (e) {
        $(".action").remove();
        var result = "data:application/vnd.ms-excel,"+encodeURIComponent( $('#codes').html() );
        this.href = result;
        this.download = "CodigosProducto.xls";
        location.reload();
    });
</script>

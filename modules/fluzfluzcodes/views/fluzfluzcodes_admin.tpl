<div class="panel">
    <h3>{l s='Product Codes'}</h3>
    {if !$codes}
        <div class="bootstrap">
            <div class="alert alert-warning">
                No codes related to this product.
            </div>
	</div>
    {else}
        <form method="post">
            <p>{l s = 'Total de codigos asignados al producto seleccionado' mod="fluzfluzcodes"}</p>
            {foreach $totals as $total}
                <span>{$total.estado}:&nbsp;</span><span>{$total.total}</span>
            {/foreach}
            <br><br>
            <div style="height: 400px; overflow-y: scroll; overflow-x: hidden;" id="codes">
                <table class="table">
                    <thead>
                        <tr>
                            <th><strong>{l s='Codes'}</strong></th>
                            <th style="text-align: center;"><strong>Estado</strong></th>
                            <th style="text-align: center;"><strong>Orden</strong></th>
                            <th style="text-align: center;"><strong>Accion</strong></th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach $codes as $code}
                            <tr>
                                <td>{$code.code}</td>
                                <td style="text-align: center;">{$code.estado}</td>
                                <td style="text-align: center;">{$code.order}</td>
                                <td style="text-align: center;">
                                    {if $code.order == ""}
                                        <img style="cursor: pointer;" title="{l s='Delete'}" src="../img/admin/delete.gif" onclick="sendAction('deletecode', '{$id_product}', '{$code.code}');">
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

<script type="text/javascript">
    function sendAction(action, product, code) {
        var msgError = "Se ha generado un error ejecutando la accion porfavor intente de nuevo.";
        if ( action == "deletecode" && product != "" && code != "" ) {
            conf = confirm( 'Confirma que desea eliminar el codigo '+code );
            if ( conf == true ) {
                $.ajax({
                    method: "POST",
                    url: "{$module_dir}ajax/fluzfluzcodes_admin.php",
                    data: { action: action, product: product, code: code }
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
    $("#btnExport").click(function (e) {
        var result = "data:application/vnd.ms-excel,"+encodeURIComponent( $('#codes').html() );
        this.href = result;
        this.download = "CodigosProducto.xlsx";
        return true;
    });
</script>
<div class="panel">
    <h3>Codigo Productos</h3>
    <p>{l s = 'Total de codigos asignados al producto seleccionado' mod="fluzfluzcodes"}</p>
    {foreach $totals as $total}
        <span>{$total.estado}:&nbsp;</span><span>{$total.total}</span>
    {/foreach}
    <br><br>
    <div style="height: 450px; overflow-y: scroll;">
    <table border="1">
        <tr>
            <td style="text-align: center"><strong>Codigos</strong></td>
            <td style="text-align: center"><strong>Estado</strong></td>
            <td style="text-align: center"><strong>&nbsp;Orden&nbsp;</strong></td>
        </tr>
        {foreach $codes as $code}
            <tr>
                <td>&nbsp;&nbsp;&nbsp;{$code.code}&nbsp;&nbsp;&nbsp;</td>
                <td>&nbsp;&nbsp;&nbsp;{$code.estado}&nbsp;&nbsp;&nbsp;</td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;{$code.order}&nbsp;&nbsp;&nbsp;&nbsp;</td>
            </tr>
        {/foreach}
    </table>
    </div>
</div>
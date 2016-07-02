<div class="panel">
    <h3>Codigo Productos</h3>
    <table border="1">
        <tr>
            <td style="text-align: center"><strong>Codigos</strong></td>
        </tr>
        {foreach $codes as $code}
            <tr>
                <td>&nbsp;&nbsp;&nbsp;{$code.code}&nbsp;&nbsp;&nbsp;</td>
            </tr>
        {/foreach}
    </table>
</div>
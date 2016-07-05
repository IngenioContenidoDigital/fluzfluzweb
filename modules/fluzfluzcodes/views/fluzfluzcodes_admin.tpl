<div class="panel">
    <h3>{l s='Product Codes'}</h3>
    {if !$codes}
        <div class="bootstrap">
            <div class="alert alert-warning">
                No codes related to this product.
            </div>
	</div>
    {else}
        <p>{l s = 'Total de codigos asignados al producto seleccionado' mod="fluzfluzcodes"}</p>
        {foreach $totals as $total}
            <span>{$total.estado}:&nbsp;</span><span>{$total.total}</span>
        {/foreach}
        <br><br>
        <div style="height: 400px; overflow-y: scroll; overflow-x: hidden;">
            <table class="table">
                <thead>
                    <tr>
                        <th><strong>{l s='Codes'}</strong></th>
                        <th style="text-align: center"><strong>Estado</strong></th>
                        <th style="text-align: center"><strong>Orden</strong></th>
                        <th style="text-align: center;"><strong>{l s='Action'}</strong></th>
                    </tr>
                </thead>
                <tbody>
                    {foreach $codes as $code}
                        <tr>
                            <td>{$code.code}</td>
                            <td style="text-align: center;">{$code.estado}</td>
                            <td style="text-align: center;">{$code.order}</td>
                            <td style="text-align: center;"><img style="cursor: pointer;" title="{l s='Delete'}" src="../img/admin/delete.gif"></td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
        <div class="panel-footer">
            <button class="button btn btn-default" type="button"><i class="process-icon-export"></i>{l s='Export'}</button>
        </div>
    {/if}
</div>

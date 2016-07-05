<div class="panel">
    <h3>{l s='Product Codes'}</h3>
    {if !$codes}
        <div class="bootstrap">
            <div class="alert alert-warning">
                No codes related to this product.
            </div>
	</div>
    {else}
        <div style="height: 400px; overflow-y: scroll; overflow-x: hidden;">
            <table class="table">
                <thead>
                    <tr>
                        <th><strong>{l s='Codes'}</strong></th>
                        <th style="text-align: center;"><strong>{l s='Action'}</strong></th>
                    </tr>
                </thead>
                <tbody>
                    {foreach $codes as $code}
                        <tr>
                            <td>{$code.code}</td>
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
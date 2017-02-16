<div class="container">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6"></div>
        <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
        <div class="form-group marco">
            <div class="row">
                <form>
                    <div class="tit">
                        <span class="ws-tit">{l s='Tus Numeros a recargar' mod='fluzfluzapi'}</span>
                    </div>
                    <br/>
                {foreach from=$productlist item=product}
                    <div class="row">
                    <div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 merchant"><img src="{$img_manu_dir}{$product.id_manufacturer}.jpg" width="45px"/></div>
                    <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4 nombre">{$product.name}</div>
                    <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                    <select name="{$product.id_product}" class="numero">
                        <option value="0" id="noselect">Elije Telefono</option>
                    {foreach from=$phones item=phone}
                        {if $phone.phone!=null or $phone.phone!=""}
                            <option value="{$phone.phone}">(&nbsp;{$phone.phone|substr:0:3}&nbsp;)&nbsp;{$phone.phone|substr:3:3}&nbsp;-&nbsp;{$phone.phone|substr:6:4}</option>
                        {/if}
                    {/foreach}
                    </select>
                    </div>
                    <br><br>
                    </div>
                {/foreach}
                </form>
            </div>
            <br>
            <div class="row">
                <div class="col-xs-3 col-sm-4 col-md-4 col-lg-3" style="background-color: transparent;">
                    <img class="action" src="/modules/fluzfluzapi/images/button-add.png" width="18px"/><span class="atit">&nbsp;&nbsp;A&ntilde;adir Nuevo&nbsp;&nbsp;</span>
                </div>
                <div class="col-xs-9 col-sm-8 col-md-8 col-lg-9 pull-right">
                    (&nbsp;<input type="number" size="3" maxlength="3" name="pre1" id="pre1" class="nuevo"/>&nbsp;)&nbsp;
                    &nbsp;<input type="number" size="3" maxlength="3" name="pre2" id="pre2" class="nuevo"/>&nbsp;
                    -&nbsp;<input type="number" size="4" maxlength="4" name="pre3" id="pre3" class="nuevo"/>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
    
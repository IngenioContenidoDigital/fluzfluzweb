<div class="panel">
    <h3>Banners Producto</h3>
    <form enctype="multipart/form-data" method="post">
        <center><h5>* Unicamete archivos JPG *</h5></center>
        <table class="table">
            <thead>
                <tr>
                    <th style="text-align: center;"><strong>Banner</strong></th>
                    <th style="text-align: center;"><strong>Imagen</strong></th>
                    <th style="text-align: center;"><strong>Cambiar</strong></th>
                    <th style="text-align: center;"><strong></strong></th>
                </tr>
            </thead>
            <tbody>
                {foreach key=key item=image from=$images}
                    <tr>
                        <td style="text-align: center;"><strong>{$key + 1}</strong></td>
                        <td style="text-align: center;"><img src="{$image}" width="500px"></td>
                        <td style="text-align: center;"><input type="file" name="img_{$key}" accept="image/jpeg"></td>
                        <td style="text-align: center;"><button type="submit" name="deleteImgBannerProduct_{$key}" class="pull-right btn btn-default"><i class="icon-trash"></i> {l s='Borrar esta imagen'}</button></td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
        <div class="panel-footer">
            <button type="submit" name="submitImgBannerProduct" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Subir Imagenes'}</button>
        </div>
    </form>
</div>

<script type="text/javascript">
    
</script>

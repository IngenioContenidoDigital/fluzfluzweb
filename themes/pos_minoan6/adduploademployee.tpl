{*
* All-in-one Rewards Module
*
* @category  Prestashop
* @category  Module
* @author    Yann BONNAILLIE - ByWEB
* @copyright 2012-2015 Yann BONNAILLIE - ByWEB (http://www.prestaplugins.com)
* @license   Commercial license see license.txt
* Support by mail  : contact@prestaplugins.com
* Support on forum : Patanock
* Support on Skype : Patanock13
*}
<!-- MODULE allinone_rewards -->

{capture name=path}<a href="{$link->getPageLink('my-account', true)|escape:'html':'UTF-8'}">{l s='My account' mod='allinone_rewards'}</a><span class="navigation-pipe">{$navigationPipe|escape:'html':'UTF-8'}</span>{l s='My rewards account' mod='allinone_rewards'}{/capture}

{if version_compare($smarty.const._PS_VERSION_,'1.6','<')}
    {include file="$tpl_dir./breadcrumb.tpl"}
{/if}

{literal}
    <script>
        var listcopy = '';
    </script>
{/literal}

<form method="post" enctype="multipart/form-data" id="uploademployeebusiness" class="contenedorEmployeeBusiness" name="uploademployeebusiness">
    <div class="row row-upload">
        <div class="col-lg-12 title-browser">
            <p class="title-panel-upload"> Importar desde CSV </p>
        </div>
        <div class="col-lg-12 browse-div">
            <div class="custom-file-upload">
                <!--<label for="file">File: </label>--> 
                <input type="file" name="file" id="file" />
            </div>
        </div>
    </div>
    <div class="row">
        <span> Si tienes problemas para subir un CSV. Aseg&uacute;rese de que su hoja de c&aacute;lculo est&eacute; formateada correctamente. Descargar <a href="../csvcustomer/carga_customer_example.csv" class="link-down">CSV de Ejemplo</a></span>
    </div>
    <div class="row">        
        <div class="col-lg-6 div-btn-submit">
            <button class="btn btn-default btn-save-submit" type="submit" id="upload-employee" name="upload-employee">
                <span> SUBIR ARCHIVO </span>
            </button>
        </div>    
    </div>
</form>  
<form class="copy-form-csv" method="post" enctype="multipart/form-data" id="uploadcopyemployeebusiness" name="uploadcopyemployeebusiness">
    <div class="row row-upload">
        <div class="col-lg-12 title-browser">
            <p class="title-panel-upload"> O Arrastre el Archivo CSV</p>
        </div>
    </div>
    <article>
        <div id="holder"></div> 
        <p id="status">API de archivos y API de FileReader no compatibles</p>
    </article>
    <div class="row">
        <span> Si tienes problemas para subir un CSV. Aseg&uacute;rese de que su hoja de c&aacute;lculo est&eacute; formateada correctamente. Descargar <a href="../csvcustomer/carga_customer_example.csv" class="link-down">CSV de Ejemplo</a> </span>
    </div>
    <div class="row">        
        <div class="col-lg-6 div-btn-submit">
            <button class="btn btn-default btn-save-submit" type="submit" id="upload-copy" name="upload-copy">
                <span> SUBIR ARCHIVO </span>
            </button>
        </div>    
    </div>
</form>
<div id="url_fluz" style="display:none;">{$base_dir_ssl}</div>
{literal}
    <script>
        var holder = document.getElementById('holder'),
        state = document.getElementById('status');

        if (typeof window.FileReader === 'undefined') {
            state.className = 'fail';
        } else {
            state.className = 'success';
            state.innerHTML = 'File API & FileReader available';
        }

        holder.ondragover = function() {
            this.className = 'hover';
            return false;
        };
        holder.ondragend = function() {
            this.className = '';
            return false;
        };
        holder.ondrop = function(e) {
            this.className = 'viewcsv';
            e.preventDefault();

            var file = e.dataTransfer.files[0],
            reader = new FileReader();
            reader.onload = function(event) {
                console.log(event.target);
                holder.innerText = event.target.result;
                
                var array = event.target.result;
                
                var extractValidString = array.match(/[\w @.]+(?=,?)/g);
                var noOfCols = 6;
                var objFields = extractValidString.splice(0,noOfCols);
                var arr = [];
                while(extractValidString.length>0) {
                    var obj = {};
                    var row = extractValidString.splice(0,noOfCols)
                    for(var i=0;i<row.length;i++) {
                        obj[objFields[i]] = row[i].trim()
                    }
                    arr.push(obj)
                }
                
                listcopy = JSON.stringify(arr);
            };
            reader.readAsText(file);

            return false;
        };
        
        $('#upload-copy').click(function(e){
            var url = document.getElementById("url_fluz").innerHTML;        
            $.ajax({
                url : urlTransferController,
                type : 'POST',
                data : 'action=submitcopy&listcopy='+listcopy,
                
                success : function() {
                   window.location.replace(""+url+"confirmtransferfluzbusiness");
                }
            });
            e.preventDefault();
        });
    </script>
{/literal}
{literal}
    <style>
        #left_column{display: none;}
    </style>
{/literal}

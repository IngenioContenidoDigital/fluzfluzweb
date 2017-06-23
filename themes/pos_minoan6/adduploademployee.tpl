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
<form method="post" enctype="multipart/form-data" id="uploademployeebusiness" class="contenedorEmployeeBusiness" name="uploademployeebusiness">
    <div class="row row-upload">
        <div class="col-lg-12 title-browser">
            <p class="title-panel-upload"> Import From CSV </p>
        </div>
        <div class="col-lg-12 browse-div">
            <div class="custom-file-upload">
                <!--<label for="file">File: </label>--> 
                <input type="file" name="file" id="file" />
            </div>
        </div>
    </div>
    <div class="row">
        <span> If you're having issues uploading a CSV. please make sure you're spreadsheet is formated correctly. </span>
    </div>
    <div class="row">        
        <div class="col-lg-6 div-btn-submit">
            <button class="btn btn-default btn-save-submit" type="submit" id="upload-employee" name="upload-employee">
                <span> SUBMIT </span>
            </button>
        </div>    
    </div>
</form>  
<form class="copy-form-csv" method="post" enctype="multipart/form-data" id="uploadcopyemployeebusiness" name="uploadcopyemployeebusiness">
    <div class="row row-upload">
        <div class="col-lg-12 title-browser">
            <p class="title-panel-upload"> Or Copy/Pasted file </p>
        </div>
    </div>
    <article>
        <div id="holder"></div> 
        <p id="status">File API & FileReader API not supported</p>
    </article>
    <div class="row">
        <span> If you're having issues uploading a CSV. please make sure you're spreadsheet is formated correctly. </span>
    </div>
    <div class="row">        
        <div class="col-lg-6 div-btn-submit">
            <button class="btn btn-default btn-save-submit" type="submit" id="upload-copy" name="upload-copy">
                <span> SUBMIT </span>
            </button>
        </div>    
    </div>
</form>

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
                listcopy = JSON.stringify(array);
                       
                $.ajax({
                     url : urlTransferController,
                     type : 'POST',
                     data : 'action=copycustomer&listcopy='+listcopy,
                     success : function(s) {
                         console.log(s);
                     }
                 });
            };
            console.log(file);
            reader.readAsText(file);

            return false;
        };
    </script>
{/literal}
{literal}
    <style>
        #left_column{display: none;}
    </style>
{/literal}
{literal}
    <script>
        $(document).ready(function(){
            $('#file').change( function(){
               var file = $(this).val();
               
               if(file != ""){
                   console.log('archivo');
                   
               }
               else{
                   console.log('archivo vacio');
                   
               }
               
            });
        });
    </script>
{/literal}
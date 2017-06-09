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
<form>
    <div class="row row-upload">
        <div class="col-lg-12 title-browser">
            <p class="title-panel-upload"> Import From CSV </p>
        </div>
        <div class="col-lg-12 browse-div">
            <div class="custom-file-upload">
                <!--<label for="file">File: </label>--> 
                <input type="file" id="file" name="myfiles[]" multiple />
            </div>
        </div>
    </div>
    <div class="row">
        <span> If you're having issues uploading a CSV. please make sure you're spreadsheet is formated correctly. </span>
    </div>
    <div class="row">        
        <div class="col-lg-6 div-btn-submit">
            <button class="btn btn-default btn-save-submit" type="submit" id="save-info">
                <span> SUBMIT </span>
            </button>
        </div>    
    </div>
</form>  
<form class="copy-form-csv">
    <div class="row row-upload">
        <div class="col-lg-12 title-browser">
            <p class="title-panel-upload"> Or Copy/Pasted file </p>
        </div>
    </div>
    <div class="row">
        <span> If you're having issues uploading a CSV. please make sure you're spreadsheet is formated correctly. </span>
    </div>
    <div class="row">        
        <div class="col-lg-6 div-btn-submit">
            <button class="btn btn-default btn-save-submit" type="submit" id="save-info">
                <span> SUBMIT </span>
            </button>
        </div>    
    </div>
</form>
{literal}
    <style>
        #left_column{display: none;}
    </style>
{/literal}
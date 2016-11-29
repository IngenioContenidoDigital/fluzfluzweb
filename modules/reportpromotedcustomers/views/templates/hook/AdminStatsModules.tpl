{*
<form action="#" method="post" id="reportForm" class="form-horizontal">
    <div class="row row-margin-bottom">
        <label class="control-label col-lg-3">
            {l s='Choose a category' mod='belvg_statscatalog'}
        </label>
        <div class="col-lg-6">
            <select name="estat_id_category" onchange="$('#reportForm').submit();">
                <option value="0">{l s='All' mod='belvg_statscatalog'}</option>
                {foreach $categories as $category}
                    <option value="{$category.id_category|intval}" {if $id_category == $category.id_category}selected="selected"{/if}>
                        {$category.name|escape:false} (#{$category.id_category|intval})
                    </option>
                {/foreach}
            </select>
        </div>
    </div>
    <div class="row row-margin-bottom">
        <label class="control-label col-lg-3">
            {l s='Choose employee' mod='belvg_statscatalog'}
        </label>
        <div class="col-lg-6">
            <select name="estat_id_employee" onchange="$('#reportForm').submit();">
                <option value="0">{l s='All' mod='belvg_statscatalog'}</option>
                {foreach $employees as $employee}
                    <option value="{$employee.id_employee|intval}" {if $id_employee == $employee.id_employee}selected="selected"{/if}>
                        {$employee.firstname|escape:false} {$employee.lastname|escape:false}
                    </option>
                {/foreach}
            </select>
        </div>
    </div>
</form>
*}
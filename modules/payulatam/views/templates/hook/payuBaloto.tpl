{if isset($error)}
 <p style="color:red">{l s='An error occured, please try again later.' mod='payulatam'}</p>
{else}
    
    <div class="container">      
        <div class="row">
            <div class="col-xs-12 col-sm-10 col-md-8">
                <form role="form" class="form-horizontal" method="POST" action="{$base_dir|regex_replace:"/[http://]/":""|escape:'htmlall':'UTF-8'}/modules/payulatam/payuBaloto.php" id="formBaloto" name="formBaloto" autocomplete="off" >
                    <div class="form-group">
                        <label for="payubaloto" class="control-label hidden-xs col-sm-6 " style="text-align: left;"></label>
                            <div class="col-xs-12 col-sm-6">  
                                <button type="submit" id="payubaloto" name="pagar_baloto" class="button btn btn-default standard-checkout button-medium">
                                    <span> Pagar Con Baloto
                                        <i class="icon-chevron-right right"></i>
                                    </span>
                                </button>
                            </div>
                    </div>
            </form>
            </div>
        </div>
    </div>
{/if}


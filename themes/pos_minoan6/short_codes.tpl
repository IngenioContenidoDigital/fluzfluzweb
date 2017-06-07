
        <div class="row">
            <h3 class="title-shortcode">
                <i class="icon-mail"></i>
                {l s='Choose Short Codes'}
            </h3>
            <!--<div class="row">
                <div class="col-lg-12  m-top">
                    <select id="mail_name" name="mail_name">
                        <option value=''>{l s='Seleccione Plantilla de Email'}</option>
                        <option value='16-backoffice_order'>{l s='16-backoffice_order'}</option>
                        <option value='16-cancellation_account'>{l s='16-cancellation_account'}</option>
                        <option value='16-invitation_cancel'>{l s='16-invitation_cancel'}</option>
                        <option value='16-order_conf_freefluz'>{l s='16-order_conf_freefluz'}</option>
                        <option value='16-order_conf'>{l s='16-order_conf'}</option>
                        <option value='16-remember_cart'>{l s='16-remember_cart'}</option>
                        <option value='16-remember_inactive_account'>{l s='16-remember_inactive_account'}</option>
                        <option value='16-rememberinvitenewusers'>{l s='16-rememberinvitenewusers'}</option>
                        <option value='16-sponsorship-invitation-novoucher'>{l s='16-sponsorship-invitation-novoucher'}</option>
                    </select>
                </div>
            </div>-->  
            <div class="row">
                <div id="view_vars"></div>
            </div>        
        </div>
                       
{literal}
    <script>
        $(document).ready(function(){
            var args = top.tinymce.activeEditor.windowManager.getParams();
            var value = args.template;
            $.ajax({
                method:"POST",
                data: {"value": value},
                url: "/shortcodefunction.php",
                success: function(result){
                   
                   var data =  jQuery.parseJSON(result);
                   var content = '';
                    for (var i=0;i<data.length;i++){
                            content += '<div id="vars-'+data[i]+'" class="vars">'+data[i]+'</div>';
                        }
                    
                    $("#view_vars").html(content);
                    $(".vars").on('click', function(){
                       var text = $(this).html();
                       window.parent.tinyMCE.activeEditor.execCommand( 'mceInsertContent', 0, text);
                       tinyMCEPopup.close();
                    });
                }
            })
        });
        
        $('#mail_name').change(function(){ 
            var value = $(this).val();
            $.ajax({
                method:"POST",
                data: {"value": value},
                url: "/shortcodefunction.php",
                success: function(result){
                   
                   var data =  jQuery.parseJSON(result);
                   var content = '';
                    for (var i=0;i<data.length;i++){
                            content += '<div id="vars-'+data[i]+'" class="vars">'+data[i]+'</div>';
                        }
                    
                    $("#view_vars").html(content);
                    $(".vars").on('click', function(){
                       var text = $(this).html();
                       window.parent.tinyMCE.activeEditor.execCommand( 'mceInsertContent', 0, text);
                       tinyMCEPopup.close();
                    });
                }
            });
        });
    </script>    
{/literal}                        
{literal}
    <style>
        #header, #footer, #launcher, #right_column, .breadcrumb { display: none!important; }
        .title-shortcode{text-align: center; margin-bottom: 0px;}
        .m-top{text-align: center;}
        #view_vars{text-align: center;font-size: 14px;line-height: 25px;}
        .vars {cursor: pointer;}
        select#mail_name, select#mail_name-color {
            -webkit-appearance: button;
            -webkit-border-radius: 2px;
            -webkit-box-shadow: 0px 1px 3px rgba(0, 0, 0, 0.1);
            -webkit-padding-end: 20px;
            -webkit-padding-start: 2px;
            -webkit-user-select: none;
            background-image: url(http://i62.tinypic.com/15xvbd5.png), -webkit-linear-gradient(#FAFAFA, #F4F4F4 40%, #E5E5E5);
            background-position: 97% center;
            background-repeat: no-repeat;
            border: 1px solid #AAA;
            color: #555;
            font-size: inherit;
            margin: 20px;
            overflow: hidden;
            padding: 5px 10px;
            text-overflow: ellipsis;
            white-space: nowrap;
            width: 300px;
         }

        select#mail_name-color {
           color: #fff;
           background-image: url(http://i62.tinypic.com/15xvbd5.png), -webkit-linear-gradient(#779126, #779126 40%, #779126);
           background-color: #779126;
           -webkit-border-radius: 20px;
           -moz-border-radius: 20px;
           border-radius: 20px;
           padding-left: 15px;
        }
    </style>    
{/literal}
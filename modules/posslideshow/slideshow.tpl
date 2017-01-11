
<div class="pos-slideshow">
	<div class="flexslider ma-nivoslider">
        <div class="pos-loading"></div>
			
            <div id="pos-slideshow-home" class="slides">
				
                {$count=0}
                {foreach from=$slides key=key item=slide}
					
                    {if $slide.link}<a href="{$slide.link}">{/if}<img style ="display:none" src="{$slide.image}"  data-thumb="{$slide.image}"  alt="" title="#htmlcaption{$slide.id_pos_slideshow}"  />{if $slide.link}</a>{/if}
			   {/foreach}
            </div>
            {if $slideOptions.show_caption != 0}
                {foreach from=$slides key=key item=slide}
                    <div id="htmlcaption{$slide.id_pos_slideshow}" class="pos-slideshow-caption nivo-html-caption nivo-caption">
							<div class="timethai" style=" 
								position:absolute;
								z-index:99;
								top:0;
								left:0;
								background-color: rgba(49, 56, 72, 0.298);
								height:5px;
								-webkit-animation: myfirst {$slideOptions.pause_time}ms ease-in-out;
								-moz-animation: myfirst {$slideOptions.pause_time}ms ease-in-out;
								-ms-animation: myfirst {$slideOptions.pause_time}ms ease-in-out;
								animation: myfirst {$slideOptions.pause_time}ms ease-in-out;
							
							">
							</div>
				
				<div class="pos-slideshow-content ">
								<div class="container">
									{if $slide.title}
									<div class="pos-slideshow-title">
										   <h3>{$slide.title}</h3>
									</div>
									{/if}
									<div class="pos_description">
											{$slide.description}
									</div>
								
									 {if $slide.link}
									<div class="pos-slideshow-readmore">
										<a href="{$slide.link}">{l s='Shop now' mod= 'posslideshow'}</a>	
									</div>
									{/if}
								</div>	
							</div>	
								
                    </div>
                 {/foreach}
             {/if}
        </div>
    </div>

 <script type="text/javascript">
	

	
    $(window).load(function() {
		$(document).off('mouseenter').on('mouseenter', '.pos-slideshow', function(e){
		$('.pos-slideshow .timethai').addClass('pos_hover');
	});

	 $(document).off('mouseleave').on('mouseleave', '.pos-slideshow', function(e){
	   $('.pos-slideshow .timethai').removeClass('pos_hover');
	 });
        $('#pos-slideshow-home').nivoSlider({
			effect: '{if $slideOptions.animation_type != ''}{$slideOptions.animation_type}{else}random{/if}',
			slices: 15,
			boxCols: 8,
			boxRows: 4,
			animSpeed: '{if $slideOptions.animation_speed != ''}{$slideOptions.animation_speed}{else}600{/if}',
			pauseTime: '{if $slideOptions.pause_time != ''}{$slideOptions.pause_time}{else}5000{/if}',
			startSlide: {if $slideOptions.start_slide != ''}{$slideOptions.start_slide}{else}0{/if},
			directionNav: {if $slideOptions.show_arrow != 0}{$slideOptions.show_arrow}{else}false{/if},
			controlNav: {if $slideOptions.show_navigation != 0}{$slideOptions.show_navigation}{else}false{/if},
			controlNavThumbs: false,
			pauseOnHover: true,
			manualAdvance: false,
			prevText: '<i class="icon-angle-left"></i>',
			nextText: '<i class="icon-angle-right"></i>',
                        afterLoad: function(){
                         $('.pos-loading').css("display","none");
                        },     
                        beforeChange: function(){ 
                            $('.bannerSlideshow1').removeClass("pos_in"); 
                            $('.bannerSlideshow2').removeClass("pos_in"); 
                            $('.bannerSlideshow3').removeClass("pos_in"); 
                        }, 
                        afterChange: function(){ 
                             $('.bannerSlideshow1').addClass("pos_in"); 
                            $('.bannerSlideshow2').addClass("pos_in"); 
                            $('.bannerSlideshow3').addClass("pos_in"); 
                        }
 		});
    });
</script>

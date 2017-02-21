<?php global $SMTheme; ?>


<?php
	$slides=$SMTheme->get_slides();
	if (!is_array($slides)||count($slides)==0) exit;
?>
	
	
<div class='slider-container'>
	<div class="slider">
			<div class="fp-slides">
				<?php foreach ($slides as $num=>$slide) { ?>
							<div class="fp-slides-item">
								<div class='slider-bgr'></div>
								<div class="fp-thumbnail">
									<?php if ($SMTheme->get('slider', 'showthumbnail')) { ?>
									<a href="<?php echo $slide['link']?>" title=""><img src="<?php echo $slide['img']?>" alt="<?php echo $slide['ttl']?>" /></a>
									<?php } ?>
								</div>
								<?php if ($SMTheme->get('slider', 'showtext')||$SMTheme->get('slider', 'showttl')) { ?>
								<div class="fp-content-wrap">
									<div class="fp-content">
									
										<?php if ($SMTheme->get('slider', 'showttl')) { ?>
										<h3 class="fp-title"><?php echo $slide['ttl']?></h3>
										<?php } ?>
										
										<?php if ($SMTheme->get('slider', 'showtext')) { ?>
										<p class="fp-desc"><?php echo $slide['content']?></p>
										<?php } ?>
										
										<?php if ($SMTheme->get('slider', 'showhrefs')) { ?>
											<a class="fp-more" href="<?php echo $slide['link']?>"><?php echo $SMTheme->_('readmore');?></a>
										<?php } ?>
										
									</div>
								</div>
								<?php } ?>
							</div>
				<?php } ?>
			</div>
			
	
			
			<div class="fp-nav">
				<span class="fp-pager">&nbsp;</span>
			</div>  
			
	</div>
</div>
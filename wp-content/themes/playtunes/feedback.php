<?php
	/*Template Name: Contact form*/
?>
<?php 
	global $SMTheme;
?>
			
			<?php get_header(); ?>
			
			<?php if ( have_posts() ) : the_post(); ?>
				<article <?php post_class('article-box'); ?>>
				<!-- Contacts Title -->
				<h1 class="entry-title"><?php the_title(); ?></h1>

						
						
				<!-- Contacts Thumbnail -->
				<?php the_post_thumbnail(
								'post-thumbnail',
								array("class" => $SMTheme->get( 'layout','imgpos' ) . " featured_image")
				); ?>
				
				<div class="entry-content">
				<!-- Contacts Content -->
						<?php the_content( ); ?>
						
						
						
						<!-- Contacts Map -->
						<?php if ( $SMTheme->get( 'contactform','address' ) != '' ) {?>
							<div class='googlemap waiting'><div id="map_canvas" style="width: 100%; height: 300px;"></div></div>
							<script>jQuery(function(){loadGMap('<?php echo $SMTheme->get( 'contactform','address' )?>', 'map_canvas', 16)});</script>
						<?php } ?>
					
					
					
						<!-- Contacts Form -->
						<?php echo ( isset( $error ) && $error != '' )?'<p style="color:'.$errorcode.'">'.$error.'</p>':''?>
						<?php if ($SMTheme->get( 'contactform', 'email' )) { ?>
							<form action='' method='POST' class='feedback'>
							<h3><?php echo $SMTheme->_( 'feedbackttl' ); ?></h3>
							<i><?php echo $SMTheme->_( 'feedbackbefore' ); ?></i>
								<?php
									foreach ($SMTheme->get( 'contactform', 'contactform' ) as $key=>$detail) {
										switch ($detail['type']) {
											case 'text':
											?>
							<p><?php echo $detail['ttl']?>: <?php echo ($detail['req']=='required')?'(*)':''?>
							<div class='input'><input type='text' value='' name='<?php echo $key?>'<?php echo ($detail['req']=='required')?" required='true'":''?> /></div>
							</p>
											<?php
											break;
											case 'textarea':
											?>
							<p><?php echo $detail['ttl']?>: <?php echo ($detail['req']=='required')?'(*)':''?>
							<div class='input'><textarea rows='5' name='<?php echo $key?>'<?php echo ($detail['req']=='required')?" required='true'":''?>></textarea></div>
							</p>
											<?php
											break;
										}
									}
								?>
							<center><input type='submit' class='readmore' value='<?php echo $SMTheme->_( 'send' );?>' /></center>
							</form>
							<script>
								jQuery('.feedback input').each(function(){
									jQuery(this).attr('name','<?php echo $_SESSION['commentinput']; ?>['+jQuery(this).attr('name')+']');
								});
								jQuery('.feedback textarea').each(function(){
									jQuery(this).attr('name','<?php echo $_SESSION['commentinput']; ?>['+jQuery(this).attr('name')+']');
								});
							 </script>
						<?php } ?>
					
					
					
						<!-- Contacts Form Text -->
						<?php if ($SMTheme->get( 'contactform','text' )) { ?>
							<div style='margin-bottom:20px;'>
							<?php echo $SMTheme->get( 'contactform','text' )?>
							</div>
						<?php } ?>
					
					
					
						<!-- Contacts Form Text -->
						<?php
							$details = $SMTheme->get( 'contactform', 'details' );
							if (!empty($details)) {
						?>
							<ul class='contact-details'>
							<?php foreach ($details as $key=>$detail) { ?>
								<li class='contact<?php echo $key?>'><?php echo $detail['content']?></li>
							<?php } ?>
							</ul>
							<style>
								<?php foreach ($SMTheme->get( 'contactform', 'details' ) as $key=>$detail) { ?>
									ul.contact-details li.contact<?php echo $key?> {
										background:url(<?php echo $detail['img']?>) left top no-repeat;
									}
								<?php } ?>
							</style>
						<?php } else { ?>
							<style>
								form.feedback { width:100%;	}
							</style>
						<?php	} ?>
						
					<div class="clear"></div>
				</div><!-- .entry-content -->
				</article>
			<?php endif; ?>
			
			<?php get_footer(); ?>
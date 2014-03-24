<?php
$time = compost::getMetaValue( $id, 'time' );
$views = compost::getMetaValue( $id, 'views' );
$halflife = compost::getMetaValue( $id, 'halflife' );

$health = ( $views > $halflife ) ? 0 : 100 - floor( $views / $halflife * 100 );

?>

<li data-id="<?php echo $id; ?>">
	<?php if( compost::is_list() ) { ?>
	<a href="<?php echo compost::getbaseUrl(); ?>?id=<?php echo $id; ?>" class="permalink">
	<?php } ?>
		<img src="<?php echo compost::getImageUrl( $id ); ?>">		
	<?php if( compost::is_list() ) { ?>
	</a>
	<?php } ?>

	<div class="tools">
		<?php if( compost::is_loggedin() ) { ?>
		<a href="<?php echo compost::getbaseUrl(); ?>?delete=<?php echo $id; ?>" class="delete" title="delete">delete</a>
		<?php } ?>
		<a href="<?php echo compost::getImageUrl( $id ); ?>" class="share" title="share">share</a>
	</div>

	<ul class="metalist">
		<!--
		<li class="time">
			<span class="day"><?php echo date( 'd', $time ); ?>.</span>
			<span class="month"><?php echo date( 'm', $time ); ?>.</span>
			<span class="day"><?php echo date( 'y', $time ); ?></span>
		</li>
		-->
		<li class="health"><span class="chart" data-health="<?php echo $health; ?>%"><span class="bar"></span><span class="number"><?php echo $health; ?>%</span></span></li>
	</ul>

</li>
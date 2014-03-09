<?php
$time = compost::getMetaItem( $id, 'time' );
$views = compost::getMetaItem( $id, 'views' );
$halflife = compost::getMetaItem( $id, 'halflife' );

$health = ( $views > $halflife ) ? 0 : 100 - floor( $views / $halflife * 100 );

?>

<li data-id="<?php echo $id; ?>">
	<a href="<?php echo compost::getbaseUrl(); ?>?id=<?php echo $id; ?>" class="permalink">
		<img src="<?php echo compost::getbaseUrl(); ?>?stream=<?php echo $id; ?>">		
	</a>

	<?php if( compost::is_loggedin() ) { ?>
	<div class="tools">
		<a href="<?php echo compost::getbaseUrl(); ?>?delete=<?php echo $id; ?>" class="delete">delete</a>
	</div>
	<?php } ?>


	<p><?php echo compost::getMetaItem( $id, 'description' ) ?></p>
	<ul class="metalist">
		<!--
		<li class="time">
			<span class="day"><?php echo date( 'd', $time ); ?>.</span>
			<span class="month"><?php echo date( 'm', $time ); ?>.</span>
			<span class="day"><?php echo date( 'y', $time ); ?></span>
		</li>
	-->
		<li class="health"><span class="chart" data-health="<?php echo $health; ?>%"><span class="number"><?php echo $health; ?>%</span></span></li>
	</ul>


</li>
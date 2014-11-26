<?php s::start(); ?>
<!DOCTYPE html>
<html class="no-js">
<head>
	<title>compost &gt; <?php echo c::get( 'user_display_name' ); ?></title>
	<meta charset="utf-8">

    <meta name="HandheldFriendly" content="True">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="cleartype" content="on"> 

	<link rel="stylesheet" type="text/css" href="templates/css/style.css">

	<script type="text/javascript" src="templates/js/all.js"></script>

    <!-- eat this -->
	<meta property="og:site_name" content="compost &gt; <?php echo c::get( 'user_display_name' ); ?>">
	<?php if( compost::is_item() ) { ?>
	<meta property="og:title" content="<?php echo compost::getMetaValue( $id, 'description' ) ?>">
	<meta property="og:image" content="<?php echo compost::getImageUrl( $id ); ?>">
	<?php } else { ?>
    <meta property="og:image" content="http://post-prism.net/compost/templates/img/preview.png">
    <?php } ?>
    <!-- -->

    <link rel="icon" href="img/favicon-16.png" type="image/png">
</head>
<?php 
$classes = '';

if( compost::is_loggedin() ) {
	$classes .= ' loggedin';
}

if( compost::is_list() ) {
	$classes .= ' list';
}

if( compost::is_item() ) {
	$classes .= ' item';
}
?>
<body class="<?php echo $classes; ?>">
	<div class="shell">
		<div class="head">
			<h1><a href="http://post-prism.net" title="learn more about compost." class="app_name">compost</a> &gt; <a href="<?php echo compost::getBaseUrl(); ?>" class="username" title="view all of <?php echo c::get( 'user_display_name' ); ?>'s images."><?php echo c::get( 'user_display_name' ); ?></a></h1>
			<?php compost::renderTools(); ?>
		</div>
<?php s::start(); ?>
<!DOCTYPE html>
<html>
<head>
	<title>compost:<?php echo c::get( 'user_display_name' ); ?></title>
	<meta charset="utf-8">

    <meta name="HandheldFriendly" content="True" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="cleartype" content="on" /> 

	<link rel="stylesheet" type="text/css" href="templates/css/style.css" />
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
			<h1><a href="<?php echo compost::getBaseUrl(); ?>">compost:<span class="username"><?php echo c::get( 'user_display_name' ); ?></span></a></h1>
			<?php compost::renderTools(); ?>
		</div>
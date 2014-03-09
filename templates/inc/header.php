<?php s::start(); ?>
<!DOCTYPE html>
<html>
<head>
	<title>...</title>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="templates/css/style.css" />
</head>
<?php 
$classes = '';

if( compost::is_loggedin() ) {
	$classes .= ' loggedin';
}

?>
<body class="<?php echo $classes; ?>">
	<div class="shell">
		<div class="head">
			<h1><a href="<?php echo compost::getBaseUrl(); ?>">compost:<span class="username"><?php echo c::get( 'user_display_name' ); ?></span></a></h1>
			<?php compost::renderTools(); ?>
		</div>
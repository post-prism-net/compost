<?php	
// user settings
c::set( 'user_name', 'martin' );
c::set( 'user_pass', '1234' );
c::set( 'user_display_name', 'Martin' );

// path settings
c::set( 'path_meta', 'data/meta/' );
c::set( 'path_images', 'data/images/' );
c::set( 'path_templates', 'templates/' );

// image settings
c::set( 'image_width', 800 );
c::set( 'quality_maximum', 95 );
c::set( 'quality_minimum', 1 );
c::set( 'scale_maximum', 1 );
c::set( 'scale_minimum', 0.75 );
c::set( 'halflife', 5 );

// function settings
c::set( 'delete_after_halflife', true );
c::set( 'block', false );
c::set( 'block_duration', 3600 );

// template settings
c::set( 'render_limit', 10 );
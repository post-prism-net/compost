<?php
/**
 * compost
 *
 * @version 0.1
 * @author Martin Wecke <martin@hatsumatsu.de>
 * @copyright Copyright 2014 Martin Wecke
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

include( 'kirby.php' );

// session start
s::start();

// init app
compost::init();

class compost {

	/*
	 * Init 
	 *
	 */
	static function init() {
		global $messages;

		// configuration file
		require( 'config.php' );

		$messages = array();

		self::selectView();
	}

	/** 
	 * View controller
	 *
	 */
	static function selectView() {

		// login
		if( get( 'login' ) ) {
			self::login();
		}

		// logout
		if( get( 'logout' ) ) {
			self::logout();	
		}

		// upload
		if( get( 'upload' ) ) {
			self::upload();
		}

		// delete image
		if( get( 'delete' ) ) {
			$id = filter_var( get( 'delete' ), FILTER_SANITIZE_NUMBER_INT );
			self::deleteImage( $id );
		}

		// stream image file
		if( get( 'stream' ) ) {
			$id = filter_var( get( 'stream' ), FILTER_SANITIZE_NUMBER_INT );
			self::streamImage( $id );
			exit;	
		}

		// item 
		if( get( 'id' ) ) {
			$id = filter_var( get( 'id' ), FILTER_SANITIZE_NUMBER_INT );
			self::templateItem( $id );
		} else {
			self::templateList();
		}

	} 

	/** 
	 * Read meta directory
	 * 
	 *	@return array 	Array of image IDs
	 */
	static function getIds() {

		$ids = dir::read( c::get( 'path_meta' ) );

		// sort by time DESC
		arsort( $ids ); 

		// reset array keys
		$ids = array_values( $ids );

		// erase .json file extension
		for( $i = 0; $i < count( $ids ); $i++ ) {
			$ids[ $i ] = f::name( $ids[ $i ] );
		}

		return $ids;
	}

	/** 
	 * Get meta data by image ID
	 * 
	 *	@param 	int 	$id image ID
	 *  @return array 	array of metadata
	 */
	static function getMeta( $id ) {
		$meta = json_decode( f::read( c::get( 'path_meta' ) . $id . '.json' ), true );

		return $meta;
	}

	/** 
	  * Get meta value by image ID and meta key
	  *
	  * @param 	int 	$id image ID
	  * @param 	string 	$key meta key
	  * @param 		 	$value meta value
	  */
	static function getMetaValue( $id, $key ) {

		$meta = self::getMeta( $id );

		return $meta[ $key ];
	}

	/** 
	  * Save image meta 
	  *
	  * @param 	int 	$id image ID
	  * @param 	array 	$meta meta array
	  */
	static function setMeta( $id, $meta = array() ) {
		$meta = json_encode( $meta );

		f::write( c::get( 'path_meta' ) . $id . '.json', $meta );
	}

	/** 
	  * Save a specific meta value by key
	  *
	  * @param 	int 	$id image ID
	  * @param 	string 	$key meta key
	  * @param 		 	$value meta value
	  */
	static function setMetaValue( $id, $key, $value ) {

		$meta = self::getMeta( $id );
		$meta[ $key ] = $value;

		self::setMeta( $id, $meta );
	}

	/** 
	  * Upload routine called by view controller
	  *
	  */
	static function upload() {
		global $messages;

		$id = time();

		// meta
		$meta = array();

		$meta['time'] = time();
		$meta['description'] = str::sanitize( get( 'description' ) );
		$meta['halflife'] = c::get( 'halflife' );
		$meta['views'] = 0;

		if( self::createImage( $id ) ) {

			self::setMeta( $id, $meta );
			self::redirectBack();

			$messages[] = 'photo added.';

		}

	}

	/** 
	  * Upload image file 
	  * > http://php.net/manual/de/features.file-upload.php#114004
	  *
	  * @param 	int 	$id image ID
	  */
	static function createImage( $id ) {
		global $messages;

		try {
		   
		    if( !isset( $_FILES['file']['error'] ) 
		    	|| is_array( $_FILES['file']['error'] ) ) {
		        throw new RuntimeException( 'Invalid parameters.' );
		    }

		    switch( $_FILES['file']['error'] ) {
		        case UPLOAD_ERR_OK:
		            break;
		        case UPLOAD_ERR_NO_FILE:
		            throw new RuntimeException( 'No file sent.' );
		        case UPLOAD_ERR_INI_SIZE:
		        case UPLOAD_ERR_FORM_SIZE:
		            throw new RuntimeException( 'Exceeded filesize limit.' );
		        default:
		            throw new RuntimeException( 'Unknown errors.' );
		    }

		    if( $_FILES['file']['size'] > 20971520 ) {
		        throw new RuntimeException( 'Exceeded filesize limit.' );
		        $messages[] = 'File size too big.';
		    }

		    $finfo = new finfo( FILEINFO_MIME_TYPE );
		    if( false === $ext = array_search(
		        $finfo->file( $_FILES['file']['tmp_name'] ),
		        array(
		            'jpg' => 'image/jpeg',
		            'png' => 'image/png'
		        ),
		        true
		    ) ) {
		        throw new RuntimeException( 'Invalid file format.' );
		    }

		    if( !move_uploaded_file(
		        $_FILES['file']['tmp_name'],
		        sprintf( '%s/%s.%s',
		        	c::get( 'path_images' ),
		            $id,
		            $ext
		        )
		    ) ) {
		        throw new RuntimeException( 'Failed to move uploaded file.' );
		    }

		    if( $ext == 'png' ) {
		    	//convert PNG to JPEG
		    	self::PNGtoJPEG( $id );
		    }

		    return true;

		} catch ( RuntimeException $e ) {

		    echo $e->getMessage();

		}

	}

	/** 
	  * Delete image routine called by view controller 
	  * 
	  * @param 	int 	$id image ID
	  */
	static function deleteImage( $id ) {
		global $messages;

		if( self::is_loggedin() ) {

			$src_img = c::get( 'path_images' ) . $id . '.jpg';
			$src_meta = c::get( 'path_meta' ) . $id . '.json';

			// delete image and meta data
			if( f::remove( $src_img ) 
			 && f::remove( $src_meta ) ) {
				$messages[] = 'deleted image.';
			} else {
				$messages[] = 'error deleting image.';
			}

			self::redirectBack();

		}

	}

	/** 
	  * image manipulation routine
	  * 
	  * @param 	int 	$id image ID
	  * @param 	array 	$meta meta data array
	  */
	static function processImage( $id, $meta ) {

		// logged in?
		if( !self::is_loggedin() ) {

			// block present?
			if( c::get( 'block' ) && self::is_blocked( $id ) ) {
				return;
			}

			$path =  c::get( 'path_images' ) . $id . '.jpg';

			// open image
		    if( $image = @ImageCreateFromJPEG( $path ) ) {

		    	if( $meta['views'] < $meta['halflife'] ) {

			    	// get size
					$_size = getimagesize( $path );


					$size = array();
					$size['width'] = $_size[0];
					$size['height'] = $_size[1];

					$quality = self::calculateQuality( $meta['halflife'], $meta['views'] );
					$scale = self::calculateScale( $meta['halflife'], $meta['views'] );
					$new_size = self::calculateSize( $size, $scale );

					$new_image = imagecreatetruecolor( $new_size['width'], $new_size['height'] );
					imagecopyresampled( $new_image, $image, 0, 0, 0, 0, $new_size['width'], $new_size['height'], $size['width'], $size['height'] );

			    	imagejpeg( $new_image, $path, $quality['compression'] );

		    	} else {

		    		// remove image after halflife
		    		if( c::get( 'delete_after_halflife' ) ) {
		    			self::deleteImage( $id );
		    		} 

		    	}

				// increse view counter
				$views = $meta['views'] + 1;
				self::setMetaValue( $id, 'views', $views );

				// set block cookie 
				if( c::get( 'block' ) ) {
					self::block( $id );
				}

		    }

		}
 
	}

	/**
	 * Convert PNG image to JPEG and delete original 
	 *
	 * @param 	{int} 	id image ID
	 */
	static function PNGtoJPEG( $id ) {
		
		$path =  c::get( 'path_images' ) . $id . '.png';

		// open image
		if( $image = @ImageCreateFromPNG( $path ) ) {

	    	// get size
			$_size = getimagesize( $path );

			$size = array();
			$size['width'] = $_size[0];
			$size['height'] = $_size[1];

			$new_path = c::get( 'path_images' ) . $id . '.jpg';

			$new_image = imagecreatetruecolor( $size['width'], $size['height'] );
			imagecopyresampled( $new_image, $image, 0, 0, 0, 0, $size['width'], $size['height'], $size['width'], $size['height'] );
	    	
	    	// delete PNG image
	    	if( @imagejpeg( $new_image, $new_path, 100 ) ) {
	    		f::remove( c::get( 'path_images' ) . $id . '.png' );
	    	}

		}

	}

	/** 
	  * Qualculate JPEG quality based on views and image halflife 
	  * 
	  * @param 	int 	$halflife 
	  * @param 	int 	$views
	  * @return int 	0 > quality >= 100
	  */
	static function calculateQuality( $halflife, $views ) {

		if( $views == 0 ) {
			$views = 1;
		}

		// destruction: 100%
		if( $views > $halflife ) {
			$factor = 1;
		// destruction: 0%
		} else {
			$factor = 1 - ( $views / $halflife );
		}

		$factor = ( $factor < 0 ) ? 0 : $factor;

		$quality = array();
		$quality['compression'] = ( ( c::get( 'quality_maximum' ) - c::get( 'quality_minimum' ) ) * $factor ) + c::get( 'quality_minimum' );

		return $quality;
	}

	/** 
	  * Qualculate image scale factor quality based on views and image halflife 
	  * 
	  * @param 	int 	$halflife 
	  * @param 	int 	$views
	  * @return int 	0 >= scale factor >= 1
	  */
	static function calculateScale( $halflife, $views ) {

		if( $views == 0 ) {
			$views = 1;
		}

		// factor 
		if( $views > $halflife ) {
			$factor = 1;
		} else {
			$factor = 1 - ( $views / $halflife );
		}

		$scale = array();
		$scale['scale'] = ( ( c::get( 'scale_maximum' ) - c::get( 'scale_minimum' ) ) * $factor ) + c::get( 'scale_minimum' );

		return $scale;
	}

	/** 
	  * Qualculate image size based on current image size and scale factor
	  * 
	  * @param 	array 	$size 
	  * @param 	int 	$scale factor
	  * @return int 	image size
	  */
	static function calculateSize( $size, $scale ) {

			$width = $size['width'];
			$height = $size['height'];

			$ratio = $width / $height;

		    if( $width <= c::get( 'image_width' ) && isset( $scale ) ) {

		    	$new_width = round( c::get( 'image_width' ) * $scale['scale'] );
		    	$new_height = round( $new_width / $ratio );

		    } else {

		    	$new_width = c::get( 'image_width' );
		    	$new_height = round( $new_width / $ratio );

		    }


		$size = array();
		$size['width'] = $new_width;
		$size['height'] = $new_height;

		return $size;
	}

	/** 
	  * Render list
	  * 
	  */
	static function templateList() {
		global $view;
		$view = 'list';

		include( c::get( 'path_templates' ) . 'list.php' );
	}

	/** 
	  * Render item
	  * 
	  */
	static function templateItem( $id ) {
		global $view;
		$view = 'item';

		include( c::get( 'path_templates' ) . 'item.php' );
	}

	/**
	  * Render list of images 
	  *
	  *	@param 	int 	@limit number of images to display
	  *	@param 	int 	@offset number of images to skip
	  */
	static function renderList( $page = 0 ) {
		global $view;

		$page = ( get( 'page' ) ) ? intval( get( 'page' ) ) : $page;
		$offset = $page * c::get( 'render_limit' );
		$limit = c::get( 'render_limit' );

		$ids = self::getIds();
		$total = count( $ids );

		if( $limit > $total ) {
			$limit = $total;
		}

		if( $offset + $limit > $total && $offset < $total ) {
			$limit = $total - $offset;
		}

		for( $i = $offset; $i < ( $offset + $limit ); $i++ ) {

			if( $ids[$i] ) {

				$id = $ids[ $i ];

				include( c::get( 'path_templates' ) . 'inc/list-item.php' );

			}

		}

	}

	/**
	  * Render pagination 
	  *
	  */
	static function renderPagination() {
		$page = ( get( 'page' ) ) ? intval( get( 'page' ) ) : 0;
		$offset = ( intval( get( 'offset' ) ) ) ? intval( get( 'offset' ) ) : 0; 
		$limit = c::get( 'render_limit' ); 

		$total = count( self::getIds() );
		$pages = ceil( $total / $limit );

		if( $total > $limit ) {
			echo '<div class="pagination">';

			if( $page > 0 ) {
				$page_link = '?page=' . intval( $page - 1 );
				echo '<a href="' . $page_link . '" class="newer">newer</a>';
			}

			echo '<span class="current">';
			echo ( $page + 1 ) . ' / ' . $pages;
			echo '</span>';

			if( $page < ( $pages - 1 ) ) {
				$page_link = '?page=' . intval( $page + 1 );
				echo '<a href="' . $page_link . '" class="older">older</a>';
			}

			echo '</div>';
		}
	}

	/**
	  * Render tools 
	  *
	  */
	static function renderTools() {

		if( self::is_loggedin() ) {
			include( 'templates/inc/loggedin.php' ); 
		} else {
			include( 'templates/inc/loggedout.php' ); 
		}

	}

	/**
	  * Render messages
	  * 
	  */
	static function renderMessages() {
		global $messages;

		if( $messages ) {
			echo '<ul class="messagelist">';
			foreach( $messages as $message ) {
				echo '<li>';
				echo $message;
				echo '<a href="#" class="close">close</a>';
				echo '</li>';
			}
			echo '</ul>';
		}

	}

	/** 
	  * Check if current view is list view
	  * 
	  * @return boolean 
	  */
	static function is_list() {
		global $view;

		if( $view == 'list') {
			return true;
		} else {
			return false;
		}
	}

	/** 
	  * Check if current view is item view
	  * 
	  * @return boolean 
	  */
	static function is_item() {
		global $view;

		if( $view == 'item') {
			return true;
		} else {
			return false;
		}		
	}

	/**
	  * Get image URL by ID
	  *
	  *	@param 	int 	@id image ID
	  *	@return string 	image URL
	  */
	static function getImageUrl( $id ) {
		$url = self::getbaseUrl() . '?stream=' . $id;

		return $url;	
	}

	/**
	  * Get the app's base URL
	  *
	  *	@return string 	app URL
	  */
	static function getBaseUrl() {
	  $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443 ) ? 'https://' : 'http://';
	  $path = explode( '?', $_SERVER['REQUEST_URI'], 2 );
	  $url = $protocol . $_SERVER['HTTP_HOST'] . $path[0];

	  return $url;
	}

	/**
	  * Stream and process image to browser by ID
	  * 
	  * @param 	int 	$id image ID
	  */
	static function streamImage( $id ) {

		if( $id ) { 

			$src = c::get( 'path_images' )  . $id . '.jpg';

			if( file_exists( $src ) && is_readable( $src ) ) { 
			
				$meta = self::getMeta( $id );
				self::processImage( $id, $meta );

				header( 'Content-type: image/jpeg' ); 
				header( 'Content-length: ' . filesize( $src ) ); 

				$file = @fopen( $src, 'rb' ); 

				if( $file ) { 
					fpassthru( $file ); 
					exit; 
				} 
			} 
		}
	}

	/**
	  * Login routine
	  * 
	  */
	static function login() {
		global $messages;

		if( str::sanitize( get( 'user_name' ) ) == c::get( 'user_name' )
		 && str::sanitize( get( 'user_pass' ) ) == c::get( 'user_pass' ) ) {

			// set session var
			s::set( 'user_loggedin', true );
			session_write_close(); 
			self::redirectBack();

		} else {
			$messages[] = 'login failed. wrong user / password?';
		}

	}

	/**
	  * Logout routine
	  * 
	  */
	static function logout() {
		global $messages;

		$messages[] = 'you are logged out.';
		s::remove( 'user_loggedin' );
		self::redirectBack();
	}

	/**
	  * Check if user is logged in
	  * 
	  * @return 	boolean
	  */
	static function is_loggedin() {

		if( s::get( 'user_loggedin' ) ) {
			return true;
		} else {
			return false;
		}

	}

	/**
	  * Set block cookie
	  *
	  * @param 	{int} 	id image ID
	  */
	static function block( $id ) {
		cookie::set( $id, 1, c::get( 'block_duration' ), self::getBaseUrl() );
	}

	/**
	  * Check if block cookie is set 
	  * 
	  * @param 	{int} 	id image ID
	  * @return {boolean} result
	  */
	static function is_blocked( $id ) {
		if( cookie::get( $id ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	  * Redirect user after form submit to enable F5 page reload
	  * 
	  */
	static function redirectBack() {
		header( 'Location: ' . $_SERVER['HTTP_REFERER'] );		
	}

} // end compost
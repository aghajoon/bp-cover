<?php
/*
Plugin Name: BuddyPress cover
Plugin URI: http://webcaffe.ir
Description: Adds cover image in profile page and groups buddypress.
Version: 2.1.4.2
Author: asghar hatampoor
Author URI: http://webcaffe.ir


*/
if ( !defined( 'ABSPATH' ) ) exit;

if ( !defined( 'BPCO_PLUGIN_VERSION' ) )
	define( 'BPCO_PLUGIN_VERSION', '2.1.4.2' );

if ( !defined( 'BPCO_PLUGIN_DIRNAME' ) )
	define( 'BPCO_PLUGIN_DIRNAME', basename( dirname( __FILE__ ) ) );
	

if ( !defined( 'BPCO_PLUGIN_DIR' ) )
	define( 'BPCO_PLUGIN_DIR', trailingslashit( WP_PLUGIN_DIR . '/' . BPCO_PLUGIN_DIRNAME ) );

if ( !defined( 'BPCO_PLUGIN_URL' ) ) {
	$plugin_url = trailingslashit( plugins_url( BPCO_PLUGIN_DIRNAME ) );
	define( 'BPCO_PLUGIN_URL', $plugin_url );
}
if ( !defined('BP_COVER_TEMPLATES_DIR') ){
	define( 'BP_COVER_TEMPLATES_DIR',BPCO_PLUGIN_DIR . 'templates/');
}
if ( !defined('BP_COVER_TEMPLATES_URL') ){
	define( 'BP_COVER_TEMPLATES_URL',BPCO_PLUGIN_URL . 'templates/');
}
	
require_once ( BPCO_PLUGIN_DIR . 'bp-cover-group.php' );
require_once ( BPCO_PLUGIN_DIR . 'bp-cover-admin.php' );		
		
function bp_cover_load_textdomain() {
    load_plugin_textdomain('bp-cover', false, dirname(plugin_basename(__FILE__)) . "/languages/");
}
add_action('init', 'bp_cover_load_textdomain');

function bp_cover_scripts() {				
    wp_enqueue_style( 'bp-cover-css', BPCO_PLUGIN_URL . 'css/bp-cover.css');	
}
add_action( 'bp_before_activity_loop', 'bp_cover_scripts' );
add_action( 'bp_before_group_header', 'bp_cover_scripts' );

function bp_cover_enqueue_scripts() {
    wp_enqueue_style( 'bp-cover-css', BPCO_PLUGIN_URL . 'css/bp-cover.css');
    wp_enqueue_script( 'jquery-ui-draggable' );	
	wp_enqueue_script( 'bp-cover-js', BPCO_PLUGIN_URL . 'js/bp-cover.js', $dep = array(), $version = BPCO_PLUGIN_VERSION );	
}

add_action( 'bp_after_member_home_content', 'bp_cover_enqueue_scripts' );

function bp_cover_load_template () {
       global $bp;
	   $theme_default = 'default';
	   $theme_new = get_option( 'bp_cover_skin' );
	   $theme = ! empty( $theme_new ) ? $theme_new : $theme_default;				   
	   require_once ( BPCO_PLUGIN_DIR .'templates/'. $theme . '/theme.php' );
			
}
add_action('init', 'bp_cover_load_template');

function bp_cover_avatar_box($args = '') {
       global $bp;
	   return bp_core_fetch_avatar( $args );	
       extract( $args );    
}

function select_pic_for_cover($activity_id ) {
   global $bp;		
   $user_id=bp_loggedin_user_id();  
   $activity_id = $_POST['activity_id']; 
   $attachment_id = bp_activity_get_meta( $activity_id, 'bp_cover_activity', true );
   $fileurl = wp_get_attachment_image_src( $attachment_id ,'full');
        update_user_meta($user_id,'bp_cover',$fileurl[0]); 
		update_user_meta($user_id,'bp_cover_height',$fileurl[2]);  
		delete_user_meta($user_id,'bp_cover_position'); 
}
add_action('wp_ajax_select_pic_for_cover', 'select_pic_for_cover');		
		
function button_rtmedia_for_cover($media_id ) {  
global $rtmedia_media, $rtmedia,$bp;	
   $user_id=bp_loggedin_user_id();  
   $media_id = rtmedia_id();  
   if ( isset( $rtmedia_media->media_type ) ) {
   $author_id = $rtmedia_media->media_author;
		if ( $rtmedia_media->media_type == 'photo' ) {
		if ( bp_displayed_user_id () ) {
		if ($user_id == $author_id){
     echo '<a href="#" class="rtmcover" title="'.__("Select", "bp-cover").'" onclick="select_pic_rtmedia_for_cover(\''.$media_id.'\', \''.admin_url( 'admin-ajax.php' ).'\'); return false;">
					'.__("Set Cover", "bp-cover").'</a>';	
      					
					}
					} else if ( bp_is_group () ) {
					if (is_admin() || $bp->is_item_admin ) {
	 echo '<a href="#" class="rtmcover" title="'.__("Select", "bp-cover").'" onclick="select_pic_rtmedia_for_cover_group(\''.$media_id.'\', \''.admin_url( 'admin-ajax.php' ).'\'); return false;">
					'.__("Set Cover group", "bp-cover").'</a>';	
      					
				   }
				}
			}	
		}
	
}					
add_action('rtmedia_action_buttons_after_media', 'button_rtmedia_for_cover');

function select_pic_rtmedia_for_cover($photo_id ) {
  global $wpdb;
   $photo_id = $_POST['photo_id'];
   $user_id=bp_loggedin_user_id();  
   $tmb_qry = " SELECT media_id FROM ".$wpdb->prefix."rt_rtm_media WHERE media_author='".$user_id."' AND id='".$photo_id."'  AND media_type='photo' ORDER BY id DESC LIMIT 0, 8 ";   
   $tmb_res = $wpdb->get_results($tmb_qry);
   if(!empty($tmb_res)) {
	    foreach($tmb_res as $tmb_dat) {      
        $media_id = $tmb_dat->media_id;
        $src = wp_get_attachment_image_src($media_id );
		update_user_meta($user_id,'bp_cover',$src[0]);        				
		}     
    }
      delete_user_meta($user_id,'bp_cover_position'); 	
}
add_action('wp_ajax_select_pic_rtmedia_for_cover', 'select_pic_rtmedia_for_cover');
			
function delete_pic_cover($activity_id ) {
   global $bp; 	
   $user_id=bp_loggedin_user_id();  
   $activity_id = $_POST['activity_id']; 
   $attachment_id = bp_activity_get_meta( $activity_id, 'bp_cover_activity', true );    
        wp_delete_attachment( $attachment_id,true);
		delete_post_meta( $attachment_id,true);
        delete_user_meta($user_id,'bp_cover');        
		delete_user_meta($user_id,'bp_cover_position');  		
		bp_activity_delete( array( 'id' => $activity_id, 'user_id' => $bp->loggedin_user->id ) );
		BP_Activity_Activity::delete_activity_meta_entries( $activity_id );
}
add_action('wp_ajax_delete_pic_cover', 'delete_pic_cover');

function bp_cover_position( ) {
    $user_id=bp_loggedin_user_id(); 
    $id = ( isset( $_POST['id'] ) ) ? $_POST['id'] : '';
    update_user_meta( $user_id, 'bp_cover_position', $id );
    if( empty( $id ) )
    return;
  echo $id;
}
add_action('wp_ajax_bp_cover_position', 'bp_cover_position');	
add_action('wp_ajax_nopriv_bp_cover_position','bp_cover_position');


function core_get_user_displayname_box( $user_id ) {	
	return bp_core_get_user_displayname( $user_id );
	$user_info = get_userdata( $user_id );
	return $user_info->user_nicename;
}
function core_get_user_id_box( $user_id ) {	
	$user_info = get_userdata( $user_id );
	return $user_info->ID;
}
	
function core_get_user_description_box( $user_id ) {
	$user_description = get_userdata( $user_id );
	return $user_description->description;
}
function core_get_user_description_limit( $user_id ) {
	$user_bio = core_get_user_description_box( $user_id );
	$biography = wp_trim_words( $user_bio, 15, '...' );
	return $biography;
}
function bp_cover_delete($activity_id){
 global $bp;
     delete_user_meta(bp_loggedin_user_id(),'bp_cover'); 
	 delete_user_meta(bp_loggedin_user_id(),'bp_cover_position');	
	  die();
}
add_action('wp_ajax_bp_cover_delete', 'bp_cover_delete');	
	
function bp_cover_refresh() {
 global $bp;
	$user_id=$bp->displayed_user->id;      
     if(empty($user_id))
         return false;	
     $image=get_user_meta($user_id, 'bp_cover', true);				 
		  $filter = " <div class='image-upload-container'>
				 <img class='img-profile-header-background' id='user-banner-image'
                 src='$image'width='100%' style='width: 100%;'> </div>";		 		 
		 echo $filter;		
		die();
	}	
add_action('wp_ajax_bp_cover_refresh', 'bp_cover_refresh');	

function bp_avatar_refresh() {
 global $bp;	
    	$avatar_url = bp_core_fetch_avatar( array( 'type' => 'full', 'html' => false, 'item_id' => $user_id ) ); 		
		  $filter = " <div class='ava'> <img class='img-rounded profile-user-photo' id='user-profile-image'
            src=".$avatar_url."></div>";		 		 
		 echo $filter;		
		die();
	}	
add_action('wp_ajax_bp_avatar_refresh', 'bp_avatar_refresh');	

function bp_cover_handle_upload() {
global $bp, $wpdb;		
	if(!$user_id&&$bp->displayed_user->id)
         $user_id=$bp->displayed_user->id;      
    if(empty($user_id))
        return false;
		$activity_table = $wpdb->prefix."bp_activity";
		$activity_meta_table = $wpdb->prefix."bp_activity_meta";	
		$sql = "SELECT COUNT(*) as photo_count FROM $activity_table a INNER JOIN $activity_meta_table am ON a.id = am.activity_id WHERE a.user_id = %d AND meta_key = 'bp_cover_activity'";
		$sql = $wpdb->prepare( $sql, $user_id );
		$cnt = $wpdb->get_var( $sql );
		$max_cnt =bp_cover_get_max_total();
    if( $cnt < $max_cnt ) {         
	if( $_POST['encodedimg'] ) {  
    $file =  $_POST['imgsize'] ;
	$max_upload_size=bp_cover_get_max_media_size();  
      if( $max_upload_size > $file){ 
      $imgresponse = array();
      $uploaddir =wp_upload_dir();     
      /* let's decode the base64 encoded image sent */
      $img = $_POST['encodedimg'];
      $img = str_replace('data:'.$_POST['imgtype'].';base64,', '', $img);
      $img = str_replace(' ', '+', $img);
      $data = base64_decode($img);
      
      $imgname = wp_unique_filename( $uploaddir['path'], $_POST['imgname'] );   
      $filepath = $uploaddir['path'] . '/' . $imgname;
      $fileurl = $uploaddir['url'] . '/' . $imgname;
      
      /* now we write the image in dir */
   
      $success = file_put_contents($filepath, $data);

      if($success){
         $imgresponse[0] = "1";
         $imgresponse[1] = $fileurl;         

        update_user_meta(bp_loggedin_user_id(),'bp_cover',$fileurl);       
		delete_user_meta(bp_loggedin_user_id(),'bp_cover_position');  	
		
		do_action('bp_cover_uploaded',$fileurl);
	
      } else {
         $imgresponse[0] = "0";
         $imgresponse[1] = __('Upload Failed! Unable to write the image on server', 'bp-cover');
         
      } 
	  } else {
         $imgresponse[0] = "0";
         $imgresponse[1] = sprintf( __( 'The file you uploaded is too big. Please upload a file under %s', 'bp-cover'), size_format($max_upload_size) );
         
      } 	
      
   }else {
      $imgresponse[0] = "0";
      $imgresponse[1] = __('Upload Failed! No image sent', 'bp-cover');
      
   }
  
    }else {
      $imgresponse[0] = "0";
      $imgresponse[1] = sprintf( __('Max total images allowed %d in a cover gallery', 'bp-cover'),$max_cnt);
      
   }
   /* if everything is ok, we send back url to thumbnail and to full image */
   echo json_encode( $imgresponse );
   die();
}
 
add_action('wp_ajax_bp_cover_handle_upload', 'bp_cover_handle_upload');
add_action( 'wp_ajax_nopriv_bp_cover_handle_upload', 'bp_cover_handle_upload' );

function bp_cover_get_max_total(){
$total=get_option('bp_cover_profie_item');
$size_in_kb=20;
    if(empty ($total))
        $total=$size_in_kb; 
		
   return apply_filters("bp_cover_get_max_total",$total);
}

function bp_cover_get_max_media_size($converted=true){
    $size_in_kb=get_option('bp_cover_max_upload_size');
    if(empty ($size_in_kb))
        $size_in_kb=2000;
        if(!$converted)
        return  apply_filters("bp_cover_get_max_size_kb",$size_in_kb);
   $allowed_size=$size_in_kb*1024;
   return apply_filters("bp_cover_get_max_size_bytes",$allowed_size);
}


function bp_caver_avatar_handle_upload() {
  global $bp;
   if( $_POST['encodedimg'] ) {
      $user_id = !empty( $_POST['user_id'] ) ? $_POST['user_id'] : bp_displayed_user_id() ; 
      $imgresponse = array();
      $uploaddir = bp_core_avatar_upload_path() . '/avatars'; 
	  if( ! file_exists( $uploaddir ) ) { 
 	                mkdir( $uploaddir ); 
      }     
      $img = $_POST['encodedimg'];
      $img = str_replace('data:'.$_POST['imgtype'].';base64,', '', $img);
      $img = str_replace(' ', '+', $img);
      $data = base64_decode($img);  
	  $filepath = $uploaddir . '/' . $user_id;
	  if( ! file_exists( $filepath ) ) { 
 	                mkdir( $filepath ); 
      }
      $imgname = wp_unique_filename( $uploaddir, $_POST['imgname'] );         
      $fileurl = $filepath . '/' . $imgname;
	  $siteurl = trailingslashit( get_blog_option( 1, 'siteurl' ) );
	  $url = str_replace(ABSPATH,$siteurl,$fileurl);   
      $success = file_put_contents($fileurl, $data);
      $file =  $_POST['imgsize'] ;
	  $max_upload_size=bp_cover_get_max_media_size();  
      if( $max_upload_size > $file){ 
        if($success){
         $imgresponse[0] = "1";
         $imgresponse[1] = $fileurl;
         
         $size = getimagesize( $fileurl );
	
         /* Check image size and shrink if too large */
         if ( $size[0] > 150 ) {
            $original_file = image_resize( $fileurl, 150, 150, true );
            //$ava_file = image_resize( $fileurl, 250, 250, true );
            /* Check for thumbnail creation errors */
            if ( is_wp_error( $original_file ) ) {
               $imgresponse[0] = "0";
               $imgresponse[1] = sprintf( __( 'Upload Failed! Error was: %s', 'bp-cover' ), $original_file->get_error_message() );
               die();
            }
  
		$avatar_to_crop = str_replace( bp_core_avatar_upload_path(), '', $original_file );
		bp_core_delete_existing_avatar( array( 'item_id' => $user_id, 'avatar_path' => bp_core_avatar_upload_path() .'/avatars/' . $user_id ) );
			$crop_args = array( 'item_id' => $user_id, 'original_file' => $avatar_to_crop, 'crop_w' => 0, 'crop_h' => 0);		
				bp_core_avatar_handle_crop( $crop_args )  ;	
				//$url = str_replace(ABSPATH,$siteurl,$ava_file);   
				update_user_meta(bp_loggedin_user_id(),'profile_avatar',$url);
				do_action( 'xprofile_avatar_uploaded' );
        } else{			
      
		 $imgresponse[0] = "0";
         $imgresponse[1] = __('Upload Failed! Your photo must be larger than 150px', 'bp-cover');
         		 
		 }
				
      } else {
         $imgresponse[0] = "0";
         $imgresponse[1] = __('Upload Failed! Unable to write the image on server', 'bp-cover');
         
      }
      } else {
         $imgresponse[0] = "0";
         $imgresponse[1] = sprintf( __( 'The file you uploaded is too big. Please upload a file under %s', 'bp-cover'), size_format($max_upload_size) );
         
      } 	
   }else {
      $imgresponse[0] = "0";
      $imgresponse[1] = __('Upload Failed! No image sent', 'bp-cover');
      
   }
   /* if everything is ok, we send back url to thumbnail and to full image */
   echo json_encode( $imgresponse );
   die();
}
 
add_action('wp_ajax_bp_caver_avatar_handle_upload', 'bp_caver_avatar_handle_upload');
add_action( 'wp_ajax_nopriv_bp_caver_avatar_handle_upload', 'bp_caver_avatar_handle_upload' );
		
function bp_cover_get_image_scr($user_id=false){
    global $bp;
    if(!$user_id&&$bp->displayed_user->id)
            $user_id=$bp->displayed_user->id;    
     if(empty($user_id))
         return false;
     $image=get_user_meta($user_id, 'bp_cover', true);	 
	 $filter .= "<img class='bp-cover' src='$image'  />";		
		return $filter; 
}

function bp_cover_get_avatar_scr($user_id=false){
    global $bp;
    if(!$user_id&&$bp->displayed_user->id)
            $user_id=$bp->displayed_user->id;
    
     if(empty($user_id))
         return false;
     $image=get_user_meta($user_id, 'profile_avatar', true);	 
	 $filter .= "<img class='bp-ava' src='$image'  />";		
	    return $filter;  
}

function bp_cover_record_activity() {
global $bp;
 if(!$user_id&&$bp->displayed_user->id)
            $user_id=$bp->displayed_user->id;
    
     if(empty($user_id))
         return false;
if ( !function_exists( 'bp_activity_add' ) )
return false;
$r = array(
           'user_id' => $bp->loggedin_user->id,
           'content' =>bp_cover_get_image_scr() ,
           'action'=>sprintf( __( '%s uploaded a new cover picture', 'bp-cover' ),bp_core_get_userlink( $bp->loggedin_user->id )),
           'primary_link' => bp_core_get_userlink( $bp->loggedin_user->id ),
		   'component_name' => $bp->profile->id,
		   'component_action' =>"change_cover",
		   'item_id' => $bp->loggedin_user->id,
		   'secondary_item_id' => false,		   
		   'recorded_time' => gmdate( "Y-m-d H:i:s" ),
		   'hide_sitewide' => false
		   );
  
extract( $r, EXTR_SKIP ); 
        $activity_id = bp_activity_add( $r );
		$uploaddir =wp_upload_dir();    
		$name = $_POST['imgname'];			
        $attachment = array();
		$type = $_POST['imgtype'];
		$filename = $uploaddir['path']. '/' . $name;
		$title = wp_unique_filename( $uploaddir['path'], $_POST['imgname'] );  
		$url =  $uploaddir['url'] . '/' . $name;		 				
		$attachment = array(
		    'post_mime_type' => $type,
			'guid' => $url,
			'post_title' => $title,			
		);
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
		$attachment_id = wp_insert_attachment($attachment,$filename);
		$attach_data = wp_generate_attachment_metadata( $attachment_id, $filename );
        wp_update_attachment_metadata( $attachment_id,  $attach_data );
		$fileurl = wp_get_attachment_image_src( $attachment_id ,'full');
		update_user_meta($user_id,'bp_cover_height',$fileurl[2]);  
		bp_activity_update_meta( $activity_id, 'bp_cover_activity', $attachment_id );
	
}
add_action("bp_cover_uploaded","bp_cover_record_activity");


function xprofile_new_avatar_activity_new() {
 global $bp;
     if ( !function_exists( 'bp_activity_add' ) )
     return false;
     $user_id = apply_filters( 'xprofile_new_avatar_user_id', $bp->displayed_user->id );
     $userlink = bp_core_get_userlink( $user_id );
     bp_activity_add( array(
     'user_id' => $user_id,
     'content' =>bp_cover_get_avatar_scr() ,
     'action' => apply_filters( 'xprofile_new_avatar_action', sprintf( __( '%s uploaded a new profile picture', 'buddypress' ), $userlink ), $user_id ),
     'component' => 'profile',
     'type' => 'new_avatar'
 ));
}
add_action( 'xprofile_avatar_uploaded', 'xprofile_new_avatar_activity_new' );

function bp_cover_get_default_avatar () {
	$default_avatar = get_option('bp_cover_avatar');
            if ( ! empty( $default_avatar ) ){
                 $avatar = $default_avatar;
				}else{
                 $avatar = BPCO_PLUGIN_URL ."/images/default_user.jpg";
			    }			
            
			  return apply_filters( 'bp_cover_get_default_avatar',$avatar );
}
add_filter('bp_core_mysteryman_src','bp_cover_get_default_avatar' );



function bp_cover_actions(){
  global $bp;     
	      remove_action(  'xprofile_avatar_uploaded', 'bp_xprofile_new_avatar_activity'  ); 
		  
}
add_action( "init","bp_cover_actions", 5 );


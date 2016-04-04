<?php
function bp_cover_group_scripts() {	
	wp_enqueue_style( 'bp-cover-group-css', BPCO_PLUGIN_URL . 'css/bp-cover-group.css', $dep = array(), $version = BPCO_PLUGIN_VERSION );
	wp_enqueue_script( 'bp-cover-group-js', BPCO_PLUGIN_URL . 'js/bp-cover-group.js', $dep = array(), $version = BPCO_PLUGIN_VERSION );	
      wp_enqueue_script( 'jquery-ui-draggable' );	
}
add_action( 'bp_before_group_header', 'bp_cover_group_scripts' );



function cover_group_name(){
    $group_id=bp_get_current_group_id();
        if(empty($group_id))
            return;
   $group = groups_get_group( array( 'group_id' => $group_id ) );
   return $group->name;

}
function cover_group_description(){
    $group_id=bp_get_current_group_id();
        if(empty($group_id))
            return;
   $group = groups_get_group( array( 'group_id' => $group_id ) );
   return substr( $group->description, 0, 60 );

}


function bp_cover_group_position( ) {
    $group_id=bp_get_current_group_id(); 
    $id = ( isset( $_POST['id'] ) ) ? $_POST['id'] : '';
    groups_update_groupmeta( $group_id, 'bp_cover_group_position', $id );
    if( empty( $id ) )
    return;
  echo $id;
}
add_action('wp_ajax_bp_cover_group_position', 'bp_cover_group_position');	
add_action('wp_ajax_nopriv_bp_cover_group_position','bp_cover_group_position');

function bp_group_cover_refresh() {
 global $bp;
	$group_id=bp_get_current_group_id();      
     if(empty($group_id))
         return false;	
     $image=groups_get_groupmeta($group_id, 'bp_cover_group', true);				 
		  $filter = " <div class='cover'>
				 <img class='bp-cover-top' 
                 src='$image'width='100%' style='width: 100%;'> </div>";
		 		$filter .='<div id="bpci-polaroid-upload-group"> </div><div id="profile-mass">'.__("Drag cover", "bp-cover").'</div> ';
		 echo $filter;		
		die();
	}	
add_action('wp_ajax_bp_group_cover_refresh', 'bp_group_cover_refresh');	

function bp_cover_group_get_image_scr(){
    global $bp;
    if(empty($group_id))
     $group_id=$bp->groups->current_group->id; 
     $image=groups_get_groupmeta($group_id, 'bp_cover_group', true);	 
	 $filter .= "<img class='bp-cover' src='$image'/>";		
		return $filter; 
}

function delete_pic_cover_group($activity_id ) {
   global $bp; 	
   $group_id=$bp->groups->current_group->id;   
   $activity_id = $_POST['activity_id']; 
   $attachment_id = bp_activity_get_meta( $activity_id, 'all_bp_cover_group', true );  
        wp_delete_attachment( $attachment_id,true);
        groups_delete_groupmeta($group_id,'bp_cover_group');        
		groups_delete_groupmeta($group_id,'bp_cover_group_position');  		
		bp_activity_delete( array( 'id' => $activity_id, 'item_id' => $group_id ) );
		BP_Activity_Activity::delete_activity_meta_entries( $activity_id );
}
add_action('wp_ajax_delete_pic_cover_group', 'delete_pic_cover_group');

function select_cover_for_group($activity_id ) {
   global $bp;		
   $group_id=$bp->groups->current_group->id;  
   $activity_id = $_POST['activity_id']; 
   $attachment_id = bp_activity_get_meta( $activity_id, 'all_bp_cover_group', true );
   $fileurl = wp_get_attachment_image_src( $attachment_id ,'full');     
		groups_update_groupmeta($group_id,'bp_cover_group',$fileurl[0]);       		
		groups_update_groupmeta($group_id,'bp_cover_group_position',0); 
        /*groups_record_activity( array(
            'action'            => sprintf( __( '%s changed their cover group %s', 'bp-cover' ),
			                       bp_core_get_userlink( $bp->loggedin_user->id ),								
                                   '<a href="' . bp_get_group_permalink( $group ) . '">' . esc_attr( $group->name ) . '</a>') ,
            'type'              => 'cover_changed',
			'item_id' => $group_id,
			'content' =>bp_cover_group_get_image_scr() ,
            'item_id'           => $group_id,
        ) );*/		

}
add_action('wp_ajax_select_cover_for_group', 'select_cover_for_group');

function select_pic_rtmedia_for_cover_group($photo_id ) {
  global $bp , $wpdb;
   $photo_id = $_POST['photo_id'];
   $group_id=$bp->groups->current_group->id;  
   $tmb_qry = " SELECT media_id FROM ".$wpdb->prefix."rt_rtm_media WHERE id='".$photo_id."' ORDER BY id DESC LIMIT 0, 8 ";   
   $tmb_res = $wpdb->get_results($tmb_qry);   
   if(!empty($tmb_res)) {
	    foreach($tmb_res as $tmb_dat) {      
        $media_id = $tmb_dat->media_id;
        $src = wp_get_attachment_image_src($media_id );
		groups_update_groupmeta($group_id,'bp_cover_group',$src[0]);       	    				
		}     
    }
    groups_update_groupmeta($group_id,'bp_cover_group_position',0); 


}
add_action('wp_ajax_select_pic_rtmedia_for_cover_group', 'select_pic_rtmedia_for_cover_group');
		
function bp_cover_group_handle_upload($activity_id) {
global $bp, $wpdb;
$group_id=bp_get_current_group_id();
$activity_table = $wpdb->prefix."bp_activity";
		$activity_meta_table = $wpdb->prefix."bp_activity_meta";	
		$sql = "SELECT COUNT(*) as photo_count FROM $activity_table a INNER JOIN $activity_meta_table am ON a.id = am.activity_id WHERE a.item_id = %d AND meta_key = 'all_bp_cover_group'";
		$sql = $wpdb->prepare( $sql, $group_id );
		$cnt = $wpdb->get_var( $sql );
		$max_cnt = bp_cover_get_max_total();
    if( $cnt < $max_cnt ) {  
    if( $_POST['encodedimg'] ) {  
      $file =  $_POST['imgsize'] ;
	  $max_upload_size=bp_cover_get_max_media_size();  
      if( $max_upload_size > $file){ 
      $group_id=$bp->groups->current_group->id;     
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
        $size = @getimagesize( $filepath );
        $attachment = array(
		    'post_mime_type' => $_POST['imgtype'],
			'guid' => $fileurl,
			'post_title' => $imgname,			
		);		
		require_once( ABSPATH . 'wp-admin/includes/image.php' );
		$attachment_id = wp_insert_attachment($attachment,$filepath);
		$attach_data = wp_generate_attachment_metadata( $attachment_id, $filepath );
        wp_update_attachment_metadata( $attachment_id,  $attach_data );
        groups_update_groupmeta($group_id,'bp_cover_group',$fileurl);       		
		groups_update_groupmeta($group_id,'bp_cover_group_position',0);		
		$group = groups_get_group ( array ( "group_id" => $group_id ) );
        $activity_id = groups_record_activity( array(
            'action'            => sprintf( __( '%s uploaded a new cover picture to the group %s', 'bp-cover' ),
			                       bp_core_get_userlink( $bp->loggedin_user->id ),								
                                   '<a href="' . bp_get_group_permalink( $group ) . '">' . esc_attr( $group->name ) . '</a>') ,
            'type'              => 'cover_added',
			'item_id' => $group_id,
			'content' =>bp_cover_group_get_image_scr() ,
            'item_id'           => $group_id,
        ) );
		
        bp_activity_update_meta( $activity_id, 'all_bp_cover_group', $attachment_id );
		update_post_meta($attachment_id, 'bp_cover_group_thumb', $imgresponse[2]);		
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
 
add_action('wp_ajax_bp_cover_group_handle_upload', 'bp_cover_group_handle_upload');
add_action( 'wp_ajax_nopriv_bp_cover_group_handle_upload', 'bp_cover_group_handle_upload' );

<?php 

function bp_cover_mani_scripts() {	
	wp_enqueue_style( 'bp-cover-profile-css', BPCO_PLUGIN_URL . 'templates/mani/css/style.css', $dep = array(), $version = BPCO_PLUGIN_VERSION );
	wp_enqueue_script( 'bp-cover-profile-js', BPCO_PLUGIN_URL . 'templates/mani/js/bp-cover-profile.js', $dep = array(), $version = BPCO_PLUGIN_VERSION );	
}
add_action( 'bp_after_member_home_content', 'bp_cover_mani_scripts' );

function cover_image_scr($user_id=false){

   		global $bp, $wpdb;		
	if(!$user_id&&$bp->displayed_user->id)
         $user_id=$bp->displayed_user->id;      
    if(empty($user_id))
        return false;
		$activity_table = $wpdb->prefix."bp_activity";
		$activity_meta_table = $wpdb->prefix."bp_activity_meta";				
		$sql = "SELECT a.*, am.meta_value FROM $activity_table a INNER JOIN $activity_meta_table am ON a.id = am.activity_id WHERE a.user_id = $user_id AND meta_key = 'bp_cover_activity' ORDER BY a.date_recorded DESC";	
		$pics  = $wpdb->get_results($sql,ARRAY_A);

	 $avatar_options = array (  'type' => 'full', 'id' => 'user-profile-image','class' => 'img-rounded profile-user-photo');   
	 $avatar = bp_cover_avatar_box( $avatar_options );
     $image=get_user_meta($user_id, 'bp_cover', true);
	 $author_id =  get_current_user_id();
	 $pos=get_user_meta($user_id, 'bp_cover_position', true);	
	 $height_or=get_user_meta($user_id, 'bp_cover_height', true);
	 $height_ol=800;
	 $height = ! empty( $height_or ) ? $height_or : $height_ol;	
	 $cover_profile = get_option( 'bp_cover_profile' );
	 $filter = '<input id="id"  name="id"  type="hidden" >
                <div class="panel">';
	 		
	 $filter .= '<div class="panel-profile-header">';
	  if(!empty($image)){	 
	  $filter .= '<style>
	  .image-cover{
			 top:'.$pos.'px;
			 background-image: url("'.$image.'")!important;
			 min-height:'.$height.'px!important;
			 }
	</style>';	
	 $filter .= '<div class="image-upload-container">
                 <div class="image-cover img-profile-header-background"></div>			
				 <div id="bpci-polaroid-upload-banner"></div><div id="profile-mass">'.__("Drag cover", "bp-cover").'</div> <div id="mass-drag">'.__("Position saved", "bp-cover").'</div></div> ';
		}else{
		if(!empty($cover_profile)){
	 $filter .= '<div class="image-upload-container">
	             <div id="bpci-polaroid-upload-banner"> </div>
	<style>
	  .image-cover{
			 background-image: url("'.$cover_profile.'")!important;
			 }
	</style>
				 <div class="image-cover img-profile-header-background"></div></div> ';
		}else{
	 $filter .= '<div class="image-upload-container">
	             <div id="bpci-polaroid-upload-banner"> </div>
			     <div class="image-cover img-profile-header-background"></div></div>	';	
		    }
		}
     $filter .= '<div class="img-profile-data">
                <h1>'.core_get_user_displayname_box( $user_id ).'</h1>
                
                </div>';
			if ($user_id == $author_id){
			$filter .= '<div style="display: none;" class="image-upload-buttons" id="banner-image-upload-buttons">
			            <div class="uploadBox"  id="bannerfileupload">';
			
			foreach( $pics as $pic ){
			$attachment_id = isset($pic['meta_value']) ? (int)$pic['meta_value'] : 0;
			}
			$filter .='<a href="javascript:void(0)" id="uploadcover" class="btnc  btn-sm" href="" title="'.__("Upload", "bp-cover").'"><i class="dashicons dashicons-upload"></i></a>				      
                <input type="file" name="cover_filename" id="cover_pic" class="uploadFile"  >';  
		
			if ( $attachment_id > 0 ){
			$filter .= '<a class="btnc  btn-sm" data-modal-id="popup1" title="'.__("Gallery", "bp-cover").'"><i class="dashicons dashicons-format-gallery"></i></a>';
			}
           	if(!empty($image)) {
			$filter .='<a  class="btnc btn-save btn-sm" title="'.__("Save", "bp-cover").'" ><i class="dashicons dashicons-yes"></i></a> <a  class="edit-cover btnc  btn-sm" title="'.__("Edit Position", "bp-cover").'"><i class="dashicons dashicons-sort"></i></a><a class="btnc btn-remove btn-sm" title="'.__("Delete", "bp-cover").'"><i class="dashicons dashicons-trash"></i></a>';
			}
            $filter .= ' </div></div>';			
			}  
			$filter .= '<div class="image-upload profile-user-photo-container ava" ><div id="bpci-polaroid-upload-avatar"> </div>';				
			$filter .= $avatar ;
			if ($user_id == $author_id){
			$filter .= '<div class="image-upload-buttons-avatar" id="profile-image-upload-buttons">
				
                <div class="uploadBox" id="profilefileupload">                               
				<a href="javascript:void(0)" id="uploadavatar" class="btnc  btn-sm" href="" title="'.__("Upload", "bp-cover").'">
                <i class="dashicons dashicons-upload"></i></a>
				<input type="file" name="avatar_filename" id="avatar_pic" class="uploadFile"  > 
                </div>				
                </div> ';				
			}
			$filter .= '</div></div></div>';
			
		 echo apply_filters( 'cover_image_scr',$filter);
}


add_action('bp_before_member_header','cover_image_scr');

function bp_cover_gallery_pop() {
    global $bp, $wpdb;		
	if(!$user_id&&$bp->displayed_user->id)
         $user_id=$bp->displayed_user->id;      
    if(empty($user_id))
        return false;
		$activity_table = $wpdb->prefix."bp_activity";
		$activity_meta_table = $wpdb->prefix."bp_activity_meta";				
		$sql = "SELECT a.*, am.meta_value FROM $activity_table a INNER JOIN $activity_meta_table am ON a.id = am.activity_id WHERE a.user_id = $user_id AND meta_key = 'bp_cover_activity' ORDER BY a.date_recorded DESC";	
		$pics  = $wpdb->get_results($sql,ARRAY_A);	
		$author_id =  get_current_user_id();
		if ($user_id == $author_id){
        $filter .= '<div class="js-modalbox"><div id="popup1" class=" modal-box" >
                <header> 
                <h3>'.__("Gallery Cover", "bp-cover").'</h3>
				<a class="btnc btn-small js-modal-close"title="'.__("Close", "bp-cover").'"><i class="dashicons dashicons-no"></i></a> 
                </header>
                <div class="modal-body"><ul class="gallery-pic" id="bp-cover-pic">';
            foreach( $pics as $pic ){
			$attachment_id = isset($pic['meta_value']) ? (int)$pic['meta_value'] : 0;				
			if ( $attachment_id > 0 ){
			$activity_id = $pic[ 'id' ];
			        $attachment_new = get_post_meta($attachment_id, 'bp_cover_thumb', true);
					$attachment_old = wp_get_attachment_image_src( $attachment_id , 'thumbnail');                   			
					$attachment = ! empty( $attachment_new ) ? $attachment_new : $attachment_old[0];					
					$filter .= '<li class="gallery-cover">
					<div id="'.$activity_id.'">
					<div class ="delete-cover">
					<a href="#" class="delete-pic" title="'.__("Delete", "bp-cover").'" onclick="delete_pic_cover(\''.$activity_id.'\', \''.admin_url( 'admin-ajax.php' ).'\'); return false;"><i class="dashicons dashicons-trash"></i></a></div>
					<span class="delete-loader"></span>
					<div class ="pic-cover">
					<a href="#" class="select-pic" title="'.__("Select", "bp-cover").'" onclick="select_pic_for_cover(\''.$activity_id.'\', \''.admin_url( 'admin-ajax.php' ).'\'); return false;">
					<img src="'.$attachment.'" /></a></div></div></li>
				';
				    }
			    }
                $filter .= ' </ul></div>
                <footer> </footer></div></div>';
				}
echo $filter;	
	wp_die( );  
}
add_action('wp_ajax_bp_cover_gallery_pop','bp_cover_gallery_pop');
add_action( 'wp_ajax_nopriv_bp_cover_gallery_pop', 'bp_cover_gallery_pop' );

function cover_group_image_scr(){
   		global $bp,$groups_template, $wpdb;	
        $user_id=get_current_user_id(); 
		$group_id=bp_get_current_group_id();
		$avatar_options = array ( 
		'item_id' => $group_id,
		'object' => 'group',
		'type' => 'full',
		'class' => 'ava-cover',
		'width' => 90,
		'height' => 90
		);
		
	    $cover_group = get_option( 'bp_cover_group' );
        $cover=groups_get_groupmeta($group_id, 'bp_cover_group',true);
		$pos=groups_get_groupmeta($group_id, 'bp_cover_group_position', true);
		$avatar_group = bp_core_fetch_avatar( $avatar_options );
                $filter ='<input id="id"  name="id"  type="hidden" ><div id="gal">
				            <nav class="galnav "><div class="group-cover">';	 
		if ( empty( $group ) ) {
		$group =& $groups_template->group;
	    }
        $query = new BP_Group_Member_Query( array(
			'group_id'   => $group->id,
			'group_role' => 'admin',
			'type'       => 'first_joined',
		) );
		if ( ! empty( $query->results ) ) {
			$group->admins = $query->results;			
		}		
		if(!empty($cover)){	 
		 	  $filter .= '<style>
		              .group-cover-img{
					         top:'.$pos.'px;
							 background-image: url("'.$cover.'")!important;
							 min-height: 800px!important;
							 }
						</style>';	
							$filter .='<div class="cover"><div class="group-cover-img bp-cover-top"></div></div>								
							<div id="bpci-polaroid-upload-group"> </div><div id="profile-mass">'.__("Drag cover", "bp-cover").'</div><div id="mass-drag">'.__("Position saved", "bp-cover").'</div>';
							}else{
							    if(!empty($cover_group)){
                            $filter .='<style>
							             .group-cover-img{
											  background-image: url("'.$cover_group.'")!important;											 
											  }
										</style>
							<div class="cover"><div class="group-cover-img"></div><div id="bpci-polaroid-upload-group"> </div>';
							    }else{
							$filter .='<div class="cover"><div class="image-cover"></div><div id="bpci-polaroid-upload-group"> </div>';
							     }
							}
				$filter .=	'</div><li><input type="radio" name="btn" value="group-cover" checked="checked"/>
                             <label for="btn">'.$avatar_group .'</label>
                             <figure> ';
							$filter .='<figcaption>
							 <div class="ava-group">'.$avatar_group .'</div>
                             <h4>'.cover_group_name().'</h4>							
                             <nav role="navigation">
                             <p>'.cover_group_description().'</p><ul>';
       
		if ( ! empty( $group->admins ) ) { 		
		foreach( (array) $group->admins as $admin ) {
		$admin_link = bp_core_get_user_domain( $admin->user_id, $admin->user_nicename, $admin->user_login );
		$admin_ava = bp_core_fetch_avatar( 
		array( 
		'item_id' => $admin->user_id, 
		'email' => $admin->user_email ,
		'type' => 'full',
		'class' => 'ava-cover',
		'width' => 90,
		'height' => 90 ) );
                    $filter .=	' <li><a href="'.$admin_link.'" class="entypo-dribbble">'.$admin_ava .'</a></li>';
                }
        }		
				    $filter .='</ul></nav></figcaption>';				
				    $filter .= '</figure></li>';
				if (is_admin() || $bp->is_item_admin ) { 
					$filter .='<li><input type="radio" name="btn" value="upload-cover"/>
                               <label for="btn"><i class="dashicons dashicons-admin-generic"></i></label>
				               <figure class="entypo-forward">';							  							
					$filter .='<figcaption> <nav role="navigation"><h2>'.__("Gallery Cover", "bp-cover").'</h2><ul class="gallery-cover">';
		$activity_table = $wpdb->prefix."bp_activity";
		$activity_meta_table = $wpdb->prefix."bp_activity_meta";				
		$sql = "SELECT a.*, am.meta_value FROM $activity_table a INNER JOIN $activity_meta_table am ON a.id = am.activity_id WHERE a.item_id = $group_id AND meta_key = 'all_bp_cover_group' ORDER BY a.date_recorded DESC";	
		$attachment_ids  = $wpdb->get_results($sql,ARRAY_A);
        foreach( $attachment_ids as $attachment_id ){
	    $images = isset($attachment_id['meta_value']) ? (int)$attachment_id['meta_value'] : 0;
		$attachment_new = get_post_meta($images, 'bp_cover_group_thumb', true);
		$attachment_old = wp_get_attachment_image_src( $images , 'thumbnail');                   			
		$image = ! empty( $attachment_new ) ? $attachment_new : $attachment_old[0];			
        //$image = wp_get_attachment_image_src($images);
		$activity_id = $attachment_id[ 'id' ];

		if(!empty($image)){
		            $filter.='<li ><div id="'.$activity_id.'">		
					<a href="#" class="delete-pic" title="'.__("Delete", "bp-cover").'" onclick="delete_pic_cover_group(\''.$activity_id.'\', \''.admin_url( 'admin-ajax.php' ).'\'); return false;"></a>
					<a href="#" class="pics" title="'.__("Select", "bp-cover").'" onclick="select_cover_for_group(\''.$activity_id.'\', \''.admin_url( 'admin-ajax.php' ).'\'); return false;">
					<img class="cover-pics" src="'. $image.'" /></a></div></li>';
		                  }
        }
		 if(empty($image)){	    
			                $filter .=' <h4>'.__("Gallery empty", "bp-cover").'</h4>';
							$filter .= '<p>'.__("you can upload image for cover group", "bp-cover").'</p>';						 
						  }
							$filter .='</ul><ul class="upload-cover">';
							$filter .='<div class="uploadBox"  id="groupfileupload">';
			
						    $filter .='	<a href="javascript:void(0)" id="uploadgroup" class="btnc btn-sm" href="" title="'.__("Upload", "bp-cover").'"><i class="dashicons dashicons-upload"></i> </a>
									   <input type="file" name="group_filename" id="group_pic" class="uploadFile"  >   ';						
                            $filter .= '<a  class="edit-cover btnc btn-sm" title="'.__("Edit Position", "bp-cover").'" ><i class="dashicons dashicons-sort"></i></a>
								       <a  class="btnc btn-save btn-sm" title="'.__("Save", "bp-cover").'" ><i class="dashicons dashicons-yes"></i></a></div></ul></nav></figcaption></figure></li> '  ;
							}

							$filter .='</nav></div>';	
		
		
		 echo apply_filters( 'cover_group_image_scr',$filter);
}
add_action('bp_before_group_header','cover_group_image_scr');


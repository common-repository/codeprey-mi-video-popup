<?php 
/*
 * Mivideopopup Class
 */

class Mivideopopup {

    public function int() {
        
    }

    /*
     * Adding Action to add field into page post type of admin panel on install
     * Plugin from admin panel 
     */

    public function __construct() {
        add_action('add_meta_boxes', array($this, 'mivideopop_add_meta_box'));
        add_action('save_post', array($this, 'mivideopop_save_meta_box_data'));
        add_action('pre_get_posts', array($this, 'mivideopopup_play_popup'));
        add_action('admin_footer', array($this, 'mivideopopup_popup_javascript'));
        wp_enqueue_script('placeholder', plugins_url('/placeholder.js', __FILE__), array('jquery'), '1.0', true);
        wp_localize_script('placeholder', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
    }

    /*
     * Saving Dta into data into videos table	
     * @return Returning Form for adding video
     */

    public function mivideopopup_create() {
        global $wpdb;
        if (isset($_GET['edit'])):
            if (!is_numeric($_GET['edit'])):
            endif;
        endif;

        if (isset($_POST['submit']) && !isset($_POST['videoId'])):
			
            if (!empty($_POST['popupLink'])):
                $popupLink = esc_url($_POST['popupLink']);
            elseif ($_POST['popuptype'] == "native"):
                $popupLink = esc_url($_POST['video']);
            else:
                $popupLink = "";
            endif;
            $wpdb->replace(
                    $wpdb->prefix . 'videos', array(
                'popup_title' => sanitize_text_field($_POST['title']),
                'popup_type' => sanitize_text_field($_POST['popuptype']),
                'popup_link' => sanitize_text_field($popupLink),
                'popup_autoplay' => ($_POST['autoplay']==1 ? (int) 1 : (int) 0),
                'popup_background' => sanitize_text_field($_POST['backgroundtype']),
                'color1' => sanitize_text_field($_POST['solidColor']),
                'color2' => sanitize_text_field($_POST['grantColor']),
                'picture_link' => sanitize_text_field($_POST['image']),
                'popup_size' => sanitize_text_field($_POST['videoSize']),
                'creation_date' => time(),
                'update_date' => time(),
                    ), array('%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
            );
			$_SESSION['savevideo'] = 1;
			
        endif;


        if (isset($_POST['submit']) && isset($_POST['videoId'])):

            if (!empty($_POST['popupLink'])):
                $popupLink = esc_url($_POST['popupLink']);
            elseif ($_POST['popuptype'] == "native"):
                $popupLink = esc_url($_POST['video']);
            else:
                $popupLink = "";
            endif;
			$wpdb->update(
                    $wpdb->prefix . 'videos', array(
                'popup_title' => sanitize_text_field($_POST['title']),
                'popup_type' => sanitize_text_field($_POST['popuptype']),
                'popup_link' => sanitize_text_field($popupLink),
                'popup_autoplay' => ($_POST['autoplay']==1 ? (int) 1 : (int) 0),
                'popup_background' => sanitize_text_field($_POST['backgroundtype']),
                'color1' => sanitize_text_field($_POST['solidColor']),
                'color2' => sanitize_text_field($_POST['grantColor']),
                'picture_link' => sanitize_text_field($_POST['image']),
                'popup_size' => sanitize_text_field($_POST['videoSize']),
                'creation_date' => time(),
                'update_date' => time(),
                    ), array('id' => intval($_POST['videoId'])), array( '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s'), array('%d')
            );
			$_SESSION['savevideo'] = 2;
        endif;

        if (isset($_GET['edit'])):
            $videoRow = $wpdb->get_row("SELECT * FROM `" . $wpdb->prefix . "videos` WHERE `id`=" . intval($_GET['edit']));
        endif;
        ?>

        <div class="wrap">
            <h2>Add Popup Video</h2>
			<?php if(isset($_SESSION['savevideo'])): ?>
				<div id="message" class="updated notice notice-success is-dismissible"><p>Popup has been saved.</p></div>
			<?php unset($_SESSION['savevideo']); endif; ?>
            <form method="post" action="">
                <?php if (!empty($videoRow)): ?>
                    <input type="hidden" name="videoId" value="<?php echo $videoRow->id; ?>"/>
                <?php endif; ?>
                <table class="form-table">
                    <tr>
                        <th><label>Title</label></th>
                        <td>
						<input type="text" required="required" name="title" value="<?php if (!empty($videoRow)): echo $videoRow->popup_title;
        endif; ?>" placeholder="Popup Title" /></td>
                    </tr>
                    <tr>
                        <th>Popup Type</th>
                        <td>
                            <label><input required="required" type="radio" name="popuptype" value="native" class="popupType" <?php if (!empty($videoRow) && $videoRow->popup_type == "native"): ?> checked="checked" <?php endif; ?>/>Native</label>
                            <label><input required="required" type="radio" name="popuptype" value="youtube" class="popupType" <?php if (!empty($videoRow) && $videoRow->popup_type == "youtube"): ?> checked="checked" <?php endif; ?>/>Youtube</label>
                            <label><input required="required" type="radio" name="popuptype" value="vimeo" class="popupType" <?php if (!empty($videoRow) && $videoRow->popup_type == "vimeo"): ?> checked="checked" <?php endif; ?>/>Vimeo</label>
                        </td>
                    </tr>
                    <tr class="nativeVideo" style="<?php if (!empty($videoRow) && $videoRow->popup_type == "native"): ?>  <?php else: ?> display:none; <?php endif; ?>">
                        <th><label>Video</label></th>
                        <td>
                            <div id="videoUrl"><?php echo esc_url($videoRow->popup_link); ?></div>
                            <input id="video-url" type="hidden" name="video" />
                            <input id="upload-video" type="button" class="button" value="Upload video" />
                        </td>
                    </tr>
                    <tr class="popup_link" style="<?php if (!empty($videoRow) && $videoRow->popup_type == "youtube" || !empty($videoRow) && $videoRow->popup_type == "vimeo"): ?> <?php else: ?> display:none; <?php endif; ?>">
                        <th><label>Video Link</label></th>
                        <td><input type="text" name="popupLink" value="<?php echo esc_url($videoRow->popup_link); ?>"/></td>
                    </tr>
                    <tr>
                        <th><label>Auto Play</label></th>
                        <td>
                            <p><input type="checkbox" value="1" name="autoplay" <?php if ($videoRow->popup_autoplay == 1): ?> checked="checked" <?php endif; ?>/>&nbsp;
                            Auto Play video in popup window</p>
                        </td>
                    </tr>
                    <tr>
                        <th>Background Type</th>
                        <td>
                            <label><input type="radio" name="backgroundtype" value="solid" class="backgroundtype" <?php if (!empty($videoRow) && $videoRow->popup_background == "solid"): ?> checked="checked" <?php endif; ?>/>Solid color</label>
                            <label><input type="radio" name="backgroundtype" value="horizontal" class="backgroundtype" <?php if (!empty($videoRow) && $videoRow->popup_background == "horizontal"): ?> checked="checked" <?php endif; ?>/>Horizontal gradient</label>
                            <label><input type="radio" name="backgroundtype" value="vertical" class="backgroundtype" <?php if (!empty($videoRow) && $videoRow->popup_background == "vertical"): ?> checked="checked" <?php endif; ?> />Vertical gradient</label>
                            <label><input type="radio" name="backgroundtype" value="picture" class="backgroundtype" <?php if (!empty($videoRow) && $videoRow->popup_background == "picture"): ?> checked="checked" <?php endif; ?>/>Picture</label>
                        </td>
                    </tr>
                    <tr style="<?php if (!empty($videoRow) && $videoRow->popup_background == "solid" || !empty($videoRow) && $videoRow->popup_background == "horizontal" || !empty($videoRow) && $videoRow->popup_background == "vertical"): ?> <?php else: ?> display:none; <?php endif; ?>" id="colorOptions">
                        <th><label>Color</label></th>
                        <td>
                            <input type="text" name="solidColor" class="solidcolor" style="<?php if (!empty($videoRow) && $videoRow->popup_background == "solid" || !empty($videoRow) && $videoRow->popup_background == "horizontal" || !empty($videoRow) && $videoRow->popup_background == "vertical"): ?> display:block; <?php else: ?>  display:none; <?php endif; ?>" placeholder="Color" value="<?php
                    if (!empty($videoRow)) {
                        echo $videoRow->color1;
                    }
                    ?>"/><br>
                            <br>
                            <input type="text" name="grantColor" class="grantColor" style="<?php if (!empty($videoRow) && $videoRow->popup_background == "horizontal" || !empty($videoRow) && $videoRow->popup_background == "vertical"): ?> display:block; <?php else: ?> display:none; <?php endif; ?>" placeholder="2nd color" value="<?php
                    if (!empty($videoRow)) {
                        echo $videoRow->color2;
                    }
                    ?>"/>
                        </td>
                    </tr>
                    <tr id="picture" style="<?php if (!empty($videoRow) && $videoRow->popup_background == "picture"): ?>  <?php else: ?> display:none; <?php endif; ?>">
                        <th><label>Picture</label></th>
                        <td>
                            <div id="imageUrl"><?php echo esc_url($videoRow->picture_link); ?></div>
                            <input id="image-url" type="hidden" name="image" value="<?php echo esc_url($videoRow->picture_link); ?>"/>
                            <input id="upload-button" type="button" class="button" value="Upload Image" />
                        </td>
                    </tr>
                    <tr>
                        <th><label>Video Size</label></th>
                        <td>
                            <select name="videoSize" required="required">
                                <option value="">Please Select...</option>
                                <option value="small" <?php if (!empty($videoRow) && $videoRow->popup_size == "small"): ?> selected="selected" <?php endif; ?>>Small- 180x135</option>
                                <option value="medium" <?php if (!empty($videoRow) && $videoRow->popup_size == "medium"): ?> selected="selected" <?php endif; ?>>Medium- 360x270</option>
                                <option value="large" <?php if (!empty($videoRow) && $videoRow->popup_size == "large"): ?> selected="selected" <?php endif; ?>>Large- 540x405</option>
                                <option value="xxl" <?php if (!empty($videoRow) && $videoRow->popup_size == "xxl"): ?> selected="selected" <?php endif; ?>>XXL - 720x540</option>
                            </select>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes" />
                </p>	
            </form>
        </div>	
        <script>
            jQuery(function($){
                $(".popupType").click(function(){
                    if($(this).val()=="youtube" || $(this).val()=="vimeo"){
                        $(".nativeVideo").hide("slide");
                        $(".popup_link").show("slide");
                    }else{
                        $(".popup_link").hide("slide");
                    }
                    if($(this).val()=="native"){
                        $(".popup_link").hide("slide");
                        $(".nativeVideo").show("slide");
                    }else{
                        $(".nativeVideo").hide("slide");
                    }
                });
                $(".backgroundtype").click(function(){
                				
                    if($(this).val()=="solid"){
                        $("#colorOptions").show();
                        $(".solidcolor").show();
                        $(".grantColor").hide();
                        $("#picture").hide();
                    }
                    if($(this).val()=="horizontal" || $(this).val()=="vertical"){
                        $("#colorOptions").show();
                        $(".solidcolor").show();
                        $(".grantColor").show();
                        $("#picture").hide();
                    }
                				
                    if(!($(this).val()=="horizontal" || $(this).val()=="vertical" || $(this).val()=="solid")){
                        $("#colorOptions").hide();
                        $(".solidcolor").hide();
                        $(".grantColor").hide();
                        $("#picture").show();
                    }
                });
                			
                var mediaUploader;

                $("#upload-button").click(function(e) {
                    e.preventDefault();
                    // If the uploader object has already been created, reopen the dialog
                    if (mediaUploader) {
                        mediaUploader.open();
                        return;
                    }
                    // Extend the wp.media object
                    mediaUploader = wp.media.frames.file_frame = wp.media({
                        title: "Choose Image",
                        library: {type: "image"},
                        button: {
                            text: "Choose Image"
                        }, multiple: false });

                    // When a file is selected, grab the URL and set it as the text field"s value
                    mediaUploader.on("select", function() {
                        attachment = mediaUploader.state().get("selection").first().toJSON();
                        console.log(attachment);
                        $("#image-url").val(attachment.url);
                        $("#imageUrl").html(attachment.url);
                    });
                    // Open the uploader dialog
                    mediaUploader.open();
                });
                  
                var mediavUploader;

                $("#upload-video").click(function(e) {
                    e.preventDefault();
                    // If the uploader object has already been created, reopen the dialog
                    if (mediavUploader) {
                        mediavUploader.open();
                        return;
                    }
                    // Extend the wp.media object
                    mediavUploader = wp.media.frames.file_frame = wp.media({
                        title: "Choose Video",
                        library: {type: "video"},
                        button: {
                            text: "Choose Video"
                        }, multiple: false });
                        
                    // When a file is selected, grab the URL and set it as the text field"s value
                    mediavUploader.on("select", function() {
                        attachment = mediavUploader.state().get("selection").first().toJSON();
                        console.log(attachment);
                        $("#video-url").val(attachment.url);
                        $("#videoUrl").html(attachment.url);
                    });
                    // Open the uploader dialog
                    mediavUploader.open();
                });
                    
            })
        </script><?php
    }

    /**
     * Getting Data from database from videos table
     * @return $table Returning List of all video in table format like default style of wordpress
     */
    public function mivideopopup_listVideos() {
        global $wpdb;
		
		if(isset($_GET['delete'])):
			$wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."videos WHERE `id` = %d",intval($_GET['delete'])));
		endif;
		$videoRows = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "videos` ORDER BY `id` DESC");        
		$total_pages = count($videoRows);
		$limit = 10;                                //how many items to show per page
		$page = $_GET['paged'];
		if($page): 
			$start = ($page - 1) * $limit;          //first item to display on this page
		else:
			$start = 0;
		endif;
		$videoRows = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "videos` ORDER BY `id` DESC LIMIT $start,$limit");	
		if ($page == 0) $page = 1;                  //if no page var is given, default to 1.
		$prev = $page - 1;                          //previous page is page - 1
		$next = $page + 1;                          //next page is page + 1
		$lastpage = ceil($total_pages/$limit);      //lastpage is = total pages / items per page, rounded up.
		$lpm1 = $lastpage - 1;                      //last page minus 1

		$pagination = "";
		if($lastpage > 1)
		{   
			
			//previous button
			if ($page > 1) 
				$pagination.= "<a href=\"".site_url()."/wp-admin/admin.php?page=my-popups&amp;paged=".$prev."\">&lt;&lt;</a>";
			else
				$pagination.= ""; 

			//next button
			if ($page < $lastpage) 
				$pagination.= "<a href=\"".site_url()."/wp-admin/admin.php?page=my-popups&amp;paged=".$next."\">&gt;&gt;</a>";
			else
				$pagination.= "";
				   
		}
	
	
		 if(!empty($videoRows)):
			
        $table = "<div class='wrap'>
				<h1>My Popups <a href='".admin_url("admin.php?page=video")."' class='page-title-action'>Add Popup</a></h1>
		<table class='wp-list-table widefat fixed striped posts'>
				<thead>
					<tr>
						<td>Title</td>
						<td>Popup Type</td>
						<td>Popup Link</td>
						<td>Autoplay</td>
						<td>Date</td>
					</tr>
				</thead>
		";
		$i=1;
        foreach ($videoRows as $videoItem):

            $table .="<tr>
						<td>
							<strong>" . esc_html($videoItem->popup_title) . "</strong>
							<div class='row-actions'>
								<span class='edit'><a href='" . admin_url('admin.php?page=video&edit=' . $videoItem->id) . "'>Edit</a></span>
								<span class='delete'><a href='" . admin_url("?page=my-popups".(isset($_GET['paged']) ? "&paged=".$_GET['paged'] : "" )."&delete=" . $videoItem->id) . "'>Delete</a></span>
							</div>
						</td>
						<td>
							" . esc_html($videoItem->popup_type) . "
						</td>
						<td>
							" . $videoItem->popup_link . "
						</td>
						<td>
							" . ($videoItem->popup_autoplay == 1 ? "Yes" : "No") . "
						</td>
						<td>
							" . date("Y/m/d", $videoItem->creation_date) . "
						</td>
					</tr>";
		$i++;			
        endforeach;
        $table .= "
			<tfoot>
				<tr>
					<td>Title</td>
					<td>Popup Type</td>
					<td>Popup Link</td>
					<td>Autoplay</td>
					<td>Date</td>
				</tr>
			</tfoot>
		</table>
		<div class='tablenav bottom'>
			<div class='tablenav-pages'>
				<span class='pagination-links'>".$pagination."</span>
			</div>
		</div>	
		</div>";
        return $table;
		 else:
			return "<div class='wrap'>
				<h1>My Popups <a href='".admin_url("admin.php?page=video")."' class='page-title-action'>Add Popup</a></h1>
				<br/>No popup create yet</div>";
		 endif;
		
    }

    /**
     * 	Adding Meta  box into page 
     */
    function mivideopop_add_meta_box() {
        $screens = array('page');
        foreach ($screens as $screen):
            add_meta_box('mivideopop_sectionid', __('Add Popup', 'mivideopop_textdomain'), array('Mivideopopup', 'mivideopop_meta_box_callback'), $screen, 'side', 'default');
        endforeach;
    }

    /**
     * Prints the box content.
     * 
     * @param WP_Page $page The object for the current page.
     */
    function mivideopop_meta_box_callback($post) {
        global $wpdb;
        /*
         * Use get_post_meta() to retrieve an existing value
         * from the database and use the value for the form.
         */
        $value = get_post_meta($post->ID, 'popup_field', true);

        echo "<p><strong>Select Popup</strong></p>";
        $videoRows = $wpdb->get_results("SELECT * FROM `" . $wpdb->prefix . "videos`");
        $options = "";
        foreach ($videoRows as $videoItem):
            if ($value == $videoItem->id):
                $select = "selected='selected'";
            else:
                $select = "";
            endif;
			$options .="<option value='" . $videoItem->id . "'  " . $select . ">" . $videoItem->popup_title . "</option>";
        endforeach;
        echo '<select name="popupField"><option value="">Please Select...</option>' . $options . '</select>';
    }

    /**
     * When the post is saved, saves our custom data.
     *
     * @param int $post_id The ID of the post being saved.
     */
    function mivideopop_save_meta_box_data($post_id) {

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        // Check the user's permissions.
        if (isset($_POST['post_type']) && 'page' == $_POST['post_type']) {
            if (!current_user_can('edit_page', $post_id)) {
                return;
            }
        } else {
            if (!current_user_can('edit_post', $post_id)) {
                return;
            }
        }
        /* OK, it's safe for us to save the data now. */
        // Make sure that it is set.
        if (!isset($_POST['popupField'])) {
            return;
        }
        // Sanitize user input.
        $my_data = sanitize_text_field($_POST['popupField']);
        // Update the meta field in the database.
        update_post_meta($post_id, 'popup_field', $_POST['popupField']);
    }

    public function mivideopopup_play_popup($post_ID) {
		
		global $post, $wpdb;
		if(!is_admin() && is_page()):
				if( !session_id()):
					session_start();
				endif;	
				wp_enqueue_script('jquery.colorbox', mivideopop::plugin_url() . '/assets/js/jquery.colorbox.js', array(), '', true);
				wp_enqueue_style('jquery.colorbox', mivideopop::plugin_url() . '/assets/css/colorbox.css', array());
				wp_enqueue_script('jquery.jplayer.min', mivideopop::plugin_url() . '/assets/js/jquery.jplayer.min.js', array(), '', true);
				wp_enqueue_style('jplayer.pink.flag', mivideopop::plugin_url() . '/assets/css/jplayer.pink.flag.css', array());
				wp_enqueue_script('sss.min', mivideopop::plugin_url() . '/assets/js/sss.min.js', array(), '', true);
				wp_enqueue_style('sss', mivideopop::plugin_url() . '/assets/css/sss.css', array());
				if(isset($_SESSION['postId'])):
					if(in_array($post->ID,$_SESSION['postId'])):
						$activePop = 1;
						else:
						$activePop = 0;
					endif;
				else:
					$activePop = 0;
				endif;	
				if (mivideopop::is_request('frontend') && $post->post_type == "page" && $activePop==0):
				$var = get_post_meta($post->ID, "popup_field");
				if (!empty($var[0])):
					$popuprow = $wpdb->get_row("SELECT * FROM `" . $wpdb->prefix . "videos` WHERE `id`=" . $var[0]);
					$embed = "";
					if ($popuprow->popup_type == "youtube"):
						$linkUrl = explode("v=", $popuprow->popup_link);
						if ($popuprow->popup_autoplay == 1):
							$autoPlay = "?autoplay=1";
						else:
							$autoPlay = "";
						endif;
						if ($popuprow->popup_size == "small"):
							$embed = '<iframe width="180" height="135" src="'.esc_url('https://www.youtube.com/embed/' . $linkUrl[1] . $autoPlay) . '" frameborder="0" allowfullscreen ></iframe>';
						endif;
						if ($popuprow->popup_size == "medium"):
							$embed = '<iframe width="360" height="270" src="'.esc_url('https://www.youtube.com/embed/' . $linkUrl[1] . $autoPlay). '" frameborder="0" allowfullscreen ></iframe>';
						endif;
						if ($popuprow->popup_size == "large"):
							$embed = '<iframe width="540" height="405" src="'.esc_url('https://www.youtube.com/embed/' . $linkUrl[1] . $autoPlay).'" frameborder="0" allowfullscreen ></iframe>';
						endif;
						if ($popuprow->popup_size == "xxl"):
							$embed = '<iframe width="720" height="540" src="'.esc_url('https://www.youtube.com/embed/' . $linkUrl[1] . $autoPlay).'" frameborder="0" allowfullscreen ></iframe>';
						endif;
					elseif ($popuprow->popup_type == "vimeo"):
						$videoLink = explode('vimeo.com', $popuprow->popup_link);
						if ($popuprow->popup_autoplay == 1):
							$autoPlay = "?autoplay=1";
						else:
							$autoPlay = "";
						endif;
						if ($popuprow->popup_size=="small"):
							$embed = '<iframe width="180" height="135" src="'.esc_url('https://player.vimeo.com/video' . $videoLink[1] . $autoPlay ). '"  frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen ></iframe>';
						endif;
						if ($popuprow->popup_size=="medium"):
							$embed = '<iframe width="360" height="270" src="'.esc_url('https://player.vimeo.com/video' . $videoLink[1] . $autoPlay ).'"  frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen ></iframe>';
						endif;
						if ($popuprow->popup_size=="large"):
							$embed = '<iframe width="540" height="405" src="'.esc_url('https://player.vimeo.com/video' . $videoLink[1] . $autoPlay ).'"  frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen ></iframe>';
						endif;
						if ($popuprow->popup_size=="xxl"):
							$embed = '<iframe width="720" height="540" src="'.esc_url('https://player.vimeo.com/video' . $videoLink[1] . $autoPlay ).'"  frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen ></iframe>';
						endif;
					elseif ($popuprow->popup_type=="native"):
						if ($popuprow->popup_size=="small"):
							$embed = '<video width="180" height="135" controls="controls" id="video_player">
									<source src="' . esc_url($popuprow->popup_link) . '" type="video/mp4">
									Your browser does not support the video tag.
								</video>';
						endif;
						if($popuprow->popup_size=="medium"):
							$embed = '<video width="360" height="270" controls="controls" id="video_player">
									<source src="' . esc_url($popuprow->popup_link) . '" type="video/mp4">
									Your browser does not support the video tag.
								</video>';
						endif;
						if($popuprow->popup_size=="large"):
							$embed = '<video width="540" height="405" controls="controls" id="video_player">
									<source src="' . esc_url($popuprow->popup_link) . '" type="video/mp4">
									Your browser does not support the video tag.
								</video>';
						endif;
						if ($popuprow->popup_size=="xxl"):
							$embed = '<video width="720" height="540" controls="controls" id="video_player">
									<source src="' . esc_url($popuprow->popup_link) . '" type="video/mp4">
									Your browser does not support the video tag.
								</video>';
						endif;
					else:
						$embed = "Video not found";
					endif;			
				?>
					<script>
				jQuery(document).ready(function($){
					$.colorbox({inline:true, width:"50%",href:"#mypopwrap"});
				});
				</script>

				<?php
					if ($popuprow->popup_autoplay == "1" && $popuprow->popup_type == "native"):
				?>
				<script type="text/javascript">
					jQuery(document).ready(function($) {
						$("#video_player")[0].play();
					});
				</script>
				<?php endif; ?> 
				<?php
					$background = "";
					if ($popuprow->popup_background == "solid") {
						?>
						<style>
							#cboxMiddleLeft,#cboxMiddleRight,#cboxContent,#cboxTopLeft,#cboxTopCenter,#cboxTopRight,#cboxBottomLeft,#cboxBottomCenter,#cboxBottomRight{background:<?php echo $popuprow->color1; ?>;}
						</style>
						<?php
					}
					if ($popuprow->popup_background == "picture") {
						?>  
						<style>
							#cboxMiddleLeft,#cboxMiddleRight,#cboxContent,#cboxTopLeft,#cboxTopCenter,#cboxTopRight,#cboxBottomLeft,#cboxBottomCenter,#cboxBottomRight{background: url("<?php echo $popuprow->picture_link; ?>") repeat 0 0; background-size: 150px;}
						</style>
						<?php
					}
					if ($popuprow->popup_background == "horizontal") {
						?>
						<style>
							#cboxMiddleLeft,#cboxMiddleRight,#cboxContent,#cboxTopLeft,#cboxTopCenter,#cboxTopRight,#cboxBottomLeft,#cboxBottomCenter,#cboxBottomRight{
								background: <?php echo $popuprow->color1; ?>;
								background: -moz-linear-gradient(left,  <?php echo $popuprow->color1; ?> 0%, <?php echo $popuprow->color2; ?> 100%);
								background: -webkit-linear-gradient(left,  <?php echo $popuprow->color1; ?> 0%,<?php echo $popuprow->color2; ?> 100%);
								background: linear-gradient(to right,  <?php echo $popuprow->color1; ?> 0%,<?php echo $popuprow->color2; ?> 100%);
								filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?php echo $popuprow->color1; ?>', endColorstr='<?php echo $popuprow->color2; ?>',GradientType=1 );  
							}
						</style>   
						<?php
					}
					if ($popuprow->popup_background == "vertical") {
						?>
						<style>
							#cboxMiddleLeft,#cboxMiddleRight,#cboxContent,#cboxTopLeft,#cboxTopCenter,#cboxTopRight,#cboxBottomLeft,#cboxBottomCenter,#cboxBottomRight{
								background: <?php echo $popuprow->color1; ?>;
								background: -moz-linear-gradient(top,  <?php echo $popuprow->color1; ?> 0%, <?php echo $popuprow->color2; ?> 100%);
								background: -webkit-linear-gradient(top,  <?php echo $popuprow->color1; ?> 0%,<?php echo $popuprow->color2; ?> 100%);
								background: linear-gradient(to bottom,  <?php echo $popuprow->color1; ?> 0%,<?php echo $popuprow->color2; ?> 100%);
								filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='<?php echo $popuprow->color1; ?>', endColorstr='<?php echo $popuprow->color2; ?>',GradientType=0 );
							}
						</style>
									<?php
								}
								if(!isset($_SESSION['postId'])):
									$_SESSION['postId'] = array($post->ID);
									else:
									$newArr = array_unique(array_merge(array($post->ID),$_SESSION['postId']));
									$_SESSION['postId'] = $newArr;
								endif; 
								?>
								
				<div style='display:none'>
				<div id='mypopwrap'>
					<?php echo $embed; ?>
				</div>
				</div>
				<script type="text/javascript">
					jQuery("document").ready(function($){
						$("#cboxClose").click(function(){
							location.reload();
						});
					});
				</script>
				<?php 
				endif;

				endif;
		endif;
        return $post_ID;
    }

    public function mivideopopup_popup_javascript() {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($){
                $("#popupType").change(function(){
                    if($(this).val()!=""){
                        var data = {
                            'action': 'my_action',
                            'selecType': $(this).val()
                        };

                        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                        jQuery.post(ajaxurl, data, function(response) {
                            $("#postId").attr("required", "true");
                            $("#postId").show();
                            $("#postId").html(response);
                        });                             
                    }else{
                        $("#postId").attr("required", "false");
                        $("#postId").hide(); 
                    }
                });
            });
        </script>
        <?php
    }
}

$DEMenu = new Mivideopopup();
?>
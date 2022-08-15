<?php
/*
  Plugin Name: Quiz Making
  Plugin URI: https://oefentheorieexamens.nl/
  Description: Quiz Making Plugin for Oefentheorieexamens
  Version: 1.0
  Author: Wordpress Development Team
  Author URI: https://oefentheorieexamens.nl/
 */

//for custom postype
function my_custom_quiz() {
    $labels = array(
        'name' => _x('quiz', 'post type general name'),
        'singular_name' => _x('quiz', 'post type singular name'),
        'add_new' => _x('Add New', 'quiz'),
        'add_new_item' => __('Add New quiz'),
        'edit_item' => __('Edit quiz'),
        'new_item' => __('New quiz'),
        'all_items' => __('All quiz'),
        'view_item' => __('View quiz'),
        'search_items' => __('Search quiz'),
        'not_found' => __('No quiz found'),
        'not_found_in_trash' => __('No quiz found in the Trash'),
        'parent_item_colon' => '',
        'menu_name' => 'Custom Quiz'
    );

    $args = array(
        'labels' => $labels,
        'description' => 'quiz',
        'public' => false,
        'show_ui' => true,
        'capability_type' => 'post',
        'menu_position' => 5,
        'supports' => array('title', 'thumbnail', 'editor', 'page-attributes', 'author', 'revisions', 'post-formats',),
        'has_archive' => false,
        'menu_icon' => 'dashicons-admin-tools'
    );

    register_post_type('quiz', $args);
}

add_action('init', 'my_custom_quiz');

// Act on plugin activation
register_activation_hook(__FILE__, "activate_myplugin");

// Act on plugin de-activation
register_deactivation_hook(__FILE__, "deactivate_myplugin");

// Activate Plugin
function activate_myplugin() {

    // Execute tasks on Plugin activation
    // Insert DB Tables
    init_db_myplugin();
}

// De-activate Plugin
function deactivate_myplugin() {

    // Execute tasks on Plugin de-activation
}

// Initialize DB Tables
function init_db_myplugin() {

    // Code to create DB Tables
    // WP Globals
    global $table_prefix, $wpdb;
    // Customer Table
    $quizTable = $table_prefix . 'custom_quiz_data';
    $charset_collate = $wpdb->get_charset_collate();

    // Create Customer Table if not exist
    if ($wpdb->get_var("show tables like '$quizTable'") != $quizTable) {
        // Query - Create Table
        $sql = "CREATE TABLE `$quizTable` (";
        $sql .= " `id` int(11) NOT NULL auto_increment, ";
        $sql .= " `user_id` varchar(500) NOT NULL, ";
        $sql .= " `quiz_data` longtext NOT NULL, ";
        $sql .= " `post_modified` datetime DEFAULT '0000-00-00 00:00:00' NOT NULL, ";
        $sql .= " PRIMARY KEY (id) ";
        $sql .= ") $charset_collate";
        // Include Upgrade Script
        require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
        // Create Table
        dbDelta($sql);
    }
}

//for Quiz Category
function tr_create_my_taxonomy() {
    register_taxonomy("quiz-categories", array("quiz"), array("hierarchical" => true, "label" => "Quiz Categories", 'show_admin_column' => true,
        "singular_label" => "Category",));
    register_taxonomy("question-categories", array("quiz"), array("hierarchical" => true, "label" => "Question Categories", 'show_admin_column' => false, 'show_in_quick_edit' => false, 'meta_box_cb' => false,
        "singular_label" => "Category",));
    $labels = array(
        'name' => _x('Quiz Tags', 'taxonomy general name'),
        'singular_name' => _x('Quiz Tag', 'taxonomy singular name'),
        'search_items' => __('Search Quiz Tags'),
        'popular_items' => __('Popular Quiz Tags'),
        'all_items' => __('All Quiz Tags'),
        'parent_item' => null,
        'parent_item_colon' => null,
        'edit_item' => __('Edit Tag'),
        'update_item' => __('Update Tag'),
        'add_new_item' => __('Add New Tag'),
        'new_item_name' => __('New Tag Name'),
        'separate_items_with_commas' => __('Separate tags with commas'),
        'add_or_remove_items' => __('Add or remove tags'),
        'choose_from_most_used' => __('Choose from the most used tags'),
        'menu_name' => __('Quiz Tags'),
    );
    register_taxonomy('quiz-tags', array("quiz"), array(
        'hierarchical' => false,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var' => true,
        'rewrite' => array('slug' => 'quiz-tags'),
    ));
}

add_action('init', 'tr_create_my_taxonomy');

function ask_admin_repeater_script() {
    $plugin_url = plugin_dir_url(__FILE__);
    wp_enqueue_media();
    wp_enqueue_script('jquery-ui', plugin_dir_url(__FILE__) . '/js/jquery-ui.js', array('jquery'), '', true);
    wp_enqueue_script('jquery.pep.min', plugin_dir_url(__FILE__) . '/js/jquery.pep.js', array('jquery'), '', true);
    wp_enqueue_script('drag-drop', plugin_dir_url(__FILE__) . '/js/drag-drop.js', array('jquery'), '', true);
    wp_enqueue_script('ask-admin-repeater-js', plugin_dir_url(__FILE__) . '/js/repeatable-fields.js', array('jquery'), '', true);
    wp_localize_script('ask-admin-repeater-js', 'askAdmin', array(
        'title' => __("Choose an image", "ask"),
        'btn_txt' => __("Use image", "ask"),
    ));
    wp_enqueue_style('custom-admin-style', $plugin_url . 'css/admin-style.css');
}

add_action('admin_enqueue_scripts', 'ask_admin_repeater_script');

function wpse_load_plugin_css() {
    $plugin_url = plugin_dir_url(__FILE__);
    wp_enqueue_script('jquery-ui', plugin_dir_url(__FILE__) . '/js/jquery-ui.js', array('jquery'), '', true);
    wp_enqueue_script('jquery.pep.min', plugin_dir_url(__FILE__) . '/js/jquery.pep.js', array('jquery'), '', true);
    wp_enqueue_script('modernizr.min', plugin_dir_url(__FILE__) . '/js/modernizr.min.js', array('jquery'), '', true);
    wp_enqueue_style('font-awesome-css', $plugin_url . 'css/all.css');
    wp_enqueue_style('custom-style', $plugin_url . 'css/style.css');
}

add_action('wp_enqueue_scripts', 'wpse_load_plugin_css');

// Add Meta Box to post
add_action('admin_init', 'nested_repeter_callback', 2);

function nested_repeter_callback() {
    add_meta_box('nested-repeter-data', 'Quiz Questions', 'nested_repeter_meta_box_callback', 'quiz', 'normal', 'default');
}

function nested_repeter_meta_box_callback($post) {
    global $post;
    $change_logs = get_post_meta($post->ID, 'nested_repeter_group', true);
    wp_nonce_field('nestedRepeaterLog', 'formType');
    ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.repeater/1.2.1/jquery.repeater.min.js"></script>
    <style>
        #nested_repeter .inner-outer-repeater{
            display:block;
            width:100%;
            border:1px solid grey;
        }
        #nested_repeter .inner-outer-repeater .inner-outer-wrapper,#nested_repeter .inner-outer-repeater .answers-desc,#nested_repeter .inner-outer-repeater .question_category{
            display:flex;
            margin:0 auto;
            border-bottom: 1px solid grey;
            padding: 10px;
        }
        #nested_repeter .inner-outer-repeater .data-repeater-delete{
            display:flex;
            margin:0 auto;
            padding: 10px;
        }
    </style>
    <div id="nested_repeter">
        <div class="wc-repeater">
            <div data-repeater-list="change-log" class="inner_td">
                <h1>Create Your Questions</h1>
                <?php
                if (!empty($change_logs)) {
                    $cnt = 0;
                    ?>
                    <?php
                    foreach ($change_logs as $change_log) {
                        $cnt++;
                        ?>
                        <h3 style="border: 1px solid #2271b1;border-radius: 50%;margin: 10px 95% 10px 8px;text-align: center;color: white;background: #2271b1;"><?php echo $cnt; ?></h3>
                        <div data-repeater-item>
                            <div class="inner-outer-repeater set-spacing">
                                <div class="middle-section-repeter">
                                    <div class="inner-outer-wrapper">
                                        <div class="flex-wrapper-question">
                                            <div class="textarea">
                                                <label>Add Questions : </label>
                                                <textarea name="Questions" 
                                                          value="" placeholder="Add Questions"><?php echo $change_log['Questions']; ?></textarea>
                                            </div>
                                            <div class="question_category">
                                                <div class="question-set">
                                                    <label>Question Timer : </label>
                                                    <input type="text" name="question_timer" placeholder="Question Timer" value="<?php echo $change_log['question_timer']; ?>"/>
                                                </div>
                                                <div class="question-set margin-top-10">
                                                    <label for="choose_category">Choose Question Category</label>
                                                    <select name="question_category" id="choose_category">
                                                        <?php
                                                        $tax_terms = get_terms('question-categories', array('hide_empty' => '0',));

                                                        foreach ($tax_terms as $tax_term):
                                                            $select = ($change_log['question_category'] == $tax_term->term_id ) ? "Selected" : "";
                                                            echo '<option value="' . $tax_term->term_id . '" ' . $select . '>' . $tax_term->name . '</option>';
                                                        endforeach;
                                                        ?>
                                                    </select> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex-set-main">
                                            <div class="img">
                                                <?php $checked = ($change_log['dragable_option'][0] == 'dragable_option' ? true : false);
                                                ?>
                                                <div class="upload-wrapper <?php echo ($checked ? 'flex-set-main imageAnswerContainer' : ''); ?>">
                                                    <label>Upload Image : </label>
                                                    <?php
                                                    if ($change_log['logo']) {
                                                        $attachment_url = wp_get_attachment_image_src($change_log['logo'], 'full');
                                                        ?>
                                                        <div class="image_section <?php echo ($checked ? 'js-openPopup' : ''); ?>" <?php echo ($checked ? 'data-popup="#img_' . $change_log['logo'] . '"' : ''); ?>>
                                                            <img src="<?php echo $attachment_url[0]; ?>" width="150px" height="150px" />
                                                        </div>
                                                        <?php if ($checked) { ?>
                                                            <div class="popup hotspotPopup" id="img_<?php echo $change_log['logo']; ?>">
                                                                <button class="closeButton js-popupClose">
                                                                    <svg class="icon-cross"><use xlink:href="/svg/icons.svg#icon-cross"></use></svg>
                                                                </button>
                                                                <div class="window flex-set-main">

                                                                    <div class="hotspotImage" style="background-image:url(<?php echo $attachment_url[0]; ?>);width: 990px;height: 400px;background-size: CONTAIN;">
                                                                        <div class="hotspotAnswer Image">
                                                                            <div class="dropArea"></div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="dragables js-multiple">
                                                                        <?php foreach ($change_log['notes'] as $note) { ?>
                                                                            <div class="dragContainer">
                                                                                <div class="dragable" data-option="<?php echo $note['answer_option']; ?>" style=""><?php echo $note['answer_option']; ?></div>
                                                                                <div class="dragableSpot"><?php echo $note['answer_option']; ?></div>
                                                                            </div>
                                                                        <?php } ?>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                            <?php
                                                        }
                                                    }
                                                    ?>	
                                                    <button type="button" class="ask-upload_image_button button" style="display:<?php echo (!empty($change_log['logo']) ) ? 'none' : 'block'; ?>"><?php _e('Add image', 'woocommerce'); ?></button>
                                                    <input type="hidden" class="ask-logo" name="logo" value="<?php if ($change_log['logo'] != '') echo esc_attr($change_log['logo']); ?>" />
                                                    <button type="button" class="ask-remove_image_button button" style="display:<?php echo ( empty($change_log['logo']) ) ? 'none' : 'block'; ?>"><?php _e('Remove image', 'ask'); ?></button>
                                                </div>    
                                            </div>
                                            <div class="checkbox-main">
                                                <div class='checkbox-wrap'>
                                                    <label>Option Draggable? </label>
                                                    <?php $checked = ($change_log['dragable_option'][0] == 'dragable_option' ? "checked=checked" : ""); ?>
                                                    <input type="checkbox" name="dragable_option" class="custom-check-drag" <?php echo $checked; ?> value='dragable_option'>
                                                    <p>Add Option Text without space</p>
                                                </div>
                                                <div class='checkbox-wrap'>
                                                    <label>Option Images? </label>
                                                    <?php $checked = ($change_log['image_option'][0] == 'image_option' ? "checked=checked" : ""); ?>
                                                    <input type="checkbox" name="image_option" class="custom-check-img" <?php echo $checked; ?> value='image_option'>
                                                </div>
                                            </div>
                                            <!-- innner repeater -->
                                            <div class="inner-repeater">
                                                <label>Question's Option : </label>

                                                <div data-repeater-list="notes" class="inner_tr">
                                                    <?php if (!empty($change_log['inner-list'])) { ?>

                                                        <?php foreach ($change_log['inner-list'] as $note) { ?>
                                                            <div data-repeater-item>
                                                                <div class="img option-img" style='display:none;'>
                                                                    <div class="upload-wrapper">
                                                                        <label>Upload Image : </label>
                                                                        <?php if ($note['img_option']) { ?>
                                                                            <div class="image_section">
                                                                                <img src="<?php echo esc_url($image = wp_get_attachment_thumb_url($note['img_option'])); ?>" width="150px" height="150px" />
                                                                            </div>
                                                                        <?php } ?>	
                                                                        <button type="button" class="ask-upload_image_button button" style="display:<?php echo (!empty($note['img_option']) ) ? 'none' : 'block'; ?>"><?php _e('Add image', 'woocommerce'); ?></button>
                                                                        <input type="hidden" class="ask-logo" name="img_option" value="<?php if ($note['img_option'] != '') echo esc_attr($note['img_option']); ?>" />
                                                                        <button type="button" class="ask-remove_image_button button" style="display:<?php echo ( empty($note['img_option']) ) ? 'none' : 'block'; ?>"><?php _e('Remove image', 'ask'); ?></button>
                                                                    </div>    
                                                                </div>
                                                                <input type="text" class="quiz-answer" name="answer_option" value="<?php echo $note['answer_option']; ?>" placeholder="Question's Option" />
                                                                <input data-repeater-delete class="button custom-delete"  type="button" value="-" />
                                                            </div>
                                                        <?php } ?>
                                                        <?php
                                                    } else if (!empty($change_log['notes'])) {
                                                        foreach ($change_log['notes'] as $note) {
                                                            ?>
                                                            <div data-repeater-item class="data-repeater-img">
                                                                <div class="img option-img" style='display:none;'>
                                                                    <div class="upload-wrapper">
                                                                        <label>Upload Image : </label>
                                                                        <?php if ($note['img_option']) { ?>
                                                                            <div class="image_section">
                                                                                <img src="<?php echo esc_url($image = wp_get_attachment_thumb_url($note['img_option'])); ?>" width="150px" height="150px" />
                                                                            </div>
                                                                        <?php } ?>	
                                                                        <button type="button" class="ask-upload_image_button button" style="display:<?php echo (!empty($note['img_option']) ) ? 'none' : 'block'; ?>"><?php _e('Add image', 'woocommerce'); ?></button>
                                                                        <input type="hidden" class="ask-logo" name="img_option" value="<?php if ($note['img_option'] != '') echo esc_attr($note['img_option']); ?>" />
                                                                        <button type="button" class="ask-remove_image_button button" style="display:<?php echo ( empty($note['img_option']) ) ? 'none' : 'block'; ?>"><?php _e('Remove image', 'ask'); ?></button>
                                                                    </div>    
                                                                </div>
                                                                <input type="text" class="quiz-answer" name="answer_option" value="<?php echo $note['answer_option']; ?>" placeholder="Question's Option" />
                                                                <?php
                                                                $data = $note['answer_' . $note['answer_option']];
                                                                $a = explode('_', $data);
                                                                $top = str_replace('t-', '', $a[0]);
                                                                $left = str_replace('l-', '', $a[1]);
                                                                ?>
                                                                <input name="answer_<?php echo $note['answer_option']; ?>" data-left="<?php echo $left; ?>" data-top="<?php echo $top; ?>" type="hidden" class="target<?php echo $note['answer_option']; ?>" placeholder="Target <?php echo $note['answer_option']; ?>" value="<?php echo $data; ?>">
                                                                <input data-repeater-delete class="button custom-delete"  type="button" value="-" />
                                                            </div>
                                                            <?php
                                                        }
                                                    } else {
                                                        ?>
                                                        <div data-repeater-item class="data-repeater-img">
                                                            <div class="img option-img" style='display:none;'>
                                                                <div class="upload-wrapper">
                                                                    <label>Upload Image : </label>
                                                                    <?php if ($note['img_option']) { ?>
                                                                        <div class="image_section">
                                                                            <img src="<?php echo esc_url($image = wp_get_attachment_thumb_url($note['img_option'])); ?>" width="150px" height="150px" />
                                                                        </div>
                                                                    <?php } ?>	
                                                                    <button type="button" class="ask-upload_image_button button" style="display:<?php echo (!empty($note['img_option']) ) ? 'none' : 'block'; ?>"><?php _e('Add image', 'woocommerce'); ?></button>
                                                                    <input type="hidden" class="ask-logo" name="img_option" value="<?php if ($note['img_option'] != '') echo esc_attr($note['img_option']); ?>" />
                                                                    <button type="button" class="ask-remove_image_button button" style="display:<?php echo ( empty($note['img_option']) ) ? 'none' : 'block'; ?>"><?php _e('Remove image', 'ask'); ?></button>
                                                                </div>    
                                                            </div>

                                                            <input type="text" class="quiz-answer" name="answer_option" value="" placeholder="Question's Option" />
                                                            <input data-repeater-delete class="button custom-delete"  type="button" value="-" />
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                                <div class="data-repeater-create"><input class="button" data-repeater-create type="button" value="+"/></div>
                                            </div>

                                        </div>

                                    </div>
                                    <div class="answers-desc">
                                        <div class="text-label-set">
                                            <label>Suggestion Description : </label>
                                            <textarea name="suggestion_Message" 
                                                      value="" placeholder="Suggestion Description"><?php echo $change_log['suggestion_Message']; ?></textarea>
                                        </div>
                                        <div class="text-label-set flex-checkbox">
                                            <label>Show Suggestion Description in Front?: </label>
                                            <?php $checked = ($change_log['show_suggestion_desc_front'][0] == 'show_suggestion_desc_front' ? "checked=checked" : ""); ?>
                                            <input type="checkbox"  class="custom-check" name="show_suggestion_desc_front" <?php echo $checked; ?> value="show_suggestion_desc_front">
                                        </div>
                                    </div>
                                    <div class="answers-desc">
                                        <div class="text-label-set correct-ans-content">
                                            <label>Correct Answer : </label>
                                            <textarea type="text" name="correct_answer" placeholder="Correct Answer" value="<?php echo $change_log['correct_answer']; ?>"><?php echo $change_log['correct_answer']; ?></textarea>
                                        </div>
                                        <div class="text-label-set">
                                            <label> Result Description : </label>
                                            <textarea name="result_answer_Message" 
                                                      value="" placeholder="To show description at result page"><?php echo $change_log['result_answer_Message']; ?></textarea>
                                        </div>

                                    </div>

                                </div>
                                <div class="remove-vertical-wrapper">

                                    <div class="data-repeater-delete"><input data-repeater-delete class="button"  type="button" value="-" /></div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <div data-repeater-item>
                        <h3>Add New Question</h3>
                        <div class="inner-outer-repeater set-spacing">

                            <div class="middle-section-repeter">
                                <div class="inner-outer-wrapper">
                                    <div class="flex-wrapper-question">
                                        <div class="textarea">
                                            <label>Add Questions : </label>
                                            <textarea name="Questions" 
                                                      value="" placeholder="Add Questions"></textarea>
                                        </div>
                                        <div class="question_category">
                                            <div class="question-set">
                                                <label>Question Timer : </label>
                                                <input type="text" name="question_timer" placeholder="Question Timer" value=""/>
                                            </div>
                                            <div class="question-set margin-top-10">
                                                <label for="choose_category">Choose Question Category</label>
                                                <select name="question_category" id="choose_category">
                                                    <?php
                                                    $tax_terms = get_terms('question-categories', array('hide_empty' => '0',));

                                                    foreach ($tax_terms as $tax_term):
                                                        $select = ($change_log['question_category'] == $tax_term->term_id ) ? "Selected" : "";
                                                        echo '<option value="' . $tax_term->term_id . '" ' . $select . '>' . $tax_term->name . '</option>';
                                                    endforeach;
                                                    ?>
                                                </select> 
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-set-main">
                                        <div class="img">
                                            <div class="upload-wrapper">
                                                <label>Upload Image : </label>	
                                                <button type="button" class="ask-upload_image_button button" ><?php _e('Add image', 'woocommerce'); ?></button>
                                                <input type="hidden" class="ask-logo" name="logo" value="" />
                                                <button type="button" class="ask-remove_image_button button" style="display:none;"><?php _e('Remove image', 'ask'); ?></button>
                                            </div>    
                                        </div>
                                        <div class="checkbox-main">
                                            <div class='checkbox-wrap'>
                                                <label >Option Draggable? </label>
                                                <input type="checkbox" name="dragable_option" class="custom-check-drag" value='dragable_option'>
                                            </div>
                                            <div class='checkbox-wrap'>
                                                <label>Option Images? </label>
                                                <input type="checkbox" name="image_option" class="custom-check-img" value='image_option'>
                                            </div>
                                        </div>

                                        <!-- innner repeater -->
                                        <div class="inner-repeater">
                                            <label>Question's Option : </label>

                                            <div data-repeater-list="inner-list" class="inner_tr">
                                                <div data-repeater-item class="data-repeater-img">
                                                    <div class="img option-img" style='display: none;'>
                                                        <div class="upload-wrapper">
                                                            <label>Upload Image : </label>	
                                                            <button type="button" class="ask-upload_image_button button" ><?php _e('Add image', 'woocommerce'); ?></button>
                                                            <input type="hidden" class="ask-logo" name="img_option" value="" />
                                                            <button type="button" class="ask-remove_image_button button" style="display:none;"><?php _e('Remove image', 'ask'); ?></button>
                                                        </div>    
                                                    </div>  
                                                    <input type="text" class="quiz-answer" name="answer_option" value="" placeholder="Question's Option" />
                                                    <input data-repeater-delete class="button"  type="button" value="-" />
                                                </div>
                                            </div>
                                            <div class="data-repeater-create"><input class="button" data-repeater-create type="button" value="+"/></div>
                                        </div>
                                    </div>  
                                </div>
                                <div class="answers-desc">
                                    <div class="text-label-set">
                                        <label>Suggestion Description : </label>
                                        <textarea name="suggestion_Message" 
                                                  value="" placeholder="Suggestion Description"></textarea>
                                    </div>
                                    <div class="text-label-set flex-checkbox">
                                        <label>Show Suggestion Description in Front?: </label>
                                        <input type="checkbox"  class="custom-check" checked="checked" name="show_suggestion_desc_front" value="show_suggestion_desc_front">
                                    </div>
                                </div>
                                <div class="answers-desc">
                                    <div class="text-label-set correct-ans-content">
                                        <label>Correct Answer : </label>
                                        <textarea type="text" name="correct_answer" placeholder="Correct Answer" value=""></textarea>
                                    </div>
                                    <div class="text-label-set">
                                        <label> Result Description : </label>
                                        <textarea name="result_answer_Message" 
                                                  value="" placeholder="To show description at result page"></textarea>
                                    </div>

                                </div>

                            </div>
                            <div class="remove-vertical-wrapper">

                                <div class="data-repeater-delete"><input data-repeater-delete class="button"  type="button" value="-" /></div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div><input data-repeater-create class="button btn-add-new"  type="button" value="+"/></div>
        </div>
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function () {
            jQuery('.wc-repeater').repeater(
                    {
                        repeaters: [{
                                selector: '.inner-repeater'
                            }]
                    });
            // On Create New Question, New Question Images will clear + Images Option will be only one.
            jQuery(document).on('click', '.btn-add-new', function () {
                jQuery(document).find('.inner-outer-repeater.set-spacing').last().find('.ask-remove_image_button').each(function () {
                    jQuery(this).trigger('click');
                });
                jQuery(document).find('.inner-outer-repeater.set-spacing').last().find('.data-repeater-img').not(':first').remove();
            });

            jQuery(document).find('.ask-remove_image_button').on('click', function () {
                jQuery(this).parent().find('img').remove();
                jQuery(this).parent().find('.ask-upload_image_button').show();
            });

            // Draggable Option Start
            jQuery(document).find('.custom-check-drag').each(function () {
                if (jQuery(this).is(':checked')) {
                    jQuery(this).parents('.inner-outer-repeater').find('.correct-ans-content').hide();
                }
            });

            jQuery(document).on('change', '.custom-check-drag', function () {
                if (jQuery(this).is(':checked')) {
                    jQuery(this).parents('.inner-outer-repeater').find('.correct-ans-content').hide();
                } else {
                    jQuery(this).parents('.inner-outer-repeater').find('.correct-ans-content').show();
                }
            });

            jQuery(document).find('.dragContainer').find('.dragable').each(function () {
                var parent = jQuery(this).parents('.inner-outer-repeater');
                var option = jQuery(this).data('option');
                var top = parent.find('.inner_tr').find('.target' + option).attr('data-top');
                var left = parent.find('.inner_tr').find('.target' + option).attr('data-left');
                jQuery(this).css('top', top);
                jQuery(this).css('left', left);
            });
            // Draggable Option End

            //Multi Image Option Start
            jQuery(document).find('.custom-check-img').each(function () {
                if (jQuery(this).is(':checked')) {
                    jQuery(this).parents('.inner-outer-repeater').find('.option-img').show();
                    jQuery(this).parents('.inner-outer-repeater').find('input.quiz-answer').hide();
                }
            });

            jQuery(document).on('change', '.custom-check-img', function () {
                if (jQuery(this).is(':checked')) {
                    jQuery(this).parents('.inner-outer-repeater').find('.option-img').show();
                    jQuery(this).parents('.inner-outer-repeater').find('input.quiz-answer').hide();
                } else {
                    jQuery(this).parents('.inner-outer-repeater').find('.option-img').hide();
                    jQuery(this).parents('.inner-outer-repeater').find('input.quiz-answer').show();
                }
            });

            jQuery(document).on('click', 'div.data-repeater-create', function () {
                jQuery(this).parents('.set-spacing').find('.data-repeater-img:last').find('.ask-remove_image_button').trigger('click');
                if (jQuery(this).parents('.set-spacing').find('.custom-check-img').is(':checked')) {
                    jQuery(this).parents('.set-spacing').find('.inner_tr').find('.option-img').show();
                    jQuery(this).parents('.set-spacing').find('.inner_tr').find('input.quiz-answer').hide();
                } else {
                    jQuery(this).parents('.set-spacing').find('.inner_tr').find('.option-img').hide();
                    jQuery(this).parents('.set-spacing').find('.inner_tr').find('input.quiz-answer').show();
                }
            });
            //Multi Image Option End 
            //Popup
            jQuery(".js-openPopup").click(function (t) {
                var e, i;
                t.preventDefault(),
                        popup(jQuery(this).attr("data-popup"), jQuery(this).data("hires-url"))
            });

        });
        function popup(t, e) {
            jQuery("body").css("overflow", "hidden"),
                    jQuery(t).addClass("js-show"),
                    setTimeout((function () {
                        jQuery(t).addClass("js-animate")
                    }), 25),
                    jQuery(t).click((function () {
                closePopup();
            })).children().click((function (t) {
                t.stopPropagation();
            }
            )),
                    jQuery(".js-popupClose").click((function (t) {
                t.preventDefault(),
                jQuery(this).hide();
                        closePopup();
            }
            )),
                    jQuery(document).keyup((function (t) {
                27 === t.keyCode && closePopup();
            }));
        }
        function closePopup() {
            jQuery(".popup").removeClass("js-animate"),
                    setTimeout((function () {
                        jQuery("body").css("overflow", "auto"),
                                jQuery(".popup").removeClass("js-show")
                    }), 200);
        }
    </script>
    <?php
}

add_action('save_post', 'nested_repeter_meta_box_save');

function nested_repeter_meta_box_save($post_id) {
    if (!isset($_POST['formType']) && !wp_verify_nonce($_POST['formType'], 'nestedRepeaterLog'))
        return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;
    if (!current_user_can('edit_post', $post_id))
        return;
    update_post_meta($post_id, 'nested_repeter_group', $_POST['change-log']);
}

// Calling start & End page content
function diwp_add_wysiwyg_editor_metabox() {
    add_meta_box(
            'diwp-wysiwyg-editor',
            'Quiz Start & End Page',
            'diwp_custom_html_code_editor',
            'quiz', 'normal', 'default'
    );
}

add_action('add_meta_boxes', 'diwp_add_wysiwyg_editor_metabox');

function diwp_custom_html_code_editor($post) {
    // Quiz start page editor
    $get_start_page = get_post_meta($post->ID, 'quiz_start_page', true);
    wp_nonce_field('nestedRepeaterStartPage', 'formTypeStartPage');
    ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.repeater/1.2.1/jquery.repeater.min.js"></script>
    <style>
        .data-repeater-item{
            width:100%;
            display: flex;
        }
        .inner-outer-repeater{
            width:95%
        }
        .inner-btn{
            width: 5%;
            display: flex;
            margin: 30% 10px;
        }

    </style>
    <div id="nested_repeter_quiz_start_description">
        <?php
        $settings = array(
            'wpautop' => true, // enable auto paragraph?
            'media_buttons' => true, // show media buttons?
            'tabindex' => '',
            'editor_css' => '', //  additional styles for Visual and Text editor,
            'editor_class' => '', // sdditional classes to be added to the editor
            'teeny' => true, // show minimal editor
            'dfw' => false, // replace the default fullscreen with DFW
            'tinymce' => array(
                // Items for the Visual Tab
                'toolbar1' => 'bold,italic,underline,bullist,numlist,link,unlink,forecolor,undo,redo,',
            ),
            'quicktags' => array(
                // Items for the Text Tab
                'buttons' => 'strong,em,underline,ul,ol,li,link,code'
            )
        );
        ?>
        <div class="wc-repeater">
            <div data-repeater-list="change-log-quiz-start-description" class="inner_td">
                <h3>Quiz Start Page Description</h3>
                <?php
                if (!empty($get_start_page)) {
                    $cnt = 0;
                    ?>
                    <?php
                    foreach ($get_start_page as $get_start) {
                        $cnt++;
                        ?>
                        <div data-repeater-item class="data-repeater-item" >

                            <div class="inner-outer-repeater" >
                                <?php
                                wp_editor($get_start['quiz_start_page' . $cnt], 'quiz_start_page' . $cnt, array());
                                ?>
                            </div>
                            <div class="inner-btn">
                                <div class="data-repeater-delete"><input data-repeater-delete class="button"  type="button" value="-" /></div>
                                <div class="data-content-btn" style="display:none;"> <input class="button" data-content="<?= $cnt ?>" type="button" value="+"/></div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    ?>
                    <div data-repeater-item class="data-repeater-item" >

                        <div class="inner-outer-repeater" >
                            <?php
                            wp_editor('', 'quiz_start_page' . $cnt, array());
                            ?>
                        </div>
                        <div class="inner-btn">
                            <!--<div class="data-repeater-delete"><input data-repeater-delete class="button"  type="button" value="-" /></div>-->
                            <div class="data-content-btn" style="display:none;"> <input class="button" data-content="<?= $cnt ?>" type="button" value="+"/></div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>

        </div>  
    </div>
    <script type="text/javascript">
        jQuery(document).ready(function () {
            //This will add new wp editor with blank content
            jQuery(document).on('click', '#nested_repeter_quiz_start_description .button', function () {
                var $this = jQuery(this);
                var cnt = jQuery(this).attr('data-content');
                cnt++;
                var ajax_url = "<?php echo admin_url('admin-ajax.php') ?>";
                jQuery.ajax({
                    url: ajax_url,
                    data: {cnt: cnt, action: 'get_quiz_start_new_content'},
                    type: 'POST',
                    success: function (response) {
                        jQuery('#nested_repeter_quiz_start_description .inner_td').append(response);
                        jQuery('#quiz_start_page' + cnt).attr('name', 'change-log-quiz-start-description[' + (cnt - 1) + '][quiz_start_page' + cnt + ']');
                        jQuery($this).attr('data-content', cnt);
                        if (typeof (tinyMCE) == "object") {
                            tinyMCE.init({
                                selector: '.wp-editor-area'
                            });
                        }
                        jQuery('.data-content-btn').hide();
                        jQuery(document).find('#nested_repeter_quiz_start_description .data-repeater-item:last').find('.data-content-btn').show();
                    }
                });
            });
            jQuery(document).find('#nested_repeter_quiz_start_description .data-repeater-item:last').find('.data-content-btn').show();
        });

    </script>
    <?php
    echo "<br>";
    //Quiz End Page Editor
    $get_end_page = get_post_meta($post->ID, 'quiz_end_page', true);
    echo "<h3>Quiz End Page Description</h3>" . '<br>';
    wp_editor($get_end_page, 'quiz_end_page', array());
}

// Ajax function to fetch quiz start new content in admin
add_action('wp_ajax_get_quiz_start_new_content', 'get_quiz_start_new_content_fun');
add_action('wp_ajax_nopriv_get_quiz_start_new_content', 'get_quiz_start_new_content_fun');

function get_quiz_start_new_content_fun() {
    if (isset($_POST['cnt']) && !empty($_POST['cnt'])) {
        $settings = array(
            'wpautop' => true, // enable auto paragraph?
            'media_buttons' => true, // show media buttons?
            'tabindex' => '',
            'editor_css' => '', //  additional styles for Visual and Text editor,
            'editor_class' => '', // sdditional classes to be added to the editor
            'teeny' => true, // show minimal editor
            'dfw' => false, // replace the default fullscreen with DFW
            'tinymce' => array(
                // Items for the Visual Tab
                'toolbar1' => 'bold,italic,underline,bullist,numlist,link,unlink,forecolor,undo,redo,',
            ),
            'quicktags' => array(
                // Items for the Text Tab
                'buttons' => 'strong,em,underline,ul,ol,li,link,code'
            )
        );
        ?>
        <div data-repeater-item class="data-repeater-item" >
            <div class="inner-outer-repeater" >
                <h4>Add New Content</h4>
                <?php
                wp_editor('', 'quiz_start_page' . $_POST['cnt'], array());
                ?>
            </div>
            <div class="inner-btn">
                <div class="data-repeater-delete"><input data-repeater-delete class="button"  type="button" value="-" /></div>
                <div class="data-content-btn" style="display:none;"> <input class="button" data-content="<?php echo $_POST['cnt']; ?>" type="button" value="+"/></div>
            </div>
        </div>
        <?php
        wp_die();
    }
}

//To save The Start & End page data
function diwp_save_custom_wp_editor_content($post_id) {
    if (!isset($_POST['formTypeStartPage']) && !wp_verify_nonce($_POST['formTypeStartPage'], 'nestedRepeaterStartPage'))
        return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return;
    if (!current_user_can('edit_post', $post_id))
        return;
    update_post_meta($post_id, 'quiz_start_page', $_POST['change-log-quiz-start-description']);

    if (isset($_POST['quiz_end_page'])) {
        update_post_meta($post_id, 'quiz_end_page', $_POST['quiz_end_page']);
    }
}

add_action('save_post', 'diwp_save_custom_wp_editor_content');

//For Quiz Timer
function diwp_add_timer_editor_metabox() {
    add_meta_box(
            'diwp-text-editor',
            'Quiz Timer',
            'diwp_custom_html_text',
            'quiz'
    );
}

add_action('add_meta_boxes', 'diwp_add_timer_editor_metabox');

function diwp_custom_html_text($post) {
    wp_nonce_field(basename(__FILE__), "meta-box-nonce");
    // Quiz start page editor
    $get_start_page = get_post_meta($post->ID, 'quiz_timer', true);
    ?>
    <td>
    <th>
        <label>Quiz Timer</label></th>
    <input name="quiz_timer" type="text" value="<?php echo get_post_meta($post->ID, "quiz_timer", true); ?>">
    </td>
    <?php
}

function diwp_save_custom_wp_timer_content($post_id) {
    if (!isset($_POST["meta-box-nonce"]) || !wp_verify_nonce($_POST["meta-box-nonce"], basename(__FILE__)))
        return $post_id;
    if (!current_user_can("edit_post", $post_id))
        return $post_id;
    if (defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
        return $post_id;
    $meta_box_text_value = "";
    if (isset($_POST["quiz_timer"])) {
        $meta_box_text_value = $_POST["quiz_timer"];
    }
    update_post_meta($post_id, "quiz_timer", $meta_box_text_value);
}

add_action('save_post', 'diwp_save_custom_wp_timer_content');

//For Next Quiz
function diwp_add_next_quiz_metabox() {
    add_meta_box(
            'diwp-text-quiz-editor',
            'Next Quiz',
            'diwp_next_quiz_link',
            'quiz'
    );
}

add_action('add_meta_boxes', 'diwp_add_next_quiz_metabox');

function diwp_next_quiz_link($post) {
    wp_nonce_field(basename(__FILE__), "meta-box-quiz-nonce");
    ?>        
    <label>Next Quiz ID</label>
    <input name="next_quiz" type="text" value="<?php echo get_post_meta($post->ID, "next_quiz", true); ?>">

    <?php
}

function diwp_save_custom_next_quiz($post_id) {
    if (!isset($_POST["meta-box-quiz-nonce"]) || !wp_verify_nonce($_POST["meta-box-quiz-nonce"], basename(__FILE__)))
        return $post_id;
    if (!current_user_can("edit_post", $post_id))
        return $post_id;
    if (defined("DOING_AUTOSAVE") && DOING_AUTOSAVE)
        return $post_id;
    $meta_box_text_value = "";
    if (isset($_POST["next_quiz"])) {
        $meta_box_text_value = $_POST["next_quiz"];
    }
    update_post_meta($post_id, "next_quiz", $meta_box_text_value);
}

add_action('save_post', 'diwp_save_custom_next_quiz');
// For Quiz category Custom Metabox Calling
include('quiz_question_category.php');
// for quiz shortcode calling
include('quiz_view.php');
// <!--For Custom post type column add with shortcode-->
add_filter('manage_quiz_posts_columns', 'posts_columns_id', 5);
add_action('manage_quiz_posts_custom_column', 'posts_custom_id_columns', 5, 2);

function posts_columns_id($defaults) {
    $defaults['wps_post_id'] = __('Quiz Shortcode');
    return $defaults;
}

function posts_custom_id_columns($column_name, $id) {
    if ($column_name === 'wps_post_id') {
        echo "[view_quiz quiz_id=" . $id . "]";
    }
}

function store_result_data_db_result() {
    global $wpdb;
    $quiz_table = $wpdb->prefix . "custom_quiz_data";
    $wpdb->insert($quiz_table, array(
        "user_id" => '1',
        "quiz_data" => json_encode($_POST),
        "post_modified" => date("Y-m-d H:i:s"),
    ));
    $filterQuestion = array();
    ob_start();
    //Multiple Category wise Questions Array
    for ($i = 0; $i < count($_POST['quiz_data']); $i++) {

        for ($j = 0; $j < count($_POST['quiz_data'][$i]); $j++) {

            $filterQuestion[$_POST['quiz_data'][$i][$j]['catID']]['category'] = $_POST['quiz_data'][$i][$j]['category'];
            $filterQuestion[$_POST['quiz_data'][$i][$j]['catID']]['questions'][$j]['question'] = $_POST['quiz_data'][$i][$j]['question'];
            $filterQuestion[$_POST['quiz_data'][$i][$j]['catID']]['questions'][$j]['answer'] = $_POST['quiz_data'][$i][$j]['answer'];
            $filterQuestion[$_POST['quiz_data'][$i][$j]['catID']]['questions'][$j]['correct'] = $_POST['quiz_data'][$i][$j]['correct'];
            $filterQuestion[$_POST['quiz_data'][$i][$j]['catID']]['questions'][$j]['checked'] = $_POST['quiz_data'][$i][$j]['checked'];
            $filterQuestion[$_POST['quiz_data'][$i][$j]['catID']]['questions'][$j]['img'] = $_POST['quiz_data'][$i][$j]['img'];
            $filterQuestion[$_POST['quiz_data'][$i][$j]['catID']]['questions'][$j]['resultAnsDesc'] = $_POST['quiz_data'][$i][$j]['resultAnsDesc'];
            $filterQuestion[$_POST['quiz_data'][$i][$j]['catID']]['questions'][$j]['img_option'] = $_POST['quiz_data'][$i][$j]['img_option'];
            $filterQuestion[$_POST['quiz_data'][$i][$j]['catID']]['questions'][$j]['draggable_option'] = $_POST['quiz_data'][$i][$j]['draggable_option'];

            if ($_POST['quiz_data'][$i][$j]['checked'] == 'true') {
                $filterQuestion[$_POST['quiz_data'][$i][$j]['catID']]['archieved'] += 1;
            }
            $filterQuestion[$_POST['quiz_data'][$i][$j]['catID']]['allCnt'] = count($_POST['quiz_data'][$i]);
            if (($_POST['quiz_data'][$i][$j]['correct'] == $_POST['quiz_data'][$i][$j]['answer']) && $_POST['quiz_data'][$i][$j]['checked'] == 'true') {
                $filterQuestion[$_POST['quiz_data'][$i][$j]['catID']]['countcorrect'] += 1;
            } else {
                $filterQuestion[$_POST['quiz_data'][$i][$j]['catID']]['countwrong'] += 1;
            }
        }
    }
    $counter = 1;

    foreach ($filterQuestion as $key => $filter) {
        $pass_criteria = get_term_meta($key, 'pass_criteria', true);
        if ($pass_criteria <= $filter['countcorrect']) {
            $cat_status = 'Pass';
            $cat_icon = 'fas fa-thumbs-up';
        } else {
            $cat_status = 'Fail';
            $cat_icon = 'fas fa-thumbs-down';
        }
        ?>
        <!--Result Page Html-->
        <div class="main_th_wrappers">
            <h3 class="result-part"><i class="<?php echo $cat_icon; ?>"></i><span><?php echo $filter['category']; ?></span></h3>
            <div class='right_in_theorie_hj'><p>Dit onderdeel bestaat uit <?php echo $filter['allCnt']; ?> vragen waarvan je er minimaal <?php echo get_term_meta($key, 'pass_criteria', true); ?> goed moet beantwoorden.</p></div>
            <div class="result-progress group">
                <div class="progress-bar achieved" style="min-width: 100%;">
                    <?php echo ($filter['archieved'] != '' ? $filter['archieved'] : ""); ?> Behaald / <?php echo ($filter['allCnt'] !== '' ? $filter['allCnt'] : ''); ?> Te behalen
                </div>
                <div class="progress-bar achieved right" style="min-width: 100%;"> 
                    <h4 class="res-head"><?php echo ($filter['countcorrect'] != '' ? "Deze " . $filter['countcorrect'] . " vragen had je goed" : 'Je hebt geen vragen goed'); ?></h4>
                    <button class="button rightAnsBtn <?php echo ($filter['countcorrect'] == '' ? 'not' : ''); ?> more">
                        <span>Toon alle goed vragen</span>
                    </button>

                    <div class="resultsFull passed" style="display:none;">
                        <div class="resultsFull-wrapper">
                            <?php
                            foreach ($filter['questions'] as $key1 => $question) {
                                if (($question['answer'] == $question['correct']) && ($question['checked'] == 'true')) {
                                    ?>
                                    <div class="questions">
                                        <div class="number"><?php echo $key1 + 1; ?></div>
                                        <div class="question-inner-wrap imageAnswerContainer">
                                            <?php
                                            if (!empty($question['img']) && $question['img_option'] == 'true') {
                                                $imgs = explode(',', $question['img']);
                                                ?>
                                                <div class="image js-openPopup" data-popup="#img_<?php echo str_replace(',', '', $question['img']); ?>">
                                                    <?php
                                                    foreach ($imgs as $img) {
                                                        $img_src = wp_get_attachment_image_src($img, 'full')
                                                        ?>
                                                        <img src="<?php echo $img_src[0]; ?>" alt="" >
                                                    <?php } ?>
                                                </div>
                                            <?php } else if (!empty($question['img']) && $question['img_option'] == 'false' && $question['draggable_option'] == 'true') {
                                                ?>
                                                <div class="image js-openPopup" data-popup="#img_drag<?php echo $key1 + 1; ?>">
                                                    <img src="<?php echo $question['img']; ?>" alt="" width="150px" height="150px">
                                                </div>
                                                <?php
                                            } else if (!empty($question['img']) && $question['img_option'] == 'false') {
                                                ?>
                                                <div class="image">
                                                    <img src="<?php echo $question['img']; ?>" alt="">
                                                </div>
                                                <?php
                                            } else {
                                                echo '<img src="' . plugin_dir_url(__FILE__) . 'images/placeholder.png' . '" class="placeholder_image" height="150px" width="150px">';
                                            }
                                            ?> 
                                            <div class="description">
                                                <h5><?php echo $question['question']; ?></h5>
                                                <?php
                                                if ($question['img_option'] == 'true' || $question['draggable_option'] == 'true') {
                                                    if ($question['img_option'] == 'true') {
                                                        $popup = '#img_' . str_replace(',', '', $question['img']);
                                                    } else if ($question['draggable_option'] == 'true') {
                                                        $popup = '#img_drag' . $key1 + 1;
                                                    }
                                                    ?>
                                                    <button class="button gray small ghost js-openPopup" data-popup="<?php echo $popup; ?>">Toon de juiste antwoorden</button>
                                                <?php } else { ?>
                                                    <div class="answer">Your answer <strong><?php echo $question['answer']; ?></strong></div>
                                                    <?php
                                                }
                                                if (!empty($question['resultAnsDesc'])) {
                                                    echo "<p>" . stripslashes($question['resultAnsDesc']) . '</p>';
                                                }
                                                ?>
                                            </div>
                                            <?php
                                            if ($question['img_option'] == 'true' || $question['draggable_option'] == 'true') {
                                                if ($question['img_option'] == 'true') {
                                                    $imgs = explode(',', $question['img']);
                                                    $id = 'img_' . str_replace(',', '', $question['img']);
                                                } else if ($question['draggable_option'] == 'true') {
                                                    $imgs = $question['img'];
                                                    $id = 'img_drag' . $key1 + 1;
                                                }
                                                ?>
                                                <div class="popup hotspotPopup" id="<?php echo $id; ?>">
                                                    <button class="closeButton js-popupClose" style="display: none;">
                                                        <svg class="icon-cross"><use xlink:href="/svg/icons.svg#icon-cross"></use></svg>
                                                    </button>
                                                    <div class="window">
                                                        <div class="hotspotImage">

                                                            <?php
                                                            if ($question['img_option'] == 'true') {
                                                                $ansCnt = 0;
                                                                foreach ($imgs as $img) {
                                                                    $ansCnt++;
                                                                    $is_correct = ((int) $question['correct'] == $ansCnt ? 'is-correct' : '');
                                                                    $img_src = wp_get_attachment_image_src($img, 'full');
                                                                    ?>
                                                                    <div class="hotspotAnswer <?php echo $is_correct; ?>">
                                                                        <img src="<?php echo $img_src[0]; ?>" alt="">
                                                                    </div>
                                                                    <?php
                                                                }
                                                            } else if ($question['draggable_option'] == 'true') {
                                                                $answer = $question['answer'];
                                                                $correct = $question['correct'];
                                                                $order = array();
                                                                $order_correct = array();
                                                                $a = explode('|', $answer);
                                                                $b = explode('|', $correct);
                                                                foreach ($a as $value) {

                                                                    $num = $value[0];
                                                                    $value = substr($value, 2);
                                                                    $order[$num]['position'] = $value;
                                                                }
                                                                foreach ($b as $value) {

                                                                    $num = $value[0];
                                                                    $value = substr($value, 2);
                                                                    $order_correct[$num]['position'] = $value;
                                                                }
                                                                ?>
                                                                <div class="hotspotAnswer drag-image-content">
                                                                    <div class="dragDropImage" style="position:relative;">
                                                                        <?php
                                                                        if (!empty($order) && !empty($order_correct) && $answer !== '') {
                                                                            foreach ($order as $key => $value) {
                                                                                $a = explode('_', $value['position']);
                                                                                $top = str_replace('t-', '', $a[0]);
                                                                                $left = str_replace('l-', '', $a[1]);
                                                                                foreach ($order_correct as $key1 => $value1) {
                                                                                    if ($key1 == $key && $value['position'] == $value1['position']) {
                                                                                        $class = 'is-correct';
                                                                                        $color = 'background:#0E9E4A';
                                                                                    } else if ($key1 == $key && $value['position'] != $value1['position']) {
                                                                                        $class = 'is-wrong';
                                                                                        $color = 'background:#EE2828';
                                                                                        $correct_pos = $value['position'];

                                                                                        foreach ($order_correct as $key1 => $value1) {
                                                                                            if ($value1['position'] == $correct_pos) {
                                                                                                $corre_class = 'is-correction';
                                                                                                $num = $key1;
                                                                                                $correc_color = 'background:#0E9E4A';
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                }

                                                                                if ($corre_class !== '' && $num !== '' && $class === 'is-wrong' && $top !== '' && $left !== '') {
                                                                                    ?>
                                                                                    <div class="dropAnswer <?php echo $corre_class; ?>" style="top: <?php echo $top; ?>; left: <?php echo $left; ?>;position: absolute;<?php echo $correc_color; ?>"><?php echo $num; ?></div>
                                                                                <?php }
                                                                                ?>
                                                                                <?php if ($top !== '' && $left !== '' && $key !== '') { ?>
                                                                                    <div class="dropAnswer <?php echo $class; ?>" style="top: <?php echo $top; ?>; left: <?php echo $left; ?>;position: absolute;<?php echo $color; ?>"><?php echo $key; ?></div>
                                                                                    <?php
                                                                                }
                                                                            }
                                                                        } else if (!empty($order_correct) && $answer == '') {
                                                                            foreach ($order_correct as $key1 => $value1) {

                                                                                $a = explode('_', $value1['position']);
                                                                                $top = str_replace('t-', '', $a[0]);
                                                                                $left = str_replace('l-', '', $a[1]);
                                                                                ?>
                                                                                <?php if ($top !== '' && $left !== '' && $key1 !== '') { ?>
                                                                                    <div class="dropAnswer <?php echo $class; ?>" style="top: <?php echo $top; ?>; left: <?php echo $left; ?>;position: absolute;"><?php echo $key1; ?></div>
                                                                                    <?php
                                                                                }
                                                                            }
                                                                        }
                                                                        ?>
                                                                        <img src="<?php echo $imgs; ?>">
                                                                    </div>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>

                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="progress-bar mapped wrong" style="min-width: 100%;">
                    <h4 class="res-head"><?php echo ($filter['countwrong'] != '' ? "Deze " . $filter['countwrong'] . " vragen had je fout" : 'Je hebt geen vragen fout'); ?></h4>
                    <button class="button wrongAnsBtn <?php echo ($filter['countwrong'] == '' ? 'not' : ''); ?> more">
                        <span>Toon alle foute vragen</span>
                    </button>
                    <div class="resultsFull failed" style="display:none;">
                        <div class="resultsFull-wrapper">
                            <?php
                            foreach ($filter['questions'] as $key2 => $question) {
                                if (($question['answer'] != $question['correct']) || ($question['checked'] == 'false')) {
                                    ?>
                                    <div class="questions">
                                        <div class="number"><?php echo $key2 + 1; ?></div>
                                        <div class="question-inner-wrap">
                                            <?php
                                            if (!empty($question['img']) && $question['img_option'] == 'true') {
                                                $imgs = explode(',', $question['img']);
                                                ?>
                                                <div class="image js-openPopup" data-popup="#img_<?php echo str_replace(',', '', $question['img']); ?>">
                                                    <?php
                                                    foreach ($imgs as $img) {
                                                        $img_src = wp_get_attachment_image_src($img, 'full')
                                                        ?>
                                                        <img src="<?php echo $img_src[0]; ?>" alt="">
                                                    <?php } ?>
                                                </div>
                                            <?php } else if (!empty($question['img']) && $question['img_option'] == 'false' && $question['draggable_option'] == 'true') {
                                                ?>
                                                <div class="image js-openPopup" data-popup="#img_drag<?php echo $key2 + 1; ?>">
                                                    <img src="<?php echo $question['img']; ?>" alt="" width="150px" height="150px">
                                                </div>
                                                <?php
                                            } else if (!empty($question['img'])) {
                                                ?>
                                                <div class="image">
                                                    <img src="<?php echo $question['img']; ?>" alt="">
                                                </div>
                                                <?php
                                            } else {
                                                echo '<img src="' . plugin_dir_url(__FILE__) . 'images/placeholder.png' . '" class="placeholder_image" height="150px" width="150px">';
                                            }
                                            ?>

                                            <div class="description">
                                                <h5><?php echo $question['question']; ?></h5>
                                                <?php
                                                if ($question['img_option'] == 'true' || $question['draggable_option'] == 'true') {
                                                    if ($question['img_option'] == 'true') {
                                                        $popup = '#img_' . str_replace(',', '', $question['img']);
                                                    } else if ($question['draggable_option'] == 'true') {
                                                        $popup = '#img_drag' . $key2 + 1;
                                                    }
                                                    ?>
                                                    <button class="button gray small ghost js-openPopup" data-popup="<?php echo $popup; ?>">Toon de juiste antwoorden</button>
                                                <?php } else { ?>
                                                    <div class="answer">Your answer <strong><?php echo $question['answer']; ?></strong></div>
                                                    <div class="right-answer">The correct answer is <strong><?php echo $question['correct']; ?></strong></div>
                                                    <?php
                                                }
                                                if (!empty($question['resultAnsDesc'])) {
                                                    echo "<p>" . stripslashes($question['resultAnsDesc']) . '</p>';
                                                }
                                                ?>
                                            </div>
                                            <?php
                                            if ($question['img_option'] == 'true' || $question['draggable_option'] == 'true') {
                                                if ($question['img_option'] == 'true') {
                                                    $imgs = explode(',', $question['img']);
                                                    $id = 'img_' . str_replace(',', '', $question['img']);
                                                } else if ($question['draggable_option'] == 'true') {
                                                    $imgs = $question['img'];
                                                    $id = 'img_drag' . $key2 + 1;
                                                }
                                                ?>
                                                <div class="popup hotspotPopup" id="<?php echo $id; ?>">
                                                    <button class="closeButton js-popupClose"  style="display: none;"> 
                                                        <svg class="icon-cross"><use xlink:href="/svg/icons.svg#icon-cross"></use></svg>
                                                    </button>
                                                    <div class="window">
                                                        <div class="hotspotImage">

                                                            <?php
                                                            if ($question['img_option'] == 'true') {
                                                                $ansCnt = 0;
                                                                foreach ($imgs as $img) {
                                                                    $ansCnt++;
                                                                    $is_correct = ((int) $question['correct'] == $ansCnt ? 'is-correct' : '');
                                                                    $img_src = wp_get_attachment_image_src($img, 'full');
                                                                    ?>
                                                                    <div class="hotspotAnswer <?php echo $is_correct; ?>">
                                                                        <img src="<?php echo $img_src[0]; ?>" alt="">
                                                                    </div>
                                                                    <?php
                                                                }
                                                            } else if ($question['draggable_option'] == 'true') {
                                                                $answer = $question['answer'];
                                                                $correct = $question['correct'];
                                                                $order = array();
                                                                $order_correct = array();
                                                                $a = explode('|', $answer);
                                                                $b = explode('|', $correct);
                                                                foreach ($a as $value) {

                                                                    $num = $value[0];
                                                                    $value = substr($value, 2);
                                                                    $order[$num]['position'] = $value;
                                                                }
                                                                foreach ($b as $value) {

                                                                    $num = $value[0];
                                                                    $value = substr($value, 2);
                                                                    $order_correct[$num]['position'] = $value;
                                                                }
                                                                ?>
                                                                <div class="hotspotAnswer drag-image-content">
                                                                    <div class="dragDropImage" style="position:relative;">
                                                                        <?php
                                                                        if (!empty($order) && !empty($order_correct) && $answer !== '') {
                                                                            foreach ($order as $key => $value) {
                                                                                $a = explode('_', $value['position']);
                                                                                $top = str_replace('t-', '', $a[0]);
                                                                                $left = str_replace('l-', '', $a[1]);
                                                                                foreach ($order_correct as $key1 => $value1) {
                                                                                    if ($key1 == $key && $value['position'] == $value1['position']) {
                                                                                        $class = 'is-correct';
                                                                                        $color = 'background:#0E9E4A';
                                                                                    } else if ($key1 == $key && $value['position'] != $value1['position']) {
                                                                                        $class = 'is-wrong';
                                                                                        $color = 'background:#EE2828';
                                                                                        $correct_pos = $value['position'];

                                                                                        foreach ($order_correct as $key1 => $value1) {
                                                                                            if ($value1['position'] == $correct_pos) {
                                                                                                $corre_class = 'is-correction';
                                                                                                $num = $key1;
                                                                                                $correc_color = 'background:#0E9E4A';
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                }

                                                                                if ($corre_class !== '' && $num !== '' && $class === 'is-wrong' && $top !== '' && $left !== '') {
                                                                                    ?>
                                                                                    <div class="dropAnswer <?php echo $corre_class; ?>" style="top: <?php echo $top; ?>; left: <?php echo $left; ?>;position: absolute;<?php echo $correc_color; ?>"><?php echo $num; ?></div>
                                                                                <?php }
                                                                                ?>
                                                                                <?php if ($top !== '' && $left !== '' && $key !== '') { ?>
                                                                                    <div class="dropAnswer <?php echo $class; ?>" style="top: <?php echo $top; ?>; left: <?php echo $left; ?>;position: absolute;<?php echo $color; ?>"><?php echo $key; ?></div>
                                                                                    <?php
                                                                                }
                                                                            }
                                                                        } else if (!empty($order_correct) && $answer == '') {
                                                                            foreach ($order_correct as $key1 => $value1) {

                                                                                $a = explode('_', $value1['position']);
                                                                                $top = str_replace('t-', '', $a[0]);
                                                                                $left = str_replace('l-', '', $a[1]);
                                                                                ?>
                                                                                <?php if ($top !== '' && $left !== '' && $key1 !== '') { ?>
                                                                                    <div class="dropAnswer <?php echo $class; ?>" style="top: <?php echo $top; ?>; left: <?php echo $left; ?>;position: absolute;"><?php echo $key1; ?></div>
                                                                                    <?php
                                                                                }
                                                                            }
                                                                        }
                                                                        ?>
                                                                        <img src="<?php echo $imgs; ?>">
                                                                    </div>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>

                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="one_t_btns">
                <div class="inner_one_page_bt our_theori_sd">
                    <ul>
                        <?php foreach ($filter['questions'] as $key3 => $question) {
                            ?>
                            <li> 
                                <?php if (($question['answer'] == $question['correct']) && ($question['checked'] == 'true')) { ?>
                                    <span class="green_b"><?php echo $key3 + $counter; ?></span>
                                <?php } else { ?>
                                    <span class="red_tp"><?php echo $key3 + $counter; ?></span>
                                <?php } ?>
                            </li> 
                            <?php
                        }
                        ?>
                    </ul>
                </div>
            </div>
            <div class='cat-wise-desc'><p style="text-align: center;">Jij bent <?php echo $cat_status; ?> !</p></div>
        </div>
    <?php }
    ?>
    <script>
        //Open Popup
        jQuery(".js-openPopup").click(function (t) {
            var e, i;
            t.preventDefault(),
                    popup($(this).attr("data-popup"), $(this).data("hires-url"))
        });
        function popup(t, e) {
            jQuery(".js-popupClose").show();
            jQuery("body").css("overflow", "hidden"),
                    jQuery(t).addClass("js-show"),
                    setTimeout((function () {
                        jQuery(t).addClass("js-animate")
                    }), 25),
                    jQuery(t).click((function () {
                closePopup();
            })).children().click((function (t) {
                t.stopPropagation();
            }
            )),
                    jQuery(".js-popupClose").click((function (t) {
                t.preventDefault(),
                
                jQuery(this).hide();
                        closePopup();
            }
            )),
                    jQuery(document).keyup((function (t) {
                27 === t.keyCode && closePopup();
            }));
        }
        function closePopup() {
             jQuery(".js-popupClose").hide();
            jQuery(".popup").removeClass("js-animate"),
                    setTimeout((function () {
                        jQuery("body").css("overflow", "auto"),
                                jQuery(".popup").removeClass("js-show")
                    }), 200);
        }
        //Toggle for View Answers in Result Page
        if (jQuery(document).find('.rightAnsBtn.not').length > 0) {
            jQuery(document).find('.rightAnsBtn.not').hide();
        }
        if (jQuery(document).find('.wrongAnsBtn.not').length > 0) {
            jQuery(document).find('.wrongAnsBtn.not').hide();
        }

        jQuery(document).on('click', '.rightAnsBtn.more', function () {
            jQuery(this).removeClass('more');
            jQuery(this).addClass('less');
            jQuery(this).find('span').text('Verberg alle goed vragen');
            jQuery(this).siblings(".resultsFull.passed").slideDown();
        });
        jQuery(document).on('click', '.rightAnsBtn.less', function () {
            jQuery(this).removeClass('less');
            jQuery(this).addClass('more');
            jQuery(this).find('span').text('Toon alle goed vragen');
            jQuery(this).siblings(".resultsFull.passed").slideUp();
        });
        jQuery(document).on('click', '.wrongAnsBtn.more', function () {
            jQuery(this).removeClass('more');
            jQuery(this).addClass('less');
            jQuery(this).find('span').text('Verberg alle foute vragen');
            jQuery(this).siblings(".resultsFull.failed").slideDown();
        });
        jQuery(document).on('click', '.wrongAnsBtn.less', function () {
            jQuery(this).removeClass('less');
            jQuery(this).addClass('more');
            jQuery(this).find('span').text('Toon alle foute vragen');
            jQuery(this).siblings(".resultsFull.failed").slideUp();
        });
    </script>
    <?php
    $content = ob_get_contents();
    ob_clean();
    wp_send_json(array('message' => 'Success', 'html' => $content));
    wp_die();
}

add_action('wp_ajax_nopriv_store_result_data_db', 'store_result_data_db_result');
add_action('wp_ajax_store_result_data_db', 'store_result_data_db_result');
?>
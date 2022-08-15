<?php

function quiz_frontend_display($atts) {
//    if (is_admin()) {
//        return true;
//    }
    ob_start();
    global $post;

    //For Shortcode Attributes Relation
    $atts = shortcode_atts(array(
        'quiz_id' => null
            ), $atts);
    //for custom taxquery
    $question_taxanomy = array(
        'taxonomy' => 'question-categories'
    );
    $question_categories = get_categories($question_taxanomy);
    foreach ($question_categories as $question_single_cat) {

        $metaquery[] = array(
            'taxonomy' => 'question-categories',
            'field' => 'slug',
            'terms' => $question_single_cat->slug,
        );
    }

    $args = array(
        'post_type' => 'quiz',
        'post__in' => $atts,
        'meta_query' => $metaquery
    );
    $loop = new WP_Query($args);

    $show_desc = false;
    $show_plan = false;
    $show_next_quiz_btn = false;
    while ($loop->have_posts()) : $loop->the_post();
        $quiz_cats = get_the_terms($post->ID, 'quiz-categories');
//        Show Plans if Free Exam
        if (!empty($quiz_cats)) {
            foreach ($quiz_cats as $quiz_cat) {
                $show_plan = ($quiz_cat->slug == 'free' ? true : false);
//                Show Next Quiz Button if Paid Exam
                $show_next_quiz_btn = ($quiz_cat->slug == 'paid' ? true : false);
            }
        }
        $quiz_tags = get_the_terms($post->ID, 'quiz-tags');
//        Show Description if Theoritical Exam
        if (!empty($quiz_tags)) {
            foreach ($quiz_tags as $quiz_tag) {
                $show_desc = ($quiz_tag->slug == 'thoeirtical-exam' ? true : false);
            }
        }
        ?>  
        <div class="wrapper" id="home_quiz_section">
            <div id="est_number_of_text" style="display: none;"><?php echo get_field('tigno__number_of_check_question'); ?></div>
            <section class="card_section" id="Div1" style="padding-top: 50px;">
                <div class="container">
                    <div class="card_part">
                        <div class="card_body">
                            <div class="card_header">
                                <div class="logo">
                                    <a href="#">
                                        <img src="<?php echo get_the_post_thumbnail_url(); ?>">
                                    </a>
                                </div>
                            </div>
                            <div class="card_content">
                                <?php
                                echo wpautop(get_the_content());
                                ?>
                            </div>
                            <div class="card_footer">
                                <a id="Button1" value="start examen">start examen</a>
                            </div>
                        </div>
                    </div>  
                </div>
            </section>


            <?php
            $get_start_page = get_post_meta($post->ID, 'quiz_start_page', true);
            if (!empty($get_start_page[0])) {
                $cnt = 0;
                $total_content = count($get_start_page);
                foreach ($get_start_page as $get_start) {
                    $cnt++;
                    if(!empty($get_start['quiz_start_page' . $cnt])){
                    $nextBtn = ($total_content == $cnt ? 'START HET THEORIE-EXAMEN' : 'Next');
                    ?>
                    <!--Quiz Start Description-->
                    <section class="timmer_section1" id="nextDiv<?php echo $cnt; ?>" style="display: none;">             
                        <div class="container">
                            <div class="timmer_inner">
                                <div class="timmer_body">
                                    <div class="timmer_content">
                                        <div class="middle_time_box">
                                        </div>
                                        <?php
                                        echo wpautop($get_start['quiz_start_page' . $cnt]);
                                        ?>
                                        <div class="btn_box">
                                            <a id="nextBtn<?php echo $cnt; ?>" class="nextBtnTimmer" value="<?php echo $nextBtn; ?>"><?php echo $nextBtn; ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <?php
                    }
                }
            }
            $get_questions = get_post_meta($post->ID, 'nested_repeter_group', true);

            $listofcategories = [];
            foreach ($get_questions as $k => $question) {
                $listofcategories[$question['question_category']][$k]['cat_id'] = $question['question_category'];
                $listofcategories[$question['question_category']][$k]['Questions'] = $question['Questions'];
                $listofcategories[$question['question_category']][$k]['logo'] = $question['logo'];
                $listofcategories[$question['question_category']][$k]['image_option'] = $question['image_option'][0];
                $listofcategories[$question['question_category']][$k]['notes'] = $question['notes'];
                $listofcategories[$question['question_category']][$k]['correct_answer'] = $question['correct_answer'];
                $listofcategories[$question['question_category']][$k]['dragable_option'] = $question['dragable_option'][0];
                $listofcategories[$question['question_category']][$k]['question_timer'] = $question['question_timer'];
                $listofcategories[$question['question_category']][$k]['suggestion_Message'] = $question['suggestion_Message'];
                $listofcategories[$question['question_category']][$k]['result_answer_Message'] = $question['result_answer_Message'];
                $listofcategories[$question['question_category']][$k]['show_suggestion_desc_front'] = $question['show_suggestion_desc_front'][0];
            }
            //Category wise Questions Array
            $listofcategories_map = array();
            foreach ($listofcategories as $key => $questions) {

                $listofcategories_map[$key] = array_values($questions);
            }

            $storearr = array();

            foreach ($listofcategories as $key => $qlist) {
                $last_key_of = key(array_slice($qlist, -1, 1, true));
                foreach ($qlist as $key2 => $q) {

                    if ($key2 == $last_key_of) {
                        $storearr[] = $key;
                    } else {
                        $storearr[] = $key;
                    }
                }
            }

            function js_str($s) {
                return '"' . addcslashes($s, "\0..\37\"\\") . '"';
            }

            function js_array($array) {
                $temp = array_map('js_str', $array);
                return '[' . implode(',', $temp) . ']';
            }

            $last_f_key = key(array_slice($listofcategories, -1, 1, true));
            foreach ($listofcategories_map as $k => $questions) {

                $cat_count = get_term_meta($k, 'cat_timer', true);
                $prev_disable = get_term_meta($k, 'disable_cat_btn', true) != '' ? "true" : "false";
                ?>

                <div class="category-<?php echo get_cat_slugs($k); ?> quiz_cat" id="cat-<?php echo $k; ?>" data-catid="<?php echo $k; ?>" data-category-time="<?php echo ( get_term_meta($k, 'cat_timer', true) != '' ) ? "true" : "false"; ?>" <?php echo ( $cat_count != '' ) ? 'data-cat-start="' . $cat_count . '"' : ""; ?> data-category-disable="<?php echo ( get_term_meta($k, 'disable_cat_btn', true) != '' ) ? "true" : "false"; ?>" >
                    <!--Question Category Start Page-->
                    <div id="number_of_pass_criteria" style="display: none;"><?php echo get_term_meta($k, 'pass_criteria', true); ?></div>
                    <section class="timmer_sectio each_quiz" id="Divscat-<?php echo $k; ?>" style="display: none;">
                        <div class="container">
                            <div class="timmer_inner">
                                <div class="timmer_body">
                                    <div class="timmer_content">
                                        <div class="middle_time_box">
                                            <div>
                                                <?php
                                                echo get_term_meta($k, 'wysiwyg_field_1', true);
                                                ?>
                                            </div>
                                        </div>
                                        <div class="btn_box">
                                            <a class="Button_scat" id="scatid-<?php echo $k; ?>" data-catid="<?php echo $k; ?>" value="Veel succes met dit onderdeel!">GA NAAR DE EERSTE VRAAG</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <!--Question Slide-->
                    <div class="quiz_header_wrapper">
                        <div class="quiz_header">
                            <div class="timmer_footer" style="position: relative;display: none;">
                                <div class="toolbar-index">
                                    <span class="index-long">
                                        <small>Vraag</small> 
                                        <?php
                                        $nq = count($questions);
                                        ?>
                                        <b class="show_qu"></b> 
                                        <small>van</small> 
                                        <b><?php echo $nq; ?></b>
                                    </span> 
                                </div>
                            </div>
                            <div class="countdown" style="text-align: center; position: relative; top: 0; width: 100%;background-color: #006591;text-align: center;padding: 28px 0;height: 80px;color: #fff;font-size: 16px; display: none;">
                                <svg class="timer">
                                <circle class="progress-frame" cx="20" cy="20" r="18"></circle>
                                <circle class="progress" cx="20" cy="20" r="18"></circle>
                                </svg>
                                <div class="time"><span class="countdown-numbers"></span></div>
                            </div>
                            <div class="counter_cat" style="text-align: center; position: relative; top: 0; width: 100%;background-color: #006591;text-align: center;padding: 28px 0;height: 50px;color: #fff;font-size: 16px;display: none;"></div>
                        </div>
                    </div>
                    <?php $last_key = key(array_slice($questions, -1, 1, true)); ?>
                    <?php foreach ($questions as $j => $question) { ?>

                        <section class="timmer_section each_quiz Div4que<?php echo ($j == 0) ? '' : $j; ?>" data-category="<?php echo $k; ?>"  data-count="<?php echo $j; ?>" style="display: none;" <?php
                        if ($j == $last_key) {
                            echo "data-step='last'";
                        } else {
                            echo "";
                        }
                        ?> timer="notstart" skip-previous="false">
                            <div class="container">
                                <div class="timmer_inner">
                                    <div class="timmer_body">
                                        <div class="timmer_header" style="display: none !important;">
                                            <div class="time_box">
                                                <div class="countdown1">
                                                    <span class="get_question_timer">
                                                        <?php
                                                        echo $question['question_timer'];
                                                        ?>
                                                    </span>
                                                    <a id="end_quiz" style="padding: 8px;font-size: 15px;border: 2px solid white;border-radius: 50px;box-shadow: 0px 0px 4px grey;
                                                       transition: 0.3s;text-align: right;position: absolute;top: -8px;
                                                       color: wheat;">Quit</a>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="mydivs">
                                            <div class="question_content" id="middlecontent-<?php echo $k; ?>">
                                                <h3><?php echo $question['Questions']; ?></h3>
                                                <div>
                                                    <div class="inner_question_box">
                                                        <?php if ($question['image_option'] == 'image_option') { ?>
                                                            <div class="left_q_box">
                                                                <?php
                                                                $option = $question['notes'];
                                                                $img_answer = 0;
                                                                $img_ids = array();
                                                                foreach ($option as $options) {
                                                                    if (!empty($options['img_option'])) {
                                                                        $img_ids[] = $options['img_option'];
                                                                    }
                                                                }
                                                                ?>
                                                                <div class="question-images">
                                                                    <?php
                                                                    foreach ($option as $options) {
                                                                        $img_answer++;
                                                                        if (!empty($options['img_option'])) {
                                                                            $attachment_url = wp_get_attachment_image_src($options['img_option'], 'full');
                                                                            ?>
                                                                            <a class='img-content'>
                                                                                <img src='<?php echo $attachment_url[0]; ?>'>
                                                                                <input class="choose_answer" type="radio" id="<?PHP echo $options['img_option']; ?>" name="category-<?php echo $j; ?>" data-catname="<?php echo get_cat_names($k); ?>" data-catid="<?php echo $k; ?>" data-questionTitle="<?php echo $question['Questions']; ?>" data-defauultanswer="<?php echo $question['correct_answer']; ?>" data-question="<?php echo $j; ?>" data-draggable-option="false" data-resultans-desc="<?php echo $question['result_answer_Message']; ?>" data-img='<?php echo implode(',', $img_ids); ?>' data-img-option="true" value="<?php echo $img_answer; ?>" <?php echo $tag_attr; ?>> <label style="display:none;"><?PHP echo $options['img_option']; ?></label>
                                                                                <label class="hotspot" for="<?PHP echo $options['img_option']; ?>"></label>
                                                                            </a>
                                                                            <?php
                                                                        }
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>

                                                        <?php } else if ($question['dragable_option'] == 'dragable_option') { ?>
                                                            <div class="left_q_box imageAnswerContainer">

                                                                <div class="image">
                                                                    <div class="dropContainer">
                                                                        <?php
                                                                        $option = $question['notes'];
                                                                        $tag_attr = "";
                                                                        $myClass = "";
                                                                        foreach ($option as $options) {
                                                                            $data = $options['answer_' . $options['answer_option']];
                                                                            $a = explode('_', $data);
                                                                            $top = str_replace('t-', '', $a[0]);
                                                                            $left = str_replace('l-', '', $a[1]);
                                                                            $top1 = (((int)str_replace('px','',$top)/400) * 100);
                                                                            $left1 = (((int)str_replace('px','',$left)/633.578) * 100);
                                                                            
                                                                            if($data !== ''){
                                                                            ?>
                                                                            <div class="dropTarget" data-answer="<?php echo $options['answer_option']; ?>" style="left: <?php echo $left1; ?>%; top: <?php echo $top1; ?>%"></div>
                                                                        <?php } 
                                                                        }?>
                                                                    </div>
                                                                    <?php
                                                                    $attachment_id = $question['logo'];
                                                                    $attachment_url = wp_get_attachment_image_src($attachment_id, 'full');
                                                                    if (!empty($attachment_url)) {
                                                                        ?>
                                                                        <img src="<?php echo $attachment_url[0]; ?>"  />
                                                                        <?php
                                                                    } else {
                                                                        echo '<img src="' . plugin_dir_url(__FILE__) . 'images/placeholder.png' . '" class="placeholder_image">';
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                            <div class="right_q_box">
                                                                <div class="dragables js-multiple">
                                                                    <?php $cnt = 0; ?>
                                                                    <?php foreach ($option as $options) {
                                                                        ?>
                                                                        <div class="dragContainer">
                                                                            <div class="dragable drag_desktop" data-option="<?php echo $options['answer_option']; ?>" style="user-select: none; margin: 0px;left:67.8%;top:<?php echo $cnt; ?>px;"><?php echo $options['answer_option']; ?></div>
                                                                            <div class="dragable drag_mobile" data-option="<?php echo $options['answer_option']; ?>" style="user-select: none; margin: 0px;left:<?php echo $cnt; ?>px;top:83%;"><?php echo $options['answer_option']; ?></div>
                                                                            <div class="dragableSpot"><?php echo $options['answer_option']; ?></div>
                                                                        </div>
                                                                        <?php
                                                                        $cnt = $cnt + 80;
                                                                    }
                                                                    ?>
                                                                </div>
                                                                <div class="answerContainer" style='display:none;'>
                                                                    <?php
                                                                    $option1 = $question['notes'];
                                                                    $ans = array();
                                                                    $correct_answer = '';
                                                                    $attachment_url = wp_get_attachment_image_src($attachment_id, 'full');
                                                                    foreach ($option1 as $options) {
                                                                        $ans[$options['answer_option']] = $options['answer_' . $options['answer_option']];
                                                                    }
                                                                    for ($i = 1; $i <= count($ans); $i++) {
                                                                        $correct_answer .= $i . '=' . $ans[$i] . '|';
                                                                    }
                                                                    $correct_answer = rtrim($correct_answer, "|");
                                                                    ?>
                                                                    <a>
                                                                        <input class="choose_answer drag-answer target" type="hidden" name="category-<?php echo $j; ?>" id="category-<?php echo $j; ?>" data-catname="<?php echo get_cat_names($k); ?>" data-catid="<?php echo $k; ?>" data-questionTitle="<?php echo $question['Questions']; ?>" data-defauultanswer="<?php echo $correct_answer; ?>" data-question="<?php echo $j; ?>" data-draggable-option="true" data-resultans-desc="<?php echo $question['result_answer_Message']; ?>" data-img='<?php echo $attachment_url[0]; ?>' data-img-option="false" value="" <?php echo $tag_attr; ?>> 
                                                                    </a>

                                                                </div>
                                                            <?php if ($question['suggestion_Message'] !== '' && ($show_desc || $question['show_suggestion_desc_front'] == 'show_suggestion_desc_front')) { ?>
                                                                    <div class="list_box_menu drag-drop-discription" id="suggestion_area-<?php echo ($j == 0) ? '0' : $j; ?>" style="<?php echo ($question['dragable_option'] == 'dragable_option' ? 'display: block;' : 'display: none;'); ?>">
                                                                        <ul>
                                                                            <li>
                                                                                <p class="suggestion_area"><?php echo $question['suggestion_Message']; ?></p>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                            <?php } ?>
                                                            </div>
                                                            <?php } else {
                                                            ?>
                                                            <div class="left_q_box">
                                                                <?php
                                                                $attachment_id = $question['logo'];
                                                                if (!empty($attachment_id)) {
                                                                    echo wp_get_attachment_image($attachment_id);
                                                                } else {
                                                                    echo '<img src="' . plugin_dir_url(__FILE__) . 'images/placeholder.png' . '" class="placeholder_image">';
                                                                }
                                                                ?>
                                                            </div>
                                                            <div class="right_q_box">
                                                                <div class="list_box_menu">
                                                                    <?php
                                                                    $option = $question['notes'];
                                                                    $tag_attr = "";
                                                                    $myClass = "";
                                                                    foreach ($option as $options) {
                                                                        if ($show_desc) {
                                                                            $tag_attr = 'data-tag=' . $show_desc;
                                                                        }
                                                                        ?>
                                                                        <ul>
                                                                            <li>
                                                                                <?php
                                                                                if ($show_desc) {
                                                                                    if ($question['correct_answer'] == $options['answer_option']) {
                                                                                        $myClass = "class='flagGreen'";
                                                                                    } else {
                                                                                        $myClass = "class='WrongAns'";
                                                                                    }
                                                                                }
                                                                                ?>
                                                                                <a <?php echo $myClass; ?>>
                                                                                    <input class="choose_answer" type="radio" name="category-<?php echo $j; ?>" id="category-<?php echo $j; ?>" data-catname="<?php echo get_cat_names($k); ?>" data-catid="<?php echo $k; ?>" data-questionTitle="<?php echo $question['Questions']; ?>" data-defauultanswer="<?php echo $question['correct_answer']; ?>" data-question="<?php echo $j; ?>" data-draggable-option="false" data-resultans-desc="<?php echo $question['result_answer_Message']; ?>" data-img='<?php echo wp_get_attachment_image_url($attachment_id) ?>' data-img-option="false" value="<?php echo $options['answer_option']; ?>" <?php echo $tag_attr; ?>> <?PHP echo $options['answer_option']; ?>
                                                                                </a>
                                                                            </li>
                                                                        </ul>
                                                                    <?PHP }
                                                                    ?>
                                                                </div>

                                                            <?php if ($question['suggestion_Message'] !== '' && ($show_desc || $question['show_suggestion_desc_front'] == 'show_suggestion_desc_front')) { ?>
                                                                    <div class="list_box_menu " id="suggestion_area-<?php echo ($j == 0) ? '0' : $j; ?>" style="display: none;">
                                                                        <ul>
                                                                            <li>
                                                                                <p class="suggestion_area"><?php echo $question['suggestion_Message']; ?></p>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                            <?php } ?>
                                                            </div>
                                                    <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="button_section">  
                                            <button class="prev" data-currcat="<?php echo $k; ?>" data-prevtarget="<?php echo $j - 1; ?>" data-currtarget="<?php echo $j; ?>" style="padding: 10px;font-size: 15px;border: 2px solid;border-radius: 50px;box-shadow: 0px 0px 4px grey;transition: 0.3s; display: none;">Previous Question</button>
                                            <button class="next" id="nextcat-<?php echo $k; ?>" data-currcat="<?php echo $k; ?>" data-currtarget="<?php echo $j; ?>" data-target="<?php echo $j + 1; ?>" style="padding: 10px;font-size: 15px;border: 2px solid;border-radius: 50px;box-shadow: 0px 0px 4px grey;transition: 0.3s;">Next Question
                                            </button>
                                            <button class="next_slide" data-currtarget="<?php echo $j; ?>"  data-currcat="<?php echo $k; ?>" data-target="<?php echo $j + 1; ?>" data-catid="<?php echo $k; ?>" id="ecatid-<?php echo $k; ?>" <?php
                                            if ($k == $last_f_key) {
                                                echo "data-mstep='last'";
                                            } else {
                                                echo "";
                                            }
                                            ?> style="padding: 10px;font-size: 15px;border: 2px solid;border-radius: 50px;box-shadow: 0px 0px 4px grey;transition: 0.3s;display: none;">
                                                Finish Quiz
                                            </button>   
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                        <!---------------->
                    <?php } ?>
                    <!--Question Category End Page-->
                    <section class="timmer_sectio" id="Divecat-<?php echo $k; ?>" style="display: none;" >
                        <div class="container">
                            <div class="timmer_inner">
                                <div class="timmer_body">
                                    <div class="timmer_content">
                                        <div class="middle_time_box">
                                            <div>
                                                <?php
                                                echo get_term_meta($k, 'wysiwyg_field_2', true);
                                                ?>
                                            </div>
                                        </div>
                                        <div class="new_one_cnt">
                                            <h2>Overzicht</h2>
                                            <ul>
                                                <li><i class="far fa-square"></i><span>Onbeantwoord</span></li>
                                                <li><i class="fas fa-square"></i><span>Beantwoord</span></li>
                                                <!-- <li><i class="fas fa-square"></i><span>Niet oproepbaar</span></li>
                                                <li><i class="fas fa-star"></i><span>Gemarkeerd</span></li> -->
                                            </ul>
                                        </div>
                                        <div class="one_t_btns">
                                            <div class="inner_one_page_bt">
                                                <ul class="result-<?php echo $k; ?>" data-total="<?php echo count($questions); ?>">
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="btn_box">
                                            <a class="Button_ecat" data-nextcat="" data-catid="<?php echo $k; ?>" id="ecatid-<?php echo $k; ?>" <?php
                                            if ($k == $last_f_key) {
                                                echo "data-mstep='last'";
                                            } else {
                                                echo "";
                                            }
                                            ?> value="Veel succes met dit onderdeel!">GA NAAR DE EERSTE VRAAG</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <!-------------------->
                </div>
            <?php } ?>
            <?php require_once( plugin_dir_path(__FILE__) . 'endQuiz.php' ); ?>
            <?php require_once( plugin_dir_path(__FILE__) . 'result-page.php' ); ?>
            <?php
            if ($show_plan) {
                echo do_shortcode('[buy_packages]'); 
            }
            ?>
        </div> 

        <script type="text/javascript">
            //Drag Drop Functionality On Next Button Click, Also on Page Load
            function drag_drop(currentQuestion) {
                var Div4que = (currentQuestion === 0 ? '.Div4que' : '.Div4que' + currentQuestion);
                var data = [];
                if (jQuery('.right_q_box').children('.dragables').length > 0) {
                    var r = []
                            , a = function t(e, i) {
                                var n = i.position().left + "px"
                                        , s = i.position().top + "px";
                                e.css({
                                    top: parseInt(s),
                                    left: parseInt(n)
                                })
                            };

                    jQuery(Div4que).find(".dragable").pep({
                        droppable: jQuery(Div4que).find(".dragable").parents('.inner_question_box').find(".dropTarget"),
                        overlapFunction: !1,
                        useCSSTranslation: !1,
                        cssEaseDuration: 250,
                        drag: function t(e, i) {
                            this.$el.parents('.inner_question_box').find(".dropContainer .dropTarget").addClass("lightUp")
                        },
                        stop: function t(e, i) {
                            this.$el.parents('.inner_question_box').find(".dropContainer .dropTarget").removeClass("lightUp")
                        },
                        rest: function t(e, i) {
                            var n = this
                                    , s = this.activeDropRegions[0];
                                    console.log(n);
                            optionAlreadyPlaced = r.filter((function (t) {
                                return t
                            }
                            )).find((function (t) {
                                return t.$el.data("option") === n.$el.data("option")
                            }
                            ));
                            var o = parseInt(r.indexOf(optionAlreadyPlaced));

                            if (s) {
                                var l = s.data("answer");
                                a(this.$el, s);
                                var h = r[l];

                                (optionAlreadyPlaced && h && (a(h.$el, jQuery("[data-answer='".concat(o, "']"))),
                                        r[o] = h),
                                        !optionAlreadyPlaced && h && r[l].revert(),
                                        optionAlreadyPlaced && !h && (r[o] = null)),
                                        r[l] = this,
                                        r.forEach((function (t, e) {
                                            var i = t.$el.data("option");
                                        }
                                        ))
                                var top = s.css('top');
                                var left = s.css('left');
                                var pos = 't-' + top + '_l-' + left;
                                data[this.$el.data("option")] = pos;
                                var data1 = '';
                                jQuery.each(data, function (key, val) {
                                    if (key !== 0 && val !== undefined) {
                                        data1 += key + '=' + val + '|';
                                    }
                                });
                                var data1 = data1.slice(0, -1);
                                jQuery(Div4que).find('.dragable').parents('.inner_question_box').find(".target").val(data1);
                            } else
                                r[o] = null
                        },
                        revert: !0,
                        revertIf: function t(e, i) {
                            return !this.activeDropRegions.length
                        }
                    });
                }
            }
            jQuery(document).ready(function ($) {

        <?php if ($show_next_quiz_btn) { ?>
                    jQuery('#resultPage').find('.theorie_final_content').find('.next_quiz-btn').show();
        <?php } ?>
                setTimeout(function () {
                    localStorage.clear();
                    sessionStorage.clear();
                }, 3000);


                var orderTimer = <?php echo js_array($storearr); ?>;

                var first_cat = jQuery('.quiz_cat').first();
                var checkedCatbasedTimer = first_cat.attr('data-category-time');

                if (checkedCatbasedTimer === 'false') {
                    var inital_timer_value = jQuery('.quiz_cat').first().find('.Div4que .get_question_timer').text().trim();
                } else {
                    var inital_timer_value = first_cat.attr('data-cat-start');
                }
                var currentCat = jQuery('.quiz_cat').first().attr('data-catid');

                // Initialize game logic using Key and Value pair
                var game = {
                    questions: orderTimer,
                    currentQuestion: 0,
                    counter: inital_timer_value,
                    currentCat: currentCat,
                    countdown: function () {
                        if (game.currentCat !== undefined) {
                            if (game.counter === 0 && !jQuery('#Divecat-' + game.currentCat).is(':visible'))
                            {
                                game.timeUp();
                            } else if (game.counter > 0) {
                                game.counter--;


                                var parent_cat = $('#cat-' + game.currentCat);
                                if (game.currentQuestion === 0) {
                                    var max = parent_cat.find('.Div4que').find('.get_question_timer').text().trim();
                                } else {
                                    var max = parent_cat.find('.Div4que' + game.currentQuestion).find('.get_question_timer').text().trim();
                                }
                                var second = max;
                                var progressElm = document.querySelector('#cat-' + game.currentCat).querySelector(".progress");
                                var circumference = 2 * Math.PI * progressElm.getAttribute('r');
                                progressElm.style.strokeDasharray = circumference;
                                progressElm.style.strokeDashoffset = circumference * 0;
                                percentage = game.counter / second * 100;
                                progressElm.style.strokeDashoffset = circumference - (percentage / 100) * circumference;

                                var checkedCatbasedTimer = parent_cat.attr('data-category-time');

                                if (checkedCatbasedTimer === 'false')
                                {
                                    parent_cat.find('.countdown').show();
                                    $('.counter_cat').hide();
                                    parent_cat.find('.countdown-numbers').html(game.counter);

                                } else if (checkedCatbasedTimer === 'true')
                                {
                                    $('.countdown').hide();
                                    $('#cat-' + game.currentCat).find('.countdown-numbers').html(game.counter);
                                    $('#cat-' + game.currentCat).find('.counter_cat').show();
                                    var minutes_value = Math.ceil(game.counter / 60);
                                    $('#cat-' + game.currentCat).find('.counter_cat').html(minutes_value + ' minutes');
                                }

                                jQuery('.show_qu').text(game.currentQuestion + 1);
                            }
                        }

                    },
                    loadQuestion: function ()
                    {
                        var t = game.currentQuestion;
                        if (t === 0) {

                            $('#cat-' + game.currentCat).find('.Div4que').show("slide", {direction: "right"}, 1000);
                            $('#cat-' + game.currentCat).find('.Div4que').find('.question_content').show();
                            $('#cat-' + game.currentCat).find('.Div4que').attr('timer', 'start');
                            $('#cat-' + game.currentCat).find('.Div4que').attr('skip-previous', 'true');
                            $('#cat-' + game.currentCat).find('.Div4que').find('.prev').hide();

                        } else {
                            $('#cat-' + game.currentCat).find('.Div4que' + t).show("slide", {direction: "right"}, 1000);
                            $('#cat-' + game.currentCat).find('.Div4que' + t).find('.question_content').show();
                            $('#cat-' + game.currentCat).find('.Div4que' + t).attr('timer', 'start');
                            $('#cat-' + game.currentCat).find('.Div4que' + t).attr('skip-previous', 'true');

                        }
                    },
                    nextQuestion: function () {

                        game.currentQuestion++;
                        game.loadQuestion();
                        drag_drop(game.currentQuestion);
                        var parent_cat = $('#cat-' + game.currentCat);
                        var checkedCatbasedTimer = parent_cat.attr('data-category-time');
                        if (checkedCatbasedTimer === 'false')
                        {
                            clearInterval(timer);
                            timer = setInterval(game.countdown, 1000);

                            if (game.currentQuestion === 0) {
                                var inital_timer_value = parent_cat.find('.Div4que .get_question_timer').text().trim();
                            } else {
                                var inital_timer_value = parent_cat.find('.Div4que' + game.currentQuestion + ' .get_question_timer').text().trim();
                            }
                            game.counter = inital_timer_value;
                            $('.counter_cat').hide();
                            parent_cat.find('.countdown').show();
                            parent_cat.find('.countdown-numbers').html(game.counter);

                        } else if (checkedCatbasedTimer === 'true')
                        {
                            var minutes_value = game.counter / 60;
                            $('.countdown').hide();
                            parent_cat.find('.counter_cat').show();
                            parent_cat.find('.countdown-numbers').html(game.counter);
                            parent_cat.find('.counter_cat').html(minutes_value + ' minutes');
                        }


                        var category_id = game.currentCat;
                        var nextItem = $('#cat-' + game.currentCat).nextAll('.quiz_cat').first().attr('data-catid');
                        var checknext = Object.keys(orderTimer)[game.currentQuestion];
                        var checklast = $('#cat-' + game.currentCat).find('.Div4que' + checknext).attr('data-step');

                        if (checklast !== 'last') {
                            $(".next_slide").hide();
                            $('#cat-' + game.currentCat).find('.Div4que' + checknext).find(".next").show();
                        } else {
                            $(".next").hide();
                            $('#cat-' + game.currentCat).find('.Div4que' + checknext).find(".next_slide").show();
                        }

                        $('.next_slide').attr('data-snextcat', nextItem);
                        $('.next_slide').attr('data-sCurrcat', category_id);
                    },

                    prevQuestion: function ()
                    {
                        game.currentQuestion--;
                        game.loadQuestion();
                        var parent_cat = $('#cat-' + game.currentCat);
                        var checkedCatbasedTimer = parent_cat.attr('data-category-time');

                        if (checkedCatbasedTimer === 'false') {
                            var inital_timer_value = parent_cat.find('.Div4que' + game.currentQuestion + ' .get_question_timer').text().trim();
                            game.counter = inital_timer_value;
                            $('.counter_cat').hide();
                            $('#cat-' + game.currentCat).find('.countdown').show();
                            parent_cat.find('.countdown-numbers').html(game.counter);

                        } else if (checkedCatbasedTimer === 'true') {
                            var minutes_value = game.counter / 60;
                            $('.countdown').hide();
                            parent_cat.find('.counter_cat').show();
                            parent_cat.find('.counter_cat').html(minutes_value + ' minutes');
                        }


                        var category_id = game.currentCat;
                        var nextItem = $('#cat-' + game.currentCat).nextAll('.quiz_cat').first().attr('data-catid');

                        var checknext = Object.keys(orderTimer)[game.currentQuestion];
                        var checklast = $('#cat-' + game.currentCat).find('.Div4que' + checknext).attr('data-step');

                        if (checklast !== 'last') {
                            $(".next_slide").hide();
                            $('#cat-' + game.currentCat).find('.Div4que' + checknext).find(".next").show();
                        } else {
                            $('#cat-' + game.currentCat).find('.Div4que' + checknext).find(".next_slide").show();
                        }
                        $('.next_slide').attr('data-snextcat', nextItem);
                        $('.next_slide').attr('data-sCurrcat', category_id);
                    },
                    timeUp: function () {
                        if (game.currentCat !== undefined) {
                            var checknext = game.currentQuestion;
                            var checklast = $('#cat-' + game.currentCat).find('.Div4que' + checknext).attr('data-step');
                            var target = game.currentQuestion;
                            var id = $('#cat-' + game.currentCat).find("input[name=category-" + target + "]").attr('data-question');
                            var cat_name = $('#cat-' + game.currentCat).find("input[name=category-" + target + "]").attr('data-catname');
                            var q_title = $('#cat-' + game.currentCat).find("input[name=category-" + target + "]").attr('data-questiontitle');
                            var defaultAns = $('#cat-' + game.currentCat).find("input[name=category-" + target + "]").attr('data-defauultanswer');
                            var value = $('#cat-' + game.currentCat).find("input[name=category-" + target + "]").val();
                            var catId = $('#cat-' + game.currentCat).find("input[name=category-" + target + "]").attr('data-catid');
                            var result_answer_Message = $('#cat-' + game.currentCat).find("input[name=category-" + target + "]").attr('data-resultans-desc');
                            var img_option = $('#cat-' + game.currentCat).find("input[name=category-" + target + "]").attr('data-img-option');
                            var draggable_option = $('#cat-' + game.currentCat).find("input[name=category-" + target + "]").attr('data-draggable-option');
                            var img = $('#cat-' + game.currentCat).find("input[name=category-" + target + "]").attr('data-img');
                            var checked_val = (draggable_option === 'true' && value !== '' ? 'true' : 'false');
                            addNewShow(cat_name, catId, q_title, value, defaultAns, checked_val, id, result_answer_Message, img_option, draggable_option, img);
                            if (checklast !== 'last') {
                                if (game.currentQuestion === orderTimer.length - 1) {
                                    setTimeout(game.results, 1 * 50);
                                } else
                                {
                                    setTimeout(game.nextQuestion, 1 * 50);
                                    var t1 = game.currentQuestion;
                                    if (t1 === 0) {
                                        $('#cat-' + game.currentCat).find('.Div4que').hide();
                                    } else {
                                        $('#cat-' + game.currentCat).find('.Div4que' + t1).hide();
                                        $('#cat-' + game.currentCat).find('.Div4que' + t1).find('.next_slide').show();
                                    }
                                }
                                $('.quiz_header').show();
                            } else
                            {
                                var t1 = game.currentQuestion;
                                var nextItem = $('#cat-' + game.currentCat).find('.Div4que' + game.currentQuestion).parents('.quiz_cat').nextAll('.quiz_cat').first().attr('data-catid');

                                $('#cat-' + game.currentCat).find('#Divecat-' + game.currentCat).show();
                                $('#cat-' + game.currentCat).find('.Div4que' + t1).hide('fast');
                                $('#cat-' + game.currentCat).find('#ecatid-' + game.currentCat).attr('data-nextcat', nextItem);
                                $('#cat-' + game.currentCat).find('.Button_ecat').attr('data-nextcat', nextItem);

                                setTimeout(game.results, 1 * 50);
                                $('.quiz_header').hide();
                                $('.timmer_footer').hide();
                                $('#cat-' + game.currentCat).find('.counter_cat').text('');
                                $('.countdown').hide();
                                $('.counter_cat').hide();
                                $('#cat-' + game.currentCat).find('.countdown-numbers').text('');
                                var i = sessionStorage.length;
                                sessionStorage.setItem('q' + i, localStorage.getItem('cat-' + game.currentCat));
                                $('.each_quiz').hide();
                                clearInterval(timer);
                            }
                        }
                    },
                    results: function ()
                    {
                        var currCat = game.currentCat;

                        load_answer(currCat);
                        jQuery('.loader').hide();
                        if (jQuery('#cat-' + currCat).nextAll('.quiz_cat').first().length) {
                            game.currentQuestion = 0;
                        }
                        clearInterval(timer);
                        jQuery('.loader').hide();
                    },
                    clicked: function (e)
                    {
                        clearInterval(timer);
                    },
                    answeredCorrectly: function ()
                    {
                        clearInterval(timer);
                        setTimeout(game.nextQuestion, 2 * 50);
                    },
                    answeredIncorrectly: function ()
                    {
                        clearInterval(timer);
                        game.incorrect++;
                        if (game.currentQuestion == questions.length - 1) {
                            setTimeout(game.results, 2 * 50);
                        } else
                        {
                            setTimeout(game.nextQuestion, 2 * 50);
                        }
                    },
                    reset: function ()
                    {
                        game.currentQuestion = 0;
                        game.counter = 0;
                    }
                };
                drag_drop(0);


                $('.list_box_menu ul li a').click(function ()
                {
                    $('.list_box_menu ul li a').removeClass("active");
                    var tag = $(this).find('.choose_answer').attr('data-tag');
                    $(this).addClass("active");

                    // Check if theoretical quiz
                    if (tag == 1) {
                        jQuery(this).parents('.list_box_menu').addClass('list_box_selected');
                        var correct_answer = $(this).find('.choose_answer').attr('data-defauultanswer');
                        var currenttarget = $(this).find('.choose_answer').attr('id');
                        var next_check = $(this).find("input[" + currenttarget + "]").is(':checked');
                        var value = $(this).find('.choose_answer').val();
                        if (correct_answer == value) {
                            $(this).css('pointer-events', "none");
                            $(this).parents('.list_box_menu').find('.WrongAns').css('pointer-events', "none");
                        } else {
                            if (next_check === false) {
                                var mytemp = $(this).hasClass('flagGreen');
                                if (mytemp)
                                {
                                    $(this).css("background-color", "green");
                                } else {
                                    $(this).addClass("wrong");
                                    $(this).parents('.list_box_menu').find('.flagGreen').css("background-color", "green");
                                }
                                $(this).parents('.list_box_menu').find('.WrongAns').css('pointer-events', "none");
                                $(this).parents('.list_box_menu').find('.flagGreen').css('pointer-events', "none");
                            } else {
                                $(this).parents('.list_box_menu').find('.flagGreen').css('pointer-events', "auto");
                                $(this).parents('.list_box_menu').find('.WrongAns').css('pointer-events', "auto");
                            }
                            $(this).removeClass("active");
                        }

                    } else {
                        $(this).addClass("active");
                    }
                    $(this).find('.choose_answer').attr('checked', 'checked');
                });

                var rvwList = $("#mydivs .question_content").hide();
                rvwList.slice(0, 1).show();
                var size_li = rvwList.length;
                var x = 1;
                start = 0;

                //Next Button Click
                $('.next').click(function () {
                    var target_div = $(this).attr('data-target');
                    var parent_div = $(this).parents('.timmer_section.each_quiz');
                    var get_nextdivcat = $(this).parents('.quiz_cat').nextAll('.quiz_cat').first().attr('data-catid');
                    if (parent_div.find('.list_box_menu').hasClass('list_box_selected')) {
                        parent_div.find('.flagGreen').css("background-color", "green");
                        parent_div.find('.flagGreen').css('pointer-events', "none");
                        parent_div.find('.WrongAns').css('pointer-events', "none");
                    }

                    var currenttarget = $(this).attr('data-currtarget');
                    var checked = $("input[name=category-" + currenttarget + "]:checked").prop("checked");

                    var next_cat = '.Div4que' + target_div;
                    var get_cat = $(this).parents('.quiz_cat').find(next_cat).attr('data-category');
                    var current_cat = $(this).attr('data-currcat');
                    var iflast = $(this).parents('.quiz_cat').find(next_cat).attr('data-step');
                    var checkCatPrevDisable = $(this).parents('.quiz_cat').attr('data-category-disable');
                    var prev_target = $('.prev').attr('data-prevtarget');

                    if (checkCatPrevDisable == 'true')
                    {
                        $('.prev').hide();
                    } else
                    {
                        $('.prev').show();
                        $('.button_section').css('justify-content', 'space-between');
                    }

                    if (get_cat == current_cat)
                    {

                        $('.next_slide').attr('data-snextcat', get_nextdivcat);
                        $('.next_slide').attr('data-sCurrcat', current_cat);
                        $('.next').show();

                    } else
                    {
                        $('.next').hide();
                        $('.next_slide').hide();
                    }

                    if (iflast == 'last')
                    {

                        $('.next').hide();
                        $('.next_slide').show();

                    } else
                    {
                        $('.next').show();
                        $('.next_slide').hide();
                    }

                    setTimeout(game.nextQuestion, 2 * 50);
                    parent_div.hide();


                    var target = $(this).attr('data-currtarget');
                    var checked = $(this).parents('.quiz_cat').find("input[name=category-" + target + "]:checked").prop("checked");

                    //Add Checked Answer to LocalStorage 
                    if (checked)
                    {
                        var id = $("input[name=category-" + target + "]:checked").attr('data-question');
                        var cat_name = $("input[name=category-" + target + "]:checked").attr('data-catname');
                        var q_title = $("input[name=category-" + target + "]:checked").attr('data-questiontitle');
                        var defaultAns = $("input[name=category-" + target + "]:checked").attr('data-defauultanswer');
                        var value = $("input[name=category-" + target + "]:checked").val();
                        var result_answer_Message = $(this).parents('.quiz_cat').find("input[name=category-" + target + "]:checked").attr('data-resultans-desc');
                        var img_option = $(this).parents('.quiz_cat').find("input[name=category-" + target + "]:checked").attr('data-img-option');
                        var draggable_option = $(this).parents('.quiz_cat').find("input[name=category-" + target + "]:checked").attr('data-draggable-option');

                        var catId = $("input[name=category-" + target + "]:checked").attr('data-catid');
                        var img = $(this).parents('.quiz_cat').find("input[name=category-" + target + "]:checked").attr('data-img');
                        addNewShow(cat_name, catId, q_title, value, defaultAns, 'true', id, result_answer_Message, img_option, draggable_option, img);

                    } else {
                        var id = $(this).parents('.quiz_cat').find("input[name=category-" + target + "]").attr('data-question');
                        var cat_name = $(this).parents('.quiz_cat').find("input[name=category-" + target + "]").attr('data-catname');
                        var q_title = $(this).parents('.quiz_cat').find("input[name=category-" + target + "]").attr('data-questiontitle');
                        var defaultAns = $(this).parents('.quiz_cat').find("input[name=category-" + target + "]").attr('data-defauultanswer');
                        var value = $(this).parents('.quiz_cat').find("input[name=category-" + target + "]").val();
                        var catId = $(this).parents('.quiz_cat').find("input[name=category-" + target + "]").attr('data-catid');
                        var result_answer_Message = $(this).parents('.quiz_cat').find("input[name=category-" + target + "]").attr('data-resultans-desc');
                        var img_option = $(this).parents('.quiz_cat').find("input[name=category-" + target + "]").attr('data-img-option');
                        var draggable_option = $(this).parents('.quiz_cat').find("input[name=category-" + target + "]").attr('data-draggable-option');

                        var img = $(this).parents('.quiz_cat').find("input[name=category-" + target + "]").attr('data-img');

                        var checked_val = (draggable_option === 'true' && value !== '' ? 'true' : 'false');
                        addNewShow(cat_name, catId, q_title, value, defaultAns, checked_val, id, result_answer_Message, img_option, draggable_option, img);
                    }

                });

                //Quiz Step Button Click
                $('#Button1').on('click', function ()
                {
                    $('#Div1').hide();
                    if(jQuery('.timmer_section1').length > 0){
                        $('.timmer_section1:first').show("slide", {direction: "right"}, 1000);
                    }else{
                        var getcatID = $(this).parents('#Div1').nextAll('.quiz_cat').first().data("catid");
                        $('#Divscat-' + getcatID).show("slide", {direction: "right"}, 1000);
                    }
                });

                $('.nextBtnTimmer').on('click', function ()
                {
                    jQuery(this).parents('.timmer_section1').hide();
                    if ($(this).parents('.timmer_section1').nextAll(".timmer_section1").first().hasClass('timmer_section1')) {
                        $(this).parents('.timmer_section1').nextAll(".timmer_section1").first().show("slide", {direction: "right"}, 1000);
                    } else {
                        var getcatID = $(this).parents('.timmer_section1').nextAll('.quiz_cat').first().data("catid");
                        $('#Divscat-' + getcatID).show("slide", {direction: "right"}, 1000);
                    }
                });

                $("#Button3").click(function ()
                {
                    $("#Div").each(function () {
                        $(this).toggleClass("example");
                    });
                });

                $('.next_slide').on('click', function ()
                {
                    $('.Div4que').hide();
                    $('#Divecat').show("slide", {direction: "right"}, 1000);
                    var getCurCat = jQuery(this).attr('data-currcat');
                    $(this).parents('.quiz_cat').find('#Divecat-' + getCurCat).show("slide", {direction: "right"}, 1000);
                    var getcat = $(this).attr('data-scurrcat');
                    var getnxtcatid = $(this).attr('data-snextcat');

                    $(this).parents('.quiz_cat').find('.btn_box').find('#ecatid-' + getcat).attr('data-nextcat', getnxtcatid);
                    $('[data-category=' + getcat + ']').hide();
                    $('.timmer_footer').hide();

                    var target = $(this).attr('data-currtarget');
                    var checked = $(this).parents('.quiz_cat').find("input[name=category-" + target + "]:checked").prop("checked");

                    //Add Checked Answer to LocalStorage 
                    if (checked)
                    {
                        var id = $("input[name=category-" + target + "]:checked").attr('data-question');
                        var cat_name = $("input[name=category-" + target + "]:checked").attr('data-catname');
                        var q_title = $("input[name=category-" + target + "]:checked").attr('data-questiontitle');
                        var defaultAns = $("input[name=category-" + target + "]:checked").attr('data-defauultanswer');
                        var value = $("input[name=category-" + target + "]:checked").val();
                        var result_answer_Message = $(this).parents('.quiz_cat').find("input[name=category-" + target + "]:checked").attr('data-resultans-desc');
                        var img_option = $(this).parents('.quiz_cat').find("input[name=category-" + target + "]:checked").attr('data-img-option');
                        var draggable_option = $(this).parents('.quiz_cat').find("input[name=category-" + target + "]:checked").attr('data-draggable-option');
                        var catId = $("input[name=category-" + target + "]:checked").attr('data-catid');
                        var img = $(this).parents('.quiz_cat').find("input[name=category-" + target + "]:checked").attr('data-img');
                        addNewShow(cat_name, catId, q_title, value, defaultAns, 'true', id, result_answer_Message, img_option, draggable_option, img);

                    } else {
                        var id = $(this).parents('.quiz_cat').find("input[name=category-" + target + "]").attr('data-question');
                        var cat_name = $(this).parents('.quiz_cat').find("input[name=category-" + target + "]").attr('data-catname');
                        var q_title = $(this).parents('.quiz_cat').find("input[name=category-" + target + "]").attr('data-questiontitle');
                        var defaultAns = $(this).parents('.quiz_cat').find("input[name=category-" + target + "]").attr('data-defauultanswer');
                        var value = $(this).parents('.quiz_cat').find("input[name=category-" + target + "]").val();
                        var catId = $(this).parents('.quiz_cat').find("input[name=category-" + target + "]").attr('data-catid');
                        var result_answer_Message = $(this).parents('.quiz_cat').find("input[name=category-" + target + "]").attr('data-resultans-desc');
                        var img_option = $(this).parents('.quiz_cat').find("input[name=category-" + target + "]").attr('data-img-option');
                        var draggable_option = $(this).parents('.quiz_cat').find("input[name=category-" + target + "]").attr('data-draggable-option');
                        var img = $(this).parents('.quiz_cat').find("input[name=category-" + target + "]").attr('data-img');
                        var checked_val = (draggable_option === 'true' && value !== '' ? 'true' : 'false');
                        addNewShow(cat_name, catId, q_title, value, defaultAns, checked_val, id, result_answer_Message, img_option, draggable_option, img);
                    }

                    $('#cat-' + game.currentCat).find('.counter_cat').text('');
                    $('.countdown').hide();
                    $('.counter_cat').hide();
                    $('#cat-' + game.currentCat).find('.countdown-numbers').text('');

                    game.results();

                    if ($(this).parents('.each_quiz').attr('data-step') == 'last') {
                        $(this).parents('.each_quiz').hide();
                    }
                    $('.quiz_header').hide();
                    clearInterval(timer);
                    game.counter = 0;
                });
                $('#end_quiz').on('click', function ()
                {
                    location.reload();
                });


                /*For Main Category wise data calling*/
                $('.Button_scat').on('click', function ()
                {
                    timer = setInterval(game.countdown, 1000);
                    game.loadQuestion();
                    var showquestionID = $(this).attr('data-currcatid');
                    var hidescatdiv = $(this).attr('data-catid');
                    var parent_cat = $(this).parents('.quiz_cat');

                    var checkedCatbasedTimer = parent_cat.attr('data-category-time');
                    if (checkedCatbasedTimer === 'false')
                    {
                        $('.counter_cat').hide();
                        var inital_timer_value = parent_cat.find('.Div4que .get_question_timer').text().trim();
                        game.counter = inital_timer_value;
                        $(this).parents('.quiz_cat').find('.countdown').show();
                        parent_cat.find('.countdown-numbers').html(game.counter);
                    } else if (checkedCatbasedTimer === 'true')
                    {

                        var inital_timer_value = parent_cat.attr('data-cat-start');
                        game.counter = inital_timer_value;
                        var minutes_value = game.counter / 60;
                        $('.countdown').hide();
                        $(this).parents('.quiz_cat').find('.counter_cat').show();

                        parent_cat.find('.countdown-numbers').html(game.counter);
                        $(this).parents('.quiz_cat').find('.counter_cat').html(minutes_value + ' minutes');
                    }
                    $(this).parents('.quiz_cat').find('#Divscat-' + hidescatdiv).hide('slow');
                    $(this).parents('.quiz_cat').find('#nextcat-' + showquestionID).show();
                    $('.button_section').css('justify-content', 'flex-end');
                    $('.prev').hide();
                    if (game.currentQuestion == game.questions.length - 1) {
                        $('.next_slide').show();
                        $('.next').hide();
                    } else {
                        $('.next_slide').hide();
                        $('.next').show();
                    }
                    $(this).parents('.quiz_cat').find('.timmer_footer').show();
                    $(this).parents('.quiz_cat').find('.quiz_header').show();
                });

                $('.Button_ecat').on('click', function ()
                {
                    var getnextdivcat = $(this).attr('data-nextcat');
                    var hide_ecat = $(this).attr('data-catid');
                    var ifquizend = $(this).attr('data-mstep');

                    $('#Divecat').hide("slide", {direction: "left"}, 500);

                    $('#scatid-' + getnextdivcat).attr('data-currcatID', getnextdivcat);
                    $("#Divecat-" + hide_ecat).hide("slide", {direction: "left"}, 500);
                    var i = sessionStorage.length;
                    sessionStorage.setItem('q' + i, localStorage.getItem('cat-' + hide_ecat));
                    var nextCat = $('#cat-' + game.currentCat).nextAll('.quiz_cat').first().attr('data-catid');

                    game.currentCat = nextCat;
                    if (ifquizend == 'last')
                    {
                        $('#Divscat-' + getnextdivcat).hide("slide", {direction: "left"}, 500);
                        $('#endQuiz').show();
                        count_true_answer();

                        //Next_slide display result page (quiz_end)(3rd result step)
                        result_page_answer();
                        $('#resultPage').show();
                        $('#pakketkiezer').show();
                    } else
                    {
                        $('#Divscat-' + getnextdivcat).show("slide", {direction: "right"}, 1000);
                    }
                    $('.timmer_footer').hide();
                    clearInterval(timer);
                });


                //For multiple category calling

                var rvwList = $("#cat_desc .middle_time_box").hide();
                rvwList.slice(0, 1).show();
                var size_li = rvwList.length;
                var x = 1;
                start = 0;
                $('#Button').click(function ()
                {
                    if (start + x < size_li) {
                        rvwList.slice(start, start + x).hide("slide", {direction: "left"}, 500);
                        start += x;
                        rvwList.slice(start, start + x).show("slide", {direction: "right"}, 1000);
                    }
                });

                //For Question next/previous button
                $('.prev').click(function () {
                    var target_div = $(this).attr('data-prevtarget');
                    var current_div = $(this).attr('data-currTarget');
                    var parent_div = $(this).parents('.quiz_cat').find('.Div4que' + prev_target);
                    if (parent_div.find('.list_box_menu').hasClass('list_box_selected')) {
                        parent_div.find('.flagGreen').css("background-color", "green");
                        parent_div.find('.flagGreen').css('pointer-events', "none");
                        parent_div.find('.WrongAns').css('pointer-events', "none");
                    }

                    if (target_div == 0) {
                        var prev_target = '';
                    } else {
                        var prev_target = target_div;
                    }

                    $('.next').show();
                    $('.next_slide').hide();
                    $(this).parents('.quiz_cat').find('.Div4que' + prev_target).show("slide", {direction: "right"}, 1000);
                    $(this).parents('.quiz_cat').find('.Div4que' + current_div).hide("slide", {direction: "left"}, 500);
                    $(this).parents('.quiz_cat').find('.Div4que' + prev_target).find('.question_content').show("slide", {direction: "right"}, 1000);

                    game.prevQuestion();

                });


                $('.list_box_menu li a').click(function () {
                    var get_id = $(this).children().attr('data-question');
                    $('#suggestion_area-' + get_id).show();
                    $('#suggestion_area-' + get_id).fadeIn(2000);
                });
            });


            //Function For Category wise Answer 

            function load_answer(getcat)
            {
                var myFinalRes = [];
                var check = [];

                check.push(localStorage.getItem('cat-' + getcat));

                var jsonObj = check;
                var obj1 = {};

                for (var j = 0; j <= jsonObj.length; j++) {

                    if (jsonObj[j] !== undefined) {

                        obj1 = JSON.parse(jsonObj[j]);

                    }
                }
                for (var n = 0; n < obj1.length; n++) {

                    if (obj1[n] != "") {

                        var myQueNumber = obj1[n].key;

                        if (obj1[n].checked == 'true')
                        {

                            myFinalRes[myQueNumber] = "true";

                        } else if (obj1[n].checked == 'false')
                        {

                            myFinalRes[myQueNumber] = "false";
                        }
                    }

                }

                var myNumber = 0;
                $.each(myFinalRes, function (index, value) {

                    myNumber = index + 1;
                    if (value == "true")
                    {
                        $(".result-" + getcat).append('<li class="answered"><span style="color: #a4a6a7;">' + (myNumber) + '</span></li>');
                    } else
                    {
                        $(".result-" + getcat).append('<li class="unanswered"><span style="background-color: #243588 !important;color: #a4a6a7;border: thin solid #a4a6a7;">' + (myNumber) + '</span></li>');
                    }

                });

            }

            // Function For Ans Count on Final Result
            function count_true_answer()
            {
                var allQuestion = [];

                for (var k = 0, len = sessionStorage.length; k < len; ++k) {
                    if (sessionStorage.key(k).substr(0, 1) == 'q') {
                        allQuestion.push(sessionStorage.getItem('q' + k));
                    }
                }
                var totalCount = 0;
                var totalCorrect = 0;

                for (var m = 0; m < allQuestion.length; m++) {
                    var objCount = JSON.parse(allQuestion[m]);
                    var cat_correct = 0;
                    var get_total_no_of_pass_criteria = jQuery('#cat-' + objCount[0].catID).find('#number_of_pass_criteria').text();
                    for (var i = 0; i < objCount.length; i++) {
                        if (objCount[i].correct == objCount[i].answer) {
                            cat_correct++;
                            totalCorrect++;
                        }
                    }
                    if (get_total_no_of_pass_criteria <= cat_correct) {
                        totalCount++;
                    }

                }
                if (totalCount >= allQuestion.length) {
                    jQuery('#thumb_down').hide();
                    jQuery('#thumb_up').show();
                    jQuery('#show_pass').show();
                    jQuery('#show_fail').hide();
                } else {
                    jQuery('#thumb_down').show();
                    jQuery('#thumb_up').hide();
                    jQuery('#show_pass').hide();
                    jQuery('#show_fail').show();
                }

                document.getElementById('result_actualCount').textContent = totalCorrect;
            }

            //Function For Final Result
            function result_page_answer()
            {
                var allQuestionresult = [];

                for (var k = 0, len = sessionStorage.length; k < len; ++k) {
                    if (sessionStorage.key(k).substr(0, 1) == 'q') {
                        allQuestionresult.push(JSON.parse(sessionStorage.getItem('q' + k)));
                    }
                }
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "<?php echo admin_url('admin-ajax.php'); ?>",
                    data: {action: "store_result_data_db", 'quiz_data': allQuestionresult},
                    beforeSend: function () {
                        $('#show_loader').show();
                        $('#loading').css('height', '80vh');
                        $('.timmer_inner').hide();
                    },
                    success: function (response) {
                        if (response.message == "Success") {

                            $('#result').html(response.html);
                            $('#show_loader').hide();
                            $('#loading').css('height', '');
                            $('.timmer_inner').show();
                        }
                    }
                });

            }
            

            function addNewShow(catName, catID, question, currentAnswer, defaultAns, checked, id, result_answer_Message, img_option, draggable_option, img) {
                // Get array from local storage, defaulting to empty array if it's not yet set

                var showList = JSON.parse(localStorage.getItem('cat-' + catID) || "[]");
                var show = {
                    category: catName,
                    catID: catID,
                    question: question,
                    answer: currentAnswer,
                    correct: defaultAns,
                    checked: checked,
                    key: id,
                    indexkey: 'cat-' + catID,
                    resultAnsDesc: result_answer_Message,
                    img_option: img_option,
                    draggable_option: draggable_option,
                    img: img
                }

                showList[id] = show;

                localStorage.setItem('cat-' + catID, JSON.stringify(showList));

            }
        </script>  

        <?php
    endwhile;
    wp_reset_postdata();
    $output_string = ob_get_contents();
    ob_end_clean();
    return $output_string;
}

add_shortcode('view_quiz', 'quiz_frontend_display');


function get_cat_names($cat_id) {
        $cat_id = (int) $cat_id;
        $category = get_term($cat_id, 'question-categories');

        if (!$category || is_wp_error($category)) {
            return '';
        }

        return $category->name;
    }

    function get_cat_slugs($cat_id) {
        $cat_id = (int) $cat_id;
        $category = get_term($cat_id, 'question-categories');

        if (!$category || is_wp_error($category)) {
            return '';
        }

        return $category->slug;
    }

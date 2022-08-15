<section class="timmer_section custom-padding-set" id="endQuiz" style="display: none;">
    <div class="container">
        <div class="timmer_inner">
            <div class="timmer_body">
                <div class="timmer_header">
                </div>
                <div class="timmer_content">
                    <div class="middle_time_box page_one_part">
                        <p><?php
                                $get_start_page = get_post_meta( $post->ID, 'quiz_end_page' , true ); 
                                echo wpautop($get_start_page);
                                ?></p>
                        <div class="page_one my_one_thmb">
                            <p>Bedankt voor het inleveren van het examen!.</p>
                        </div>
                        <div class="thumbs_part">
                            <h3 id="show_fail">Helaas gezakt</h3>
                            <h3 id="show_pass" style="display:none;">pas</h3>
                            
                            <div class="in_thumb">
                                <span id='thumb_down'><i class="fas fa-thumbs-down"></i></span>
                                <span  id='thumb_up' style="display:none;"><i class="fas fa-thumbs-up"></i></span>
                                <h4 id="result_count">Eindresultaat: <span id="result_actualCount"></span></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

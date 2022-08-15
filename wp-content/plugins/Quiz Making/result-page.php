<section class="timmer_section custom-padding-set" id="resultPage" style="display: none;">
    <div class="container">
        <div id="loading">
            <img id="show_loader" src="<?php echo plugin_dir_url( __FILE__ ).'/images/loader-result.gif'; ?>">    
        </div>
        <div class="timmer_inner">
            <div class="timmer_body">
                <div class="timmer_header">

                </div>
                <div class="theorie_final_content">
                    <div class="inner_theorie_content">
                        <div class="right_part_in_theorie_box">
                            <div class="right_in_theorie_hj">
                                <h2>Vragen nakijken</h2>
                                <p>Hieronder vind je de uitslag per onderdeel. Door een vakje te selecteren krijg je een uitgebreide motivatie.</p>
                            </div>
                            <div id="result"></div>
                        </div>
                        
                    </div>
                    <?php if(!empty(get_post_meta($_GET['id'], "next_quiz", true))){ ?>
                        <div class="btn_box next_quiz-btn align-center-btn" style="display:none;"><a class="next_quiz" href="<?php echo site_url(); ?>/paid-quiz/?id=<?php echo get_post_meta($_GET['id'], "next_quiz", true); ?>">Volgende quiz</a></div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</section>
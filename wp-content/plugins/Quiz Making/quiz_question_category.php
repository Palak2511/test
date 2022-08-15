<?php require_once("Tax-meta-class/Tax-meta-class.php");
$config = array(
   'id' => 'demo_meta_box',                         
   'title' => 'Demo Meta Box',                      
   'pages' => array('question-categories'),                    
   'context' => 'normal',                           
   'fields' => array(), // list of meta fields (can be added by field arrays)
   'local_images' => false,                         
   'use_with_theme' => false                        
);
$my_meta = new Tax_Meta_Class($config);
//wysiwyg field
$my_meta->addWysiwyg('wysiwyg_field_1',array('name'=> 'Category Start Page Description'));
$my_meta->addWysiwyg('wysiwyg_field_2',array('name'=> 'Category End Page Description'));
$my_meta->addText('cat_timer',array('name'=> 'Category Timer(In Sec)','desc' => 'If you want to show catgorywise Time then set value otherwise leave empty'));
$my_meta->addText('pass_criteria',array('name'=> 'Pass Criteria','desc' => 'What Number you add here, will be your Pass criteria for this category only!'));
$my_meta->addCheckbox('disable_cat_btn',array('name'=>"Disable 'Previous question button ?'",'desc'=>'If you want to disable previous button categorywise then check otherwise leave empty'));
/*
* To Create a reapeater Block first create an array of fields
* use the same functions as above but add true as a last param
*/
$repeater_fields[] = $my_meta->addWysiwyg('wysiwyg_field_1',array('name'=> 'Category Start Page Description '), true);
$repeater_fields[] = $my_meta->addWysiwyg('wysiwyg_field_2',array('name'=> 'Category End Page Description'), true);
$repeater_fields[] = $my_meta->addText('cat_timer',array('name'=> 'Category Timer'), true);
$repeater_fields[] = $my_meta->addText('pass_criteria',array('name'=> 'Pass Criteria'), true);
$repeater_fields[] = $my_meta->addCheckbox('disable_cat_btn',array('name'=>"Disable 'Previous question button ?'"),true);
/*
* Then just add the fields to the repeater block
*/
$my_meta->Finish();

if (!empty($term_id) ) {

   $saved_data = get_tax_meta($term_id,'wysiwyg_field_1');
   $saved_data2 = get_tax_meta($term_id,'wysiwyg_field_2');
   $saved_data3 = get_tax_meta($term_id,'cat_timer');
   $saved_data4 = get_tax_meta($term_id,'disable_cat_btn');
   $saved_data5 = get_tax_meta($term_id,'pass_criteria');
   //get current views count
   $saved_data = get_tax_meta($term_id,'MY_VIEWS_COUNTER');
   //add 1 to current views count
   $saved_data++;
   //update views count
update_tax_meta($term_id,'MY_VIEWS_COUNTER',$saved_data);

}

?>
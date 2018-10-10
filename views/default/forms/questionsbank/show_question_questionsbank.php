<div class="contentWrapper">

    <?php

    $questionsbankpost = $vars['questionsbankpost'];
    $questionsbank = get_entity($questionsbankpost);
    $index = $vars['index'];

    $options = array('relationship' => 'questionsbank_question', 'relationship_guid' => $questionsbankpost, 'inverse_relationship' => false, 'type' => 'object', 'subtype' => 'questionsbank_question', 'metadata_name_value_pairs' => array('name' => 'index', 'value' => $index));
    $questions = elgg_get_entities_from_relationship($options);
    $one_question = $questions[0];

    if (!empty($one_question)){
    $question = $one_question->question;
    $question_html = $one_question->question_html;
    $question_explanation = $one_question->question_explanation;
    $question_type = $one_question->question_type;
    switch ($question_type) {
        case 'urls_files':
            $question_urls = explode(Chr(26), $one_question->question_urls);
            $question_urls = array_map('trim', $question_urls);
            break;
    }
    $response_type = $one_question->response_type;
    
    switch ($response_type) {
        case 'radiobutton':
        case 'checkbox':
            $responses = explode(Chr(26), $one_question->responses);
            $responses = array_map('trim', $responses);    
            break;
        case 'grid':
            $responses_rows = explode(Chr(26),$one_question->responses_rows);
            $responses_rows = array_map('trim',$responses_rows);
            $responses_columns = explode(Chr(26),$one_question->responses_columns);
            $responses_columns = array_map('trim',$responses_columns);
            break;
        case 'pairs':
            $responses_left = explode(Chr(26),$one_question->responses_left);
            $responses_left = array_map('trim',$responses_left);
            $responses_right = explode(Chr(26),$one_question->responses_right);
            $responses_right = array_map('trim',$responses_right);
            break;
        case 'dropdown':
            $question_text = $one_question->question_text;
            $responses_dropdown = explode(Chr(26),$one_question->responses_dropdown);
            $responses_dropdown = array_map('trim',$responses_dropdown);
            $text_dropdown = $one_question->question_text;
            $numbers_responses_dropdowns = explode(",",$one_question->numbers_responses_dropdowns);            
            $numbers_responses_dropdowns = array_map('trim',$numbers_responses_dropdowns);
            break;
    }

    switch ($response_type) {
        case 'radiobutton':
            $correct_responses = explode(Chr(26), $one_question->correct_responses);
            $correct_responses = array_map('trim', $correct_responses);
            $number_correct_response = "";
            $i = 1;
            foreach ($responses as $one_response) {
                foreach ($correct_responses as $one_correct_response) {
                    if (strcmp($one_response, $one_correct_response) == 0) {
                        $number_correct_response .= $i;
                        break;
                    }
                }
                $i = $i + 1;
            }
            break;
        case 'checkbox':
            $correct_responses = explode(Chr(26), $one_question->correct_responses);
            $correct_responses = array_map('trim', $correct_responses);
            $numbers_correct_responses = "";
            $i = 0;
            $j = 0;
            foreach ($responses as $one_response) {
                foreach ($correct_responses as $one_correct_response) {
                    if (strcmp($one_response, $one_correct_response) == 0) {
                        $pos = $i + 1;
                        if ($j == 0) {
                            $numbers_correct_responses .= $pos;
                        } else {
                            $numbers_correct_responses .= "," . $pos;
                        }
                        $j = $j + 1;
                        break;
                    }
                }
                $i = $i + 1;
            }
            break;
        case 'grid': 
            $correct_responses=explode(Chr(26),$one_question->correct_responses);
            $correct_responses = array_map('trim', $correct_responses); 
            $numbers_correct_responses="";
            $j=0;
            foreach($correct_responses as $one_correct_response){
                $i=0;
                foreach($responses_columns as $one_response){
                    if (strcmp($one_response,$one_correct_response)==0){
                        $pos=$i+1;
                        if ($j==0) {
                            $numbers_correct_responses .= $pos;
                        } else { 
                            $numbers_correct_responses .= "," . $pos;
                        }
                        $j=$j+1;
                        break;
                    } 
                    $i=$i+1;
                }
            }
            break;
         case 'pairs': 
            $correct_responses=explode(Chr(27),$one_question->correct_responses);
            $correct_responses = array_map('trim', $correct_responses); 
            $numbers_correct_responses="";
    	    $pairs_array = array();  
    	    $i=0;              
    	    foreach($correct_responses as $one_correct_response){
                    $one_correct_response_array = explode(Chr(26),$one_correct_response);
    		$one_correct_response_array = array_map('trim',$one_correct_response_array);
    		$j=0;
    		foreach($responses_left as $one_response_left){
    		    if(strcmp($one_response_left,$one_correct_response_array[0])==0){
                            $left_new_pair = ($j+1)."-";
    			$k=0;
             		foreach($responses_right as $one_response_right){
    		            if(strcmp($one_response_right,$one_correct_response_array[1])==0){
                                    $new_pair = $left_new_pair . ($k+1);
    				if (!in_array($new_pair,$pairs_array)){
    				    $pairs_array[$i] = $new_pair;
    				    $i=$i+1;
    				}
             	            }
    			    $k=$k+1;
                            }
                        }
    		    $j=$j+1;
    		}
    	    }   
    	    $i=0;
    	    foreach ($pairs_array as $one_pair) {
                    if ($i==0)
    		    $numbers_correct_responses .= $one_pair;
    		else 
    		    $numbers_correct_responses .= "," . $one_pair;
    		$i=$i+1;
                }
    	    break;
        case 'dropdown': 
            $correct_responses=explode(Chr(26),$one_question->correct_responses);
            $correct_responses = array_map('trim', $correct_responses);
            $number_dropdowns = count($correct_responses);
            $numbers_correct_responses="";
            $j=0;
            foreach($correct_responses as $one_correct_response){
                if ($j==0) {
                    $numbers_correct_responses .= $one_correct_response;
                } else { 
                    $numbers_correct_responses .= "," . $one_correct_response;
                }
                $j=$j+1;
            }
            break;

    }
    $question_tags = $one_question->tags;

    $options_response_type = array();
    $options_response_type[2] = elgg_echo('questionsbank:response_type_radiobutton');
    $options_response_type[3] = elgg_echo('questionsbank:response_type_checkbox');
    $options_response_type[4]=elgg_echo('questionsbank:response_type_grid');
    $options_response_type[5]=elgg_echo('questionsbank:response_type_pairs');
    $options_response_type[6]=elgg_echo('questionsbank:response_type_dropdown');
    $op_response_type = array();
    $op_response_type[2] = "radiobutton";
    $op_response_type[3] = "checkbox";
    $op_response_type[4]="grid";
    $op_response_type[5]="pairs";
    $op_response_type[6]="dropdown";
    $checked_radio_response_type_2 = "";
    $checked_radio_response_type_3 = "";
    $checked_radio_response_type_4 = "";
    $checked_radio_response_type_5 = "";
    $checked_radio_response_type_6 = "";

    $style_display_response_type = "display:none";
    $style_display_response_grid_type = "display:none";
    $style_display_response_pairs_type = "display:none";
    $style_display_response_dropdown_type = "display:none";

    $style_display_available_correct_responses_radiobutton = "display:none";
    $style_display_available_correct_responses_checkbox = "display:none";
    $style_display_available_correct_responses_grid = "display:none";
    $style_display_available_correct_responses_pairs = "display:none";
    $style_display_available_correct_responses_dropdown = "display:none";

    switch ($response_type) {
        case 'radiobutton':
            $checked_radio_response_type_2 = "checked = \"checked\"";
            $style_display_response_type = "display:block";
            $style_display_available_correct_responses_radiobutton = "display:block";
            break;
        case 'checkbox':
            $checked_radio_response_type_3 = "checked = \"checked\"";
            $style_display_response_type = "display:block";
            $style_display_available_correct_responses_checkbox = "display:block";
            break;
        case 'grid':
            $checked_radio_response_type_4 = "checked = \"checked\"";
            $style_display_response_grid_type = "display:block";
            $style_display_available_correct_responses_grid = "display:block";
            break;
        case 'pairs':
            $checked_radio_response_type_5 = "checked = \"checked\"";
            $style_display_response_pairs_type = "display:block";
            $style_display_available_correct_responses_pairs = "display:block";
            break;
        case 'dropdown':
            $checked_radio_response_type_6 = "checked = \"checked\"";
            $style_display_response_dropdown_type = "display:block";
            $style_display_available_correct_responses_dropdown = "display:block";
            break;
    }

    ?>
    <form action="<?php echo elgg_get_site_url() . "action/" . $action ?>"
          name="show_question_questionsbank_questionsbank" enctype="multipart/form-data" method="post">
        <?php echo elgg_view('input/securitytoken'); ?>

        <p>
            <b><?php echo elgg_echo("questionsbank:question_label"); ?></b>
        </p>
        <div class="questionsbank_question_frame">
            <?php echo elgg_view("output/text", array('value' => $question)); ?>
        </div>
        <br>

        <?php
        if (strcmp($question_html, "") != 0) {
            ?>
            <p>
                <b><?php echo elgg_echo("questionsbank:question_simple_read"); ?></b>
            </p>
            <div class="questionsbank_question_frame">
                <?php echo elgg_view("output/longtext", array('value' => $question_html)); ?>
            </div>
            <?php
        }
        if (strcmp($question_explanation, "") != 0) {
            ?>
            <br>
            <p>
                <b><?php echo elgg_echo("questionsbank:question_explanation"); ?></b>
            </p>
            <div class="questionsbank_question_frame">
                <?php echo elgg_view("output/longtext", array('value' => $question_explanation)); ?>
            </div>
            <?php
        }

        switch ($question_type) {
            case 'urls_files':
                $files = elgg_get_entities_from_relationship(array('relationship' => 'question_file_link', 'relationship_guid' => $one_question->getGUID(), 'inverse_relationship' => false, 'type' => 'object', 'subtype' => 'questionsbank_question_file', 'limit' => 0));
                ?>
                <p>
                    <b><?php echo elgg_echo("questionsbank:question_urls_files_read"); ?></b>
                </p>
                <div class="questionsbank_question_frame">
                    <?php
                    if ((count($question_urls) > 0) && (strcmp($question_urls[0], "") != 0)) {
                        foreach ($question_urls as $url) {
                            $comp_url = explode(Chr(24), $url);
                            $comp_url = array_map('trim', $comp_url);
                            $url_name = $comp_url[0];
                            $url_value = $comp_url[1];
                            if (elgg_is_active_plugin("sw_embedlycards")) {
                                echo "<div>
                       <a class='embedly-card' href='" . $url_value . "'></a>
                       </div>";
                            } else if (elgg_is_active_plugin("hypeScraper"))
                                echo elgg_view('output/sw_url_preview', array('value' => $url_value,));
                            else
                                echo "<a rel=\"nofollow\" href=\"" . $url_value . "\" target=\"_blank\">" . $url_name . "</a></br>";
                        }
                    }
                    if ((count($files) > 0) && (strcmp($files[0]->title, "") != 0)) {
                        foreach ($files as $file) {
                            $params = $file->getGUID() . "_question";
                            $icon = questions_set_icon_url($file, "small");
                            $url_file = elgg_get_site_url() . "mod/questionsbank/download.php?params=$params";
                            $trozos = explode(".", $file->title);
                            $ext = strtolower(end($trozos));
                            if (($ext == 'jpg') || ($ext == 'png') || ($ext == 'gif') || ($ext == 'tif') || ($ext == 'tiff') || ($ext == 'jpeg'))
                                echo "<p align=\"center\"><a href=\"" . $url_file . "\">" . "<img src=\"" . $url_file . "\" width=\"600px\">" . "</a></p>";
                            else
                                echo "<p><a href=\"" . $url_file . "\">" . "<img src=\"" . elgg_get_site_url() . $icon . "\">" . $file->title . "</a></p>";
                        }
                    }
                    ?>
                </div>
                <?php
                break;
        }
        ?>
        <br>

        <p>
            <b><?php echo elgg_echo("questionsbank:response_type_label"); ?></b><br/>
            <?php echo "<input type=\"radio\" disabled name=\"response_type\" value=$op_response_type[2] $checked_radio_response_type_2>$options_response_type[2]"; ?>
            <br>
            <?php echo "<input type=\"radio\" disabled name=\"response_type\" value=$op_response_type[3] $checked_radio_response_type_3>$options_response_type[3]"; ?>
            <br>
            <?php echo "<input type=\"radio\" disabled name=\"response_type\" value=$op_response_type[4] $checked_radio_response_type_4>$options_response_type[4]";?>
            <br>
            <?php echo "<input type=\"radio\" disabled name=\"response_type\" value=$op_response_type[5] $checked_radio_response_type_5>$options_response_type[5]";?>
            <br>
            <?php echo "<input type=\"radio\" disabled name=\"response_type\" value=$op_response_type[6] $checked_radio_response_type_6>$options_response_type[6]";?>
            <br>
        </p>

        <div id="resultsDiv_response_type" style="<?php echo $style_display_response_type; ?>;">
            <p>
                <b><?php echo elgg_echo("questionsbank:possible_responses_label"); ?></b>
            </p>

            <p>
            <?php
                if (count($responses) > 0) {
                    foreach ($responses as $response) {
            ?>
                        <div class="questionsbank_question_frame">
                            <?php echo elgg_view("output/text", array('value' => $response)); ?>
                        </div>
            <?php
                    }
                }
            ?>
            </p>
        </div>

        <div id="resultsDiv_response_grid_type" style="<?php echo $style_display_response_grid_type;?>;">
            <p>
                <b><?php echo elgg_echo("questionsbank:rows_responses_label"); ?></b>
            </p>          

            <p>
            <?php
                if(count($responses_rows) > 0) {
                    foreach ($responses_rows as $response) {
            ?>
                        <div class="questionsbank_question_frame">
                            <?php echo elgg_view("output/text", array('value' => $response)); ?>
                        </div>
            <?php
                    }
                } 
            ?>
            </p>

            <p>
                <b><?php echo elgg_echo("questionsbank:columns_responses_label"); ?></b>
            </p>       

            <p>
            <?php
                if(count($responses_columns) > 0) {
                    foreach ($responses_columns as $response) {
            ?>
                        <div class="questionsbank_question_frame">
                            <?php echo elgg_view("output/text", array('value' => $response)); ?>
                        </div>
            <?php         
                    }
                } 
            ?>
            </p>
            <br>
        </div>

        <div id="resultsDiv_response_pairs_type" style="<?php echo $style_display_response_pairs_type;?>;">
            <p>
                <b><?php echo elgg_echo("questionsbank:left_responses_label"); ?></b>
            </p>          

            <p>
            <?php
                if(count($responses_left) > 0) {
                    foreach ($responses_left as $response) {
            ?>
                        <div class="questionsbank_question_frame">
                            <?php echo elgg_view("output/text", array('value' => $response)); ?>
                        </div>
            <?php
                    }
                } 
            ?>
            </p>

            <p>
                <b><?php echo elgg_echo("questionsbank:right_responses_label"); ?></b>
            </p>       

            <p>
            <?php
                if(count($responses_right) > 0) {
                    foreach ($responses_right as $response) {
            ?>
                        <div class="questionsbank_question_frame">
                            <?php echo elgg_view("output/text", array('value' => $response)); ?>
                        </div>
            <?php         
                    }
                } 
            ?>
            </p>
            <br>
        </div>

        <div id="resultsDiv_response_dropdown_type" style="<?php echo $style_display_response_dropdown_type;?>;">
            <p>
                <b><?php echo elgg_echo("questionsbank:text_type_dropdown"); ?></b>
            </p>
            <div class="questionsbank_question_frame">
                <?php echo elgg_view("output/text", array('value' => $question_text)); ?>
            </div>
            <br>
            <p>
                <b><?php echo elgg_echo("questionsbank:option_dropdown_responses_label"); ?></b>
            </p>          

            <p>
            <?php
                $temp_responses_dropdown=0;
                $temp_numbers_responses_dropdowns=$numbers_responses_dropdowns;
                if(count($responses_dropdown) > 0) {
                    for($i=0;$i<$number_dropdowns;$i++){
            ?>
                        <div class="questionsbank_question_frame">                        
                            <?php
                                echo("<p>".elgg_echo("questionsbank:question_label")." ".($i+1)."</p>"); 
                            ?>
            <?php          
                        for($j=0;$j<$temp_numbers_responses_dropdowns[$i];$j++){
            ?>
                            <div class="questionsbank_question_frame">
                                <?php echo elgg_view("output/text", array('value' => $responses_dropdown[$temp_responses_dropdown])); ?>
                            </div>
            <?php
                            $temp_responses_dropdown++;
                        }
            ?>
                        </div>
            <?php
                    }
                } 
            ?>
            </p>
            <br>
        </div>

        <div id="resultsDiv_available_correct_responses_radiobutton"
             style="<?php echo $style_display_available_correct_responses_radiobutton; ?>;">
            <p>
                <b><?php echo elgg_echo("questionsbank:number_correct_response_label"); ?></b>
            </p>
            <div class="questionsbank_question_frame">
                <?php
                    echo elgg_view("output/text", array('value' => $number_correct_response));
                ?>
            </div>
        </div>

        <div id="resultsDiv_available_correct_responses_checkbox"
             style="<?php echo $style_display_available_correct_responses_checkbox; ?>;">
            <p>
                <b><?php echo elgg_echo("questionsbank:numbers_correct_responses_label"); ?></b>
            </p>
            <div class="questionsbank_question_frame">
                <?php
                    echo elgg_view("output/text", array('value' => $numbers_correct_responses));
                ?>
            </div>
        </div>

        <div id="resultsDiv_available_correct_responses_grid" style="<?php echo $style_display_available_correct_responses_grid;?>;">    
            <p>
                <b><?php echo elgg_echo("questionsbank:number_correct_response_row_label"); ?></b>
            </p>
            <div class="questionsbank_question_frame">
                <?php
                    echo elgg_view("output/text", array('value' => $numbers_correct_responses));
                ?>
            </div>     
        </div>

        <div id="resultsDiv_available_correct_responses_pairs" style="<?php echo $style_display_available_correct_responses_pairs;?>;">    
            <p>
                <b><?php echo elgg_echo("questionsbank:pairs_correct_responses_label"); ?></b>
            </p>
            <div class="questionsbank_question_frame">
                <?php
                    echo elgg_view("output/text", array('value' => $numbers_correct_responses));
                ?>
            </div>     
        </div>

        <div id="resultsDiv_available_correct_responses_dropdown" style="<?php echo $style_display_available_correct_responses_dropdown;?>;">    
            <p>
                <b><?php echo elgg_echo("questionsbank:dropdown_correct_responses_label"); ?></b>
            </p>
            <div class="questionsbank_question_frame">
                <?php
                    echo elgg_view("output/text", array('value' => $numbers_correct_responses));
                ?>
            </div>     
        </div> 
        <br>
        <p>
            <b><?php echo elgg_echo("tags"); ?></b>
        </p>
        <div class="questionsbank_question_frame">
            <?php echo elgg_view("output/tags", array('tags' => $question_tags)); ?>
        </div>
    </form>

<?php
}
?>

<script type="text/javascript"
        src="<?php echo elgg_get_site_url(); ?>mod/questionsbank/lib/jquery.MultiFile.js"></script><!-- multi file jquery plugin -->
<script type="text/javascript"
        src="<?php echo elgg_get_site_url(); ?>mod/questionsbank/lib/reCopy.js"></script><!-- copy field jquery plugin -->
<script type="text/javascript" src="<?php echo elgg_get_site_url(); ?>mod/questionsbank/lib/js_functions.js"></script>
</div>


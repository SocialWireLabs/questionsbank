<div class="contentWrapper">

    <?php

    $questionsbankpost = $vars['entity']->getGUID();
    $action = "questionsbank/edit_question";
    $index = $vars['index'];

    $options = array('relationship' => 'questionsbank_question', 'relationship_guid' => $questionsbankpost, 'inverse_relationship' => false, 'type' => 'object', 'subtype' => 'questionsbank_question', 'metadata_name_value_pairs' => array('name' => 'index', 'value' => $index));
    $questions = elgg_get_entities_from_relationship($options);
    $one_question = $questions[0];

    if (!elgg_is_sticky_form('edit_question_questionsbank')) {
        $question = $one_question->question;
        $question_html = $one_question->question_html;
        $question_explanation = $one_question->question_explanation;
        $question_type = $one_question->question_type;
        switch ($question_type) {
            case 'urls_files':
                $question_comp_urls = explode(Chr(26), $one_question->question_urls);
                $question_comp_urls = array_map('trim', $question_comp_urls);
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
                $responses_dropdown = explode(Chr(26),$one_question->responses_dropdown);
                $responses_dropdown = array_map('trim',$responses_dropdown);
                $text_dropdown = $one_question->question_text;
                $numbers_responses_dropdowns = explode(",",$one_question->numbers_responses_dropdowns);            
                $numbers_responses_dropdowns = array_map('trim',$numbers_responses_dropdowns);
                $num_temp="";
                $i=0;
                foreach ($numbers_responses_dropdowns as $one_number) {
                    if($i==0)
                        $num_temp=$one_number;
                    else
                        $num_temp.=",".$one_number;
                    $i++;
                }
                $numbers_responses_dropdowns=$num_temp;
                $page_position = 1;
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
                $correct_responses = explode(Chr(26),$one_question->correct_responses);
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
                $correct_responses = explode(Chr(27),$one_question->correct_responses);
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

    } else {
        $question = elgg_get_sticky_value('edit_question_questionsbank', 'question');
        $question_html = elgg_get_sticky_value('edit_question_questionsbank', 'question_html');
        $question_explanation = elgg_get_sticky_value('edit_question_questionsbank', 'question_explanation');
        $question_type = elgg_get_sticky_value('edit_question_questionsbank', 'question_type');
        switch ($question_type) {
            case 'urls_files':
                $question_urls_names = elgg_get_sticky_value('edit_question_questionsbank', 'question_urls_names');
                $question_urls = elgg_get_sticky_value('edit_question_questionsbank', 'question_urls');
                $i = 0;
                $question_comp_urls = array();
                foreach ($question_urls as $url) {
                    $question_comp_urls[$i] = $question_urls_names[$i] . Chr(24) . $question_urls[$i];
                    $i = $i + 1;
                }
                break;
        }
        $response_type = elgg_get_sticky_value('edit_question_questionsbank', 'response_type');
        
        switch ($response_type) {
            case 'radiobutton':
            case 'checkbox':
                $responses = elgg_get_sticky_value('edit_question_questionsbank', 'responses');
                break;            
            case 'grid':
                $responses_rows = elgg_get_sticky_value('edit_question_questionsbank','responses_rows');
                $responses_columns = elgg_get_sticky_value('edit_question_questionsbank','responses_columns');
                break;
            case 'pairs':
                $responses_left = elgg_get_sticky_value('edit_question_questionsbank','responses_left');
                $responses_right = elgg_get_sticky_value('edit_question_questionsbank','responses_right');
                break;
            case 'dropdown':
                $text_dropdown = elgg_get_sticky_value('edit_question_questionsbank','text_dropdown');
                $numbers_responses_dropdowns = elgg_get_sticky_value('edit_question_questionsbank','numbers_responses_dropdowns');
                $number_dropdowns= elgg_get_sticky_value('edit_question_questionsbank','number_dropdowns');
                $page_position = elgg_get_sticky_value('edit_question_questionsbank','page_position');
                $responses_dropdown = elgg_get_sticky_value('edit_question_questionsbank','responses_dropdown');
                break;
        }

        switch ($response_type) {
            case 'radiobutton':
                $number_correct_response = elgg_get_sticky_value('edit_question_questionsbank', 'number_correct_response');
                break;
            case 'checkbox':
                $numbers_correct_responses = elgg_get_sticky_value('edit_question_questionsbank', 'numbers_correct_responses');
                break;
            case 'grid':
                $numbers_correct_responses = elgg_get_sticky_value('edit_question_questionsbank','grid_correct_responses');
                break;
            case 'pairs':
                $numbers_correct_responses = elgg_get_sticky_value('edit_question_questionsbank','pairs_correct_responses');
                break;
            case 'dropdown':
                $numbers_correct_responses = elgg_get_sticky_value('edit_question_questionsbank','dropdown_correct_responses');
                break;
        }
        $question_tags = elgg_get_sticky_value('edit_question_questionsbank', 'question_tags');
    }

    elgg_clear_sticky_form('edit_question_questionsbank');

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
    <form action="<?php echo elgg_get_site_url() . "action/" . $action ?>" name="edit_question_questionsbank"
          enctype="multipart/form-data" method="post">

        <?php echo elgg_view('input/securitytoken'); ?>

        <p>
            <b><?php echo elgg_echo("questionsbank:question_label"); ?></b><br/>
            <?php echo elgg_view("input/text", array("name" => "question", "value" => $question)); ?>
        </p>

        <p>
            <b><?php echo elgg_echo("questionsbank:form_question_simple"); ?></b>
            <?php echo elgg_view("input/longtext", array('name' => 'question_html', 'value' => $question_html)); ?>
        </p>
        <p>
            <b><?php echo elgg_echo("questionsbank:form_question_explanation"); ?></b><br/>
            <?php echo elgg_view("input/longtext", array('name' => 'question_explanation', 'value' => $question_explanation)); ?>
        </p>

        <?php
        switch ($question_type) {
            case 'urls_files':
                ?>
                <p>
                <b><?php echo elgg_echo("questionsbank:form_question_urls"); ?></b><br/>
                <?php
                if ((count($question_comp_urls) > 0) && (strcmp($question_comp_urls[0], "") != 0)) {
                    $i = 0;
                    foreach ($question_comp_urls as $url) {
                        ?>
                        <p class="clone_urls">
                            <?php
                            $comp_url = explode(Chr(24), $url);
                            $comp_url = array_map('trim', $comp_url);
                            $url_name = $comp_url[0];
                            $url_value = $comp_url[1];
                            echo("<b>" . elgg_echo("questionsbank:form_question_url_name") . "</b>");
                            echo elgg_view("input/text", array("name" => "question_urls_names[]", "value" => $url_name));
                            echo("<b>" . elgg_echo("questionsbank:form_question_url") . "</b>");
                            echo elgg_view("input/text", array("name" => "question_urls[]", "value" => $url_value));
                            if ($i > 0) {
                                ?>
                                <!-- remove url -->
                                <a class="remove" href="#"
                                   onclick="$(this).parent().slideUp(function(){ $(this).remove() }); return false"><?php echo elgg_echo("delete"); ?></a>
                                <?php
                            }
                            ?>
                        </p>
                        <?php
                        $i = $i + 1;
                    }
                } else {
                    ?>
                    <p class="clone_urls">
                        <?php
                        $comp_url = explode(Chr(24), $question_comp_urls);
                        $comp_url = array_map('trim', $comp_url);
                        $url_name = $comp_url[0];
                        $url_value = $comp_url[1];
                        echo("<b>" . elgg_echo("questionsbank:form_question_url_name") . "</b>");
                        echo elgg_view("input/text", array("name" => "question_urls_names[]", "value" => $url_name));
                        echo("<b>" . elgg_echo("questionsbank:form_question_url") . "</b>");
                        echo elgg_view("input/text", array("name" => "question_urls[]", "value" => $url_value));
                        ?>
                    </p>
                    <?php
                }
                ?>
                <!-- add link to add more urls which triggers a jquery clone function -->
                <a href="#" class="add" rel=".clone_urls"><?php echo elgg_echo("questionsbank:add_url"); ?></a>
                <br/><br/>
                </p>
                <?php $files = elgg_get_entities_from_relationship(array('relationship' => 'question_file_link', 'relationship_guid' => $one_question->getGUID(), 'inverse_relationship' => false, 'type' => 'object', 'subtype' => 'questionsbank_question_file', 'limit' => 0)); ?>
                <p>
                <b><?php echo elgg_echo("questionsbank:form_question_files"); ?></b><br/>
                <?php echo elgg_view("input/file", array('name' => 'upload[]', 'class' => 'multi')); ?>
                <?php
                foreach ($files as $file) {
                    ?>
                    <div class="file_wrapper">
                        <a class="bold"
                           onclick="changeFormValue(<?php echo $file->getGUID(); ?>), changeImage(<?php echo $file->getGUID(); ?>)">
                            <img id="image_<?php echo $file->getGUID(); ?>"
                                 src="<?php echo elgg_get_site_url(); ?>mod/questionsbank/graphics/tick.jpeg">
                        </a>
                        <span><?php echo $file->title ?></span>
                        <?php
                        echo elgg_view("input/hidden", array('name' => $file->getGUID(), 'internalid' => $file->getGUID(), 'value' => '0'));
                        ?>
                    </div>
                    <br/>
                    <?php
                }
                ?>
                </p>
                <?php
                break;
        }
        ?>

        <p>
            <b><?php echo elgg_echo("questionsbank:response_type_label"); ?></b><br/>
            <?php echo "<input type=\"radio\" name=\"response_type\" value=$op_response_type[2] $checked_radio_response_type_2 onclick=\"questionsbank_show_response_type(2)\">$options_response_type[2]"; ?>
            <br>
            <?php echo "<input type=\"radio\" name=\"response_type\" value=$op_response_type[3] $checked_radio_response_type_3 onclick=\"questionsbank_show_response_type(3)\">$options_response_type[3]"; ?>
            <br>
            <?php echo "<input type=\"radio\" name=\"response_type\" value=$op_response_type[4] $checked_radio_response_type_4 onclick=\"questionsbank_show_response_type(4)\">$options_response_type[4]";?>
            <br>
            <?php echo "<input type=\"radio\" name=\"response_type\" value=$op_response_type[5] $checked_radio_response_type_5 onclick=\"questionsbank_show_response_type(5)\">$options_response_type[5]";?>
            <br> 
            <?php echo "<input type=\"radio\" name=\"response_type\" value=$op_response_type[6] $checked_radio_response_type_6 onclick=\"questionsbank_show_response_type(6)\">$options_response_type[6]";?>
            <br> 
        </p>

        <div id="resultsDiv_response_type" style="<?php echo $style_display_response_type; ?>;">
            <p>
                <b><?php echo elgg_echo("questionsbank:possible_responses_label"); ?></b>
            </p>

            <p>
                <?php
                if (count($responses) > 0) {
                $i = 0;
                foreach ($responses as $response) {
                ?>
            <p class="clone">
                <?php
                echo elgg_view("input/text", array("name" => "responses[]", "value" => $response));
                if ($i > 0) {
                    ?>
                    <!-- remove response -->
                    <a class="remove" href="#"
                       onclick="$(this).parent().slideUp(function(){ $(this).remove() }); return false"><?php echo elgg_echo("delete"); ?></a>
                    <?php
                }
                ?>
            </p>
            <?php
            $i = $i + 1;
            }
            } else {
                ?>
                <p class="clone">
                    <?php
                    echo elgg_view("input/text", array("name" => "responses[]", "value" => $responses));
                    ?>
                </p>
                <?php
            }
            ?>
            <!-- add link to add more responses which triggers a jquery clone function -->
            <a href="#" class="add" rel=".clone"><?php echo elgg_echo("questionsbank:add_response"); ?></a>
            <br/><br/>
            </p>

        </div>

        <div id="resultsDiv_response_grid_type" style="<?php echo $style_display_response_grid_type;?>;">

            <p>
                <b><?php echo elgg_echo("questionsbank:rows_responses_label"); ?></b>
            </p>         
            <p>
            <?php
                if(count($responses_rows) > 0) {
                    $i=0;
                    foreach ($responses_rows as $response) {
            ?>
                        <p class="clone_rows">
                        <?php
                            echo elgg_view("input/text", array("name" => "responses_rows[]","value" => $response)); 
                            if ($i>0){ 
                        ?>                          
                                <!-- remove row -->
                                <a class="remove" href="#" onclick="$(this).parent().slideUp(function(){ $(this).remove() }); return false"><?php echo elgg_echo("delete"); ?></a>   
                        <?php
                            }
                        ?>
                        </p>   
            <?php 
                        $i=$i+1;
                    }
                } else {
            ?>
                    <p class="clone_rows">
                    <?php
                        echo elgg_view("input/text", array("name" => "responses_rows[]","value" => $responses_rows));
                    ?>     
                    </p>         
            <?php
                }
            ?>
                <!-- add link to add more rows which triggers a jquery clone function -->
                <a href="#" class="add" rel=".clone_rows"><?php echo elgg_echo("questionsbank:add_row"); ?></a>
                <br /><br />
            </p>

            <p>
                <b><?php echo elgg_echo("questionsbank:columns_responses_label"); ?></b>
            </p>         
            <p>
            <?php
                if(count($responses_columns) > 0) {
                    $i=0;
                    foreach ($responses_columns as $response) {
            ?>
                        <p class="clone_columns">
                        <?php
                            echo elgg_view("input/text", array("name" => "responses_columns[]","value" => $response)); 
                            if ($i>0){ 
                        ?>                          
                                <!-- remove column -->
                                <a class="remove" href="#" onclick="$(this).parent().slideUp(function(){ $(this).remove() }); return false"><?php echo elgg_echo("delete"); ?></a>   
                        <?php
                            }
                        ?>
                        </p>   
            <?php 
                        $i=$i+1;
                    }
                } else {
            ?> 
                    <p class="clone_columns">
                    <?php
                        echo elgg_view("input/text", array("name" => "responses_columns[]","value" => $responses_columns));
                    ?>     
                    </p>         
            <?php
                }
            ?>
                <!-- add link to add more columns which triggers a jquery clone function -->
                <a href="#" class="add" rel=".clone_columns"><?php echo elgg_echo("questionsbank:add_column"); ?></a>
                <br /><br />
            </p>
        </div>

        <div id="resultsDiv_response_pairs_type" style="<?php echo $style_display_response_pairs_type;?>;">

            <p>
                <b><?php echo elgg_echo("questionsbank:left_responses_label"); ?></b>
            </p>         
            <p>
            <?php
                if(count($responses_left) > 0) {
                    $i=0;
                    foreach ($responses_left as $response) {
            ?>
                        <p class="clone_left">
                        <?php
                            echo elgg_view("input/text", array("name" => "responses_left[]","value" => $response)); 
                            if ($i>0){ 
                        ?>                          
                                <!-- remove left -->
                                <a class="remove" href="#" onclick="$(this).parent().slideUp(function(){ $(this).remove() }); return false"><?php echo elgg_echo("delete"); ?></a>   
                        <?php
                            }
                        ?>
                        </p>   
            <?php 
                        $i=$i+1;
                    }
                } else {
            ?>
                    <p class="clone_left">
                    <?php
                        echo elgg_view("input/text", array("name" => "responses_left[]","value" => $responses_left));
                    ?>     
                    </p>         
            <?php
                }
            ?>
                <!-- add link to add more left items which triggers a jquery clone function -->
                <a href="#" class="add" rel=".clone_left"><?php echo elgg_echo("questionsbank:add_left"); ?></a>
                <br /><br />
            </p>

            <p>
                <b><?php echo elgg_echo("questionsbank:right_responses_label"); ?></b>
            </p>         
            <p>
            <?php
                if(count($responses_right) > 0) {
                    $i=0;
                    foreach ($responses_right as $response) {
            ?>
                        <p class="clone_right">
                        <?php
                            echo elgg_view("input/text", array("name" => "responses_right[]","value" => $response)); 
                            if ($i>0){ 
                        ?>                          
                                <!-- remove right -->
                                <a class="remove" href="#" onclick="$(this).parent().slideUp(function(){ $(this).remove() }); return false"><?php echo elgg_echo("delete"); ?></a>   
                        <?php
                            }
                        ?>
                        </p>   
            <?php 
                        $i=$i+1;
                    }
                } else {
            ?> 
                    <p class="clone_right">
                    <?php
                        echo elgg_view("input/text", array("name" => "responses_right[]","value" => $responses_right));
                    ?>     
                    </p>         
            <?php
                }
            ?>
                <!-- add link to add more righ itemst which triggers a jquery clone function -->
                <a href="#" class="add" rel=".clone_right"><?php echo elgg_echo("questionsbank:add_right"); ?></a>
                <br /><br />
            </p>
        </div>

        <div id="resultsDiv_response_dropdown_type" style="<?php echo $style_display_response_dropdown_type;?>;">
            <div id="questionbank_contenedor_principal">
                <div id="questionbank_contenedor1">
                    <textarea name="text_dropdown" id="question" cols="50" rows="10" placeholder="Inserte la pregunta" onfocus="listener();" onselect="cancelarSeleccion();" onmousedown="pulsado();" onmouseup="noPulsado();" onmousemove="moviendose();" oncontextmenu="return false;"><?php echo $text_dropdown; ?></textarea></br>
                    <input type="button" id="questionbank_agregar" value="<?php echo elgg_echo("questionsbank:add_dropdown");?>" onclick="agregarSelect()">
                </div>
                </br></br>
                <div id="questionbank_contenedor3">
                    <div id="questionbank_contenido3">
                    <?php
                        $temp_responses_dropdown=0;
                        $temp_numbers_responses_dropdowns=explode(",",$numbers_responses_dropdowns);
                        if(isset($number_dropdowns)&&$number_dropdowns>0){
                            for($i=0; $i<$number_dropdowns; $i++){
                                if(($i+1)==$page_position)
                                    $display="block";
                                else
                                    $display="none";
                    ?>
                                <div id='pregunta_<?php echo($i+1); ?>' style='display:<?php echo($display); ?>;'>
                                    <div id='questionbank_contenido_<?php echo($i+1); ?>'>
                                        <p id='p_<?php echo($i+1); ?>'><?php echo elgg_echo("questionsbank:number_question"); echo(" "); echo($i+1); ?></p>
                    <?php
                                for($j=0; $j<$temp_numbers_responses_dropdowns[$i]; $j++){
                                    if($j>1){
                    ?>
                                        <div id='div_<?php echo($i+1); ?>_<?php echo($j+1); ?>'>
                                            <input type='text' name='responses_dropdown[]' id='pregunta_<?php echo($i+1); ?>_<?php echo($j+1); ?>' class='responses_dropdown' value='<?php echo($responses_dropdown[$temp_responses_dropdown]); ?>'></br>
                                            <a href='javascript:eliminarOpcionSelect(<?php echo($j+1); ?>);'><?php echo elgg_echo("questionsbank:delete_option_dropdown"); ?></a></br>
                                        </div>
                    <?php                                        
                                    }
                                    else{
                    ?>
                                        <input type='text' name='responses_dropdown[]' id='pregunta_<?php echo($i+1); ?>_<?php echo($j+1); ?>' class='responses_dropdown' value='<?php echo($responses_dropdown[$temp_responses_dropdown]); ?>'></br>
                    <?php                
                                    }                    
                                    $temp_responses_dropdown++;
                                }
                    ?>
                                    </div>
                                    <div id='questionbank_actuadores_<?php echo($i+1); ?>'>                                
                                        <a href='javascript:agregarOpcionSelect();'><?php echo elgg_echo("questionsbank:add_option_dropdown"); ?></a></br></br></br></br>
                                        <a id='borrar_<?php echo($i+1); ?>' href='javascript:eliminarSelect(<?php echo($i+1); ?>);'><?php echo elgg_echo("questionsbank:delete_dropdown"); ?></a>
                                    </div>
                                </div>
                    <?php
                            }
                        }
                    ?>
                    </div>
                    <div id="questionbank_actuadores3">
                    <?php
                        if(isset($number_dropdowns)&&$number_dropdowns>0){
                            for($i=0; $i<$number_dropdowns; $i++){
                                if($i==0){
                    ?>
                                    <a href='javascript:anteriorPregunta();'>&lt;&lt;</a>                                        
                    <?php
                                }
                    ?>
                                <a href='javascript:preguntaNumero(<?php echo($i+1); ?>);'><?php echo($i+1); ?></a>
                    <?php
                                if($i==($number_dropdowns-1)){
                    ?>
                                    <a href='javascript:siguientePregunta();'>&gt;&gt;</a>
                    <?php
                                }
                            }
                        }
                    ?>
                    </div>
                </div>
                <input type="hidden" name="numbers_responses_dropdowns" id="numOptionSelect" value="<?php echo $numbers_responses_dropdowns; ?>">
                <input type="hidden" name="number_dropdowns" id="cantidadPreguntas" value="<?php echo $number_dropdowns; ?>">
                <input type="hidden" name="page_position" id="PosicionPagina" value="<?php echo $page_position; ?>">
                <input type="hidden" name="listenEvent" id="listenEvent" value="false">
                <input type="hidden" id="mouseDown" value="false">
                <input type="hidden" id="select" value="false">
            </div>
        </div>

        <div id="resultsDiv_available_correct_responses_radiobutton"
             style="<?php echo $style_display_available_correct_responses_radiobutton; ?>;">

            <p>
                <b><?php echo elgg_echo("questionsbank:number_correct_response_label"); ?></b>
                <?php echo "<input type = \"text\" name = \"number_correct_response\" value = $number_correct_response>"; ?>
            </p><br>

        </div>

        <div id="resultsDiv_available_correct_responses_checkbox"
             style="<?php echo $style_display_available_correct_responses_checkbox; ?>;">

            <p>
                <b><?php echo elgg_echo("questionsbank:numbers_correct_responses_label"); ?></b>
                <?php echo elgg_view("input/text", array('name' => 'numbers_correct_responses', 'value' => $numbers_correct_responses)); ?>
            </p><br>

        </div>

        <div id="resultsDiv_available_correct_responses_grid" style="<?php echo $style_display_available_correct_responses_grid;?>;">    

            <p>
                <b><?php echo elgg_echo("questionsbank:number_correct_response_row_label"); ?></b>
                <?php echo elgg_view("input/text", array('name' => 'grid_correct_responses','value' => $numbers_correct_responses)); ?>
            </p><br>

        </div>

        <div id="resultsDiv_available_correct_responses_pairs" style="<?php echo $style_display_available_correct_responses_pairs;?>;">    

            <p>
                <b><?php echo elgg_echo("questionsbank:pairs_correct_responses_label"); ?></b>
                <?php echo elgg_view("input/text", array('name' => 'pairs_correct_responses','value' => $numbers_correct_responses)); ?>
            </p><br>

        </div>

        <div id="resultsDiv_available_correct_responses_dropdown" style="<?php echo $style_display_available_correct_responses_dropdown;?>;">    

            <p>
                <b><?php echo elgg_echo("questionsbank:dropdown_correct_responses_label"); ?></b>
                <?php echo elgg_view("input/text", array('name' => 'dropdown_correct_responses','value' => $numbers_correct_responses)); ?>
            </p><br>

        </div>

        <p>
            <b><?php echo elgg_echo("tags"); ?></b>
            <?php echo elgg_view("input/tags", array('name' => 'question_tags', 'value' => $question_tags)); ?>
        </p><br>

        <!-- add the add_response/delete_response functionality  -->
        <script type="text/javascript">
            // remove function for the jquery clone plugin
            $(function () {
                var removeLink = '<a class="remove" href="#" onclick="$(this).parent().slideUp(function(){ $(this).remove() }); return false"><?php echo elgg_echo("delete");?></a>';
                $('a.add').relCopy({append: removeLink});
            });
        </script>

        <input type="hidden" name="questionsbankpost" value="<?php echo $questionsbankpost; ?>">
        <input type="hidden" name="question_type" value="<?php echo $question_type; ?>">
        <input type="hidden" name="index" value="<?php echo $index; ?>">

        <?php
        $submit_input = elgg_view('input/submit', array('name' => 'submit', 'value' => elgg_echo("questionsbank:save")));
        echo $submit_input
        ?>

    </form>

    <script language="javascript">
        function questionsbank_show_response_type(response_type_id) {
            var resultsDiv_response_type = document.getElementById('resultsDiv_response_type');
            var resultsDiv_response_grid_type = document.getElementById('resultsDiv_response_grid_type');
            var resultsDiv_response_pairs_type = document.getElementById('resultsDiv_response_pairs_type');
            var resultsDiv_response_dropdown_type = document.getElementById('resultsDiv_response_dropdown_type');

            var resultsDiv_available_correct_responses_radiobutton = document.getElementById('resultsDiv_available_correct_responses_radiobutton');
            var resultsDiv_available_correct_responses_checkbox = document.getElementById('resultsDiv_available_correct_responses_checkbox');
            var resultsDiv_available_correct_responses_grid = document.getElementById('resultsDiv_available_correct_responses_grid');
            var resultsDiv_available_correct_responses_pairs = document.getElementById('resultsDiv_available_correct_responses_pairs');
            var resultsDiv_available_correct_responses_dropdown = document.getElementById('resultsDiv_available_correct_responses_dropdown');

            if (response_type_id == 2) {
                if (resultsDiv_available_correct_responses_radiobutton.style.display == 'none') {
                    resultsDiv_available_correct_responses_radiobutton.style.display = 'block';
                    resultsDiv_available_correct_responses_checkbox.style.display = 'none';
                    resultsDiv_available_correct_responses_grid.style.display = 'none';
                    resultsDiv_available_correct_responses_pairs.style.display = 'none';
                    resultsDiv_available_correct_responses_dropdown.style.display = 'none';
                }
            }
            if (response_type_id == 3) {
                if (resultsDiv_available_correct_responses_checkbox.style.display == 'none') {
                    resultsDiv_available_correct_responses_checkbox.style.display = 'block';
                    resultsDiv_available_correct_responses_radiobutton.style.display = 'none';
                    resultsDiv_available_correct_responses_grid.style.display = 'none';
                    resultsDiv_available_correct_responses_pairs.style.display = 'none';
                    resultsDiv_available_correct_responses_dropdown.style.display = 'none';
                }
            }
            if (response_type_id == 4){
                if (resultsDiv_available_correct_responses_grid.style.display == 'none'){
                    resultsDiv_available_correct_responses_grid.style.display = 'block';
                    resultsDiv_available_correct_responses_radiobutton.style.display = 'none';
                    resultsDiv_available_correct_responses_checkbox.style.display = 'none';
                    resultsDiv_available_correct_responses_pairs.style.display = 'none';
                    resultsDiv_available_correct_responses_dropdown.style.display = 'none';
                }
            }
            if (response_type_id == 5){
                if (resultsDiv_available_correct_responses_pairs.style.display == 'none'){
                    resultsDiv_available_correct_responses_pairs.style.display = 'block';
                    resultsDiv_available_correct_responses_radiobutton.style.display = 'none';
                    resultsDiv_available_correct_responses_checkbox.style.display = 'none';
                    resultsDiv_available_correct_responses_grid.style.display = 'none';
                    resultsDiv_available_correct_responses_dropdown.style.display = 'none';
                }
            }
            if (response_type_id == 6){
                if (resultsDiv_available_correct_responses_dropdown.style.display == 'none'){
                    resultsDiv_available_correct_responses_dropdown.style.display = 'block';
                    resultsDiv_available_correct_responses_radiobutton.style.display = 'none';
                    resultsDiv_available_correct_responses_checkbox.style.display = 'none';
                    resultsDiv_available_correct_responses_grid.style.display = 'none';     
                    resultsDiv_available_correct_responses_pairs.style.display = 'none';               
                }
            }

            if (resultsDiv_response_type.style.display == 'none') {
                if ((response_type_id == 2) || (response_type_id == 3))
                    resultsDiv_response_type.style.display = 'block';
            } else {
                if ((response_type_id != 2) && (response_type_id != 3))
                    resultsDiv_response_type.style.display = 'none';
            }

            if (resultsDiv_response_grid_type.style.display == 'none'){
                if (response_type_id == 4) 
                    resultsDiv_response_grid_type.style.display = 'block';
            } else {   
                if (response_type_id != 4)
                    resultsDiv_response_grid_type.style.display = 'none';
            }

            if (resultsDiv_response_pairs_type.style.display == 'none'){
                if (response_type_id == 5) 
                    resultsDiv_response_pairs_type.style.display = 'block';
            } else {   
                if (response_type_id != 5)
                    resultsDiv_response_pairs_type.style.display = 'none';
            }

            if (resultsDiv_response_dropdown_type.style.display == 'none'){
                if (response_type_id == 6) 
                    resultsDiv_response_dropdown_type.style.display = 'block';
            } else {   
                if (response_type_id != 6)
                    resultsDiv_response_dropdown_type.style.display = 'none';
            }
        }

    </script>


    <script type="text/javascript"
            src="<?php echo elgg_get_site_url(); ?>mod/questionsbank/lib/jquery.MultiFile.js"></script>
    <!-- multi file jquery plugin -->
    <script type="text/javascript" src="<?php echo elgg_get_site_url(); ?>mod/questionsbank/lib/reCopy.js"></script>
    <!-- copy field jquery plugin -->
    <script type="text/javascript"
            src="<?php echo elgg_get_site_url(); ?>mod/questionsbank/lib/js_functions.js"></script>
    <script type="text/javascript">
        function changeImage(num) {
            if (document.getElementById('image_' + num).src == "<?php echo elgg_get_site_url(); ?>mod/questionsbank/graphics/tick.jpeg")
                document.getElementById('image_' + num).src = "<?php echo elgg_get_site_url(); ?>mod/questionsbank/graphics/delete_file.jpeg";
            else
                document.getElementById('image_' + num).src = "<?php echo elgg_get_site_url(); ?>mod/questionsbank/graphics/tick.jpeg";
        }
    </script>
    <script type="text/javascript"
            src="<?php echo elgg_get_site_url(); ?>mod/questionsbank/lib/tipopregunta.js"></script>

</div>


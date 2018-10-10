<div class="contentWrapper">

    <?php

    $questionsbankpost = $vars['entity']->getGUID();
    $action = "questionsbank/add_question";

    if (!elgg_is_sticky_form('add_question_questionsbank')) {
        $question = "";
        $question_html = "";
        $question_explanation = "";
        $question_type = $vars['question_type'];
        switch ($question_type) {
            case 'urls_files':
                $question_urls = array();
                break;
        }
        $response_type = "radiobutton";
        $responses = array();
        $responses_rows = array();
        $responses_columns = array();
        $responses_left = array();
        $responses_right = array();
        $responses_dropdown = array();
        $numbers_correct_responses = "";
        $number_correct_response = "";
        $question_tags = "";
        $text_dropdown = "";
        $numbers_responses_dropdowns = "";
        $number_dropdowns= 0;
        $page_position = 0;

    } else {
        $question = elgg_get_sticky_value('add_question_questionsbank', 'question');
        $question_html = elgg_get_sticky_value('add_question_questionsbank', 'question_html');
        $question_explanation = elgg_get_sticky_value('add_question_questionsbank', 'question_explanation');
        $question_type = elgg_get_sticky_value('add_question_questionsbank', 'question_type');
        switch ($question_type) {
            case 'urls_files':
                $question_urls_names = elgg_get_sticky_value('add_question_questionsbank', 'question_urls_names');
                $question_urls = elgg_get_sticky_value('add_question_questionsbank', 'question_urls');
                $i = 0;
                $question_comp_urls = array();
                foreach ($question_urls as $url) {
                    $question_comp_urls[$i] = $question_urls_names[$i] . Chr(24) . $question_urls[$i];
                    $i = $i + 1;
                }
                break;
        }
        $response_type = elgg_get_sticky_value('add_question_questionsbank', 'response_type');;
        
        switch ($response_type) {
            case 'radiobutton':
            case 'checkbox':
                $responses = elgg_get_sticky_value('add_question_questionsbank', 'responses');
                break;          
            case 'grid':
                $responses_rows = elgg_get_sticky_value('add_question_questionsbank','responses_rows');
                $responses_columns = elgg_get_sticky_value('add_question_questionsbank','responses_columns');
                break;
            case 'pairs':
                $responses_left = elgg_get_sticky_value('add_question_questionsbank','responses_left');
                $responses_right = elgg_get_sticky_value('add_question_questionsbank','responses_right');
                break;
            case 'dropdown':
                $text_dropdown = elgg_get_sticky_value('add_question_questionsbank','text_dropdown');
                $numbers_responses_dropdowns = elgg_get_sticky_value('add_question_questionsbank','numbers_responses_dropdowns');
                $number_dropdowns= elgg_get_sticky_value('add_question_questionsbank','number_dropdowns');
                $page_position = elgg_get_sticky_value('add_question_questionsbank','page_position');
                $responses_dropdown = elgg_get_sticky_value('add_question_questionsbank','responses_dropdown');
                break;
        }

        switch ($response_type) {
            case 'radiobutton':
                $number_correct_response = elgg_get_sticky_value('add_question_questionsbank', 'number_correct_response');
                break;
            case 'checkbox':
                $numbers_correct_responses = elgg_get_sticky_value('add_question_questionsbank', 'numbers_correct_responses');
                break;
            case 'grid':
                $numbers_correct_responses = elgg_get_sticky_value('add_question_questionsbank', 'grid_correct_responses');
                break;
            case 'pairs':
                $numbers_correct_responses = elgg_get_sticky_value('add_question_questionsbank', 'pairs_correct_responses');
                break;
            case 'dropdown':
                $numbers_correct_responses = elgg_get_sticky_value('add_question_questionsbank', 'dropdown_correct_responses');
                break;
        }
        $question_tags = elgg_get_sticky_value('add_question_questionsbank', 'question_tags');
    }

    elgg_clear_sticky_form('add_question_questionsbank');

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
    <br/>
    <?php

    $tabs = array('simple' => array('title' => elgg_echo('questionsbank:question_simple'), 'url' => '?question_type=simple', 'selected' => $question_type == 'simple'),
        'urls_files' => array('title' => elgg_echo('questionsbank:question_urls_files'), 'url' => '?question_type=urls_files', 'selected' => $question_type == 'urls_files'));

    echo elgg_view('navigation/tabs', array('tabs' => $tabs));

    ?>
    <br/>

    <form action="<?php echo elgg_get_site_url() . "action/" . $action ?>" name="add_question_questionsbank"
          enctype="multipart/form-data" method="post">

        <?php echo elgg_view('input/securitytoken'); ?>

        <p>
            <b><?php echo elgg_echo("questionsbank:question_label"); ?></b><br/>
            <?php
            echo elgg_view("input/text", array('name' => 'question', 'value' => $question));
            ?>
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
                <p>
                    <b><?php echo elgg_echo("questionsbank:form_question_files"); ?></b><br/>
                    <?php echo elgg_view("input/file", array('name' => 'upload[]', 'class' => 'multi')); ?>
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
                                <!-- remove item left -->
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
                <!-- add link to add more right items which triggers a jquery clone function -->
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
                <?php echo elgg_view("input/text", array("name" => "numbers_correct_responses", "value" => $numbers_correct_responses)); ?>
            </p><br>

        </div>

        <div id="resultsDiv_available_correct_responses_grid" style="<?php echo $style_display_available_correct_responses_grid;?>;">   

            <p>
                <b><?php echo elgg_echo("questionsbank:number_correct_response_row_label"); ?></b>
                <?php echo elgg_view("input/text", array("name" => "grid_correct_responses","value" => $numbers_correct_responses)); ?>
            </p><br>

        </div>

        <div id="resultsDiv_available_correct_responses_pairs" style="<?php echo $style_display_available_correct_responses_pairs;?>;">   

            <p>
                <b><?php echo elgg_echo("questionsbank:pairs_correct_responses_label"); ?></b>
                <?php echo elgg_view("input/text", array("name" => "pairs_correct_responses","value" => $numbers_correct_responses)); ?>
            </p><br>

        </div>

        <div id="resultsDiv_available_correct_responses_dropdown" style="<?php echo $style_display_available_correct_responses_dropdown;?>;">

            <p>
                <b><?php echo elgg_echo("questionsbank:dropdown_correct_responses_label"); ?></b>
                <?php echo elgg_view("input/text", array("name" => "dropdown_correct_responses","value" => $numbers_correct_responses)); ?>
            </p><br>

        </div>

        <p>
            <b><?php echo elgg_echo("tags"); ?></b>
            <?php echo elgg_view("input/tags", array('name' => 'question_tags', 'value' => $question_tags)); ?>
        </p><br>

        <!-- add the add/delete functionality  -->
        <script type="text/javascript">
            // remove function for the jquery clone plugin
            $(function () {
                var removeLink = '<a class="remove" href="#" onclick="$(this).parent().slideUp(function(){ $(this).remove() }); return false"><?php echo elgg_echo("delete");?></a>';
                $('a.add').relCopy({append: removeLink});
            });
        </script>

        <input type="hidden" name="questionsbankpost" value="<?php echo $questionsbankpost; ?>">
        <input type="hidden" name="question_type" value="<?php echo $question_type; ?>">

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
    <script type="text/javascript"
            src="<?php echo elgg_get_site_url(); ?>mod/questionsbank/lib/tipopregunta.js"></script>
</div>

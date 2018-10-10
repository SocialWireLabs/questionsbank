<?php

gatekeeper();

$thisquestionsbankpost = get_input('thisquestionsbankpost');
$questionsbank=get_entity($thisquestionsbankpost);
$number_questions = get_input('number_questions');
$action = get_input('action');

$container_guid = get_entity($thisquestionsbankpost)->container_guid;
$container = get_entity($container_guid);

$user_guid = elgg_get_logged_in_user_guid();

// Cache to the session
elgg_make_sticky_form('download_questions_questionsbank');


if (!empty($number_questions)) {
    $number_questions_array=explode(",",$number_questions);
    foreach ($number_questions_array as $one_number_question) {
        $temp_one_number_question=explode("-",$one_number_question);
        $temp_one_number_question=array_map("trim",$temp_one_number_question);
        foreach ($temp_one_number_question as $one_number) {
            $is_integer = true;
            $mask_integer = '^([[:digit:]]+)$';
            if (ereg($mask_integer, $one_number, $same)) {
                if ((substr($same[1], 0, 1) == 0) && (strlen($same[1]) != 1)) {
                    $is_integer = false;
                }
            } else {
                $is_integer = false;
            }
            if (!$is_integer) {
                register_error(elgg_echo("questionsbank:bad_number_questions_download_upload"));
                forward("questionsbank/download_questions/$thisquestionsbankpost");
            }
        }
    }
}
else
    $number_questions="all";
$number_questions=explode(",",$number_questions);

// Remove the questionsbank post cache
elgg_clear_sticky_form('download_questions_questionsbank');

if($action=="download"){
    $options = array('relationship' => 'questionsbank_question', 'relationship_guid' => $thisquestionsbankpost, 'inverse_relationship' => false, 'type' => 'object', 'subtype' => 'questionsbank_question', 'limit' => 0, 'order_by' => 'e.time_created asc');
    $questions = elgg_get_entities_from_relationship($options);

    $file_content="";
    if (!empty($questions)) {
        $count = count($questions);
        $temp_count_question=0;
        foreach ($questions as $one_question) {
            $temp_count_question++;
            $next_question=true;
            foreach ($number_questions as $one_question_array) {
                $one_question_array=explode("-",$one_question_array);
                if(count($one_question_array)>1){
                    if($one_question_array[0]>$count||$one_question_array[1]>$count){
                        register_error(elgg_echo("questionsbank:bad_number_questions_download_upload"));
                        forward("questionsbank/download_questions/$thisquestionsbankpost");
                    }
                    if(($one_question_array[0]<=$temp_count_question&&$one_question_array[1]>=$temp_count_question)||($one_question_array[0]>=$temp_count_question&&$one_question_array[1]<=$temp_count_question)){
                        $next_question=false;
                        break;
                    }
                }
                else{
                    if($one_question_array[0]>$count){
                        register_error(elgg_echo("questionsbank:bad_number_questions_download_upload"));
                        forward("questionsbank/download_questions/$thisquestionsbankpost");
                    }
                    if($one_question_array[0]=="all"||$temp_count_question==$one_question_array[0]){
                        $next_question=false;
                        break;
                    }
                }
            }
            if (strcmp($one_question->question_type, "urls_files") == 0) {
                $next_question=true;
            }
            if(!$next_question){
                switch ($one_question->response_type) {
                    case 'radiobutton':
                        $responses=$one_question->responses;
                        $responses_array=explode(Chr(26), $responses);
                        $responses_array=array_map("trim",$responses_array);
                        $correct_responses=$one_question->correct_responses;
                        $number_responses=count($responses_array);
                        $file_content.=trim(char_to_escape($one_question->question))." {";
                        $i=1;
                        foreach ($responses_array as $one_response) {
                            if($i==1){
                                if($one_response==$correct_responses)
                                    $file_content.="=".char_to_escape($one_response);
                                else
                                    $file_content.="~".char_to_escape($one_response);
                            }
                            else{
                                if($one_response==$correct_responses)
                                    $file_content.=" =".char_to_escape($one_response);
                                else
                                    $file_content.=" ~".char_to_escape($one_response);
                            }
                            if($i==$number_responses)
                                $file_content.="}\r\n\r\n";
                            $i++;
                        }
                        break;
                    case 'checkbox':
                        $correct=false;
                        $responses=$one_question->responses;
                        $responses_array=explode(Chr(26), $responses);
                        $responses_array=array_map("trim",$responses_array);
                        $correct_responses=$one_question->correct_responses;
                        $correct_responses_array=explode(Chr(26), $correct_responses);
                        $correct_responses_array=array_map("trim",$correct_responses_array);
                        $number_responses=count($responses_array);
                        $file_content.=trim(char_to_escape($one_question->question))." {\r\n";
                        $i=1;
                        foreach ($responses_array as $one_response) {
                            foreach ($correct_responses_array as $one_correct_response) {
                                if($one_response==$one_correct_response){
                                    $correct=true;
                                    break;
                                }
                            }
                            if($correct)
                                $file_content.="=".char_to_escape($one_response);
                            else
                                $file_content.="~".char_to_escape($one_response);
                            if($i==$number_responses)
                                $file_content.="}\r\n\r\n";
                            else
                                $file_content.="\r\n";    
                            $correct=false;
                            $i++;
                        }
                        break;
                    case 'grid':
                        $responses_rows=$one_question->responses_rows;
                        $responses_rows_array=explode(Chr(26), $responses_rows);
                        $responses_rows_array=array_map("trim",$responses_rows_array);
                        $responses_columns=$one_question->responses_columns;
                        $responses_columns_array=explode(Chr(26), $responses_columns);
                        $responses_columns_array=array_map("trim",$responses_columns_array);
                        $correct_responses=$one_question->correct_responses;
                        $correct_responses_array=explode(Chr(26), $correct_responses);
                        $correct_responses_array=array_map("trim",$correct_responses_array);
                        $file_content.=trim(char_to_escape($one_question->question))."\r\n";
                        $temp_count_rows=0;
                        $count_columns=count($responses_columns_array);
                        foreach ($responses_rows_array as $one_response_row) {
                            $file_content.=char_to_escape($one_response_row)." {\r\n";
                            $temp_count_columns=0;
                            foreach ($responses_columns_array as $one_response_column) {
                                $temp_count_columns++;
                                if($temp_count_columns==$count_columns){
                                    if($one_response_column==$correct_responses_array[$temp_count_rows])
                                        $file_content.="=".char_to_escape($one_response_column)."}\r\n";
                                    else
                                        $file_content.="~".char_to_escape($one_response_column)."}\r\n";
                                }                        
                                else{
                                    if($one_response_column==$correct_responses_array[$temp_count_rows])
                                        $file_content.="=".char_to_escape($one_response_column)."\r\n";
                                    else
                                        $file_content.="~".char_to_escape($one_response_column)."\r\n";
                                }
                            }
                            $temp_count_rows++;            
                        }
                         $file_content.="\r\n";
                        break;
                    case 'pairs':
                        $responses_left=$one_question->responses_left;
                        $responses_left_array=explode(Chr(26), $responses_left);
                        $responses_left_array=array_map("trim",$responses_left_array);
                        $responses_right=$one_question->responses_right;
                        $responses_right_array=explode(Chr(26), $responses_right);
                        $responses_right_array=array_map("trim",$responses_right_array);
                        $correct_responses=$one_question->correct_responses;
                        $correct_responses_array=explode(Chr(27), $correct_responses);
                        $correct_responses_array=array_map("trim",$correct_responses_array  );                
                        $file_content.=trim(char_to_escape($one_question->question))." {\r\n";
                        $array_temp=array();
                        foreach ($responses_left_array as $one_responses_left) {
                            $array_temp[count($array_temp)]=$one_responses_left;
                            $count_one_temp=0;
                            foreach ($array_temp as $one_temp) {
                                if($one_temp==$one_responses_left)
                                    $count_one_temp++;
                            }
                            $count_correct_temp=0;
                            foreach ($correct_responses_array as $one_correct_responses_array) {
                                $one_correct_responses_array=explode(Chr(26), $one_correct_responses_array);
                                if($one_correct_responses_array[0]==$one_responses_left)
                                    $count_correct_temp++;
                                if($count_one_temp==$count_correct_temp){
                                    $file_content.="=".char_to_escape($one_responses_left)." -> ".char_to_escape($one_correct_responses_array[1])."\r\n";
                                    break;
                                }
                            }
                        }
                        $file_content.="}\r\n\r\n";
                        break;
                    case 'dropdown':
                        $index_responses_dropdown=0;
                        $responses_dropdown=$one_question->responses_dropdown;
                        $responses_dropdown_array=explode(Chr(26), $responses_dropdown);
                        $responses_dropdown_array=array_map("trim",$responses_dropdown_array);
                        $correct_responses=$one_question->correct_responses;
                        $correct_responses_array=explode(Chr(26), $correct_responses);
                        $correct_responses_array=array_map("trim",$correct_responses_array);
                        $numbers_responses_dropdowns=$one_question->numbers_responses_dropdowns;
                        $numbers_responses_dropdowns_array=explode(",", $numbers_responses_dropdowns);
                        $numbers_responses_dropdowns_array=array_map("trim",$numbers_responses_dropdowns_array);
                        $question_text=trim($one_question->question_text);
                        $file_content.=trim(char_to_escape($one_question->question))."\r\n";
                        $start=0;
                        $i=0;
                        do{
                            $question_position=strpos($question_text,"(?)",$start);
                            $temp_question_text=substr($question_text, $start, $question_position-$start);
                            $start=$question_position+3;
                            $file_content.=char_to_escape($temp_question_text)."{";
                            for($j=0;$j<$numbers_responses_dropdowns_array[$i];$j++){
                                if($j==0){
                                    if(($j+1)==$correct_responses_array[$i])
                                        $file_content.="=".char_to_escape($responses_dropdown_array[$index_responses_dropdown]);
                                    else
                                        $file_content.="~".char_to_escape($responses_dropdown_array[$index_responses_dropdown]);
                                }
                                else{
                                    if(($j+1)==$correct_responses_array[$i])
                                        $file_content.=" =".char_to_escape($responses_dropdown_array[$index_responses_dropdown]);
                                    else
                                        $file_content.=" ~".char_to_escape($responses_dropdown_array[$index_responses_dropdown]);
                                }
                                $index_responses_dropdown++;
                            }
                            $file_content.="}";
                            $i++;
                        }while(strpos($question_text,"(?)",$start));
                        $temp_question_text=substr($question_text, $start, strlen($question_position)-$start);
                        $file_content.=char_to_escape($temp_question_text)."\r\n\r\n";
                        break;
                }
            }
        }
    }

    $file = new ElggFile();
    $file->owner_guid = elgg_get_logged_in_user_guid();
    $path="file/".time().".txt";
    $file->setFilename($path);
    $file->open('write');
    $file->write($file_content);
    $file->close();

    $ruta=$file->getFilenameOnFilestore();

    if (is_file($ruta)){
        header('Content-Type: text/html; charset=UTF-8');
        header('Content-Disposition: attachment; filename="'.basename($file->getFilenameOnFilestore()).'"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: '.filesize($file->getFilenameOnFilestore()));
        $contents = $file->grabFile();
        $splitString = str_split($contents, 8192);
        foreach ($splitString as $chunk)
            echo $chunk;
        $file->delete();
        system_message("questionsbank:file_download");
        exit();
    }
    else
        exit();
}
else{
    $ruta=elgg_get_data_path ()."1/".elgg_get_logged_in_user_guid()."/file/";
    $ruta=$ruta.basename($_FILES['uploadedfile']['name']); 
    if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $ruta)) { 
        $file = new ElggFile();
        $file->owner_guid = elgg_get_logged_in_user_guid();
        $file->setFilename("file/".basename($_FILES['uploadedfile']['name']));
        $file_content= file_get_contents($file->getFilenameOnFilestore());
        $file->delete();

        $num_questions=substr_count($file_content, "\r\n\r\n");
        $start=0;
        $temp_count_question=0;
        do{
            $cursor_position=strpos($file_content,"\r\n\r\n",$start);        
            $one_question=substr($file_content,$start,$cursor_position-$start);
            $start=$cursor_position+4;
            $responses="";
            $correct_responses="";
            $temp_count_question++;
            $next_question=true;
            foreach ($number_questions as $one_question_array) {
                $one_question_array=explode("-",$one_question_array);
                if(count($one_question_array)>1){
                    if($one_question_array[0]>$num_questions||$one_question_array[1]>$num_questions){
                        register_error(elgg_echo("questionsbank:bad_number_questions_download_upload"));
                        forward("questionsbank/download_questions/$thisquestionsbankpost");
                    }
                    if(($one_question_array[0]<=$temp_count_question&&$one_question_array[1]>=$temp_count_question)||($one_question_array[0]>=$temp_count_question&&$one_question_array[1]<=$temp_count_question)){
                        $next_question=false;
                        break;
                    }
                }
                else{
                    if($one_question_array[0]!="all" && $one_question_array[0]>$num_questions){
                        register_error(elgg_echo("questionsbank:bad_number_questions_download_upload"));
                        forward("questionsbank/download_questions/$thisquestionsbankpost");
                    }
                    if($one_question_array[0]=="all"||$temp_count_question==$one_question_array[0]){
                        $next_question=false;
                        break;
                    }
                }
            }
            if(!$next_question){
                $one_row=substr($one_question, 0,strpos($one_question,"\r\n"));
                if($one_row=="")
                    $one_row=$one_question;
                if(!strpos($one_row,"{")){
                    //GRID O DROPDOWN
                    $temp_start=strpos($one_question,"{")+3;
                    $end=strpos($one_question,"}");
                    $temp_one_question=substr($one_question,$temp_start+1,$end-$temp_start-1);
                    if(strpos($temp_one_question,"\r\n"))
                        $response_type="grid";
                    else
                        $response_type="dropdown";
                }
                else{
                    //RADIOBUTTON, CHECKBOX, PAIRS
                    if(substr_count($one_question, "\r\n")==0)
                        $response_type="radiobutton";
                    else{
                        $temp_start=0;
                        for($i=0;$i<2;$i++){
                            $temp_cursor_position=strpos($one_question,"\r\n",$temp_start);
                            $temp_start=$temp_cursor_position+2;    
                        }
                        $temp_one_question=substr($one_question,strpos($one_question,"\r\n"),$temp_start-strpos($file_content,"\r\n"));
                        if(strpos($temp_one_question, "=")==0 || strpos($temp_one_question, "~")==0 && !strpos($temp_one_question, "->"))
                            $response_type="checkbox";
                        else
                            $response_type="pairs";
                    }
                }

                //Create new question
                $options = array('relationship' => 'questionsbank_question', 'relationship_guid' => $thisquestionsbankpost, 'inverse_relationship' => false, 'type' => 'object', 'subtype' => 'questionsbank_question', 'limit' => 0);
                $questions = elgg_get_entities_from_relationship($options);
                if (empty($questions)) {
                    $index = 0;
                } else {
                    $index = count($questions);
                }
                $question = new ElggObject();
                $question->subtype = "questionsbank_question";
                $question->owner_guid = elgg_get_logged_in_user_guid();
                $question->container_guid = $questionsbank->container_guid;
                $question->access_id = $questionsbank->access_id;
                if (!$question->save()) {
                    if (($file_counter > 0) && ($_FILES['upload']['name'][0] != "")) {
                        foreach ($file as $one_file) {
                            $deleted = $one_file->delete();
                            if (!$deleted) {
                                register_error(elgg_echo('questionsbank:filenotdeleted'));
                                forward("questionsbank/add_question/$questionsbankpost/$input_question_type");
                            }
                        }
                    }

                    register_error(elgg_echo('questionsbank:question_error_save'));
                    forward("questionsbank/edit/$questionsbankpost");
                }
                switch ($response_type) {
                    case 'radiobutton':
                        if(substr_count($one_question, "{")==1&&substr_count($one_question, "}")==1){
                            $question->question=escape_to_char(substr($one_question,0,strpos($one_question, "{")-1));
                            $question->question_html="";
                            $question->question_explanation="";
                            $question->question_type="simple";
                            $question->response_type="radiobutton";
                            $init=strpos($one_question, "{");
                            $end=strpos($one_question, "}");
                            $temp_responses=substr($one_question, $init+1,$end-($init+1));
                            $cursor_position_responses=0;
                            do{
                                $one_response=substr($temp_responses, $cursor_position_responses, strpos($temp_responses, " ",$cursor_position_responses)-$cursor_position_responses);
                                $temp_cursor_position_responses=$cursor_position_responses;
                                $cursor_position_responses=strpos($temp_responses, " ",$cursor_position_responses)+1;
                                if($cursor_position_responses==1){
                                    $one_response=substr($temp_responses, $temp_cursor_position_responses, strlen($temp_responses)-$temp_cursor_position_responses);
                                    $cursor_position_responses=strlen($temp_responses);
                                }
                                if(substr($one_response, 0, 1)=="="){
                                    if($correct_responses=="")
                                        $correct_responses.=substr($one_response, 1,strlen($one_response)-1);
                                    else
                                        $correct_responses.=Chr(26).substr($one_response, 1,strlen($one_response)-1);
                                }
                                if($responses=="")
                                    $responses.=substr($one_response, 1,strlen($one_response)-1);
                                else
                                    $responses.=Chr(26).substr($one_response, 1,strlen($one_response)-1);
                            }while($cursor_position_responses!=strlen($temp_responses));
                        }
                        $question->responses=escape_to_char($responses);
                        $question->correct_responses=escape_to_char($correct_responses);
                        $question->tags=$response_type;
                        $question->index=$index;        
                        break;
                    case 'checkbox':
                        if(substr_count($one_question, "{")==1&&substr_count($one_question, "}")==1){
                            $question->question=escape_to_char(substr($one_question,0,strpos($one_question, "{")-1));
                            $question->question_html="";
                            $question->question_explanation="";
                            $question->question_type="simple";
                            $question->response_type="checkbox";
                            $init=strpos($one_question, "{")+2;
                            $end=strpos($one_question, "}");
                            $temp_responses=substr($one_question, $init+1,$end-($init+1));
                            $cursor_position_responses=0;
                            do{
                                $one_response=substr($temp_responses, $cursor_position_responses, strpos($temp_responses, "\r\n",$cursor_position_responses)-$cursor_position_responses);
                                $temp_cursor_position_responses=$cursor_position_responses;
                                $cursor_position_responses=strpos($temp_responses, "\r\n",$cursor_position_responses)+2;
                                if($cursor_position_responses==2){
                                    $one_response=substr($temp_responses, $temp_cursor_position_responses, strlen($temp_responses)-$temp_cursor_position_responses);
                                    $cursor_position_responses=strlen($temp_responses);
                                }
                                if(substr($one_response, 0, 1)=="="){
                                    if($correct_responses=="")
                                        $correct_responses.=substr($one_response, 1,strlen($one_response)-1);
                                    else
                                        $correct_responses.=Chr(26).substr($one_response, 1,strlen($one_response)-1);
                                }
                                if($responses=="")
                                    $responses.=substr($one_response, 1,strlen($one_response)-1);
                                else
                                    $responses.=Chr(26).substr($one_response, 1,strlen($one_response)-1);
                            }while($cursor_position_responses!=strlen($temp_responses));
                        }
                        $question->responses=escape_to_char($responses);
                        $question->correct_responses=escape_to_char($correct_responses);
                        $question->tags=$response_type;
                        $question->index=$index;
                        break;
                    case 'grid':
                        $question->question=escape_to_char(substr($one_question,0,strpos($one_question, "\r\n")));
                        $question->question_html="";
                        $question->question_explanation="";
                        $question->question_type="simple";
                        $question->response_type="grid";
                        $init=strpos($one_question, "{")+2;
                        $end=strpos($one_question, "}");
                        $temp_responses=substr($one_question, $init+1,$end-($init+1));
                        $cursor_position_rows=0;
                        $cursor_position_columnns=0;
                        $cursor_position_rows=strpos($one_question,"\r\n",$cursor_position_rows)+2;
                        $cursor_position_columnns=strpos($one_question,"{",$cursor_position_rows);
                        $responses_rows="";
                        $responses_columns="";
                        $columns_check=false;
                        do{
                            $one_row=substr($one_question, $cursor_position_rows,$cursor_position_columnns-$cursor_position_rows-1);
                            if($responses_rows=="")
                                $responses_rows.=$one_row;
                            else
                                $responses_rows.=Chr(26).$one_row;
                            $init=strpos($one_question, "{",$cursor_position_rows)+2;
                            $end=strpos($one_question, "}",$cursor_position_rows);
                            $temp_responses=substr($one_question, $init+1,$end-($init+1));
                            $cursor_position_columnns=0;
                            do{
                                $one_column=substr($temp_responses, $cursor_position_columnns, strpos($temp_responses, "\r\n",$cursor_position_columnns)-$cursor_position_columnns);
                                $temp_cursor_position_responses=$cursor_position_columnns;
                                $cursor_position_columnns=strpos($temp_responses, "\r\n",$cursor_position_columnns)+2;
                                if($cursor_position_columnns==2){
                                    $one_column=substr($temp_responses, $temp_cursor_position_responses, strlen($temp_responses)-$temp_cursor_position_responses);
                                    $cursor_position_columnns=strlen($temp_responses);
                                }
                                if(substr($one_column, 0, 1)=="="){
                                    if($correct_responses=="")
                                        $correct_responses.=substr($one_column, 1,strlen($one_column)-1);
                                    else
                                        $correct_responses.=Chr(26).substr($one_column, 1,strlen($one_column)-1);
                                }
                                if(!$columns_check){
                                    if($responses_columns=="")
                                        $responses_columns.=substr($one_column, 1,strlen($one_column)-1);
                                    else
                                        $responses_columns.=Chr(26).substr($one_column, 1,strlen($one_column)-1);
                                }
                            }while($cursor_position_columnns!=strlen($temp_responses));
                            $columns_check=true;
                            $cursor_position_rows+=$end-$cursor_position_rows+3;
                            $cursor_position_columnns=strpos($one_question,"{",$cursor_position_rows);
                        }while($cursor_position_rows<strlen($one_question));
                        $question->responses_rows=escape_to_char($responses_rows);
                        $question->responses_columns=escape_to_char($responses_columns);
                        $question->correct_responses=escape_to_char($correct_responses);
                        $question->tags=$response_type;
                        $question->index=$index;
                        break;
                    case 'pairs':
                        if(substr_count($one_question, "{")==1&&substr_count($one_question, "}")==1){
                            $question->question=escape_to_char(substr($one_question,0,strpos($one_question, "{")-1));
                            $question->question_html="";
                            $question->question_explanation="";
                            $question->question_type="simple";
                            $question->response_type="pairs";
                            $init=strpos($one_question, "{")+2;
                            $end=strpos($one_question, "}");
                            $temp_responses=substr($one_question, $init+1,$end-($init+1));
                            $cursor_position_responses=0;
                            $number_responses=1;
                            do{
                                $one_response=substr($temp_responses, $cursor_position_responses, strpos($temp_responses, "\r\n",$cursor_position_responses)-$cursor_position_responses);                        
                                $temp_cursor_position_responses=$cursor_position_responses;
                                $cursor_position_responses=strpos($temp_responses, "\r\n",$cursor_position_responses)+2;
                                $one_response_array=explode("->",$one_response);
                                if($cursor_position_responses==2){
                                    $one_response=substr($temp_responses, $temp_cursor_position_responses, strlen($temp_responses)-$temp_cursor_position_responses);
                                    $cursor_position_responses=strlen($temp_responses);
                                }
                                if(substr($one_response, 0, 1)=="="){
                                    if($correct_responses=="")
                                        $correct_responses.=substr($one_response_array[0], 1,strlen($one_response_array[0])-2).Chr(26).substr($one_response_array[1], 1,strlen($one_response_array[1])-1);
                                    else
                                        $correct_responses.=Chr(27).substr($one_response_array[0], 1,strlen($one_response_array[0])-2).Chr(26).substr($one_response_array[1], 1,strlen($one_response_array[1])-1);
                                }
                                if($responses_left==""){
                                    $responses_left.=substr($one_response_array[0], 1,strlen($one_response_array[0])-2);
                                    $responses_right.=substr($one_response_array[1], 1,strlen($one_response_array[1])-1);
                                }
                                else{
                                    $responses_left.=Chr(26).substr($one_response_array[0], 1,strlen($one_response_array[0])-2);
                                    $responses_right.=Chr(26).substr($one_response_array[1], 1,strlen($one_response_array[1])-1);
                                }
                                $number_responses++;
                            }while($cursor_position_responses!=strlen($temp_responses));
                        }
                        $question->responses_left=escape_to_char($responses_left);
                        $question->responses_right=escape_to_char($responses_right);
                        $question->correct_responses=escape_to_char($correct_responses);
                        $question->tags=$response_type;
                        $question->index=$index; 
                        break;
                    case 'dropdown':
                        $question->question=escape_to_char(substr($one_question,0,strpos($one_question, "\r\n")));
                        $question->question_html="";
                        $question->question_explanation="";
                        $question->question_type="simple";
                        $question->response_type="dropdown";                   
                        $temp_end=0;
                        $question_text="";
                        $numbers_responses_dropdowns="";
                        $exit=false;
                        $cursor_position_question=strpos($one_question, "\r\n")+2;  
                        do{
                            $init=strpos($one_question, "{",$cursor_position_question);
                            $temp_end=$end;
                            $end=strpos($one_question, "}",$cursor_position_question);       
                            if($end==0&&$cursor_position_question!=0){                 
                                $cursor_position_question=strlen($one_question);
                                $question_text.=substr($one_question, $temp_end, $cursor_position_question-$temp_end);
                            }
                            else{
                                $question_text.=substr($one_question, $cursor_position_question, $init-$cursor_position_question)."(?)";
                                $cursor_position_question=$end+1;
                            }                    
                            $number_responses=1;
                            $cursor_position_responses=0;
                            $temp_cursor_position_responses=0;
                            do{                            
                                $temp_responses=substr($one_question, $init+1,$end-($init+1));
                                if($temp_responses==""){
                                    $exit=true;
                                    break;
                                }
                                $one_response=substr($temp_responses, $cursor_position_responses, strpos($temp_responses, " ",$cursor_position_responses)-$cursor_position_responses);                        
                                $temp_cursor_position_responses=$cursor_position_responses;
                                $cursor_position_responses=strpos($temp_responses, " ",$cursor_position_responses)+1;
                                if($cursor_position_responses==1){
                                    $one_response=substr($temp_responses, $temp_cursor_position_responses, strlen($temp_responses)-$temp_cursor_position_responses);
                                    $cursor_position_responses=strlen($temp_responses);
                                }
                                if(substr($one_response, 0, 1)=="="){
                                    if($correct_responses=="")
                                        $correct_responses.=$number_responses;//substr($one_response, 1,strlen($one_response)-1);
                                    else
                                        $correct_responses.=Chr(26).$number_responses;//substr($one_response, 1,strlen($one_response)-1);
                                }
                                if($responses_dropdown=="")
                                    $responses_dropdown.=substr($one_response, 1,strlen($one_response)-1);
                                else
                                    $responses_dropdown.=Chr(26).substr($one_response, 1,strlen($one_response)-1);
                                $number_responses++;
                            }while($cursor_position_responses!=strlen($temp_responses));
                            if(!$exit){
                                if($numbers_responses_dropdowns=="")
                                    $numbers_responses_dropdowns.=($number_responses-1);   
                                else
                                    $numbers_responses_dropdowns.=",".($number_responses-1);   
                            }
                            else
                                $exit=false;
                        }while($cursor_position_question!=strlen($one_question));                    
                        $question->question_text=$question_text;
                        $question->numbers_responses_dropdowns=escape_to_char($numbers_responses_dropdowns);
                        $question->responses_dropdown=escape_to_char($responses_dropdown);
                        $question->correct_responses=escape_to_char($correct_responses);
                        $question->tags=$response_type;
                        $question->index=$index;
                        break;
                }

                add_entity_relationship($thisquestionsbankpost, 'questionsbank_question', $question->getGUID());
            }
        }while(strpos($file_content,"\r\n\r\n",$start));
        system_message("questionsbank:file_upload");
    } 
    else{
        system_message("questionsbank:upload_file_error");
    }
}

// Forward
//forward("questionsbank/view/$thisquestionsbankpost");

?>


<?php

function escape_to_char($string){
    $string_process="";
    $string_process=str_replace("\{","{",$string);
    $string_process=str_replace("\}","}",$string_process);
    $string_process=str_replace("\-\>","->",$string_process);
    $string_process=str_replace("\~","~",$string_process);
    $string_process=str_replace("\=","=",$string_process);
    return $string_process;
}

function char_to_escape($string){
    $string_process="";
    $string_process=str_replace("{","\{",$string);
    $string_process=str_replace("}","\}",$string_process);
    $string_process=str_replace("->","\-\>",$string_process);
    $string_process=str_replace("~","\~",$string_process);
    $string_process=str_replace("=","\=",$string_process);
    return $string_process;
}

?>
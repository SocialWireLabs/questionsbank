<?php

gatekeeper();

$questionsbankpost = get_input('questionsbankpost');
$questionsbank = get_entity($questionsbankpost);

if ($questionsbank->getSubtype() == "questionsbank" && $questionsbank->canEdit()) {

    $user_guid = elgg_get_logged_in_user_guid();
    $index = get_input('index');

    $input_question = get_input('question');
    $input_question_html = get_input('question_html');
    $input_question_explanation = get_input('question_explanation', '', false);
    $input_question_type = get_input('question_type');
    switch ($input_question_type) {
        case 'urls_files':
            $question_urls = get_input('question_urls');
            $question_urls = array_map('trim', $question_urls);
            $question_urls_names = get_input('question_urls_names');
            $question_urls_names = array_map('trim', $question_urls_names);
            $i = 0;
            $input_question_urls = "";
            if ((count($question_urls) > 0) && (strcmp($question_urls[0], "") != 0)) {
                foreach ($question_urls as $url) {
                    if ($i != 0)
                        $input_question_urls .= Chr(26);
                    $input_question_urls .= $question_urls_names[$i] . Chr(24) . $question_urls[$i];
                    $i = $i + 1;
                }
            }
            $number_question_urls = count($question_urls);
            break;
    }
    $file_counter = count($_FILES['upload']['name']);
    $input_question_tags = get_input('question_tags');
    //Convert string of tags into a preformatted array
    $questiontagsarray = string_to_tag_array($input_question_tags);

    $input_response_type = get_input('response_type');
    switch ($input_response_type) {
        case 'radiobutton':
        case 'checkbox':
            $responses = get_input('responses');
            $responses = array_map('trim', $responses);
            $input_responses = implode(Chr(26), $responses);
            $number_responses = count($responses);
            break;        
        case 'grid':
            $responses_rows = get_input('responses_rows');
            $responses_rows = array_map('trim', $responses_rows);
            $input_responses_rows = implode(Chr(26),$responses_rows);
            $number_responses_rows = count($responses_rows);
            $responses_columns = get_input('responses_columns');
            $responses_columns = array_map('trim', $responses_columns);
            $input_responses_columns = implode(Chr(26),$responses_columns);
            $number_responses_columns = count($responses_columns);
            break;
        case 'pairs':
            $responses_left = get_input('responses_left');
            $responses_left = array_map('trim', $responses_left);
            $input_responses_left = implode(Chr(26),$responses_left);
            $number_responses_left = count($responses_left);
            $responses_right = get_input('responses_right');
            $responses_right = array_map('trim', $responses_right);
            $input_responses_right = implode(Chr(26),$responses_right);
            $number_responses_right = count($responses_right);
            break;
        case 'dropdown':
            $text_dropdown=get_input('text_dropdown');
            $numbers_responses_dropdowns=get_input('numbers_responses_dropdowns');
            $numbers_responses_dropdowns_array=explode(',', $numbers_responses_dropdowns);
            $numbers_responses_dropdowns_array=array_map('trim', $numbers_responses_dropdowns_array);
            $number_dropdowns=get_input('number_dropdowns');
            $responses_dropdown=get_input('responses_dropdown');
            $responses_dropdown=array_map('trim', $responses_dropdown);
            $input_responses_dropdown=implode(Chr(26),$responses_dropdown);
            $number_responses_option_dropdown = count($responses_dropdown);
            break;
    }

    switch ($input_response_type) {
        case 'radiobutton':
            $input_number_correct_response = get_input('number_correct_response');
            break;
        case 'checkbox':
            $input_numbers_correct_responses = get_input('numbers_correct_responses');
            if (!empty($input_numbers_correct_responses)) {
                $numbers_correct_responses = explode(',', $input_numbers_correct_responses);
                $numbers_correct_responses = array_map('trim', $numbers_correct_responses);
                $number_correct_responses = count($numbers_correct_responses);
            } else {
                $number_correct_responses = 0;
            }
            break;
        case 'grid':
            $input_numbers_correct_responses=get_input('grid_correct_responses');
            if (!empty($input_numbers_correct_responses)){
                $numbers_correct_responses = explode(',',$input_numbers_correct_responses);
                $numbers_correct_responses = array_map('trim', $numbers_correct_responses);
                $number_correct_responses = count($numbers_correct_responses);
            } else {
                $number_correct_responses = 0;
            }
            break;
        case 'pairs':
            $input_numbers_correct_responses=get_input('pairs_correct_responses');
            if (!empty($input_numbers_correct_responses)){
                $numbers_correct_responses = explode(',',$input_numbers_correct_responses);
                $numbers_correct_responses = array_map('trim', $numbers_correct_responses);
                $number_correct_responses = count($numbers_correct_responses);
            } else {
                $number_correct_responses = 0;
            }
            break;
        case 'dropdown':
            $input_numbers_correct_responses=get_input('dropdown_correct_responses');
            if (!empty($input_numbers_correct_responses)){
                $numbers_correct_responses = explode(',',$input_numbers_correct_responses);
                $numbers_correct_responses = array_map('trim', $numbers_correct_responses);
                $number_correct_responses = count($numbers_correct_responses);
            } else {
                $number_correct_responses = 0;
            }
            break;
    }


    $options = array('relationship' => 'questionsbank_question', 'relationship_guid' => $questionsbankpost, 'inverse_relationship' => false, 'type' => 'object', 'subtype' => 'questionsbank_question', 'metadata_name_value_pairs' => array('name' => 'index', 'value' => $index));
    $questions = elgg_get_entities_from_relationship($options);
    $one_question = $questions[0];

    if (empty($one_question)) {
        register_error(elgg_echo('questionsbank:question_notfound'));
        forward("questionsbank/edit_question/$questionsbankpost/$index");
    }

    $previous_files = elgg_get_entities_from_relationship(array('relationship' => 'question_file_link', 'relationship_guid' => $one_question->getGUID(), 'inverse_relationship' => false, 'types' => 'object', 'limit' => 0));


    // Cache to the session
    elgg_make_sticky_form('edit_question_questionsbank');

    // Make sure the question isn't blank
    if (strcmp($input_question, "") == 0) {
        register_error(elgg_echo("questionsbank:question_blank"));
        forward("questionsbank/edit_question/$questionsbankpost/$index");
    }

    // Question urls
    if (strcmp($input_question_type, "urls_files") == 0) {
        $blank_question_url = false;
        $questionurlsarray = array();
        $i = 0;
        foreach ($question_urls as $one_url) {
            $questionurlsarray[$i] = $one_url;
            if (strcmp($one_url, "") == 0) {
                $blank_question_url = true;
                break;
            }
            $i = $i + 1;
        }
        if (!$blank_question_url) {
            foreach ($question_urls_names as $one_url_name) {
                if (strcmp($one_url_name, "") == 0) {
                    $blank_question_url = true;
                    break;
                }
            }
        }
        if (($blank_question_url) && ($number_question_urls > 1)) {
            register_error(elgg_echo("questionsbank:url_blank"));
            forward("questionsbank/edit_question/$questionsbankpost/$index");
        }
        $same_question_url = false;
        $i = 0;
        while (($i < $number_question_urls) && (!$same_question_url)) {
            $j = $i + 1;
            while ($j < $number_question_urls) {
                if (strcmp($questionurlsarray[$i], $questionurlsarray[$j]) == 0) {
                    $same_question_url = true;
                    break;
                }
                $j = $j + 1;
            }
            $i = $i + 1;
        }
        if ($same_question_url) {
            register_error(elgg_echo("questionsbank:url_repetition"));
            forward("questionsbank/edit_question/$questionsbankpost/$index");
        }
        if (!$question_url_blank) {
            foreach ($question_urls as $url) {
                $xss_questionsbank = "<a rel=\"nofollow\" href=\"$url\" target=\"_blank\">$url</a>";
                if ($xss_questionsbank != filter_tags($xss_questionsbank)) {
                    register_error(elgg_echo('questionsbank:url_failed'));
                    forward("questionsbank/edit_question/$questionsbankpost/$index");
                }
            }
        }
    }

    // Responses
    if ((strcmp($input_response_type, "radiobutton") == 0) || (strcmp($input_response_type, "checkbox") == 0)) {
        $blank_response = false;
        $responsesarray = array();
        $i = 0;
        foreach ($responses as $one_response) {
            $responsesarray[$i] = $one_response;
            if (strcmp($one_response, "") == 0) {
                $blank_response = true;
                break;
            }
            $i = $i + 1;
        }
        if ($blank_response) {
            register_error(elgg_echo("questionsbank:response_blank"));
            forward("questionsbank/edit_question/$questionsbankpost/$index");
        }
        $same_response = false;
        $i = 0;
        while (($i < $number_responses) && (!$same_response)) {
            $j = $i + 1;
            while ($j < $number_responses) {
                if (strcmp($responsesarray[$i], $responsesarray[$j]) == 0) {
                    $same_response = true;
                    break;
                }
                $j = $j + 1;
            }
            $i = $i + 1;
        }
        if ($same_response) {
            register_error(elgg_echo("questionsbank:response_repetition"));
            forward("questionsbank/edit_question/$questionsbankpost/$index");
        }
        if ($number_responses < 2) {
            register_error(elgg_echo("questionsbank:respose_only_one_option"));
            forward("questionsbank/edit_question/$questionsbankpost/$index");
        }
    }

    if (strcmp($input_response_type,"grid")==0){
        $blank_response=false;
        $responsesrowsarray=array();
        $i=0;
        foreach($responses_rows as $one_response){
            $responsesrowsarray[$i]=$one_response;
            if (strcmp($one_response,"")==0){
                $blank_response=true;
                break;
            }
            $i=$i+1;
        }            
        if ($blank_response){
            register_error(elgg_echo("questionsbank:row_blank"));
            forward("questionsbank/edit_question/$questionsbankpost/$index");
        }
        $same_response=false;
        $i=0;
        while(($i<$number_responses_rows)&&(!$same_response)){
            $j=$i+1;
            while($j<$number_responses_rows){
                if (strcmp($responsesrowsarray[$i],$responsesrowsarray[$j])==0){
                    $same_response=true;
                    break;
                }
                $j=$j+1;
            }
            $i=$i+1;
        }
        if ($same_response){
            register_error(elgg_echo("questionsbank:row_repetition"));
            forward("questionsbank/edit_question/$questionsbankpost/$index");
        }

        $blank_response=false;
        $responsescolumnsarray=array();
        $i=0;
        foreach($responses_columns as $one_response){
            $responsescolumnsarray[$i]=$one_response;
            if (strcmp($one_response,"")==0){
                $blank_response=true;
                break;
            }
            $i=$i+1;
        }            
        if ($blank_response){
            register_error(elgg_echo("questionsbank:column_blank"));
            forward("questionsbank/edit_question/$questionsbankpost/$index");
        }
        $same_response=false;
        $i=0;
        while(($i<$number_responses_columns)&&(!$same_response)){
            $j=$i+1;
            while($j<$number_responses_columns){
                if (strcmp($responsescolumnsarray[$i],$responsescolumnsarray[$j])==0){
                    $same_response=true;
                    break;
                }
                $j=$j+1;
            }
            $i=$i+1;
        }
        if ($same_response){
            register_error(elgg_echo("questionsbank:column_repetition"));
            forward("questionsbank/edit_question/$questionsbankpost/$index");
        }
        
        if ($number_responses_columns<2){
            register_error(elgg_echo("questionsbank:column_only_one_option"));
            forward("questionsbank/edit_question/$questionsbankpost/$index");
        } 
    }

    if (strcmp($input_response_type,"pairs")==0){
        $blank_response=false;
        $responsesleftarray=array();
        $i=0;
        foreach($responses_left as $one_response){
            $responsesleftarray[$i]=$one_response;
            if (strcmp($one_response,"")==0){
                $blank_response=true;
                break;
            }
            $i=$i+1;
        }            
        if ($blank_response){
            register_error(elgg_echo("questionsbank:left_blank"));
            forward("questionsbank/edit_question/$questionsbankpost/$index");
        }

        $blank_response=false;
        $responsesrightarray=array();
        $i=0;
        foreach($responses_right as $one_response){
            $responsesrightarray[$i]=$one_response;
            if (strcmp($one_response,"")==0){
                $blank_response=true;
                break;
            }
            $i=$i+1;
        }            
        if ($blank_response){
            register_error(elgg_echo("questionsbank:right_blank"));
            forward("questionsbank/edit_question/$questionsbankpost/$index");
        }
        
        if($number_responses_left != $number_responses_right){
            register_error(elgg_echo("questionsbank:not_same_numbers_left_right"));
            forward("questionsbank/edit_question/$questionsbankpost/$index");
        }

        if ($number_responses_right<2){
            register_error(elgg_echo("questionsbank:only_one_pair"));
            forward("questionsbank/edit_question/$questionsbankpost/$index");
        } 
    }

    if (strcmp($input_response_type,"dropdown")==0){
        $blank_response=false;
        $responses_dropdown_array=array();
        $i=0;
        foreach($responses_dropdown as $one_response){
            $responses_dropdown_array[$i]=$one_response;
            if (strcmp($one_response,"")==0){
                $blank_response=true;
                break;
            }
            $i=$i+1;
        }            
        if ($blank_response){
            register_error(elgg_echo("questionsbank:option_dropdown_blank"));
            forward("questionsbank/edit_question/$questionsbankpost/$index");
        }

	$same_response = false;
        $i = 0;
        $j = 0;
	$i_end = $numbers_responses_dropdowns_array[0];
        while (($i < count($numbers_responses_dropdowns_array)) && (!$same_response)) {
	   while (($j<$i_end)&&(!$same_response)) {
	      $k=$j+1;
	      while ($k<$i_end) {
	      	 if (strcmp($responses_dropdown[$j],$responses_dropdown[$k])==0) {
	            $same_response = true;
		    break;
		 }
		 $k=$k+1;
	      }
	      $j=$j+1;
	   }
           $i ++;
	   $j = $i_end;
	   $i_end = $j+$numbers_responses_dropdowns_array[$i];
        }

        if ($same_response) {
            register_error(elgg_echo("questionsbank:dropdown_repetition"));
            forward("questionsbank/edit_question/$questionsbankpost/$index");
        }

	//There are always two options at least in each dropdown.
        if ($number_responses_option_dropdown<2){
            register_error(elgg_echo("questionsbank:dropdown_only_one_option"));
            forward("questionsbank/edit_question/$questionsbankpost/$index");
        } 
    }

    // Correct responses
    switch ($input_response_type) {
        case 'radiobutton':
            $is_integer = true;
            $mask_integer = '^([[:digit:]]+)$';
            if (ereg($mask_integer, $input_number_correct_response, $same)) {
                if ((substr($same[1], 0, 1) == 0) && (strlen($same[1]) != 1)) {
                    $is_integer = false;
                }
            } else {
                $is_integer = false;
            }
            if (!$is_integer) {
                register_error(elgg_echo("questionsbank:bad_number_correct_response"));
                forward("questionsbank/edit_question/$questionsbankpost/$index");
            }
            if (($input_number_correct_response==0)||($input_number_correct_response > $number_responses)){
                register_error(elgg_echo("questionsbank:bad_number_correct_response"));
                forward("questionsbank/edit_question/$questionsbankpost/$index");
            }
            $input_correct_responses = "";
            $i = 1;
            foreach ($responses as $one_response) {
                if ($i == $input_number_correct_response) {
                    $input_correct_responses .= $one_response;
                    break;
                }
                $i = $i + 1;
            }
            break;       
        case 'checkbox':
            foreach ($numbers_correct_responses as $one_number) {
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
                    register_error(elgg_echo("questionsbank:bad_number_correct_response"));
                    forward("questionsbank/edit_question/$questionsbankpost/$index");
                }
                if ($one_number > $number_responses) {
                    register_error(elgg_echo("questionsbank:bad_number_correct_responses"));
                    forward("questionsbank/edit_question/$questionsbankpost/$index");
                }
            }
            $input_correct_responses = "";
            if ($number_correct_responses > 0) {
                $i = 0;
                $j = 0;
                foreach ($responses as $one_response) {
                    if (in_array($i + 1, $numbers_correct_responses)) {
                        if ($j == 0) {
                            $input_correct_responses .= $one_response;
                        } else {
                            $input_correct_responses .= Chr(26) . $one_response;
                        }
                        $j = $j + 1;
                    }
                    $i = $i + 1;
                }
            } else {
                register_error(elgg_echo("questionsbank:bad_number_correct_responses"));
                forward("questionsbank/edit_question/$questionsbankpost/$index");
            }
            $correct_responses = explode(Chr(26), $input_correct_responses);
            break;
        case 'grid':
            foreach($numbers_correct_responses as $one_number){
                $is_integer = true;
                $mask_integer='^([[:digit:]]+)$'; 
                if (ereg($mask_integer,$one_number,$same)){
                    if ((substr($same[1],0,1)==0)&&(strlen($same[1])!=1)){
                        $is_integer=false;
                    }
                } else {
                    $is_integer=false;
                }   
                if (!$is_integer){
                    register_error(elgg_echo("questionsbank:bad_number_correct_response"));
                    forward("questionsbank/edit_question/$questionsbankpost/$index");
                }
                if (($one_number==0)||($one_number>$number_responses_columns)){
                    register_error(elgg_echo("questionsbank:bad_number_correct_responses"));
                    forward("questionsbank/edit_question/$questionsbankpost/$index");
                }
            }
            $input_correct_responses = "";
            if ($number_correct_responses==$number_responses_rows){
                $j=0;
                foreach ($numbers_correct_responses as $one_number){
                    $i=1;
                    foreach ($responses_columns as $one_response){
                        if ($i==$one_number){
                            if ($j==0) {
                                $input_correct_responses .= $one_response;
                            } else {
                                $input_correct_responses .= Chr(26) . $one_response;
                            }
                            $j=$j+1;
                            break;
                        }  
                        $i=$i+1;
                    }
                }
            } else {
                register_error(elgg_echo("questionsbank:bad_number_correct_responses"));
                forward("questionsbank/edit_question/$questionsbankpost/$index");
            }
            break;
        case 'pairs':
	    if ($number_correct_responses == 0) {
	       register_error(elgg_echo("questionsbank:bad_number_correct_responses"));
               forward("questionsbank/edit_question/$questionsbankpost/$index");
	    }
	    $input_correct_responses = "";
	    $array_input_correct_responses = array();
            $array_one_number=array();
	    $i = 0;
            foreach ($numbers_correct_responses as $one_number){
                $array_one_number = explode('-', $one_number);
                $is_integer = true;
                $mask_integer='^([[:digit:]]+)$'; 
                if (ereg($mask_integer,$array_one_number[0],$same_left)&&ereg($mask_integer,$array_one_number[1],$same_right)){
                    if ((substr($same_left[1],0,1)==0)&&(strlen($same_left[1])!=1)||(substr($same_right[1],0,1)==0)&&(strlen($same_right[1])!=1)){
                        $is_integer=false;
                    }
                } else {
                    $is_integer=false;
                }   
                if (!$is_integer){
                    register_error(elgg_echo("questionsbank:bad_number_correct_response"));
                    forward("questionsbank/edit_question/$questionsbankpost/$index");
                }
		if (($array_one_number[0]==0)||($array_one_number[1]==0)){
	           register_error(elgg_echo("questionsbank:bad_number_correct_responses"));
                   forward("questionsbank/edit_question/$questionsbankpost/$index");
	        }
                if ($array_one_number[0]>$number_responses_right||$array_one_number[1]>$number_responses_right){
                    register_error(elgg_echo("questionsbank:bad_number_correct_responses"));
                    forward("questionsbank/edit_question/$questionsbankpost/$index");
                }
           
                $j=1;
	        foreach ($responses_left as $one_response_left) {
                    if ($j==$array_one_number[0]) {
                        $new_input_correct_response = $one_response_left;
		        break;			
		    }
		    $j=$j+1;
	        }
	        $k=1;
	        foreach ($responses_right as $one_response_right) {
                    if ($k==$array_one_number[1]) {
                        $new_input_correct_response .= Chr(26) . $one_response_right;
		        break;			
		    }
		    $k=$k+1;
	        }
	        if (!in_array($new_input_correct_response,$array_input_correct_responses)){
	            $array_input_correct_responses[$i] = $new_input_correct_response;
		    $i=$i+1;
	        }
            }   
	    $i=0;
	    foreach ($array_input_correct_responses as $one_input_correct_response) {
                if ($i==0) {
		    $input_correct_responses = $one_input_correct_response;
		} else {
		    $input_correct_responses .= Chr(27) . $one_input_correct_response;    
		}
		$i=$i+1;
            }
            break;
        case 'dropdown':
            $i=0;
            foreach($numbers_correct_responses as $one_number){
                $is_integer = true;
                $mask_integer='^([[:digit:]]+)$'; 
                if (ereg($mask_integer,$one_number,$same)){
                    if ((substr($same[1],0,1)==0)&&(strlen($same[1])!=1)){
                        $is_integer=false;
                    }
                } else {
                    $is_integer=false;
                }   
                if (!$is_integer){
                    register_error(elgg_echo("questionsbank:bad_number_correct_response"));
                    forward("questionsbank/edit_question/$questionsbankpost/$index");
                }
                if (($one_number==0)||($one_number>$numbers_responses_dropdowns_array[$i])){
                    register_error(elgg_echo("questionsbank:bad_number_correct_responses"));
                    forward("questionsbank/edit_question/$questionsbankpost/$index");
                }
                $i++;
            }
            if($number_correct_responses!=$number_dropdowns){
                register_error(elgg_echo("questionsbank:bad_number_correct_responses"));
                forward("questionsbank/edit_question/$questionsbankpost/$index");
            }
            $i=0;
            $input_correct_responses = "";
            foreach ($numbers_correct_responses as $one_number) {
                if ($i==0) {
                    $input_correct_responses = $one_number;
                } else {
                    $input_correct_responses .= Chr(26) . $one_number;    
                }
                $i=$i+1;
            } 

            break;
    }

    if (!empty($previous_files)) {
        $previous_file_counter = count($previous_files);
    } else {
        $previous_file_counter = 0;
    }
    foreach ($previous_files as $one_file) {
        $value = get_input($one_file->getGUID());
        if ($value == '1') {
            $previous_file_counter = $previous_file_counter - 1;
        }
    }

    if ((strcmp($input_question_type, "urls_files") == 0) && ((($file_counter + $previous_file_counter + $number_question_urls) == 0) || ((($previous_file_counter + $number_question_urls) == 0) && ($_FILES['upload']['name'][0] == "")))) {
        register_error(elgg_echo('questionsbank:not_question_urls_files'));
        forward("questionsbank/edit_question/$questionsbankpost/$index");
    }
    if (($file_counter > 0) && ($_FILES['upload']['name'][0] != "")) {
        $file_save_well = true;
        $file = array();
        for ($i = 0; $i < $file_counter; $i++) {
            $file[$i] = new QuestionsQuestionsbankPluginFile();
            $file[$i]->subtype = "questionsbank_question_file";
            $prefix = "file/";
            $filestorename = elgg_strtolower(time() . $_FILES['upload']['name'][$i]);
            $file[$i]->setFilename($prefix . $filestorename);
            $file[$i]->setMimeType($_FILES['upload']['type'][$i]);
            $file[$i]->originalfilename = $_FILES['upload']['name'][$i];
            $file[$i]->simpletype = elgg_get_file_simple_type($_FILES['upload']['type'][$i]);
            $file[$i]->open("write");
            if (isset($_FILES['upload']) && isset($_FILES['upload']['error'][$i])) {
                $uploaded_file = file_get_contents($_FILES['upload']['tmp_name'][$i]);
            } else {
                $uploaded_file = false;
            }
            $file[$i]->write($uploaded_file);
            $file[$i]->close();
            $file[$i]->title = $_FILES['upload']['name'][$i];
            $file[$i]->owner_guid = $user_guid;
            $file[$i]->container_guid = $questionsbank->container_guid;
            $file[$i]->access_id = $questionsbank->access_id;
            $file_save = $file[$i]->save();
            if (!$file_save) {
                $file_save_well = false;
                break;
            }
        }
        if (!$file_save_well) {
            foreach ($file as $one_file) {
                $deleted = $one_file->delete();
                if (!$deleted) {
                    register_error(elgg_echo('questionsbank:filenotdeleted'));
                    forward("questionsbank/edit_question/$questionsbankpost/$index");
                }
            }
            register_error(elgg_echo('questionsbank:file_error_save'));
            forward("questionsbank/edit_question/$questionsbankpost/$index");
        }
    }


    ////////////////////////////////////////////////////////////
    // Edit question

    if (!$one_question->save()) {
        if (($file_counter > 0) && ($_FILES['upload']['name'][0] != "")) {
            foreach ($file as $one_file) {
                $deleted = $one_file->delete();
                if (!$deleted) {
                    register_error(elgg_echo('questionsbank:filenotdeleted'));
                    forward("questionsbank/edit_question/$questionsbankpost/$index");
                }
            }
        }

        register_error(elgg_echo('questionsbank:question_error_save'));
        forward("questionsbank/edit/$questionsbankpost");
    }

    //Delete previous question files
    switch ($input_question_type) {
        case 'urls_files':
            foreach ($previous_files as $one_file) {
                $value = get_input($one_file->getGUID());
                if ($value == '1') {
                    $file1 = get_entity($one_file->getGUID());
                    $deleted = $file1->delete();
                    if (!$deleted) {
                        register_error(elgg_echo('questionsbank:filenotdeleted'));
                        forward("questionsbank/edit_question/$questionsbankpost/$index");
                    }
                }
            }
            break;
    }

    $one_question->question = $input_question;
    $one_question->question_html = $input_question_html;
    $one_question->question_explanation = $input_question_explanation;
    $one_question->question_type = $input_question_type;
    if (is_array($questiontagsarray)) {
        $one_question->tags = $questiontagsarray;
    }

    switch ($input_question_type) {
        case 'urls_files':
            $one_question->question_urls = $input_question_urls;
            break;
    }
    if (($file_counter > 0) && ($_FILES['upload']['name'][0] != "")) {
        for ($i = 0; $i < $file_counter; $i++) {
            add_entity_relationship($one_question->getGUID(), 'question_file_link', $file[$i]->getGUID());
        }
    }
    $one_question->response_type = $input_response_type;

    switch ($input_response_type) {
        case 'radiobutton':
        case 'checkbox':
            $one_question->responses = $input_responses;
            break;
        case 'grid':
            $one_question->responses_rows = $input_responses_rows;
            $one_question->responses_columns = $input_responses_columns;
            break;
        case 'pairs':
            $one_question->responses_left = $input_responses_left;
            $one_question->responses_right = $input_responses_right;
            break;
        case 'dropdown':
            $question->question_text = $text_dropdown;
            $question->numbers_responses_dropdowns = $numbers_responses_dropdowns;
            $one_question->responses_dropdown = $input_responses_dropdown;
            break;
    }

    switch ($input_response_type) {
        case 'radiobutton':
        case 'checkbox':
            $one_question->correct_responses = $input_correct_responses;
            break;
        case 'grid':
            $one_question->correct_responses = $input_correct_responses;
            break;
        case 'pairs':
            $one_question->correct_responses = $input_correct_responses;
            break;
        case 'dropdown':
            $one_question->correct_responses = $input_correct_responses;
            break;
    }

    // Remove the questionsbank post cache
    elgg_clear_sticky_form('edit_question_questionsbank');

    // System message   
    system_message(elgg_echo("questionsbank:updated"));

    // Add to river
    //add_to_river('river/object/questionsbank/update','update',$user_guid,$questionsbankpost); 	

    // Forward
    forward("questionsbank/view/$questionsbankpost");
}

?>
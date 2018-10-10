<?php

gatekeeper();

$thisquestionsbankpost = get_input('thisquestionsbankpost');
$thisquestionsbank = get_entity($thisquestionsbankpost);

if ($thisquestionsbank->getSubtype() == "questionsbank" && $thisquestionsbank->canEdit()) {

    $user_guid = elgg_get_logged_in_user_guid();
    $questionsbankpost = get_input('questionsbankpost');
    $questionsbank = get_entity($questionsbank);

    $options = array('relationship' => 'questionsbank_question', 'relationship_guid' => $questionsbankpost, 'inverse_relationship' => false, 'type' => 'object', 'subtype' => 'questionsbank_question', 'limit' => 0);
    $questions = elgg_get_entities_from_relationship($options);

    $questions_selection_type = get_input('questions_selection_type');
    if (strcmp($questions_selection_type, 'questionsbank_manual_questions_selection_type') == 0) {
        $selected_questions_indexes = get_input('selected_questions_indexes');
        $selected_questions_indexes_array = explode(",", $selected_questions_indexes);
        $i = 0;
        $the_selected_questions_indexes = array();
        if (!empty($selected_questions_indexes_array)) {
            if (is_array($selected_questions_indexes_array)) {
                foreach ($selected_questions_indexes_array as $one_selected_question_index) {
                    $question_name = "question_" . $one_selected_question_index;
                    $question_selected = get_input($question_name);
                    if (strcmp($question_selected, "on") == 0) {
                        $the_selected_questions_indexes[$i] = $one_selected_question_index;
                        $i = $i + 1;
                    }
                }
            } else {
                $question_name = "question_" . $selected_questions_indexes_array;
                $question_selected = get_input($question_name);
                if (strcmp($question_selected, "on") == 0) {
                    $the_selected_questions_indexes[$i] = $selected_questions_indexes_array;
                    $i = $i + 1;
                }
            }
        }
        if ($i == 0) {
            register_error(elgg_echo('questionsbank:not_selected_questions_import'));
            forward($_SERVER['HTTP_REFERER']);
        }
    } else {
        $random_selected_questions_indexes = get_input('random_selected_questions_indexes');
        $random_selected_questions_indexes_array = explode(",", $random_selected_questions_indexes);
        $i = 0;
        $the_selected_questions_indexes = array();
        if (is_array($random_selected_questions_indexes_array)) {
            foreach ($random_selected_questions_indexes_array as $one_selected_question_index) {
                $the_selected_questions_indexes[$i] = $one_selected_question_index;
                $i = $i + 1;
            }
        } else {
            $the_selected_questions_indexes[$i] = $random_selected_questions_indexes_array;
            $i = $i + 1;
        }
    }
    $i = 0;
    $the_selected_questions = array();
    foreach ($questions as $one_question) {
        if (in_array($one_question->index, $the_selected_questions_indexes)) {
            $the_selected_questions[$i] = $one_question;
            $i = $i + 1;
        }
    }

    $options = array('relationship' => 'questionsbank_question', 'relationship_guid' => $thisquestionsbankpost, 'inverse_relationship' => false, 'type' => 'object', 'subtype' => 'questionsbank_question', 'limit' => 0);
    $questions = elgg_get_entities_from_relationship($options);

    if (!empty($questions)) {
        $num_questions = count($questions);
    } else {
        $num_questions = 0;
    }

    $j = 0;
    foreach ($the_selected_questions as $one_selected_question) {
        $previous_files = elgg_get_entities_from_relationship(array('relationship' => 'question_file_link', 'relationship_guid' => $one_selected_question->getGUID(), 'inverse_relationship' => false, 'type' => 'object', 'limit' => 0));
        if (!empty($previous_files)) {
            $file_counter = count($previous_files);
        } else {
            $file_counter = 0;
        }

        if ((strcmp($one_selected_question->question_type, "urls_files") == 0)) {
            if ($file_counter > 0) {
                $file_save_well = true;
                $file = array();
                $i = 0;
                foreach ($previous_files as $one_previous_file) {
                    $file[$i] = new QuestionsQuestionsbankPluginFile();
                    $file[$i]->subtype = "questionsbank_question_file";
                    $prefix = "file/";
                    $name = $one_previous_file->title;
                    $mimetype = $one_previous_file->mimetype;
                    $filestorename = elgg_strtolower(time() . $name);
                    $file[$i]->setFilename($prefix . $filestorename);
                    $file[$i]->setMimeType($mimetype);
                    $file[$i]->originalfilename = $name;
                    $file[$i]->simpletype = $one_previous_file->simpletype;
                    $file[$i]->open("write");
                    $file_owner = $one_previous_file->getOwnerEntity();
                    $file_owner_time_created = date('Y/m/d', $file_owner->time_created);
                    $file_dir_root = elgg_get_config('dataroot');
                    $filename = $file_dir_root . $file_owner_time_created . "/" . $file_owner->guid . "/" . $one_previous_file->filename;
                    $content = file_get_contents($filename);
                    $file[$i]->write($content);
                    $file[$i]->close();
                    $file[$i]->title = $name;
                    $file[$i]->owner_guid = $user_guid;
                    $file[$i]->container_guid = $thisquestionsbank->container_guid;
                    $file[$i]->access_id = $thisquestionsbank->access_id;
                    $file_save = $file[$i]->save();
                    if (!$file_save) {
                        $file_save_well = false;
                        break;
                    }
                    $i = $i + 1;
                }
                if (!$file_save_well) {
                    foreach ($file as $one_file) {
                        $deleted = $one_file->delete();
                        if (!$deleted) {
                            register_error(elgg_echo('questionsbank:filenotdeleted'));
                            forward("questionsbank/edit/$thisquestionsbankpost");
                        }
                    }
                    register_error(elgg_echo('questionsbank:file_error_save'));
                    forward("questionsbank/edit/$thisquestionsbankpost");
                }
            }
        }


        $question = new ElggObject();
        $question->subtype = "questionsbank_question";
        $question->owner_guid = $user_guid;
        $question->container_guid = $thisquestionsbank->container_guid;
        $question->access_id = $thisquestionsbank->access_id;
        if (!$question->save()) {
            if ($file_counter > 0) {
                foreach ($file as $one_file) {
                    $deleted = $one_file->delete();
                    if (!$deleted) {
                        register_error(elgg_echo('questionsbank:filenotdeleted'));
                        forward("questionsbank/edit/$thisquestionsbankpost");
                    }
                }
            }

            register_error(elgg_echo('questionsbank:question_error_save'));
            forward("questionsbank/edit/$thisquestionsbankpost");
        }
        $question->question = $one_selected_question->question;
        $question->question_html = $one_selected_question->question_html;
        $question->question_explanation = $one_selected_question->question_explanation;
        $question->question_type = $one_selected_question->question_type;
        switch ($question->question_type) {
            case 'urls_files':
                $question->question_urls = $one_selected_question->question_urls;
                break;
        }
        if ($file_counter > 0) {
            for ($i = 0; $i < $file_counter; $i++) {
                add_entity_relationship($question->getGUID(), 'question_file_link', $file[$i]->getGUID());
            }
        }
        $question->response_type = $one_selected_question->response_type;
       
        switch ($question->response_type) {
            case 'radiobutton':
            case 'checkbox':
                $question->responses = $one_selected_question->responses;
                break;
            case 'grid':
                $question->responses_rows = $one_selected_question->responses_rows;
                $question->responses_columns = $one_selected_question->responses_columns;
                break;
            case 'pairs':
                $question->responses_left = $one_selected_question->responses_left;
                $question->responses_right = $one_selected_question->responses_right; 
                break;
            case 'dropdown':
                $question->question_text = $one_selected_question->question_text;
                $question->numbers_responses_dropdowns = $one_selected_question->numbers_responses_dropdowns;
                $question->responses_dropdown = $one_selected_question->responses_dropdown;
                break;
         } 

        switch ($question->response_type) {
            case 'radiobutton':
            case 'checkbox':
                $question->correct_responses = $one_selected_question->correct_responses;
                break;
            case 'grid':
                $question->correct_responses = $one_selected_question->correct_responses;
                break;
            case 'pairs':
                $question->correct_responses = $one_selected_question->correct_responses;
                break;
            case 'dropdown':
                $question->correct_responses = $one_selected_question->correct_responses;
                break;
        }
        $question->tags = $one_selected_question->tags;
        $question->index = $num_questions + $j;
        add_entity_relationship($thisquestionsbankpost, 'questionsbank_question', $question->getGUID());
        $j = $j + 1;
    }

    // System message
    system_message(elgg_echo("questionsbank:updated"));

    // Add to river
    //add_to_river('river/object/questionsbank/update','update',$user_guid,$thisquestionsbankpost); 

    // Forward           
    forward("questionsbank/view/$thisquestionsbankpost");
}


?>

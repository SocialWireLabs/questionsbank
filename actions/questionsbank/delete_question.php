<?php

gatekeeper();
if (is_callable('group_gatekeeper'))
    group_gatekeeper();

$questionsbankpost = get_input('questionsbankpost');
$questionsbank = get_entity($questionsbankpost);
$index = get_input('index');
$user_guid = elgg_get_logged_in_user_guid();

if ($questionsbank->getSubtype() == "questionsbank" && $questionsbank->canEdit()) {
    $options = array('relationship' => 'questionsbank_question', 'relationship_guid' => $questionsbankpost, 'inverse_relationship' => false, 'type' => 'object', 'subtype' => 'questionsbank_question', 'metadata_name_value_pairs' => array('name' => 'index', 'value' => $index, 'operand' => '>='));
    $questions = elgg_get_entities_from_relationship($options);

    $already_deleted = false;
    foreach ($questions as $one_question) {
        if ($one_question->index == $index) {
            if (!$already_deleted) {
                $already_deleted = true;
                $files = elgg_get_entities_from_relationship(array('relationship' => 'question_file_link', 'relationship_guid' => $one_question->getGUID(), 'inverse_relationship' => false, 'type' => 'object', 'limit' => 0));

                foreach ($files as $one_file) {
                    $deleted = $one_file->delete();
                    if (!$deleted) {
                        register_error(elgg_echo("questionsbank:filenotdeleted"));
                        forward("questionsbank/view/$questionsbankpost");
                    }
                }

                $deleted = $one_question->delete();
                if (!$deleted) {
                    register_error(elgg_echo("questionsbank:questionnotdeleted"));
                    forward("questionsbank/view/$questionsbankpost");
                }
            }
        } else {
            $previous_index = $one_question->index;
            $one_question->index = $previous_index - 1;
        }
    }

    // System message 
    system_message(elgg_echo("questionsbank:updated"));

    // Add to river
    //add_to_river('river/object/questionsbank/update','update',$user_guid,$questionsbankpost);

    //Forward
    forward("questionsbank/view/$questionsbankpost");
}

?>
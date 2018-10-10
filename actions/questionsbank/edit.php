<?php

gatekeeper();

$questionsbankpost = get_input('questionsbankpost');
$questionsbank = get_entity($questionsbankpost);
//$close_questionsbank = get_input('close_questionsbank');


/*
if (strcmp($close_questionsbank,'yes')==0){
   $questionsbank->opened = false;
   forward("questionsbank/edit/$questionsbankpost");
}

*/

if ($questionsbank->getSubtype() == "questionsbank" && $questionsbank->canEdit()) {

    $user_guid = elgg_get_logged_in_user_guid();

    $title = get_input('title');
    $tags = get_input('questionsbanktags');
    $access_id = get_input('access_id');
    $selected_action = get_input('submit');

    $container = get_entity($questionsbank->container_guid);

    // Cache to the session
    elgg_make_sticky_form('edit_questionsbank');

    // Make sure tags isn't blank
    if (strcmp($tags, "") == 0) {
        register_error(elgg_echo("questionsbank:tags_blank"));
        forward("questionsbank/edit/$questionsbankpost");
    }

    // Convert string of tags into a preformatted array
    $tagarray = string_to_tag_array($tags);

    // Make sure the title isn't blank
    if (strcmp($title, "") == 0) {
        register_error(elgg_echo("questionsbank:title_blank"));
        forward("questionsbank/edit/$questionsbankpost");
    }

    // Get owning user
    $owner = get_entity($questionsbank->getOwnerGUID());

    //Set its access
    $questionsbank->access_id = $access_id;

    // Set its title 
    $questionsbank->title = $title;

    // Save the questionsbank post
    if (!$questionsbank->save()) {
        register_error(elgg_echo("questionsbank:error_save"));
        forward("questionsbank/edit/$questionsbankpost");
    }

    // Now let's add tags.
    if (is_array($tagarray)) {
        $questionsbank->tags = $tagarray;
    }

    $options = array('relationship' => 'questionsbank_question', 'relationship_guid' => $questionsbankpost, 'inverse_relationship' => false, 'type' => 'object', 'subtype' => 'questionsbank_question');
    $questions = elgg_get_entities_from_relationship($options);

    // Questions and files access
    if (!empty($questions)) {
        foreach ($questions as $one_question) {
            $one_question->access_id = $questionsbank->access_id;
            if (!$one_question->save()) {
                register_error(elgg_echo("questionsbank:question_error_save"));
                forward("questionsbank/edit/$questionsbankpost");
            }
            $files = elgg_get_entities_from_relationship(array('relationship' => 'question_file_link', 'relationship_guid' => $one_question->getGUID(), 'inverse_relationship' => false, 'type' => 'object', 'limit' => 0));

            foreach ($files as $one_file) {
                $one_file->access_id = $one_question->access_id;
                if (!$one_file->save()) {
                    register_error(elgg_echo("questionsbank:file_error_save"));
                    forward("questionsbank/edit/$questionsbankpost");
                }
            }

        }
    }

    // Remove the questionsbank questionsbank cache        
    elgg_clear_sticky_form('edit_questionsbank');

    //Success message
    system_message(elgg_echo("questionsbank:updated"));

    // Forward 	
    if (strcmp($selected_action, elgg_echo('questionsbank:save')) == 0) {
        // Add to river
        elgg_create_river_item(array(
            'view' => 'river/object/questionsbank/update',
            'action_type' => 'update',
            'subject_guid' => $user_guid,
            'object_guid' => $questionsbankpost,
        ));

        if ($container instanceof ElggGroup) {
            forward(elgg_get_site_url() . 'questionsbank/group/' . $container->guid);
        } else {
            forward(elgg_get_site_url() . 'questionsbank/owner/' . $owner->username);
        }
    } /*else {
        if (strcmp($selected_action, elgg_echo('questionsbank:add_question')) == 0) {
            forward("questionsbank/add_question/$questionsbankpost");
        } else {
            forward("questionsbank/import_questionsbank/$questionsbankpost");
        }
    }*/
}

?>

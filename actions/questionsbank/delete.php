<?php

gatekeeper();

$questionsbankpost = get_input('guid');

$questionsbank = get_entity($questionsbankpost);
$container = get_entity($questionsbank->container_guid);
$owner = get_entity($questionsbank->getOwnerGUID());

if ($questionsbank->getSubtype() == "questionsbank" && $questionsbank->canEdit()) {
    //Delete questions (and files (question and correct response))
    $options = array('relationship' => 'questionsbank_question', 'relationship_guid' => $questionsbankpost, 'inverse_relationship' => false, 'type' => 'object', 'subtype' => 'questionsbank_question', 'limit' => 0);
    $questions = elgg_get_entities_from_relationship($options);

    if (!empty($questions)) {
        foreach ($questions as $one_question) {
            $files = elgg_get_entities_from_relationship(array('relationship' => 'question_file_link', 'relationship_guid' => $one_question->getGUID(), 'inverse_relationship' => false, 'type' => 'object', 'limit' => 0));

            foreach ($files as $one_file) {
                $deleted = $one_file->delete();
                if (!$deleted) {
                    register_error(elgg_echo("questionsbank:filenotdeleted"));
                    if ($container instanceof ElggGroup) {
                        forward(elgg_get_site_url() . 'questionsbank/group/' . $container->username);
                    } else {
                        forward(elgg_get_site_url() . 'questionsbank/owner/' . $owner->username);
                    }
                }
            }

            $deleted = $one_question->delete();
            if (!$deleted) {
                register_error(elgg_echo("questionsbank:questionnotdeleted"));
                if ($container instanceof ElggGroup) {
                    forward(elgg_get_site_url() . 'questionsbank/group/' . $container->username);
                } else {
                    forward(elgg_get_site_url() . 'questionsbank/owner/' . $owner->username);
                }
            }
        }
    }
    // Delete it!
    $deleted = $questionsbank->delete();
    if ($deleted > 0) {
        system_message(elgg_echo("questionsbank:deleted"));
    } else {
        register_error(elgg_echo("questionsbank:notdeleted"));
    }
    if ($container instanceof ElggGroup) {
        forward(elgg_get_site_url() . 'questionsbank/group/' . $container->username);
    } else {
        forward(elgg_get_site_url() . 'questionsbank/owner/' . $owner->username);
    }
}

?>
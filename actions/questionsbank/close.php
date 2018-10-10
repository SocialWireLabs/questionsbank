<?php

gatekeeper();
if (is_callable('group_gatekeeper'))
    group_gatekeeper();

$questionsbankpost = get_input('questionsbankpost');
$questionsbank = get_entity($questionsbankpost);
$edit = get_input('edit');

if ($questionsbank->getSubtype() == "questionsbank" && $questionsbank->canEdit()) {
    //Close
    $questionsbank->opened = false;

    // System message 
    system_message(elgg_echo("questionsbank:closed_listing"));

    if (strcmp($edit, 'no') == 0) {
        forward($_SERVER['HTTP_REFERER']);
    } else {
        forward("questionsbank/edit/$questionsbankpost");
    }
}

?>

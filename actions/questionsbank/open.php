<?php

gatekeeper();
if (is_callable('group_gatekeeper'))
    group_gatekeeper();

$questionsbankpost = get_input('questionsbankpost');
$questionsbank = get_entity($questionsbankpost);

if ($questionsbank->getSubtype() == "questionsbank" && $questionsbank->canEdit()) {
    //Open
    $questionsbank->opened = true;

    // System message 
    system_message(elgg_echo("questionsbank:opened_listing"));
    forward($_SERVER['HTTP_REFERER']);
}

?>

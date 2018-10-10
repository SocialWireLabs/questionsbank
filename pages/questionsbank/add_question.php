<?php

gatekeeper();
if (is_callable('group_gatekeeper'))
    group_gatekeeper();

$questionsbankpost = get_input('questionsbankpost');
$questionsbank = get_entity($questionsbankpost);
$question_type = get_input('question_type');
if (empty($question_type))
    $question_type = 'simple';

if ($questionsbank && $questionsbank->canEdit()) {
    elgg_set_page_owner_guid($questionsbank->getContainerGUID());
    $container = elgg_get_page_owner_entity();

    if (elgg_instanceof($container, 'group')) {
        elgg_push_breadcrumb($container->name, "questionsbank/group/$container->guid/all");
    } else {
        elgg_push_breadcrumb($container->name, "questionsbank/owner/$container->username");
    }
    elgg_push_breadcrumb($questionsbank->title, $questionsbank->getURL());

    $title = elgg_echo('questionsbank:addquestionpost');
    $content = elgg_view('forms/questionsbank/add_question', array('entity' => $questionsbank, 'question_type' => $question_type));
    $body = elgg_view_layout('content', array('filter' => '', 'content' => $content, 'title' => $title));
}
echo elgg_view_page($title, $body);

?>
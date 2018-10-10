<?php

gatekeeper();
if (is_callable('group_gatekeeper'))
    group_gatekeeper();

$thisquestionsbankpost = get_input('thisquestionsbankpost');
$thisquestionsbank = get_entity($thisquestionsbankpost);

$container_guid = $thisquestionsbank->container_guid;
$container = get_entity($container_guid);

$page_owner = $container;
if (elgg_instanceof($container, 'object')) {
    $page_owner = $container->getContainerEntity();
}
elgg_set_page_owner_guid($page_owner->getGUID());

if (elgg_instanceof($container, 'group')) {
    elgg_push_breadcrumb($container->name, "questionsbank/group/$container->guid/all");
} else {
    elgg_push_breadcrumb($container->name, "questionsbank/owner/$container->username");
}
elgg_push_breadcrumb($thisquestionsbank->title, $thisquestionsbank->getURL());

if ($thisquestionsbank && $thisquestionsbank->canEdit()) {
    $title = elgg_echo('questionsbank:downloadquestionsbankpost');
    $content = elgg_view('forms/questionsbank/download_questions', array('entity' => $thisquestionsbank));
}

$body = elgg_view_layout('content', array('filter' => '', 'content' => $content, 'title' => $title));
echo elgg_view_page($title, $body);

?>
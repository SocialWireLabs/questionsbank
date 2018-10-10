<?php

gatekeeper();
if (is_callable('group_gatekeeper'))
    group_gatekeeper();

$questionsbankpost = get_input('questionsbankpost');
$questionsbank = get_entity($questionsbankpost);
$user_guid = elgg_get_logged_in_user_guid();
$user = get_entity($user_guid);

$container_guid = $questionsbank->container_guid;
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
elgg_push_breadcrumb($questionsbank->title, $questionsbank->getURL());
elgg_push_breadcrumb(elgg_echo('edit'));

if ($questionsbank && $questionsbank->canEdit()) {
    $title = elgg_echo('questionsbank:editpost');
    $content = elgg_view('forms/questionsbank/edit', array('entity' => $questionsbank));
}

$body = elgg_view_layout('content', array('filter' => '', 'content' => $content, 'title' => $title));
echo elgg_view_page($title, $body);

?>
<?php

gatekeeper();
if (is_callable('group_gatekeeper'))
    group_gatekeeper();

$questionsbankpost = get_input('questionsbankpost');
$questionsbank = get_entity($questionsbankpost);
$offset = get_input('offset');
if (empty($offset))
    $offset = 0;

if ($questionsbank) {
    elgg_set_page_owner_guid($questionsbank->getContainerGUID());
    $container = elgg_get_page_owner_entity();

    if (elgg_instanceof($container, 'group')) {
        elgg_push_breadcrumb($container->name, "questionsbank/group/$container->guid/all");
    } else {
        elgg_push_breadcrumb($container->name, "questionsbank/owner/$container->username");
    }
    elgg_push_breadcrumb($questionsbank->title);

    $title = elgg_echo('questionsbank:showquestionspost');

    $content = elgg_view('object/questionsbank', array('full_view' => true, 'entity' => $questionsbank, 'offset' => $offset));
    $content .= '<div id="comments">' . elgg_view_comments($questionsbank) . '</div>';

    $body = elgg_view_layout('content', array('filter' => '', 'content' => $content, 'title' => $title));

    echo elgg_view_page($title, $body);

} else {
    register_error(elgg_echo('questionsbank:notfound'));
    forward();
}

?>
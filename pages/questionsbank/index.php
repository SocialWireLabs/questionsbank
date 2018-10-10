<?php

gatekeeper();
if (is_callable('group_gatekeeper'))
    group_gatekeeper();

$owner = elgg_get_page_owner_entity();

if (!$owner) {
    forward('questionsbank/all');
}

$owner_guid = $owner->getGUID();

elgg_push_breadcrumb($owner->name);

elgg_register_title_button('questionsbank', 'add');

if ($owner instanceof ElggGroup)
    $username = "group:" . $owner->guid;
else
    $username = $owner->username;

$offset = get_input('offset');
if (empty($offset)) {
    $offset = 0;
}
$limit = 10;

$questionsbanks = elgg_get_entities_from_metadata(array('type' => 'object', 'subtype' => 'questionsbank', 'limit' => false, 'container_guid' => $owner_guid, 'order_by' => 'e.time_created desc'));

if ($questionsbanks) {
    $num_questionsbanks = count($questionsbanks);
} else {
    $num_questionsbanks = 0;
}

if ($num_questionsbanks > 0) {

    $k = 0;
    $item = $offset;
    $questionsbanks_range = array();
    while (($k < $limit) && ($item < $num_questionsbanks)) {
        $questionsbanks_range[$k] = $questionsbanks[$item];
        $k = $k + 1;
        $item = $item + 1;
    }

    $vars = array('count' => $num_questionsbanks, 'limit' => $limit, 'offset' => $offset, 'full_view' => false);
    $content .= elgg_view_entity_list($questionsbanks_range, $vars);
} else {
    $content = '<p>' . elgg_echo('questionsbank:none') . '</p>';
}

$title = elgg_echo('questionsbank:user', array($owner->name));

$filter_context = '';
if ($owner->getGUID() == elgg_get_logged_in_user_guid()) {
    $filter_context = 'mine';
}

$params = array('filter_context' => $filter_context, 'content' => $content, 'title' => $title,
);

if (elgg_instanceof($owner, 'group')) {
    $params['filter'] = '';
}

$body = elgg_view_layout('content', $params);

echo elgg_view_page($title, $body);

?>
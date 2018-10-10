<?php

$owner = elgg_get_page_owner_entity();
if (!$owner) {
    forward('questionsbank/all');
}

elgg_push_breadcrumb($owner->name, "questionsbank/owner/$owner->username");
elgg_push_breadcrumb(elgg_echo('friends'));

elgg_register_title_button();

$offset = get_input('offset');
if (empty($offset)) {
    $offset = 0;
}
$limit = 10;

$questionsbanks = elgg_get_entities_from_relationship(array(
    'type' => 'object',
    'subtype' => 'questionsbank',
    'limit' => false,
    'offset' => 0,
    'relationship' => 'friend',
    'relationship_guid' => $owner->getGUID(),
    'relationship_join_on' => 'container_guid'
));


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
    $content = elgg_echo('questionsbank:none');
}

$title = elgg_echo('questionsbank:user:friends', array($owner->name));

$params = array('filter_context' => 'friends', 'content' => $content, 'title' => $title);
$body = elgg_view_layout('content', $params);

echo elgg_view_page($title, $body);

?>
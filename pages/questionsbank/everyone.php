<?php

elgg_pop_breadcrumb();
elgg_push_breadcrumb(elgg_echo('questionsbanks'));

elgg_register_title_button();

$offset = get_input('offset');
if (empty($offset)) {
    $offset = 0;
}
$limit = 10;

$questionsbanks = elgg_get_entities_from_metadata(array('type' => 'object', 'subtype' => 'questionsbank', 'limit' => false, 'order_by' => 'e.time_created desc'));

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

$title = elgg_echo('questionsbank:all');

$body = elgg_view_layout('content', array('filter_context' => 'all', 'content' => $content, 'title' => $title));

echo elgg_view_page($title, $body);

?>
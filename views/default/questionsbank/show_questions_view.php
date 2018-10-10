<?php

$questionsbank = $vars['entity'];
$questionsbankpost = $questionsbank->getGUID();
$question = $vars['question'];
$index = $question->index;

$owner = $questionsbank->getOwnerEntity();
$owner_guid = $owner->getGUID();
$user_guid = elgg_get_logged_in_user_guid();

$container_guid = $questionsbank->container_guid;
$container = get_entity($container_guid);
if ($container instanceof ElggGroup) {
    $group_owner_guid = $container->owner_guid;
    $operator = false;
    if (($group_owner_guid == $user_guid) || (check_entity_relationship($user_guid, 'group_admin', $container_guid))) {
        $operator = true;
    }
}

//$info = "<div class=\"questionsbank_options\">";  //SW
$info .= "<tr>";
if (($questionsbank->canEdit()) && (!$questionsbank->opened) && (($owner_guid == $user_guid) || (($container instanceof ElggGroup) && ($operator)) || (elgg_is_admin_logged_in()))) {

    //Delete
    $url_delete = elgg_add_action_tokens_to_url(elgg_get_site_url() . "action/questionsbank/delete_question?questionsbankpost=" . $questionsbankpost . "&index=" . $index);
    $word_delete = elgg_echo('delete');

    //Edit
    $url_edit = elgg_add_action_tokens_to_url(elgg_get_site_url() . "questionsbank/edit_question/" . $questionsbankpost . "/" . $index);
    $word_edit = elgg_echo('edit');

    //$info .= "<p><a href=\"{$url_edit}\">{$word_edit}</a> <a href=\"{$url_delete}\">{$word_delete}</a></p>";
    $info1 .= "<td class='td_questions_table_option'><p><a href=\"{$url_edit}\">{$word_edit}</a></p></td>";
    $info1 .= "<td class='td_questions_table_option'><p><a href=\"{$url_delete}\">{$word_delete}</a></p></td>";
}
//$info .= "</div>";

$question_label = elgg_echo('questionsbank:question');
$text_question = elgg_get_excerpt($question->question, 45);
$tags = elgg_view('output/tags', array('tags' => $question->tags));

$url_show_question = elgg_add_action_tokens_to_url(elgg_get_site_url() . "questionsbank/show_question_questionsbank/$questionsbankpost/$questionsbankpost/$index");
if ($tags != "")
    $word_show_question = $question_label . ": " . $text_question;
else
    $word_show_question = $question_label . ": " . $text_question . "</br></br>";

$info .= "<td class='td_questions_table'>";
$info .= "<p>";
$info .= "<a href=\"{$url_show_question}\">{$word_show_question}</a>" . "<br>";
$info .= $tags;
$info .= "</p>";
$info .= "</td>";
$info .= $info1;
$info .= "</tr>";
//echo "<div class=\"contentWrapper\">";
echo elgg_echo($info);
//echo "</div>";
?>
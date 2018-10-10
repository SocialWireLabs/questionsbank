<?php

$questionsbank = $vars['entity'];
$questionsbankpost = $questionsbank->getGUID();

$offset = $vars['offset'];
$limit = 10;
$this_limit = $limit + $offset;

$options = array('relationship' => 'questionsbank_question', 'relationship_guid' => $questionsbankpost, 'inverse_relationship' => false, 'type' => 'object', 'subtype' => 'questionsbank_question', 'limit' => 0, 'order_by' => 'e.time_created desc');
$questions = elgg_get_entities_from_relationship($options);

if (!empty($questions)) {
    $count = count($questions);
    $i = 0;
    echo "<table class='questions_table'>";  //SW
    foreach ($questions as $one_question) {
        if (($i >= $offset) && ($i < $this_limit))
            echo elgg_view("questionsbank/show_questions_view", array('entity' => $questionsbank, 'question' => $one_question));
        //echo "<br>";
        $i = $i + 1;
    }
    echo "</table>";
    echo elgg_view("navigation/pagination", array('count' => $count, 'offset' => $offset, 'limit' => $limit));

}

///////////////////////////////////////////////////////////////////////////////////


$add_question_button_text = elgg_echo('questionsbank:add_question');
$add_question_button_link = elgg_get_site_url() . 'questionsbank/add_question/' . $questionsbankpost;
$add_question_button = elgg_view('input/button', array('name' => 'return', 'class' => 'elgg-button-cancel', 'value' => $add_question_button_text));
$add_question_button = "<a href=" . $add_question_button_link . ">" . $add_question_button . "</a>";

$import_questionsbank_button_text = elgg_echo('questionsbank:import_questionsbank');
$import_questionsbank_button_link = elgg_get_site_url() . 'questionsbank/import_questionsbank/' . $questionsbankpost;
$import_questionsbank_button = elgg_view('input/button', array('name' => 'return', 'class' => 'elgg-button-cancel', 'value' => $import_questionsbank_button_text));
$import_questionsbank_button = "<a href=" . $import_questionsbank_button_link . ">" . $import_questionsbank_button . "</a>";

$download_questions_button_text = elgg_echo('questionsbank:download_upload_questions');
$download_questions_button_link = elgg_get_site_url() . 'questionsbank/download_questions/' . $questionsbankpost;
$download_questions_button = elgg_view('input/button', array('name' => 'return', 'class' => 'elgg-button-cancel', 'value' => $download_questions_button_text));
$download_questions_button = "<a href=" . $download_questions_button_link . ">" . $download_questions_button . "</a>";

$wwwroot = elgg_get_config('wwwroot');
$img_template = '<img border="0" width="20" height="20" alt="%s" title="%s" src="' . $wwwroot . 'mod/questionsbank/graphics/%s" />';

$text_info = elgg_echo("questionsbank:text_info");
$img_info = sprintf($img_template, $text_info, $text_info, "info.jpeg");
$link_info = "<a href=\"{$url_info}\">{$img_info}</a>";

echo "<br>$add_question_button $import_questionsbank_button $download_questions_button $img_info";

?>




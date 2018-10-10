<?php

$thisquestionsbankpost = $vars['entity']->getGUID();
$action = "questionsbank/select_questions_questionsbank";

$questionsbankpost = $vars['questionsbankpost'];
$questionsbank = get_entity($questionsbankpost);
$tags = $vars['tags'];
if (strcmp($tags, "") != 0)
    $tagsarray = string_to_tag_array($tags);
$question_types = $vars['question_types'];
$question_types = explode(",", $question_types);
$question_types = array_map('trim', $question_types);

$i = 0;
$question_type = array();
foreach ($question_types as $one_type) {
    switch ($one_type) {
        case 'simple':
            $question_type[$i] = "simple";
            break;
        case 'urls_files':
            $question_type[$i] = "urls_files";
            break;
    }
    $i = $i + 1;
}

$response_types = $vars['response_types'];
$response_types = explode(",", $response_types);
$response_types = array_map('trim', $response_types);

$i = 0;
$response_type = array();
foreach ($response_types as $one_type) {
    switch ($one_type) {
        case 'radiobutton':
            $response_type[$i] = "radiobutton";
            break;
        case 'checkbox':
            $response_type[$i] = "checkbox";
            break;
        case 'grid':
            $response_type[$i]="grid";
            break;
        case 'pairs':
            $response_type[$i]="pairs";
            break;
        case 'dropdown':
            $response_type[$i]="dropdown";
            break;
    }
    $i = $i + 1;
}

$questions_selection_type = $vars['questions_selection_type'];
if (strcmp($questions_selection_type, 'questionsbank_random_questions_selection_type') == 0)
    $num_questions_import = $vars['num_questions_import'];

$options = array('relationship' => 'questionsbank_question', 'relationship_guid' => $questionsbankpost, 'inverse_relationship' => false, 'type' => 'object', 'subtype' => 'questionsbank_question', 'limit' => 0);
$questions = elgg_get_entities_from_relationship($options);

$selected_questions = array();
$i = 0;
foreach ($questions as $one_question) {
    if (strcmp($tags, "") == 0) {
        $tags_found = true;
    } else {
        if (empty($one_question->tags)) {
            $tags_found = false;
        } else {
            $tags_found = questionsbank_comprobate_tags($one_question->tags, $tagsarray);
        }
    }
    $question_type_found = questionsbank_comprobate_type($one_question->question_type, $question_type);
    $response_type_found = questionsbank_comprobate_type($one_question->response_type, $response_type);
    if (($tags_found) && ($question_type_found) && ($response_type_found)) {
        $selected_questions[$i] = $one_question;
        $i = $i + 1;
    }
}
$num_questions = $i;

if ($num_questions > 0) {
    if (strcmp($questions_selection_type, 'questionsbank_random_questions_selection_type') == 0) {
        if ($num_questions < $num_questions_import) {
            $selected_questions_keys = array_rand($selected_questions, $num_questions);
        } else {
            $selected_questions_keys = array_rand($selected_questions, $num_questions_import);
        }
        $random_selected_questions = array();
        $i = 0;
        if (is_array($selected_questions_keys)) {
            foreach ($selected_questions_keys as $one_key) {
                $random_selected_questions[$i] = $selected_questions[$one_key];
                $i = $i + 1;
            }
        } else {
            $random_selected_questions[$i] = $selected_questions[$selected_questions_keys];
        }
    }
}

?>
    <div class="contentWrapper">
        <form action="<?php echo elgg_get_site_url(); ?>action/<?php echo $action; ?>"
              name="select_questions_questionsbank_questionsbank" enctype="multipart/form-data" method="post">
            <?php echo elgg_view('input/securitytoken'); ?>

            <p>
                <?php
                if ($num_questions > 0){
                $i = 1;
                if (strcmp($questions_selection_type, 'questionsbank_manual_questions_selection_type') == 0) {
                    $selected_questions_indexes = "";
                    foreach ($selected_questions as $one_selected_question) {
                        $index = $one_selected_question->index;
                        $url_show_question = elgg_add_action_tokens_to_url(elgg_get_site_url() . "questionsbank/show_question_questionsbank/$thisquestionsbankpost/$questionsbankpost/$index");
                        $tags = elgg_view('output/tags', array('tags' => $one_selected_question->tags));
                        $text_question = elgg_get_excerpt($one_selected_question->question, 45);
                        $question_label = elgg_echo('questionsbank:question') . ": " . $text_question;
                        $question_name = "question_" . $index;
                        $check = "<input type = \"checkbox\" name = $question_name>";
                        $info = "";
                        $info .= "<p>" . $check . " " . "<a href=\"{$url_show_question}\">{$question_label}</a>" . "<br>";
                        $info .= $tags . "</p>";
                        echo elgg_echo($info);
                        echo "<br>";
                        if ($i == 1) {
                            $selected_questions_indexes .= $index;
                        } else {
                            $selected_questions_indexes .= "," . $index;
                        }
                        $i = $i + 1;
                    }
                } else {
                    $random_selected_questions_indexes = "";
                    foreach ($random_selected_questions as $one_selected_question) {
                        $index = $one_selected_question->index;
                        $url_show_question = elgg_add_action_tokens_to_url(elgg_get_site_url() . "questionsbank/show_question_questionsbank/$thisquestionsbankpost/$questionsbankpost/$index");
                        $tags = elgg_view('output/tags', array('tags' => $one_selected_question->tags));
                        $text_question = elgg_get_excerpt($one_selected_question->question, 45);
                        $question_label = elgg_echo('questionsbank:question') . ": " . $text_question;
                        $info = "";
                        $info .= "<p>" . "<a href=\"{$url_show_question}\">{$question_label}</a>" . "<br>";
                        $info .= $tags . "</p>";
                        echo elgg_echo($info);
                        echo "<br>";
                        if ($i == 1) {
                            $random_selected_questions_indexes .= $index;
                        } else {
                            $random_selected_questions_indexes .= "," . $index;
                        }
                        $i = $i + 1;
                    }
                }
                ?>
            </p>

        <?php
        echo "<input type=\"hidden\" name=\"thisquestionsbankpost\" value=\"{$thisquestionsbankpost}\" />";
        echo "<input type=\"hidden\" name=\"questionsbankpost\" value=\"{$questionsbankpost}\" />";
        echo "<input type=\"hidden\" name=\"questions_selection_type\" value=\"{$questions_selection_type}\" />";

        if (strcmp($questions_selection_type, 'questionsbank_manual_questions_selection_type') == 0) {
            echo "<input type=\"hidden\" name=\"selected_questions_indexes\" value=\"{$selected_questions_indexes}\" />";
        } else {
            echo "<input type=\"hidden\" name=\"random_selected_questions_indexes\" value=\"{$random_selected_questions_indexes}\" />";
        }
        ?>

            <p>
                <?php
                $submit_input = elgg_view('input/submit', array('name' => 'submit', 'value' => elgg_echo("questionsbank:import")));
                echo $submit_input;
                ?>
            </p>
        <?php
        } else {
            echo elgg_echo('questionsbank:not_questions');
        }
        ?>

        </form>
    </div>
<?php

//////////////////////////////////////////////////////////////////

function questionsbank_comprobate_tags($question_tags, $selected_tags)
{
    $found = false;
    if (is_array($selected_tags)) {
        foreach ($selected_tags as $one_selected_tag) {
            if (is_array($question_tags)) {
                if (in_array($one_selected_tag, $question_tags)) {
                    $found = true;
                    break;
                }
            } else {
                if (strcmp($one_selected_tag, $question_tags) == 0) {
                    $found = true;
                    break;
                }
            }
        }
    } else {
        if (is_array($question_tags)) {
            if (in_array($selected_tags, $question_tags)) {
                $found = true;
            }
        } else {
            if (strcmp($selected_tags, $question_tags) == 0) {
                $found = true;
            }
        }
    }
    return $found;
}

function questionsbank_comprobate_type($type, $selected_type)
{
    $found = false;
    if (is_array($selected_type)) {
        if (in_array($type, $selected_type))
            $found = true;
    } else {
        if (strcmp($type, $selected_type) == 0)
            $found = true;
    }
    return $found;
}

?>
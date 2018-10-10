<?php

$thisquestionsbankpost = $vars['entity']->getGUID();
$container_guid = get_entity($thisquestionsbankpost)->container_guid;
$container = get_entity($container_guid);

$action = "questionsbank/import_questionsbank";

$url = elgg_get_site_url() . "questionsbank/import_questionsbank/$thisquestionsbankpost";

$user_guid = elgg_get_logged_in_user_guid();

$questionsbanks = elgg_get_entities(array('type' => 'object', 'subtype' => 'questionsbank', 'limit' => false, 'owner_guid' => $user_guid));

if ($container instanceof ElggGroup) {
    $members = $container->getMembers(array('limit' => false));
    foreach ($members as $member) {
        $member_guid = $member->getGUID();
        $group_owner_guid = $container->owner_guid;
        if (($member_guid != $user_guid) && (($group_owner_guid == $member_guid) || (check_entity_relationship($member_guid, 'group_admin', $container_guid)))) {
            $other_questionsbanks = elgg_get_entities_from_metadata(array('type' => 'object', 'subtype' => 'questionsbank', 'limit' => false, 'owner_guid' => $member_guid, 'container_guid' => $container_guid));
            if ($questionsbanks)
                $questionsbanks = array_merge($questionsbanks, $other_questionsbanks);
            else
                $questionsbanks = $other_questionsbanks;
        }
    }
}

$question_type = array();
$response_type = array();
if (!elgg_is_sticky_form('import_questionsbank_questionsbank')) {
    $questionsbankpost = get_input('selectedquestionsbankpost');
    if (empty($questionsbankpost)&&(!empty($questionsbanks))){
      $first_questionsbank_guid = $questionsbanks[0]->getGUID();
      if ($first_questionsbank_guid == $thisquestionsbankpost) {
         if (count($questionsbanks)>1)
      	    $questionsbankpost = $questionsbanks[1]->getGUID();
      } else {
         $questionsbankpost = $questionsbanks[0]->getGUID();
      }
    }
    $tags = "";
    $question_type[0] = 'simple';
    $response_type[0] = 'radiobutton';
    $questions_selection_type = 'questionsbank_manual_questions_selection_type';
    $num_questions_import = "1";
} else {
    $questionsbankpost = elgg_get_sticky_value('import_questionsbank_questionsbank', 'questionsbankpost');
    $tags = elgg_get_sticky_value('import_questionsbank_questionsbank', 'tags');
    $question_type = elgg_get_sticky_value('import_questionsbank_questionsbank', 'question_type');
    $response_type = elgg_get_sticky_value('import_questionsbank_questionsbank', 'response_type');
    $questions_selection_type = elgg_get_sticky_value('import_questionsbank_questionsbank', 'questions_selection_type');
    $num_questions_import = elgg_get_sticky_value('import_questionsbank_questionsbank', 'num_questions_import');
}

elgg_clear_sticky_form('import_questionsbank_questionsbank');


$options_question_type = array(elgg_echo('questionsbank:question_simple') => 'simple', elgg_echo('questionsbank:question_urls_files') => 'urls_files');


$options_response_type = array(elgg_echo('questionsbank:response_type_radiobutton') => 'radiobutton', elgg_echo('questionsbank:response_type_checkbox') => 'checkbox', elgg_echo('questionsbank:response_type_grid') => 'grid', elgg_echo('questionsbank:response_type_pairs') => 'pairs', elgg_echo('questionsbank:response_type_dropdown') => 'dropdown');

$options_questions_selection_type = array();
$options_questions_selection_type[0] = elgg_echo('questionsbank:manual_questions_selection_type');
$options_questions_selection_type[1] = elgg_echo('questionsbank:random_questions_selection_type');
$op_questions_selection_type = array();
$op_questions_selection_type[0] = 'questionsbank_manual_questions_selection_type';
$op_questions_selection_type[1] = 'questionsbank_random_questions_selection_type';

if (strcmp($questions_selection_type, 'questionsbank_manual_questions_selection_type') == 0) {
    $checked_radio_questions_selection_type_0 = "checked = \"checked\"";
    $checked_radio_questions_selection_type_1 = "";
    $style_display_num_questions_import = "display:none";
} else {
    $checked_radio_questions_selection_type_0 = "";
    $checked_radio_questions_selection_type_1 = "checked = \"checked\"";
    $style_display_num_questions_import = "display:block";
}

?>
<div class="contentWrapper">
    <form action="<?php echo elgg_get_site_url(); ?>action/<?php echo $action; ?>"
          name="import_questionsbank_questionsbank" enctype="multipart/form-data" method="post">
        <?php echo elgg_view('input/securitytoken'); ?>

        <p>
            <b><?php echo elgg_echo('questionsbank:questionsbank_label'); ?></b>
        </p>

        <p>
            <select name="questionsbankpost" onchange="questionsbank_reload_import_questionsbank_form(this)">  <!--SW-->
                <?php
                foreach ($questionsbanks as $one_questionsbank) {
                    $questionsbank_guid = $one_questionsbank->getGUID();

                    echo $questionsbank_guid;
                    if ($thisquestionsbankpost != $questionsbank_guid) {
                        $questionsbank_title = $one_questionsbank->title;
                        ?>
                        <option
                            value="<?php echo $questionsbank_guid; ?>" <?php if ($questionsbank_guid == $questionsbankpost) echo "selected=\"selected\""; ?>> <?php echo $questionsbank_title; ?> </option>
                        <?php
                    }
                }
                ?>
            </select>
        </p>

        <p>
            <b><?php echo elgg_echo('questionsbank:tags_label'); ?></b>
        </p>
        <p>
            <?php
            if ($questionsbankpost)
               questionsbank_tagcloud_questionsbank($user_guid, $questionsbankpost);
            ?>
        </p>
        <p>
            <?php echo elgg_view('input/text', array('name' => 'tags', 'internalid' => 'tags', 'value' => $tags)); ?>
        </p>

        <p>
            <b><?php echo elgg_echo('questionsbank:question_type_label'); ?></b>
        </p>
        <p>
            <?php echo elgg_view('input/checkboxes', array('name' => 'question_type', 'options' => $options_question_type, 'value' => $question_type)); ?>
        </p>

        <p>
            <b><?php echo elgg_echo('questionsbank:response_type_label'); ?></b>
        </p>
        <p>
            <?php
            echo elgg_view('input/checkboxes', array('name' => 'response_type', 'options' => $options_response_type, 'value' => $response_type));
            ?>
        </p>

        <p>
            <b><?php echo elgg_echo('questionsbank:questions_selection_type_label'); ?></b>
        </p>
        <p>
            <?php
            echo "<input type=\"radio\" name=\"questions_selection_type\" value=$op_questions_selection_type[0] $checked_radio_questions_selection_type_0 onChange=\"questionsbank_show_num_questions_import()\">$options_questions_selection_type[0]";
            ?>
            <br>
            <?php
            echo "<input type=\"radio\" name=\"questions_selection_type\" value=$op_questions_selection_type[1] $checked_radio_questions_selection_type_1 onChange=\"questionsbank_show_num_questions_import()\">$options_questions_selection_type[1]";
            ?>
            <br>
        </p>

        <p>
        <div id="resultsDiv_num_questions_import" style="<?php echo $style_display_num_questions_import; ?>;">
            <?php
            $num_questions_import_label = elgg_echo('questionsbank:num_questions_import_label');
            echo "$num_questions_import_label <input type = \"text\" name = \"num_questions_import\" value = $num_questions_import>";
            ?>
        </div>
        </p>

        <input type="hidden" name="thisquestionsbankpost" value="<?php echo $thisquestionsbankpost; ?>">

        <p>
            <?php
            $submit_input = elgg_view('input/submit', array('name' => 'submit', 'value' => elgg_echo("questionsbank:import")));
            echo $submit_input;
            ?>
        </p>

    </form>
</div>

<?php

function questionsbank_tagcloud_questionsbank($user_guid, $questionsbankpost)
{
    $cloud = "";
    $max = 0;
    $options = array('relationship' => 'questionsbank_question', 'relationship_guid' => $questionsbankpost, 'inverse_relationship' => false, 'type' => 'object', 'subtype' => 'questionsbank_question', 'limit' => 0);
    $questions = elgg_get_entities_from_relationship($options);
    $my_tags = array();
    $my_tags_counts = array();
    $num_tags = 0;
    foreach ($questions as $one_question) {
        $tags = $one_question->tags;
        if (!empty($tags)) {
            if (is_array($tags)) {
                foreach ($tags as $tag) {
                    if (!in_array($tag, $my_tags)) {
                        $my_tags[] = $tag;
                        $my_tags_count[$tag] = 1;
                    } else {
                        $my_tags_count[$tag] = $my_tags_count[$tag] + 1;
                    }
                    $num_tags = $num_tags + 1;
                }
            } else {
                if (!in_array($tags, $my_tags)) {
                    $my_tags[] = $tags;
                    $my_tags_count[$tags] = 1;
                } else {
                    $my_tags_count[$tags] = $my_tags_count[$tags] + 1;
                }
                $num_tags = $num_tags + 1;
            }
        }
    }
    if (!empty($my_tags)) {
        foreach ($my_tags as $tag) {
            $total = $my_tags_count[$tag];
            if ($total > $max)
                $max = $total;
            if (!empty($cloud)) {
                $cloud .= ", ";
            }
            $tag_size = round((log($total) / log($max + .0001)) * 100) + 30;
            if ($tag_size < 60) {
                $tag_size = 60;
            }
            //Aquí es donde se deja de apuntar a alguna url al poner #
            $cloud .= "<a href=\"#\" onclick=\"questionsbank_get_this_tag(this)\" style=\"font-size: {$tag_size}%\"title=\"" . addslashes($tag) . " ({$total})\"style=\"text-decoration:none;\">" . htmlentities($tag, ENT_QUOTES, 'UTF-8') . "</a>";
        }
        echo("<div class=\"questionsbank_frame\">");
        echo $cloud;
        echo("</div>");
    }
}

?>

<script language="javascript">


    function questionsbank_reload_import_questionsbank_form(select) {   //SW

        location.href = "<?php echo $url; ?>" + "&selectedquestionsbankpost=" + select.options[select.selectedIndex].value;

    }


    function questionsbank_get_this_tag(link) {
        var this_tag = link.innerHTML;
        var previous_tags = document.getElementById('tags').value;
        if (previous_tags == "")
            document.import_questionsbank_questionsbank.tags.value = this_tag;
        else
            document.import_questionsbank_questionsbank.tags.value = previous_tags + "," + this_tag;
    }

    function questionsbank_show_num_questions_import() {
        var resultsDiv_num_questions_import = document.getElementById('resultsDiv_num_questions_import');

        if (resultsDiv_num_questions_import.style.display == 'none') {
            resultsDiv_num_questions_import.style.display = 'block';
        } else {
            resultsDiv_num_questions_import.style.display = 'none';
        }
    }


</script>




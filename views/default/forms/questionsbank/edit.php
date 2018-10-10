<div class="contentWrapper">

    <?php

    $questionsbank = $vars['entity'];
    $questionsbankpost = $questionsbank->getGUID();
    $action = "questionsbank/edit";
    $container_guid = $questionsbank->container_guid;
    $container = get_entity($container_guid);

    /*
    if ($questionsbank->opened){
       
       $questionsbank_opened = elgg_echo('questionsbank:opened');
       $close_questionsbank = elgg_echo('questionsbank:close');
       $form_body = "";
       $form_body .= "<p>" . $questionsbank_opened . "</p>";
       $entity_hidden = elgg_view('input/hidden', array('name' => 'questionsbankpost', 'value' => $vars['entity']->getGUID()));
       $entity_hidden .= elgg_view('input/hidden', array('name' => 'close_questionsbank', 'value' => 'yes'));
       $submit_input = elgg_view('input/submit', array('name' => 'submit', 'value' => $close_questionsbank));
       $form_body .= "<p>" . $submit_input . $entity_hidden . "</p>";
       $vars_url = elgg_get_site_url();
       echo elgg_view('input/form', array('action' => "{$vars_url}action/$action", 'body' => $form_body));
    
    } else {
    
    */

    if (!elgg_is_sticky_form('edit_questionsbank')) {
        $title = $questionsbank->title;
        $tags = $questionsbank->tags;
        $access_id = $questionsbank->access_id;
    } else {
        $title = elgg_get_sticky_value('edit_questionsbank', 'title');
        $tags = elgg_get_sticky_value('edit_questionsbank', 'questionsbanktags');
        $access_id = elgg_get_sticky_value('edit_questionsbank', 'access_id');
    }

    elgg_clear_sticky_form('edit_questionsbank');

    $tag_label = elgg_echo('tags');
    $tag_input = elgg_view('input/tags', array('name' => 'questionsbanktags', 'value' => $tags));

    if ($container instanceof ElggGroup) {
        $access_input = elgg_view('input/hidden', array('name' => 'access_id', 'value' => $access_id));
    } else {
        $access_label = elgg_echo('access');
        $access_input = elgg_view('input/access', array('name' => 'access_id', 'value' => $access_id));
    }

    $questionsbank_add_question = elgg_echo('questionsbank:add_question');
    $questionsbank_import_questionsbank = elgg_echo('questionsbank:import_questionsbank');
    $questionsbank_save = elgg_echo('questionsbank:save');
    $submit_input_add_question = elgg_view('input/submit', array('name' => 'submit', 'value' => $questionsbank_add_question));
    $submit_input_import_questionsbank = elgg_view('input/submit', array('name' => 'submit', 'value' => $questionsbank_import_questionsbank));
    $submit_input_save = elgg_view('input/submit', array('name' => 'submit', 'value' => $questionsbank_save));

    ?>
    <form action="<?php echo elgg_get_site_url() . "action/" . $action ?>" name="edit_questionsbank"
          enctype="multipart/form-data" method="post">

        <?php echo elgg_view('input/securitytoken'); ?>

        <p>
            <b><?php echo elgg_echo("questionsbank:title"); ?></b><br>
            <?php echo elgg_view("input/text", array('name' => 'title', 'value' => $title)); ?>
        </p>
        <p>
            <b><?php echo $tag_label; ?></b><br>
            <?php echo $tag_input; ?>
        </p><br>

        <?php if ($container instanceof ElggGroup) {
            echo $access_input;
        } else { ?> <p>
            <b><?php echo $access_label; ?></b><br>
            <?php echo $access_input; ?>
        </p><br>
        <?php }

        //echo "$submit_input_add_question $submit_input_import_questionsbank $submit_input_save";
        echo  "$submit_input_save";
        ?>

        <input type="hidden" name="questionsbankpost" value="<?php echo $questionsbankpost; ?>">

    </form>

    <?php
    //}
    ?>

</div>


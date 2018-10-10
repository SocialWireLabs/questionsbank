<div class="contentWrapper">

    <?php

    $action = "questionsbank/add";
    $container_guid = $vars['container_guid'];
    $container = get_entity($container_guid);
    if (!elgg_is_sticky_form('add_questionsbank')) {
        $title = "";
        $tags = "";
        if ($container instanceof ElggGroup)
            $access_id = $container->teachers_acl;
        else
            $access_id = 0;
    } else {
        $title = elgg_get_sticky_value('add_questionsbank', 'title');
        $tags = elgg_get_sticky_value('add_questionsbank', 'questionsbanktags');
        $access_id = elgg_get_sticky_value('add_questionsbank', 'access_id');
    }

    elgg_clear_sticky_form('add_questionsbank');

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

    <form action="<?php echo elgg_get_site_url() . "action/" . $action ?>" name="add_questionsbank"
          enctype="multipart/form-data" method="post">

        <?php echo elgg_view('input/securitytoken'); ?>

        <p>
            <b><?php echo elgg_echo("questionsbank:title"); ?></b><br>
            <?php echo elgg_view("input/text", array('name' => 'title', 'value' => $title));
            ?>
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
        echo "$submit_input_save";
        ?>

        <input type="hidden" name="container_guid" value="<?php echo $vars['container_guid']; ?>">

    </form>

</div>


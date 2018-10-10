<?php

$thisquestionsbankpost = $vars['entity']->getGUID();
$action = "questionsbank/download_questions";

$url = elgg_get_site_url() . "questionsbank/download_questions/$thisquestionsbankpost";


if (!elgg_is_sticky_form('download_questions_questionsbank')) {
    $number_questions = "";    
} else {
    $number_questions = elgg_get_sticky_value('download_questions_questionsbank', 'number_questions');
}

elgg_clear_sticky_form('download_questions_questionsbank');

?>
<div class="contentWrapper">
    <form action="<?php echo elgg_get_site_url(); ?>action/<?php echo $action; ?>"
          name="download_questions_questionsbank" enctype="multipart/form-data" method="post">
        <?php echo elgg_view('input/securitytoken'); ?>

        <p>
            <b><?php echo elgg_echo('questionsbank:action_label'); ?></b>
        </p>

        <p>
            <select id="action" name="action" onchange="display_action();">
                <option value="download" selected="selected"><?php echo elgg_echo("questionsbank:download_option"); ?></option>
                <option value="upload"><?php echo elgg_echo("questionsbank:upload_option"); ?></option>
            </select>
        </p>

        <p>
            <b><?php echo elgg_echo('questionsbank:num_questions_import_label'); ?></b>
        </p>
      
        <p>
            <?php echo elgg_view('input/text', array('name' => 'number_questions', 'value' => $number_questions)); ?>
        </p>

        <p>
            <input id="uploadedfile" name="uploadedfile" type="file" style="display:none;"/>
        </p>

        <input type="hidden" name="thisquestionsbankpost" value="<?php echo $thisquestionsbankpost; ?>">

        <p>
            <?php
            $submit_input = elgg_view('input/submit', array('name' => 'submit', 'value' => elgg_echo("questionsbank:download_upload_action")));
            echo $submit_input;
            ?>
        </p>

    </form>
</div>

<script language="javascript">
    function display_action(){
        var accion=document.getElementById("action");
        if(accion.value=="upload")
            document.getElementById("uploadedfile").style.display="block";
        else
            document.getElementById("uploadedfile").style.display="none";
    }
</script>




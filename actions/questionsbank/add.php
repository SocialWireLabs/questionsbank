<?php

gatekeeper();

$title = get_input('title');
$tags = get_input('questionsbanktags');
$selected_action = get_input('submit');
$access_id = get_input('access_id');
$container_guid = get_input('container_guid');
$container = get_entity($container_guid);

// Cache to the session
elgg_make_sticky_form('add_questionsbank');

// Make sure tags isn't blank
if (strcmp($tags, "") == 0) {
    register_error(elgg_echo("questionsbank:tags_blank"));
    forward($_SERVER['HTTP_REFERER']);
}

// Convert string of tags into a preformatted array
$tagarray = string_to_tag_array($tags);

// Make sure the title isn't blank
if (strcmp($title, "") == 0) {
    register_error(elgg_echo("questionsbank:title_blank"));
    forward($_SERVER['HTTP_REFERER']);
}

// Initialise a new ElggObject
$questionsbank = new ElggObject();

// Tell the system it's a questionsbank post
$questionsbank->subtype = "questionsbank";

// Set its owner to the current user
$user_guid = elgg_get_logged_in_user_guid();
$owner = get_entity($user_guid);
$questionsbank->owner_guid = $user_guid;
$questionsbank->container_guid = $container_guid;

// Set its access		
$questionsbank->access_id = $access_id;

// Set opened
//$questionsbank->opened = false;

// Set its title 
$questionsbank->title = $title;

// Now let's add tags. 
if (is_array($tagarray)) {
    $questionsbank->tags = $tagarray;
}

// Save the questionsbank post
if (!$questionsbank->save()) {
    register_error(elgg_echo("questionsbank:error_save"));
    forward($_SERVER['HTTP_REFERER']);
}

$questionsbankpost = $questionsbank->getGUID();

// Remove the questionsbank post cache
elgg_clear_sticky_form('add_questionsbank');

// Success message
system_message(elgg_echo("questionsbank:created"));

// Add to river
elgg_create_river_item(array(
    'view' => 'river/object/questionsbank/create',
    'action_type' => 'create',
    'subject_guid' => $user_guid,
    'object_guid' => $questionsbankpost,
));

// Forward 
if (strcmp($selected_action, elgg_echo('questionsbank:save')) == 0) {
    if ($container instanceof ElggGroup) {
        forward(elgg_get_site_url() . 'questionsbank/group/' . $container->username);
    } else {
        forward(elgg_get_site_url() . 'questionsbank/owner/' . $owner->username);
    }
} else {
    if (strcmp($selected_action, elgg_echo('questionsbank:add_question')) == 0) {
        forward("questionsbank/add_question/$questionsbankpost");
    } else {
        forward("questionsbank/import_questionsbank/$questionsbankpost");
    }
}

?>
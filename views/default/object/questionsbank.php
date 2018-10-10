<?php

$full = elgg_extract('full_view', $vars, FALSE);
$questionsbank = elgg_extract('entity', $vars, FALSE);

if (!$questionsbank) {
    return TRUE;
}

$owner = $questionsbank->getOwnerEntity();
$owner_icon = elgg_view_entity_icon($owner, 'tiny');
$owner_link = elgg_view('output/url', array('href' => $owner->getURL(), 'text' => $owner->name, 'is_trusted' => true));
$author_text = elgg_echo('byline', array($owner_link));
$tags = elgg_view('output/tags', array('tags' => $questionsbank->tags));
$date = elgg_view_friendly_time($questionsbank->time_created);
$metadata = elgg_view_menu('entity', array('entity' => $questionsbank, 'handler' => 'questionsbank', 'sort_by' => 'priority', 'class' => 'elgg-menu-hz'));
$subtitle = "$author_text $date $comments_link";

//////////////////////////////////////////////////
//Questionsbank information
$questionsbankpost = $questionsbank->getGUID();
//$opened=$questionsbank->opened;

///////////////////////////////////////////////////////////////////
//Links to actions

$container_guid = $questionsbank->container_guid;
$container = get_entity($container_guid);
if ($container instanceof ElggGroup) {
    $group_owner_guid = $container->owner_guid;
    $operator = false;
    if (($group_owner_guid == $user_guid) || (check_entity_relationship($user_guid, 'group_admin', $container_guid))) {
        $operator = true;
    }
}

$owner_guid = $owner->getGUID();
$user_guid = elgg_get_logged_in_user_guid();

/*The open/close operation is now commented*/
/*if (($questionsbank->canEdit())&&(($owner_guid==$user_guid)||(($container instanceof ElggGroup)&&($operator))||(elgg_is_admin_logged_in()))) {
   if ($opened){						           
      //Close
      $url_close = elgg_add_action_tokens_to_url(elgg_get_site_url() . "action/questionsbank/close?edit=no&questionsbankpost=" . $questionsbankpost);
         $word_close = elgg_echo("questionsbank:close_in_listing");
   } else {
      //Open
      $url_open = elgg_add_action_tokens_to_url(elgg_get_site_url() . "action/questionsbank/open?questionsbankpost=" . $questionsbankpost);
      $word_open = elgg_echo("questionsbank:open_in_listing");
   }
}
*/

if ($full) {
    $params = array('entity' => $questionsbank, 'title' => $title, 'metadata' => $metadata, 'subtitle' => $subtitle, 'tags' => $tags);
    $params = $params + $vars;
    $summary = elgg_view('object/elements/summary', $params);
    $body = "";

    /*The open/close operation is now commented*/
    /*//Links to actions
    if (($vars['entity']->canEdit())&&(($owner_guid==$user_guid)||(($container instanceof ElggGroup)&&($operator))||(elgg_is_admin_logged_in()))) {					     
       if ($opened){						           
      $body .= "<a href=\"{$url_close}\">{$word_close}</a>";
       } else {
            $body .= "<a href=\"{$url_open}\">{$word_open}</a>";
       }
    }
 
    */
    $body .= "<br>";

    $body .= elgg_view('questionsbank/show_questions', array('entity' => $questionsbank, 'offset' => $vars['offset']));

    echo elgg_view('object/elements/full', array('summary' => $summary, 'icon' => $owner_icon, 'body' => $body));

} else {
    $params = array('entity' => $questionsbank, 'title' => $title, 'metadata' => $metadata, 'subtitle' => $subtitle, 'tags' => $tags);
    $params = $params + $vars;
    $list_body = elgg_view('object/elements/summary', $params);

    $body = "";


    //Links to actions

    /*
    
       if (($vars['entity']->canEdit())&&(($owner_guid==$user_guid)||(($container instanceof ElggGroup)&&($operator))||(elgg_is_admin_logged_in()))) {	
     
        if ($opened){		
                       
             $body .= "<a href=\"{$url_close}\">{$word_close}</a>";
          } else {
               $body .= "<a href=\"{$url_open}\">{$word_open}</a>";
          }
    
       }
    
       */

    $body .= "<br><br>";

    $list_body .= $body;

    echo elgg_view_image_block($owner_icon, $list_body);
}

?>

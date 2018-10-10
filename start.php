<?php

/**
 * Override the ElggFile so that
 */
class QuestionsQuestionsbankPluginFile extends ElggFile
{
    protected function initialiseAttributes()
    {
        parent::initialise_attributes();
        $this->attributes['subtype'] = "questionsbank_question_file";
        $this->attributes['class'] = "ElggFile";
    }

    public function __construct($guid = null)
    {
        if ($guid && !is_object($guid)) {
            $guid = get_entity_as_row($guid);
        }
        parent::__construct($guid);
    }
}

function questionsbank_init()
{

    $item = new ElggMenuItem('questionsbank', elgg_echo('questionsbanks'), 'questionsbank/all');
    elgg_register_menu_item('site', $item);

    // Extend system CSS with our own styles, which are defined in the questionsbank/css view
    elgg_extend_view('css/elgg', 'questionsbank/css');

    // Register a page handler, so we can have nice URLs
    elgg_register_page_handler('questionsbank', 'questionsbank_page_handler');

    // Register entity type
    elgg_register_entity_type('object', 'questionsbank');

    // Register a URL handler for questionsbank posts
    elgg_register_plugin_hook_handler('entity:url', 'object', 'questionsbank_url');

    // Show questionsbanks in groups
    add_group_tool_option('questionsbank', elgg_echo('questionsbank:enable_group_questionsbanks'), false);
    elgg_extend_view('groups/tool_latest', 'questionsbank/group_module');

    // Advanced permissions
    elgg_register_plugin_hook_handler('permissions_check', 'object', 'questionsbank_permissions_check');

    // Add a menu item to the user ownerblock
    elgg_register_plugin_hook_handler('register', 'menu:owner_block', 'questionsbank_owner_block_menu');

    run_function_once("questionsbank_question_file_add_subtype_run_once");
}

function questionsbank_question_file_add_subtype_run_once()
{
    add_subtype("object", "questionsbank_question_file", "QuestionsQuestionsbankPluginFile");
}

function questionsbank_permissions_check($hook, $type, $return, $params)
{
    if (($params['entity']->getSubtype() == 'questionsbank') || ($params['entity']->getSubtype() == 'questionsbank_question') || ($params['entity']->getSubtype() == 'questionsbank_question_file')) {
        $user_guid = elgg_get_logged_in_user_guid();
        $group_guid = $params['entity']->container_guid;
        $group = get_entity($group_guid);
        if ($group instanceof ElggGroup) {
            $group_owner_guid = $group->owner_guid;
            $operator = false;
            if (($group_owner_guid == $user_guid) || (check_entity_relationship($user_guid, 'group_admin', $group_guid))) {
                $operator = true;
            }
            if ($operator)
                return true;
        }
    }
}


/**
 * Add a menu item to the user ownerblock
 */
function questionsbank_owner_block_menu($hook, $type, $return, $params)
{
    if (elgg_instanceof($params['entity'], 'user')) {
        $url = "questionsbank/owner/{$params['entity']->username}";
        $item = new ElggMenuItem('questionsbank', elgg_echo('questionsbank'), $url);
        $return[] = $item;
    } else {
        if ($params['entity']->questionsbank_enable != "no") {
            $url = "questionsbank/group/{$params['entity']->guid}/all";
            $item = new ElggMenuItem('questionsbank', elgg_echo('questionsbank:group'), $url);
            $return[] = $item;
        }
    }
    return $return;
}


/**
 * Questionsbank page handler; allows the use of fancy URLs
 *
 * @param array $page from the page_handler function
 * @return true|false depending on success
 */
function questionsbank_page_handler($page)
{
    if (isset($page[0])) {
        elgg_push_breadcrumb(elgg_echo('questionsbanks'));
        $base_dir = elgg_get_plugins_path() . 'questionsbank/pages/questionsbank';
        switch ($page[0]) {
            case "view":
                set_input('questionsbankpost', $page[1]);
                include "$base_dir/read.php";
                break;
            case "owner":
                set_input('username', $page[1]);
                include "$base_dir/index.php";
                break;
            case "group":
                set_input('container_guid', $page[1]);
                include "$base_dir/index.php";
                break;
            case "friends":
                include "$base_dir/friends.php";
                break;
            case "all":
                include "$base_dir/everyone.php";
                break;
            case "add":
                set_input('container_guid', $page[1]);
                include "$base_dir/add.php";
                break;
            case "edit":
                set_input('questionsbankpost', $page[1]);
                include "$base_dir/edit.php";
                break;
            case "add_question":
                set_input('questionsbankpost', $page[1]);
                include "$base_dir/add_question.php";
                break;
            case "edit_question":
                set_input('questionsbankpost', $page[1]);
                set_input('index', $page[2]);
                include "$base_dir/edit_question.php";
                break;
            case "import_questionsbank":
                set_input('thisquestionsbankpost', $page[1]);
                include "$base_dir/import_questionsbank.php";
                break;
            case "select_questions_questionsbank":
                set_input('thisquestionsbankpost', $page[1]);
                set_input('questionsbankpost', $page[2]);
                include "$base_dir/select_questions_questionsbank.php";
                break;
            case "show_question_questionsbank":
                set_input('thisquestionsbankpost', $page[1]);
                set_input('questionsbankpost', $page[2]);
                set_input('index', $page[3]);
                include "$base_dir/show_question_questionsbank.php";
                break;
            case "download_questions":
                set_input('thisquestionsbankpost', $page[1]);
                include "$base_dir/download_questions.php";
                break;
            default:
                return false;
        }
    } else {
        forward();
    }
    return true;
}

/**
 * Returns the URL from a questionsbank entity
 *
 * @param string $hook 'entity:url'
 * @param string $type 'object'
 * @param string $url The current URL
 * @param array $params Hook parameters
 * @return string
 */
function questionsbank_url($hook, $type, $url, $params)
{
    $questionsbank = $params['entity'];
    if ($questionsbank->getSubtype() !== 'questionsbank') {
        return;
    }
    $title = elgg_get_friendly_title($questionsbank->title);
    return $url . "questionsbank/view/" . $questionsbank->getGUID() . "/" . $title;
}


// Make sure the questionsbank initialisation function is called on initialisation
elgg_register_event_handler('init', 'system', 'questionsbank_init');

// Register actions
$action_base = elgg_get_plugins_path() . 'questionsbank/actions/questionsbank';
elgg_register_action("questionsbank/add", "$action_base/add.php");
elgg_register_action("questionsbank/edit", "$action_base/edit.php");
elgg_register_action("questionsbank/delete", "$action_base/delete.php");
elgg_register_action("questionsbank/open", "$action_base/open.php");
elgg_register_action("questionsbank/close", "$action_base/close.php");
elgg_register_action("questionsbank/add_question", "$action_base/add_question.php");
elgg_register_action("questionsbank/edit_question", "$action_base/edit_question.php");
elgg_register_action("questionsbank/delete_question", "$action_base/delete_question.php");
elgg_register_action("questionsbank/import_questionsbank", "$action_base/import_questionsbank.php");
elgg_register_action("questionsbank/select_questions_questionsbank", "$action_base/select_questions_questionsbank.php");
elgg_register_action("questionsbank/download_questions", "$action_base/download_questions.php");

?>
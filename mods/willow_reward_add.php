<?php
// Write to log.
debug_log('WILLOW_REWARD_ADD()');

// For debug.
//debug_log($update);
//debug_log($data);

// Check access.
bot_access_check($update, 'willow');

// Get reward id.
$id = $data['id'];

// Get data arg.
$arg = $data['arg'];

// Get action and reward qty.
if(strpos($arg, '-') !== false) {
    $arg = explode("-", $data['arg']);
    $action = $arg[0];
    $value = $arg[1];
} else {
    $action = $arg;
    $value = 0;
}

// Selection of reward.
if ($id == 0 && $action == 0 && $value == 0) {
    // Get all reward types from json.
    $msg = get_all_json_reward();
    $msg .= CR . '<b>' . getTranslation('reward') . ' — ' . getTranslation('reward_select_type') . '</b>';
    $keys = get_all_json_reward_keys('willow_reward_add', 'add-0');

    // Add abort navigation key.
    $nav_keys = [];
    $nav_keys[] = universal_inner_key($keys, '0', 'willow', 'reward', getTranslation('back'));
    $nav_keys[] = universal_inner_key($keys, '0', 'exit', '0', getTranslation('abort'));

    // Get the inline key array.
    $keys[] = $nav_keys;
} else if ($id > 0 && $action == 'add') {
    // Add currently available rewards.
    $msg = '<b>' . getTranslation('available_rewards') . ' </b>' . CR;
    $available = get_all_rewardlist_entries(false, false, 0, 'reward_type', '=', $id);
    if($available) {
        $msg .= $available . CR;
    } else {
        $msg .= getTranslation('none') . CR . CR;
    }

    // Set message.
    $msg .= '<b>' . getTranslation('add_this_reward') . ' </b>' . CR . CR;

    // Get quantity from value
    $rw_type = explode(":", getTranslation('reward_type_' . $id));
    $rw_type_singular = $rw_type[0];
    $rw_type_plural = $rw_type[1];
    $msg .= getTranslation('reward') . ': <b>' . $value . SP . ($value == 1 ? $rw_type_singular : $rw_type_plural) . '</b>';

    // Keys to specify qty
    $keys = quantity_keys($id, 'willow_reward_add', 'add-' . $value);

    // Add abort navigation key.
    $nav_keys = [];
    $nav_keys[] = universal_inner_key($keys, '0', 'willow_reward_add', 'add-0', getTranslation('back'));
    $nav_keys[] = universal_inner_key($keys, '0', 'exit', '0', getTranslation('abort'));

    // Get the inline key array.
    $keys[] = $nav_keys;
} else if ($id > 0 && $action == 'save') {
    // Write to log.
    debug_log('Confirmation received to save the reward with ID: ' . $id . ' and quantity: ' . $value);

    // Add reward to rewardlist.
    add_rewardlist_reward($id, $value);

    // Get inserted database id.
    $id = my_insert_id();

    // Set message.
    $msg = getTranslation('reward_successfully_saved') . CR . CR;
    $reward_entry = get_formatted_rewardlist_entry($id);
    $msg .= getTranslation('reward') . ':' . CR . $reward_entry;

    // Set keys.
    $keys = [];
}

// Telegram JSON array.
$tg_json = array();

// Edit message.
$tg_json[] = edit_message($update, $msg, $keys, ['disable_web_page_preview' => 'true'], true);

// Build callback message string.
$callback_response = 'OK';

// Answer callback.
$tg_json[] = answerCallbackQuery($update['callback_query']['id'], $callback_response, true);

// Telegram multicurl request.
curl_json_multi_request($tg_json);

exit();

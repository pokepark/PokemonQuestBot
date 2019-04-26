<?php
// Write to log.
debug_log('WILLOW_REWARD_DELETE()');

// For debug.
//debug_log($update);
//debug_log($data);

// Check access.
bot_access_check($update, 'willow');

// Get data arg.
$rewardlist_id = $data['id'];

// Get data arg.
$arg = $data['arg'];

if ($arg == 0) {
    // Build message.
    $msg = get_all_rewardlist_entries(true, false);
    $msg .= CR . '<b>' . getTranslation('reward') . ' — ' . getTranslation('select_id_to_delete') . '</b>';

    // Get keys.
    $keys = get_all_rewardlist_keys('willow_reward_delete', '1', true, false);

    // Add abort navigation key.
    $nav_keys = [];
    $nav_keys[] = universal_inner_key($keys, '0', 'willow', 'reward', getTranslation('back'));
    $nav_keys[] = universal_inner_key($keys, '0', 'exit', '0', getTranslation('abort'));

    // Get the inline key array.
    $keys[] = $nav_keys;

} else if ($arg == 1) {
    // Prohibit to delete reward ID 1 (Pokemon encounter)
    if($rewardlist_id == 1) {
        $keys = [];
        $msg = EMOJI_WARN . '<b> ' . getTranslation('bot_access_denied') . ' </b>' . EMOJI_WARN . CR . CR;
    } else {
        // Write to log.
        debug_log('Asking for confirmation to delete the reward with ID: ' . $rewardlist_id);

        // Create keys array.
        $keys = [
            [
                [
                    'text'          => getTranslation('yes'),
                    'callback_data' => $rewardlist_id . ':willow_reward_delete:' . '3'
                ]
            ],
            [
                [
                    'text'          => getTranslation('no'),
                    'callback_data' => $rewardlist_id . ':willow_reward_delete:' . '2'
                ]
            ]
        ];

        // Add navigation keys.
        $nav_keys = [];
        $nav_keys[] = universal_inner_key($keys, '0', 'willow_quest_delete', '0', getTranslation('back'));
        $nav_keys[] = universal_inner_key($keys, '0', 'exit', '0', getTranslation('abort'));

        // Get the inline key array.
        $keys[] = $nav_keys;

        // Set message.
        $msg = EMOJI_WARN . '<b> ' . getTranslation('delete_this_reward') . ' </b>' . EMOJI_WARN . CR . CR;
        $msg .= get_formatted_rewardlist_entry($rewardlist_id);
    }
} else if ($arg == 2) {
    debug_log('Reward deletion for reward ID ' . $rewardlist_id . ' was canceled!');
    // Set message.
    $msg = '<b>' . getTranslation('reward_deletion_was_canceled') . '</b>';

    // Set keys.
    $keys = [];
} else if ($arg == 3) {
    debug_log('Confirmation to delete reward ' . $rewardlist_id . ' was received!');
    // Set message.
    $msg = getTranslation('reward_successfully_deleted');

    // Set keys.
    $keys = [];

    // Delete reward from rewardlist.
    delete_rewardlist_reward($rewardlist_id);
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

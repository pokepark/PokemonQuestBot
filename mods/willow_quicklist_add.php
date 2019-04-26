<?php
// Write to log.
debug_log('WILLOW_QUICKLIST_DELETE()');

// For debug.
//debug_log($update);
//debug_log($data);

// Check access.
bot_access_check($update, 'willow');

// Get data arg.
$arg = $data['arg'];

// Get ids.
if(strpos($arg, ',') !== false) {
    // Create array (max. 2)
    $qrlist_ids = explode(',', $data['arg'], 2);

    // Set arg / questlist_id
    $arg = $qrlist_ids[0];
    $questlist_id = $qrlist_ids[1];
    $rewardlist_id = $data['id'];
} else {
    $questlist_id = $data['id'];
}

// Add quest
if ($arg == 0) {
    // Build message.
    $msg = get_all_questlist_entries();
    $msg .= CR . '<b>' . getTranslation('quest') . ' — ' . getTranslation('select_id_to_add') . '</b>';

    // Get keys.
    $keys = get_all_questlist_keys('willow_quicklist_add', '1');

    // Add abort navigation key.
    $nav_keys = [];
    $nav_keys[] = universal_inner_key($keys, '0', 'willow', 'quicklist', getTranslation('back'));
    $nav_keys[] = universal_inner_key($keys, '0', 'exit', '0', getTranslation('abort'));

    // Get the inline key array.
    $keys[] = $nav_keys;

// Add reward
} else if ($arg == 1) {
    // Build message.
    $msg = get_all_rewardlist_entries(false, false, $questlist_id);
    $msg .= CR . '<b>' . getTranslation('reward') . ' — ' . getTranslation('select_id_to_add') . '</b>';

    // Get keys.
    $keys = get_all_rewardlist_keys('willow_quicklist_add', '2,' . $questlist_id, false, false);

    // Add abort navigation key.
    $nav_keys = [];
    $nav_keys[] = universal_inner_key($keys, $questlist_id, 'willow_quicklist_add', '0', getTranslation('back'));
    $nav_keys[] = universal_inner_key($keys, '0', 'exit', '0', getTranslation('abort'));

    // Get the inline key array.
    $keys[] = $nav_keys;
} else if ($arg == 2) {
    // Write to log.
    debug_log('Asking for confirmation to add the quest with ID: ' . $questlist_id . ' to the quicklist');

    // Create keys array.
    $keys = [
        [
            [
                'text'          => getTranslation('yes'),
                'callback_data' => $rewardlist_id . ':willow_quicklist_add:' . '4,' . $questlist_id
            ]
        ],
        [
            [
                'text'          => getTranslation('no'),
                'callback_data' => $rewardlist_id . ':willow_quicklist_add:' . '3,' . $questlist_id
            ]
        ]
    ];

    // Add navigation keys.
    $nav_keys = [];
    $nav_keys[] = universal_inner_key($keys, $rewardlist_id, 'willow_quicklist_add', '1,' . $questlist_id, getTranslation('back'));
    $nav_keys[] = universal_inner_key($keys, '0', 'exit', '0', getTranslation('abort'));

    // Get the inline key array.
    $keys[] = $nav_keys;

    // Get questlist entry
    $ql = get_questlist_entry($questlist_id);

    // Set message.
    $msg = '<b> ' . getTranslation('add_this_quest_to_quicklist') . ' </b>' . CR . CR;
    $msg .= '<b>' . getTranslation('quest') . '</b>' . SP . get_formatted_questlist_entry($ql['id']) . CR;
    $msg .= '<b>' . getTranslation('reward') . '</b>' . SP . get_formatted_rewardlist_entry($rewardlist_id, $questlist_id);
} else if ($arg == 3) {
    debug_log('Adding quest with ID ' . $questlist_id . ' and reward with ID ' . $rewardlist_id . ' to the quicklist was canceled!');
    // Set message.
    $msg = '<b>' . getTranslation('quicklist_entry_addition_was_canceled') . '</b>';

    // Set keys.
    $keys = [];
} else if ($arg == 4) {
    debug_log('Confirmation to add quest ' . $questlist_id . ' and reward with ID ' . $rewardlist_id . ' to the quicklist was received!');

    // Set keys.
    $keys = [];

    // Add entry from quicklist.
    add_quicklist_entry($questlist_id, $rewardlist_id);

    // Get inserted database id.
    $id = my_insert_id();

    // Set message.
    $msg = getTranslation('quicklist_entry_successfully_added') . CR . CR;
    $msg .= '<b>' . getTranslation('quest') . '</b>' . SP . get_formatted_questlist_entry($questlist_id) . CR;
    $msg .= '<b>' . getTranslation('reward') . '</b>' . SP . get_formatted_rewardlist_entry($rewardlist_id);
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

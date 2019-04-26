<?php
// Write to log.
debug_log('WILLOW_QUEST_DELETE()');

// For debug.
//debug_log($update);
//debug_log($data);

// Check access.
bot_access_check($update, 'willow');

// Get data arg.
$questlist_id = $data['id'];

// Get data arg.
$arg = $data['arg'];

if ($arg == 0) {
    // Build message.
    $msg = get_all_questlist_entries();
    $msg .= CR . '<b>' . getTranslation('quest') . ' — ' . getTranslation('select_id_to_delete') . '</b>';

    // Get keys.
    $keys = get_all_questlist_keys('willow_quest_delete', '1');

    // Add abort navigation key.
    $nav_keys = [];
    $nav_keys[] = universal_inner_key($keys, '0', 'willow', 'quest', getTranslation('back'));
    $nav_keys[] = universal_inner_key($keys, '0', 'exit', '0', getTranslation('abort'));

    // Get the inline key array.
    $keys[] = $nav_keys;

} else if ($arg == 1) {
    // Write to log.
    debug_log('Asking for confirmation to delete the quest with ID: ' . $questlist_id);

    // Create keys array.
    $keys = [
        [
            [
                'text'          => getTranslation('yes'),
                'callback_data' => $questlist_id . ':willow_quest_delete:' . '3'
            ]
        ],
        [
            [
                'text'          => getTranslation('no'),
                'callback_data' => $questlist_id . ':willow_quest_delete:' . '2'
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
    $msg = EMOJI_WARN . '<b> ' . getTranslation('delete_this_quest') . ' </b>' . EMOJI_WARN . CR . CR;
    $msg .= get_formatted_questlist_entry($questlist_id);
} else if ($arg == 2) {
    debug_log('Quest deletion for quest ID ' . $questlist_id . ' was canceled!');
    // Set message.
    $msg = '<b>' . getTranslation('quest_deletion_was_canceled') . '</b>';

    // Set keys.
    $keys = [];
} else if ($arg == 3) {
    debug_log('Confirmation to delete quest ' . $questlist_id . ' was received!');
    // Set message.
    $msg = getTranslation('quest_successfully_deleted');

    // Set keys.
    $keys = [];

    // Get quest id from questlist.
    $ql_entry = get_questlist_entry($questlist_id);

    // Delete quest from questlist.
    delete_questlist_quest($questlist_id);

    // Delete quest from encounterlist.
    delete_encounterlist_entry($ql_entry['id'], 'quest_id');

    // Delete quest from quicklist.
    delete_quicklist_entry($ql_entry['id'], 'quest_id');
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

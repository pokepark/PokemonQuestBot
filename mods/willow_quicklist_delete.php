<?php
// Write to log.
debug_log('WILLOW_QUICKLIST_DELETE()');

// For debug.
//debug_log($update);
//debug_log($data);

// Check access.
bot_access_check($update, 'willow');

// Get data arg.
$quicklist_id = $data['id'];

// Get data arg.
$arg = $data['arg'];

if ($arg == 0) {
    // Build message.
    $msg = get_all_quicklist_entries(true, true, true);
    $msg .= CR . '<b>' . getTranslation('quicklist') . ' — ' . getTranslation('select_id_to_delete') . '</b>';

    // Get keys.
    $keys = get_all_quicklist_keys('willow_quicklist_delete', '1', 'id');

    // Add abort navigation key.
    $nav_keys = [];
    $nav_keys[] = universal_inner_key($keys, '0', 'willow', 'quicklist', getTranslation('back'));
    $nav_keys[] = universal_inner_key($keys, '0', 'exit', '0', getTranslation('abort'));

    // Get the inline key array.
    $keys[] = $nav_keys;

} else if ($arg == 1) {
    // Write to log.
    debug_log('Asking for confirmation to delete the quicklist entry with ID: ' . $quicklist_id);

    // Create keys array.
    $keys = [
        [
            [
                'text'          => getTranslation('yes'),
                'callback_data' => $quicklist_id . ':willow_quicklist_delete:' . '3'
            ]
        ],
        [
            [
                'text'          => getTranslation('no'),
                'callback_data' => $quicklist_id . ':willow_quicklist_delete:' . '2'
            ]
        ]
    ];

    // Add navigation keys.
    $nav_keys = [];
    $nav_keys[] = universal_inner_key($keys, '0', 'willow_quicklist_delete', '0', getTranslation('back'));
    $nav_keys[] = universal_inner_key($keys, '0', 'exit', '0', getTranslation('abort'));

    // Get the inline key array.
    $keys[] = $nav_keys;

    // Get quicklist entry
    $qql = get_quicklist_entry($quicklist_id);

    // Set message.
    $msg = EMOJI_WARN . '<b> ' . getTranslation('delete_this_quicklist_entry') . ' </b>' . EMOJI_WARN . CR . CR;
    $msg .= get_formatted_questlist_entry($qql['quest_id']);
} else if ($arg == 2) {
    debug_log('Deletion for quicklist entry ID ' . $quicklist_id . ' was canceled!');
    // Set message.
    $msg = '<b>' . getTranslation('quicklist_entry_deletion_was_canceled') . '</b>';

    // Set keys.
    $keys = [];
} else if ($arg == 3) {
    debug_log('Confirmation to delete quicklist entry ' . $quicklist_id . ' was received!');
    // Set message.
    $msg = getTranslation('quicklist_entry_successfully_deleted');

    // Set keys.
    $keys = [];

    // Delete entry from quicklist.
    delete_quicklist_entry($quicklist_id);
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

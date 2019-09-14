<?php
// Write to log.
debug_log('invasion_delete()');

// For debug.
//debug_log($update);
//debug_log($data);

// Access check.
invasion_access_check($update, $data, 'invasion-delete');

// Invasion id.
$invasion_id = $data['id'];

// Action.
$action = $data['arg'];

if ($action == 0) {
    // Write to log.
    debug_log('Asking for confirmation to delete the invasion with ID: ' . $invasion_id);

    // Create keys array.
    $keys = [
        [
            [
                'text'          => getTranslation('yes'),
                'callback_data' => $invasion_id . ':invasion_delete:' . '2'
            ],
            [
                'text'          => getTranslation('no'),
                'callback_data' => $invasion_id . ':invasion_delete:' . '1'
            ]
        ]
    ];

    // Set message.
    $msg = EMOJI_WARN . '<b> ' . getTranslation('delete_this_invasion') . ' </b>' . EMOJI_WARN . CR . CR;
    $invasion = get_invasion($invasion_id);
    $msg .= get_formatted_invasion($invasion, false, false, true);
} else if ($action == 1) {
    debug_log('Invasion deletion for invasion ID ' . $invasion_id . ' was canceled!');
    // Set message.
    $msg = '<b>' . getTranslation('action_aborted') . '</b>';

    // Set keys.
    $keys = [];
} else if ($action == 2) {
    debug_log('Confirmation to delete invasion ' . $invasion_id . ' was received!');
    // Set message.
    $msg = getTranslation('invasion_successfully_deleted');

    // Set keys.
    $keys = [];

    // Delete invasion.
    delete_invasion($invasion_id);
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

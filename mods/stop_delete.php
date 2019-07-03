<?php
// Write to log.
debug_log('stop_delete()');

// For debug.
//debug_log($update);
//debug_log($data);

// Check access.
bot_access_check($update, 'pokestop-delete');

// Get the id.
$id = $data['id'];

// Get the arg.
$arg = $data['arg'];

// Get pokestop info and ask to delete pokestop.
if($arg == 0) {
    $pokestop = get_pokestop($id);
    
    // Set message
    $msg = EMOJI_WARN . SP . '<b>' . getTranslation('delete_this_pokestop') . '</b>' . SP . EMOJI_WARN;
    $msg .= CR . get_pokestop_details($pokestop);

    // Create the keys.
    $keys = [
        [
            [
                'text'          => getTranslation('yes'),
                'callback_data' => $id . ':stop_delete:1'
            ]
        ],
        [
            [
                'text'          => getTranslation('no'),
                'callback_data' => '0:exit:0'
            ]
        ]
    ];

// Delete the pokestop.
} else if ($arg == 1) {
    debug_log('Deleting pokestop with ID ' . $id);
    // Get pokestop.
    $pokestop = get_pokestop($id);
    
    // Set message
    $msg = '<b>' . getTranslation('pokestop_deleted') . '</b>' . CR;
    $msg .= get_pokestop_details($pokestop);

    // Set keys
    $keys = [];

    // Delete pokestop.
    delete_pokestop($id);
    
}

// Build callback message string.
$callback_response = 'OK';

// Telegram JSON array.
$tg_json = array();

// Answer callback.
$tg_json[] = answerCallbackQuery($update['callback_query']['id'], $callback_response, true);

// Edit the message.
$tg_json[] = edit_message($update, $msg, $keys, ['disable_web_page_preview' => 'true'], true);

// Telegram multicurl request.
curl_json_multi_request($tg_json);

// Exit.
exit();

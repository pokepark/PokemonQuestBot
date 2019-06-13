<?php
// Write to log.
debug_log('stop_details()');

// For debug.
//debug_log($update);
//debug_log($data);

// Check access.
bot_access_check($update, 'pokestop-details');

// Get the id.
$id = $data['id'];

// Get pokestop info.
if($id > 0) {
    $pokestop = get_pokestop($id);
    
    // Set message
    $msg = CR . get_pokestop_details($pokestop);

    // Create the keys.
    $keys = [];

// Pokestop not found.
} else {
    // Set message.
    $msg = '<b>' . getTranslation('pokestops_not_found') . '</b>';

    // Set empty keys.
    $keys = [];
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

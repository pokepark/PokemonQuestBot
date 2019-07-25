<?php
// Write to log.
debug_log('invasion_share()');

// For debug.
//debug_log($update);
//debug_log($data);

// Access check.
invasion_access_check($update, $data, 'invasion-share');

// Get invasion id.
$id = $data['id'];

// Get chat id.
$chat = $data['arg'];

// Get invasion data.
$invasion = get_invasion($id);

// Get text and keys.
$text = get_formatted_invasion($invasion, true, true, false, true);
$keys = [];

// Send location.
if (INVASION_LOCATION == true) {
    // Send location.
    $msg_header = get_formatted_invasion($invasion, false, false, true, true);
    $msg_text = !empty($invasion['address']) ? $invasion['address'] . ', ' . substr(strtoupper(BOT_ID), 0, 1) . '-ID = ' . $invasion['id'] : $invasion['pokestop_name'] . ', ' . $invasion['id']; // DO NOT REMOVE " ID = " --> NEEDED FOR CLEANUP PREPARATION!
    $loc = send_venue($chat, $invasion['lat'], $invasion['lon'], $msg_header, $msg_text);

    // Write to log.
    debug_log('location:');
    debug_log($loc);
}

// Telegram JSON array.
$tg_json = array();

// Send the message.
$tg_json[] = send_message($chat, $text, $keys, ['reply_to_message_id' => $chat, 'disable_web_page_preview' => 'true'], true);

// Set callback keys and message
$callback_msg = getTranslation('successfully_shared');
$callback_keys = [];

// Edit message.
$tg_json[] = edit_message($update, $callback_msg, $callback_keys, false, true);

// Answer callback.
$tg_json[] = answerCallbackQuery($update['callback_query']['id'], $callback_msg, true);

// Telegram multicurl request.
curl_json_multi_request($tg_json);

exit();

<?php
// Write to log.
debug_log('quest_share()');

// For debug.
//debug_log($update);
//debug_log($data);

// Access check.
quest_access_check($update, $data, 'share');

// Get quest id.
$id = $data['id'];

// Get chat id.
$chat = $data['arg'];

// Get quest data.
$quest = get_quest($id);

// Get text and keys.
$text = get_formatted_quest($quest, true, true, false, true);
$keys = [];

// Send location.
if ($config->QUEST_LOCATION) {
    // Send location.
    $msg_header = get_formatted_quest($quest, false, false, true, true);
    $msg_text = !empty($quest['address']) ? $quest['address'] . ', ' . substr(strtoupper($config->BOT_ID), 0, 1) . '-ID = ' . $quest['id'] : $quest['pokestop_name'] . ', ' . $quest['id']; // DO NOT REMOVE " ID = " --> NEEDED FOR $config->CLEANUP PREPARATION!
    $loc = send_venue($chat, $quest['lat'], $quest['lon'], $msg_header, $msg_text);

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

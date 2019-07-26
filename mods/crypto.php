<?php
// Write to log.
debug_log('crypto()');

// For debug.
//debug_log($update);
//debug_log($data);

// Access check.
bot_access_check($update, 'invasion-create');

// Set the id.
$id = $data['id'];

// Update pokemon in the raid table.
my_query(
    "
    UPDATE    invasions
    SET       comment = '{$data['arg']}'
      WHERE   id = {$id}
    "
);

// Create the keys.
$keys = [];

// Build message string.
$msg = '<b>' . getTranslation('invasion_saved') . '</b>' . CR . CR;
$invasion = get_invasion($id);
$msg .= get_formatted_invasion($invasion, false, false, true);

// Build callback message string.
$callback_response = 'OK';

// Telegram JSON array.
$tg_json = array();

// Answer callback.
$tg_json[] = answerCallbackQuery($update['callback_query']['id'], $callback_response, true);

// Edit message.
$tg_json[] = edit_message($update, $msg, $keys, ['disable_web_page_preview' => 'true'], true);

// Get invasion messages to be updated from cleanup.
$rs = my_query(
    "
    SELECT    *
    FROM      qleanup
      WHERE   quest_id = '-{$id}'
    "
);

// Get updated invasion message.
$updated_msg = get_formatted_invasion($invasion, true, true, false, true);;
$updated_keys = [];

// Update the shared raid polls.
while ($invasionmsg = $rs->fetch_assoc()) {
    $tg_json[] = editMessageText($invasionmsg['message_id'], $updated_msg, $updated_keys, $invasionmsg['chat_id'], ['disable_web_page_preview' => 'true'], true);
} 

// Telegram multicurl request.
curl_json_multi_request($tg_json);

// Exit.
exit();

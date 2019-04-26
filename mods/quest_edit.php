<?php
// Write to log.
debug_log('quest_edit()');

// For debug.
//debug_log($update);
//debug_log($data);

// Check access.
bot_access_check($update, 'list');

// Quest id.
$quest_id = $data['id'];

// Init keys.
$keys = array();
$keys_share = array();
$keys_delete = array();

// Add keys to delete and share.
$keys_delete = universal_key($keys, $quest_id, 'quest_delete', '0', getTranslation('delete'));
$keys_share = share_keys($quest_id, 'quest_share', $update);
$keys = array_merge($keys_delete, $keys_share);

// Add abort navigation key.
$nav_keys = array();
$nav_keys[] = universal_inner_key($keys, '0', 'exit', '0', getTranslation('abort'));
$keys[] = $nav_keys;

// Set message.
$msg = '<b>' . getTranslation('quest') . ':</b>' . CR . CR;
$quest = get_quest($quest_id);
$msg .= get_formatted_quest($quest);

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

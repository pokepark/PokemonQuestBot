<?php
// Write to log.
debug_log('quest_edit_reward()');

// For debug.
//debug_log($update);
//debug_log($data);

// Check access.
bot_access_check($update, 'create');

// Pokestop id.
$pokestop_id = $data['id'];

// Questlist id and type.
$quest_id_type = explode(",", $data['arg']);
$quest_id = $quest_id_type[0];
$quest_type = $quest_id_type[1];

// Event data.
$event = explode('-', $quest_type);
$q_flag = $event[0];
$q_event = $event[1];
$q_type = $event[2];

// Get pokestop and questlist data.
$stop = get_pokestop($pokestop_id, false);
$ql_entry = get_questlist_entry($quest_id);

// Quest action
$qty_action = get_quest_action($ql_entry);

// Build message string.
$msg = '';
$msg .= getTranslation('pokestop') . ': <b>' . $stop['pokestop_name'] . '</b>' . (!empty($stop['address']) ? (CR . $stop['address']) : '');
// Event?    
if($q_event > 0) {
    $msg .= CR . '<b>' . getTranslation('quest_event_' . $q_event) . ':' . SP . getTranslation('quest_type_' . $q_type) . SP . $qty_action . '</b>';
} else {
    $msg .= CR . getTranslation('quest') . ': <b>' . getTranslation('quest_type_' . $q_type) . SP . $qty_action . '</b>';
} 
$msg .= CR . CR . '<b>' . getTranslation('reward_select_type') . '</b>';

// Create the keys.
$keys = reward_type_keys($pokestop_id, $quest_id, $quest_type);

// Telegram JSON array.
$tg_json = array();

// Edit message.
$tg_json[] = edit_message($update, $msg, $keys, false, true);

// Build callback message string.
$callback_response = 'OK';

// Answer callback.
$tg_json[] = answerCallbackQuery($update['callback_query']['id'], $callback_response, true);

// Telegram multicurl request.
curl_json_multi_request($tg_json);

exit();

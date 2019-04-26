<?php
// Write to log.
debug_log('quest_edit_qty_reward()');

// For debug.
//debug_log($update);
//debug_log($data);

// Check access.
bot_access_check($update, 'create');

// Pokestop and quest id.
$stop_quest = explode(",", $data['id']);
$pokestop_id = $stop_quest[0];
$quest_id = $stop_quest[1];

// Quest and Reward type.
$qr_types = explode(",", $data['arg']);
$quest_type = $qr_types[0];
$reward_type = $qr_types[1];

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

// Reward - Always singular.
$reward_type_text = explode(":", getTranslation('reward_type_' . $reward_type));
$reward_type_singular = $reward_type_text[0];

// Build message string.
$msg = '';
$msg .= getTranslation('pokestop') . ': <b>' . $stop['pokestop_name'] . '</b>' . (!empty($stop['address']) ? (CR . $stop['address']) : '');
// Event?    
if($q_event > 0) {
    $msg .= CR . '<b>' . getTranslation('quest_event_' . $q_event) . ':' . SP . getTranslation('quest_type_' . $q_type) . SP . $qty_action . '</b>';
} else {
    $msg .= CR . getTranslation('quest') . ': <b>' . getTranslation('quest_type_' . $q_type) . SP . $qty_action . '</b>';
}  
$msg .= CR . getTranslation('reward') . ': <b>' . ucfirst($reward_type_singular) . '</b>';
$msg .= CR . CR . '<b>' . getTranslation('reward_select_qty_reward') . '</b>';

// Create the keys.
$keys = reward_qty_type_keys($pokestop_id, $quest_id, $quest_type, $reward_type);

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

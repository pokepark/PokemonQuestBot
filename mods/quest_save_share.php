<?php
// Write to log.
debug_log('quest_save_share()');

// For debug.
//debug_log($update);
//debug_log($data);

// Check access.
bot_access_check($update, 'create');

// Set the user id.
$userid = $update['callback_query']['from']['id'];

// Pokestop and quest id.
$stop_quest = explode(",", $data['id']);
$pokestop_id = $stop_quest[0];
$quest_id = $stop_quest[1];

// Reward id.
$reward_id = $data['arg'];

// Check if quest already exists for this pokestop.
$quest_in_db = quest_duplication_check($pokestop_id);

// Quest already in database or new
if (!$quest_in_db) {
    debug_log('Saving quest to database.');

    // Insert quest.
    my_query(
        "
        INSERT INTO   quests
        SET           user_id = {$update['callback_query']['from']['id']},
                      quest_date = UTC_TIMESTAMP(),
                      pokestop_id = {$pokestop_id},
                      quest_id = {$quest_id},
                      reward_id = {$reward_id}
        "
    );
    // Get last insert id from db.
    $id = my_insert_id();

    // Write to log.
    debug_log('Saved Quest ID: ' . $id);

    // Set message and keys.
    $quest = get_quest($id);
    $msg = get_formatted_quest($quest, true, true, false, true);
    $keys = [];

    // Set callback message.
    $callback_msg = '<b>' . getTranslation('quest_saved') . '</b>' . CR . CR;
    $callback_msg .= get_formatted_quest($quest) . CR;
    $callback_msg .= '<b>' . getTranslation('successfully_shared') . '</b>';
    
    // Add buttons for predefined sharing chats.
    if (!empty($config->SHARE_QUICK)) {
        // Add keys for each chat.
        $chat = $config->SHARE_QUICK;
        // Get chat object 
        debug_log("Getting chat object for '" . $chat . "'");
        $chat_obj = get_chat($chat);

        // Check chat object for proper response.
        if ($chat_obj['ok'] == true) {
            debug_log('Proper chat object received, continuing to add key for this chat: ' . $chat_obj['result']['title']);
        }

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

        // Send the message.
        send_message($chat, $msg, $keys, ['reply_to_message_id' => $chat, 'disable_web_page_preview' => 'true']);
    } else {
        // Quest already in the database for this pokestop.
        $callback_msg = EMOJI_WARN . '<b> ' . getTranslation('action_aborted') . ' </b>' . EMOJI_WARN;
    }

} else {
    // Quest already in the database for this pokestop.
    $callback_msg = EMOJI_WARN . '<b> ' . getTranslation('quest_already_submitted') . ' </b>' . EMOJI_WARN . CR . CR;
    $quest = get_quest($quest_in_db['id']);
    $callback_msg .= get_formatted_quest($quest);
}

// Set callback keys
$callback_keys = [];

// Telegram JSON array.
$tg_json = array();

// Edit message.
$tg_json[] = edit_message($update, $callback_msg, $callback_keys, ['disable_web_page_preview' => 'true'], true);

// Answer callback.
$tg_json[] = answerCallbackQuery($update['callback_query']['id'], getTranslation('successfully_shared'), true);

// Telegram multicurl request.
curl_json_multi_request($tg_json);

exit();

<?php
// Write to log.
debug_log('invasion_save()');

// For debug.
//debug_log($update);
//debug_log($data);

// Check access.
bot_access_check($update, 'invasion-create');

// Pokestop id.
$pokestop_id = $data['id'];

// Invasion time.
$invasion_time = $data['arg'];
debug_log($invasion_time, 'Approx invasion time in minutes:');

// Check if invasion already exists for this pokestop.
$invasion_in_db = invasion_duplication_check($pokestop_id);

// Invasion already in database or new
if (!$invasion_in_db) {
    debug_log('Saving invasion to database.');

    // Insert invasion.
    my_query(
        "
        INSERT INTO   invasions
        SET           user_id = {$update['callback_query']['from']['id']},
                      pokestop_id = {$pokestop_id},
                      first_seen = UTC_TIMESTAMP(),
                      start_time = UTC_TIMESTAMP(),
                      end_time = DATE_ADD(start_time, INTERVAL {$invasion_time} MINUTE) 
        "
    );
    // Get last insert id from db.
    $id = my_insert_id();

    // Write to log.
    debug_log('Saved Invasion ID: ' . $id);

    // Set message.
    $msg = '<b>' . getTranslation('invasion_saved') . '</b>' . CR . CR;
    $invasion = get_invasion($id);
    $msg .= get_formatted_invasion($invasion, false, false, true);

    // Init keys.
    $keys = array();
    $keys_share = array();
    $keys_delete = array();

    // Add keys to delete and share.
    $keys_delete = universal_key($keys, $id, 'invasion_delete', '0', getTranslation('delete'));
    $keys_share = share_keys($id, 'invasion_share', $update, SHARE_INVASIONS);
    $keys = array_merge($keys_delete, $keys_share);
} else {
    // Invasion already in the database for this pokestop.
    $msg = EMOJI_WARN . '<b> ' . getTranslation('invasion_already_submitted') . ' </b>' . EMOJI_WARN . CR . CR;
    $invasion = get_invasion($invasion_in_db['id']);
    $msg .= get_formatted_invasion($invasion);

    // Empty keys.
    $keys = [];
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

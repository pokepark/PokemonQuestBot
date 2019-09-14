<?php
// Write to log.
debug_log('DEX()');

// For debug.
//debug_log($update);
//debug_log($data);

// Check access.
bot_access_check($update, 'dex');

// Create keys array.
$keys = [];

// Get pokemon name.
$pokemon = trim(substr($update['message']['text'], 4));

// Set message.
$msg = '<b>' . getTranslation('pokemon') . ':</b>' . CR;
$msg .= get_dex_entry($pokemon);

// Send message.
send_message($update['message']['chat']['id'], $msg, $keys, ['reply_markup' => ['selective' => true, 'one_time_keyboard' => true]]);

exit();

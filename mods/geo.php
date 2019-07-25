<?php
// Write to log.
debug_log('GEO()');

// For debug.
//debug_log($update);
//debug_log($data);

// Check access.
bot_access_check($update, 'access-bot');


// Latitude and longitude
$lat = '';
$lon = '';

// Get latitude / longitude values from Telegram Mobile Client
if (isset($update['message']['location'])) {
    $lat = $update['message']['location']['latitude'];
    $lon = $update['message']['location']['longitude'];

    $coords = $lat . ',' . $lon;

    // Debug
    debug_log('Lat=' . $lat);
    debug_log('Lon=' . $lon);

    // Set message.
    $msg = '<b>' . getTranslation('quest') . SP . getTranslation('or') . SP . getTranslation('invasion') . '</b> — ' . getTranslation('select_action');
} else {
    // Set message.
    $msg = getTranslation('invalid_input');
    sendMessage($update['message']['chat']['id'], $msg);
    exit();
}

// Init empty keys array.
$keys = [];

// Create keys array.
$keys = [
    [
        [
            'text'          => getTranslation('quest'),
            'callback_data' => '0:quest_geo:' . $coords 
        ],
        [
            'text'          => getTranslation('invasion'),
            'callback_data' => '0:invasion_geo:' . $coords
        ]
    ]
];

// Add navigation keys.
$nav_keys = [];
$nav_keys[] = universal_inner_key($keys, '0', 'exit', '0', getTranslation('abort'));

// Get the inline key array.
$keys[] = $nav_keys;

// Send message.
send_message($update['message']['chat']['id'], $msg, $keys, ['reply_markup' => ['selective' => true, 'one_time_keyboard' => true]]);

exit();

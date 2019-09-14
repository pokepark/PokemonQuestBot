<?php
// Write to log.
debug_log('EVENT()');

// For debug.
// debug_log($update);
// debug_log($data);

// Check access.
bot_access_check($update, 'event');

// Get config name and value.
$input = trim(substr($update['message']['text'], 6));

// Set event name for current language and default language.
if(!empty($input)) {
    $eventfile = BOT_LANG_PATH . '/event.json';
    $data = '{"quest_event_9999":{"' . DEFAULT_LANGUAGE . '":"' . $input . '"}}';
    file_put_contents($eventfile, $data);
    $msg = getTranslation('event_saved') . CR . CR;
    $msg .= getTranslation('event') . ': <b>' . $input . '</b>' . CR;
    debug_log('Current event:' . $input);

// Tell user how to set config and what is allowed to be set by config.
} else {
    $msg = '<b>' . getTranslation('event_name_missing') . '</b>';
    debug_log('Unsupported request for submitting an event!');
}

// Send message.
sendMessage($update['message']['chat']['id'], $msg);

?>

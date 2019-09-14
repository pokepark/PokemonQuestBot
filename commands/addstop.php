<?php
// Write to log.
debug_log('ADDSTOP()');

// For debug.
// debug_log($update);
// debug_log($data);

// Check access.
bot_access_check($update, 'pokestop-add');

// Get stop coords.
$input = trim(substr($update['message']['text'], 8));

// Count commas given in input.
$count = substr_count($input, ",");

// 1 comma as it should be?
// E.g. 52.5145434,13.3501189
if($count == 1) {
    $lat_lon = explode(',', $input);
    $lat = $lat_lon[0];
    $lon = $lat_lon[1];

// Lat and lon with comma instead of dot?
// E.g. 52,5145434,13,3501189
} else if($count == 3) {
    $lat_lon = explode(',', $input);
    $lat = $lat_lon[0] . '.' . $lat_lon[1];
    $lon = $lat_lon[2] . '.' . $lat_lon[3];
} else {
    // Invalid input - send the message and exit.
    $msg = getTranslation('invalid_input');
    sendMessage($update['message']['chat']['id'], $msg);
    exit();
}

// Set stop name.
$stop_name = '#' . $update['message']['from']['id'];

// Get address.
$addr = get_address($lat, $lon);
$address = format_address($addr);

// Insert / update stop.
try {

    global $db;

    // Build query to check if stop is already in database or not
    $rs = my_query("
    SELECT    COUNT(*)
    FROM      pokestops
      WHERE   pokestop_name = '{$stop_name}'
     ");

    $row = $rs->fetch_row();

    // Pokestop already in database or new
    if (empty($row['0'])) {
        // insert stop in table.
        debug_log('Pokestop not found in database pokestop list! Inserting pokestop "' . $stop_name . '" now.');
        $query = '
        INSERT INTO pokestops (pokestop_name, lat, lon, address)
        VALUES (:stop_name, :lat, :lon, :address)
        ';
        $msg = getTranslation('pokestop_added');
    } else {
        // Get stop by temporary name.
        $stop = get_pokestop_by_telegram_id($stop_name);

        // If stop is already in the database, make sure no quest is shared before continuing!
        if($stop) {
            debug_log('Pokestop found in the database! Checking for shared quest now!');
            $stop_id = $stop['id'];

            // Check for duplicate quest
            $duplicate_id = 0;
            $duplicate_id = quest_duplication_check($stop_id);

            // Continue with stop creation
            if($duplicate_id > 0) {
                debug_log('Quest is shared for that stop!');
                debug_log('Tell user to update the stop name and exit!');

                // Show message that a quest is shared for that pokestop.
                $quest_id = $duplicate_id;
                $quest = get_quest($quest_id);

                // Build message.
                $msg = EMOJI_WARN . SP . getTranslation('quest_already_exists') . SP . EMOJI_WARN . CR . get_formatted_quest($quest);

                // Tell user to update the stop name first to create another pokestop
                $msg .= getTranslation('pokestopname_then_location');
                $keys = [];

                // Send message.
                send_message($update['message']['chat']['id'], $msg, $keys, ['reply_markup' => ['selective' => true, 'one_time_keyboard' => true]]);

                exit();
            } else {
                debug_log('No shared quest found! Continuing now ...');
            }
        } else {
            // Set stop_id to 0
            $stop_id = 0;
            debug_log('No pokestop found in the database! Continuing now ...');
        }

        // Update pokestops table to reflect pokestop changes.
        debug_log('Pokestop found in database pokestops list! Updating pokestop "' . $stop_name . '" now.');
        $query = '
            UPDATE        pokestops
            SET           lat = :lat,
                          lon = :lon,
                          address = :address
            WHERE         pokestop_name = :stop_name
        ';
        $msg = getTranslation('pokestop_updated');
    }

    $statement = $dbh->prepare($query);
    $statement->bindValue(':stop_name', $stop_name, PDO::PARAM_STR);
    $statement->bindValue(':lat', $lat, PDO::PARAM_STR);
    $statement->bindValue(':lon', $lon, PDO::PARAM_STR);
    $statement->bindValue(':address', $address, PDO::PARAM_STR);
    $statement->execute();

    // Get last insert id.
    if (empty($row['0'])) {
        $stop_id = $dbh->lastInsertId();
    }

    // Stop details.
    if($stop_id > 0) {
        $stop = get_pokestop($stop_id);
        $msg .= CR . CR . get_pokestop_details($stop);
    }
} catch (PDOException $exception) {

    error_log($exception->getMessage());
    $dbh = null;
    exit();
}

// Set keys.
$keys = [];

// Send the message.
send_message($update['message']['chat']['id'], $msg, $keys, ['disable_web_page_preview' => 'true']);

?>

# About

Telegram bot for sharing quests in Pokemon Go. Developers are welcome to join https://t.me/PokemonBotSupport

# Screenshots

#### Example quest with reward:
![Example quest](/screenshots/quest-example.png?raw=true "Example quest")

#### Example quest with pokemon encounter:
![Example quest](/screenshots/quest-example-with-pokemon-encounter.png?raw=true "Example quest")

# Installation and configuration

## Webserver

Preferrably:
- Apache2
- PHP7
- MySQL5 or MariaDB10
- Curl
- SSL Certificate ( https://www.letsencrypt.org )

The following apache packages need to be installed:
- PDO_mysql (ubuntu: php-mysql)
- PHP_curl (ubuntu: php-curl)

## Git clone

#### Core module inside bot folder

For git 2.13 and above:

`git clone --recurse-submodules https://github.com/florianbecker/PokemonQuestBot.git`

If you're running an older version of git use the deprecated recursive command:

`git clone --recursive https://github.com/florianbecker/PokemonQuestBot.git`

#### Core module outside bot folder

If you like to keep the core repo outside the bot folder so multiple bots can access the core (e.g. via the [PokemonBotBridge](https://github.com/florianbecker/PokemonBotBridge.git "PokemonBotBridge")) you can do the following:

Clone the bot repo to e.g. `var/www/html`:

`git clone https://github.com/florianbecker/PokemonQuestBot.git`

Clone the core repo to e.g. `var/www/html`:

`git clone https://github.com/florianbecker/php.core.telegram.git`

Change to the bot folder and create a symlink to make core accessible for the bot:
```
cd /var/www/html/PokemonQuestBot
rm -rf core/
ln -sf /var/www/html/php.core.telegram core
```

## Bot token

Start chat with https://t.me/BotFather and create bot token.

Bot Settings: 
 - Enable Inline mode
 - Allow Groups
   - Group Privacy off

## Database

Create a new mysql database and user for your bot.

Only allow localhost access.

Import `pokemon-quest-bot.sql` as default DB structure and `quests-rewards-encounters.sql` for the current quests. You can find these files in the sql folder.

Command DB structure: `mysql -u USERNAME -p DATABASENAME < sql/pokemon-quest-bot.sql`

Command quests, rewards and encounters: `mysql -u USERNAME -p DATABASENAME < quests-rewards-encounters.sql`

## Config

Inside the config folder, copy the example config.json.example to your own config.json and edit the values (explained further).

Don't forget to change the file permissions of your config file to 0600 (e.g. `chmod 0600 config.json`) afterwards.

Some values are missing as the bot has default values. If you like to change those, you need to add and define them in your config.json file, e.g. `"DDOS_MAXIMUM":"10"`.

## Database connection

Enter the details for the database connection to the config.json file via `DB_HOST`, `DB_NAME`, `DB_USER` and `DB_PASSWORD`.

## General config and log files

Set `DEBUG` to true, to enable the debug logfile.

Set `DEBUG_LOGFILE` to the location of the logfile, e.g. /var/log/tg-bots/dev-raid-bot.log. Make sure to create the log dir, e.g. /var/log/tg-bots/ and set it writeable by webserver.

Set `APIKEY_HASH` to the hashed value of your bot token (preferably lowercase) using a hash generator, e.g. https://www.miniwebtool.com/sha512-hash-generator/ 

Set `DDOS_MAXIMUM` to the amount of callback queries each user is allowed to do each minute. If the amount is reached any further callback query is rejected by the DDOS check. Default value: 10.

Set `BRIDGE_MODE` to true when you're using the PokemonBotBridge. If you're not using the PokemonBotBridge the default value of false is used. PokemonBotBridge: https://github.com/florianbecker/PokemonBotBridge

## Proxy

Set `CURL_USEPROXY` with a value of `true` in case you are running the bot behind a proxy server.

Set `CURL_PROXYSERVER` to the proxy server address and port, for example:

```
"CURL_USEPROXY":"false",
"CURL_PROXYSERVER":"http://your.proxyserver.com:8080",
```

## Webhooks

Set Telegram webhook via webhook.html, e.g. https://yourdomain.com/botdir/webhook.html

## Languages

You can set several languages for the bot. Available languages are (A-Z):
 - DE (German)
 - EN (English)
 - FR (French)
 - NL (Dutch)

Set `LANGUAGE_PRIVATE` for the prefered language the bot will answer users when they chat with them. Leave blank that the bot will answer in the users language. If the users language is not supported, e.g. ZH-CN (Chinese), the bot will always use EN (English) as fallback language.

Set `LANGUAGE_PUBLIC` to the prefered language for shared quests. Default value: EN

So if you want to have the bot communication based on the users Telegram language, e.g. Russian, and show the shared quests in German for example:

```
"LANGUAGE_PRIVATE":"",
"LANGUAGE_PUBLIC":"DE",
```

## Timezone and Google maps API

Set `TIMEZONE` to the timezone you wish to use for the bot. Predefined value from the example config is "Europe/Berlin".

Optionally you can you use Google maps API to lookup addresses of gyms based on latitude and longitude. Therefore get a Google maps API key. 

To get a new API key, navigate to https://console.developers.google.com/apis/credentials and create a new API project, e.g. PokemonQuestBot 

Once the project is created select "API key" from the "Create credentials" dropdown menu - a new API key is created.

After the key is created, you need to activate it for both: Geocoding and Timezone API

Therefore go to "Dashboard" on the left navigation pane and afterwards hit "Enable APIs and services" on top of the page.

Search for Geocoding and Timezone API and enable them. Alternatively use these links to get to Geocoding and Timezone API services:

https://console.developers.google.com/apis/library/timezone-backend.googleapis.com

https://console.developers.google.com/apis/library/geocoding-backend.googleapis.com

Finally check the dashboard again and make sure Google Maps Geocoding API and Google Maps Time Zone API are listed as enabled services.

Set `MAPS_LOOKUP` to true and put the API key in `MAPS_API_KEY` in your config.


## Quest creation

There are several options to customize the creation of quests:

Set `QUEST_VIA_LOCATION` to true to allow quest creation from a location shared with the bot.

Set `QUEST_LOCATION` to true to send back the location as message in addition to the quest.

Set `QUEST_STOPS_RADIUS` to the amount in meters the bot will search for pokestops around the location shared with the bot.

Set `QUEST_HIDE_REWARDS` to true to hide specific reward types, e.g. berries or revives. Specify the reward types you want to hide in `QUEST_HIDDEN_REWARDS` separated by comma. 

Example to hide pokeballs, berries, potions and revives: `"QUEST_HIDDEN_REWARDS":"2,7,10,12"`

Every ID/number for all the available reward types:

| Reward ID | Reward type |
|-----------|-------------|
| 1         | Pokemon     |
| 2         | Pokeball    |
| 3         | Stardust    |
| 4         | Rare candy  |
| 5         | Fast TM     | 
| 6         | Charged TM  | 
| 7         | Berry       |
| 8         | golden Berry|
| 9         | silver Berry|
| 10        | Potion      | 
| 11        | Max Potion  | 
| 12        | Revive      | 
| 13        | Max Revive  | 
| 14        | Evolve Item | 
| 15        | Dragon Scale| 
| 16        | Sun Stone   | 
| 17        | King's Rock | 
| 18        | Metal Coat  | 
| 19        | Up-Grade    | 


## Quest sharing

You can share quests with any chat in Telegram via a share button.

Sharing quests can be restricted, so only specific chats/users can be allowed to share a quest - take a look at the permission system!

With a predefined list you can specify the chats which should appear as buttons for sharing quests.

For the ID of a chat either forward a message from the chat to a bot like @RawDataBot, @getidsbot or search the web for another method ;)

Example:

#### Predefine sharing to the chats -100111222333 and -100444555666

`"SHARE_CHATS":"-100111222333,-100444555666"`


## Invasion creation

There are several options to customize the creation of Team Rocket invasions:

Set `INVASION_VIA_LOCATION` to true to allow invasion creation from a location shared with the bot.

Set `INVASION_LOCATION` to true to send back the location as message in addition to the invasion.

Set `INVASION_STOPS_RADIUS` to the amount in meters the bot will search for pokestops around the location shared with the bot.

Set `INVASION_DURATION_SHORT` to the amount of time in minutes how long at least the invasion is probably at the pokestop.

Set `INVASION_DURATION_LONG` to the amount of time in minutes how long idially the invasion is probably at the pokestop.

Set `INVASION_DURATION_EVENT` to the amount of time in minutes how long invasions will be at pokestops during an event. This will give the users the options to choose between the event invasion time and the shortest possible invasion time in 5 minute steps.

## Invasion sharing

You can share invasions with any chat in Telegram via a share button.

Sharing invasions can be restricted, so only specific chats/users can be allowed to share an invasion - take a look at the permission system!

With a predefined list you can specify the chats which should appear as buttons for sharing invasions.

For the ID of a chat either forward a message from the chat to a bot like @RawDataBot, @getidsbot or search the web for another method ;)

Example:

#### Predefine sharing to the chats -100111222333 and -100444555666

`"SHARE_INVASIONS":"-100111222333,-100444555666"`


## Portal Import

Set `PORTAL_IMPORT` to `true` to enable the possibility to import portals from Telegram Ingress Bots.

## Cleanup

The bot features an automatic cleanup of telegram messages as well as cleanup of the database (quests tables).

To activate cleanup you need to change the config and create a cronjob to trigger the cleanup process as follows:

Set the `CLEANUP` in the config to `true` and define a cleanup secret/passphrase under `CLEANUP_SECRET`.

Activate the cleanup of telegram messages and/or the database for quests by setting `CLEANUP_QUEST_TELEGRAM` / `CLEANUP_QUEST_DATABASE` to true.

The cleanup process will automatically detect old quests which are not from the present day.

Finally set up a cronjob to trigger the cleanup. You can also trigger telegram / database cleanup per cronjob: For no cleanup use 0, for cleanup use 1 and to use your config file use 2 or leave "telegram" and "database" out of the request data array.

A few examples for quests - make sure to replace the URL with yours:

#### Cronjob using cleanup values from config.json for quests: Just the secret without telegram/database OR telegram = 2 and database = 2

`curl -k -d '{"cleanup":{"secret":"your-cleanup-secret/passphrase"}}' https://localhost/index.php?apikey=111111111:AABBCCDDEEFFGGHHIIJJKKLLMMNNOOPP123`

OR

`curl -k -d '{"cleanup":{"secret":"your-cleanup-secret/passphrase","telegram":"2","database":"2"}}' https://localhost/index.php?apikey=111111111:AABBCCDDEEFFGGHHIIJJKKLLMMNNOOPP123`

#### Cronjob to clean up telegram quest messages only: telegram = 1 and database = 0 

`curl -k -d '{"cleanup":{"secret":"your-cleanup-secret/passphrase","telegram":"1","database":"0"}}' https://localhost/index.php?apikey=111111111:AABBCCDDEEFFGGHHIIJJKKLLMMNNOOPP123`

#### Cronjob to clean up database and telegram quest messages: telegram = 1 and database = 1

`curl -k -d '{"cleanup":{"secret":"your-cleanup-secret/passphrase","telegram":"1","database":"1"}}' https://localhost/index.php?apikey=111111111:AABBCCDDEEFFGGHHIIJJKKLLMMNNOOPP123`

# Access permissions

## Public access

When no telegram id, group, supergroup or channel is specified in `BOT_ADMINS` the bot will allow everyone to use it (public access).

Example for public access: `"BOT_ADMINS":""`

## Access and permissions

The `MAINTAINER_ID` is not able to access the bot nor has any permissions as that id is only contacted in case of errors and issues with the bot configuration.

The `BOT_ADMINS` have all permissions and can use any feature of the bot.

Telegram Users have no access to bot functions (unless you configured it).

In order to allow telegram chats to access the bot and use commands/features, you need to create an access file.

It does not matter if a chat is a user, group, supergroup or channel - any kind of chat is supported as every chat has a chat id!

Those access files need to be placed under the subdirectory 'access' and follow a special name scheme.

| Chat type                     | User role      | Name of the access file           | Example                   |
|-------------------------------|----------------|-----------------------------------|---------------------------|
| User                          | -              | `accessCHAT_ID`                   | `access111555999`         |
|                               |                |                                   |                           |
| Group, Supergroup, Channel    | Any role       | `accessCHAT_ID`                   | `access-100224466889`     |
|                               | Creator        | `creatorCHAT_ID`                  | `creator-100224466889`    |
|                               | Admin          | `adminsCHAT_ID`                   | `admins-100224466889`     |
|                               | Member         | `membersCHAT_ID`                  | `members-100224466889`    |
|                               | Restricted     | `restrictedCHAT_ID`               | `restricted-100224466889` |
|                               | Kicked         | `kickedCHAT_ID`                   | `kicked-100224466889`     |

As you can see in the table, you can define different permissions for the creator, the admins and the members of a group, supergroup and channel.

You can also create just one access file, so any user has the same permission regardless of their role in the chat. But this is not recommended (see important note below!).

**Important: Any role means any role - so in addition to roles 'creator', 'administrator' or 'member' this will also grant 'restricted' and 'kicked' users to access the bot with the defined permissions!

To exclude 'restricted' and 'kicked' users when using an access file for any role (e.g. `access-100224466889`) you can add the permissions `ignore-restricted` and `ignore-kicked` to the access file!

User with the role 'left' are automatically receiving an 'Access denied' from the bot as they willingly have choosen to leave the chat through which they got access to the bot!**

Every access file allows the access for a particular chat and must include the permissons which should be granted to that chat.

To differ between all those access file you can add any kind of comment to the filename of the access file itself. Just make sure to not use a number (0-9) right after the chat id!

Consider you have 4 channels. One for each district of your town: east, west, south and north. So you could name the access file for example like this:

```
access-100333444555 South-Channel
access-100444555666+NorthernChannel
admins-100222333444_West-District
creator-100111222333-Channel-East-District
creator-100444555666+NorthernChannel
members-100111222333-Channel-East-District
members-100222333444_West-District
```

## Permissions overview

The following table shows the permissions you need to write into an access file (last column) to grant permissions to chats.

In an access file it is **One permission per line** - so not separated by space, comma or any other char!

A few examples for access files can be found below the permission overview table.


| Access     | **Action and /command**                                            | Permission inside access file              |
|------------|--------------------------------------------------------------------|--------------------------------------------|
| Bot        | Access the bot itself                                              | `access-bot`                               |
|            | Deny access to restricted group/supergroup/channel members         | `ignore-restricted`                        |
|            | Deny access to kicked group/supergroup/channel members             | `ignore-kicked`                            |
|            |                                                                    |                                            |
| Quest      | Create quests `/start`, `/new`                                     | `create`                                   |
|            | List all quests `/list`                                            | `list`                                     |
|            | Delete OWN quests `/delete`                                        | `delete-own`                               |
|            | Delete ALL quests `/delete`                                        | `delete-all`                               |
|            |                                                                    |                                            |
| Invasion   | Create invasions `/rocket`                                         | `invasion-create`                          |
|            | List all invasions `/rocketlist`                                   | `invasion-list`                            |
|            | Delete OWN invasions `/rocketdelete`                               | `invasion-delete-own`                      |
|            | Delete ALL invasions `/rocketdelete`                               | `invasion-delete-all`                      |
|            | Add comments to invasions `/crypto`                                | `invasion-create`                          |
|            |                                                                    |                                            |
| Sharing    | Share OWN created quests to predefined chats 'SHARE_CHATS'         | `share-own`                                |
|            | Share ALL created quests to predefined chats 'SHARE_CHATS'         | `share-all`                                |
|            | Share OWN created quests to any chat                               | `share-own` and `share-any-chat`           |
|            | Share ALL created quests to any chat                               | `share-all` and `share-any-chat`           |
|            | Share OWN created invasions to predefined chats 'SHARE_INVASIONS'  | `invasion-share-own`                       |
|            | Share ALL created invasions to predefined chats 'SHARE_INVASIONS'  | `invasion-share-all`                       |
|            | Share OWN created invasions to any chat                            | `invasion-share-own` and `share-any-chat`  |
|            | Share ALL created invasions to any chat                            | `invasion-share-all` and `share-any-chat`  |
|            |                                                                    |                                            |
| Pokestop   | Get pokestop details `/pokestop`                                   | `pokestop-details`                         |
|            | Edit pokestop name `/stopname`                                     | `pokestop-name`                            |
|            | Edit pokestop address `/stopaddress`                               | `pokestop-address`                         |
|            | Edit pokestop gps coordinates `/stopgps`                           | `pokestop-gps`                             |
|            | Add pokestop `/addstop`                                            | `pokestop-add`                             |
|            | Delete pokestop `/deletestop`                                      | `pokestop-delete`                          |
|            |                                                                    |                                            |
| Portal     | Import portals via inline search from other bots                   | `portal-import`                            |
|            |                                                                    |                                            |
| Event      | Set current quest event name `/event`                              | `event`                                    |
|            |                                                                    |                                            |
| Dex        | Get pokemon id by pokemon name `/dex`                              | `dex`                                      |
|            |                                                                    |                                            |
| Willow     | Manage quests, rewards, encounters and quicklist `/willow`         | `willow`                                   |
|            |                                                                    |                                            |
| Help       | Show help `/help`                                                  | `help`                                     |


#### Example: Allow the user 111555999 to create quests and share them to the predefined chat list

Access file: `access\access111555999`

Content of the access file, so the actual permissions:
```
access-bot
create
share-own
```

#### Example: Allow the creator and the admins of the channel -100224466889 to create quests as well as sharing quests created by their own or others to the predefined chat list or any other chat

Access file for the creator: `access\creator-100224466889`

Access file for the admins: `access\admins-100224466889`

Important: The minus `-` in front of the actual chat id must be part of the name as it's part of the chat id!

Content of the access files, so the actual permissions:
```
access-bot
create
share-all
share-own
share-any-chat
```

# Customization

The bot allows you to customize things and therefore has a folder 'custom' for your customizations.

## Custom icons

In case you do not like some of the predefined icons and might like to change them to other/own icons:
- Create a file named `constants.php` in the custom folder
- Lookup the icon definitions you'd like to change in either the core or bot constants.php (`core/bot/constants.php` and `constants.php`)
- Define your own icons in your custom constants.php
- For example to change the yellow exclamation mark icon to a red exclamation mark put the following in your `custom/constants.php`:

`<?php
defined('EMOJI_WARN')           or define('EMOJI_WARN',    iconv('UCS-4LE', 'UTF-8', pack('V', 0x2757)));
`
- Make sure to not miss the first line which declares the file as php file!
- To get the codes (here: 0x2757) of the icons/emojis, take a look at one of the large emoji databases in the web. They ususally have them mentioned and also show how the icons look like on different systems.

## Custom translation

To change translations you can do the following:
- Create a file named `language.json` in the custom folder
- Find the translation name/id by searching the core and bot language.php files (`core/lang/language.php` and `lang/language.php`)
- Set your own translation in your custom language.json
- For example to change the translation of 'Friday' to a shorter 'Fri' put the following in your `custom/language.json`:

```
{
    "weekday_5":{
        "EN":"Fri"
    }
}
```
- Make sure to create a valid JSON file for your custom translations
- To verify your custom language.json you can use several apps, programs and web services.

# Usage

## Bot commands
### Command: No command - just send your location to the bot

The bot will guide you through the creation of a quest based on the settings in the config file and ask you for the quest type and quest action to be done and the reward which will be given upon quest fulfillment.

### Command: No command - using inline search of @PortalMapBot or @Ingressportalbot

You can add new pokestops to the bot using the inline search of one of the bots mentioned above. Just search for a portal name, e.g. `Brandenburger Tor`, and select one of the portals shown as result of your search.

On selection the portal information will get posted as a normal message and detected, so a new pokestop is automatically created from the portal info in that message.

In case the portal is already in your pokestop list / database, it will get updated with the new info (latitude, longitude and address) from the message.

Example: `@Ingressportalbot Brandenburger Tor`


### Command: /start or /new

Create a quest by searching for the Pokestop name in the database. The bot will answer with all pokestops matching the name, e.g. "Brandenburger Tor".

Example input: `/quest Brandenburger Tor`


### Command: /list

The bot will allow you to get a list of the quests from today, share and delete all quests.


### Command: /delete

Delete an existing quest. With this command you can delete a quest from telegram and the database. Use with care!

Based on your access to the bot, you may can only delete quests you created yourself and cannot delete quests from other bot users.


### Command: /rocket

Create a Team Rocket invasion by searching for the Pokestop name in the database. The bot will answer with all pokestops matching the name, e.g. "Brandenburger Tor".

Example input: `/rocket Brandenburger Tor`


### Command: /rocketlist

The bot will allow you to get a list of the currently active Team Rocket Invasions, share and delete all of them.


### Command: /rocketdelete

Delete an existing Team Rocket Invasion. With this command you can delete an invasion from telegram and the database. Use with care!

Based on your access to the bot, you may can only delete invasions you created yourself and cannot delete invasions from other bot users.


### Command: /crypto

The bot will allow you to add a comment like the pokemon you have to beat, to a currently active Team Rocket Invasion.

Example input: `/crypto Snorlax, Snorlax, Snorlax`


### Command: /pokestop

Get details of a pokestop. The full name or at least a part of the name of the pokestop is required.

Example input: `/pokestop Brandenburger Tor`


### Command: /addstop

The bot will add a pokestop under the coordinates you're submitting. First latitude, then longitude. The pokestop is added under the name '#YourTelegramID' (e.g. '#111555777') and you need to change the name afterwards using the `/stopname` command. You cannot submit a second pokestop unless you changed the name of the first pokestop. In case you submit a second pokestop without changing the name of the previously submitted pokestop, the first pokestop coordinates will be overwritten!

Example input: `/addstop 52.5145434,13.3501189`


### Command: /stopname

The bot will set the name of a pokestop to your input. The id of the pokestop is required.

Example input: `/stopname 34, Brandenburger Tor`


### Command: /stopaddress

The bot will set the address of a pokestop to your input. The id of the pokestop is required. You can delete the pokestop address using the keyword 'reset'.

Example input: `/stopaddress 34, Großer Stern, 10557 Berlin`

Example input to delete the gym address: `/stopaddress 34, reset`


### Command: /stopgps

The bot will set the gps coordinates of a pokestop to your input. The id of the pokestop is required.

Example input: `/stopgps 34, 52.5145434,13.3501189`


### Command: /deletestop

Delete a pokestop from the database. The full name or at least a part of the name of the pokestop is required. Select a pokestop and confirm the deletion to remove it from the database.

Example input: `/deletestop Brandenburger Tor`


### Command: /event

Set the current event name for quests - only one event can be set! If you use the command more than once, the previously submitted event name will always be overwritten!

To add events permanently, edit the quest_event.json in the lang folder!

Example input: `/event Adventureweek`


### Command: /help

The bot will give a personal help based on the permissions you have to access and use it.


### Command: /dex

Get the pokemon id of a pokemon by name. Works for local languages pokemon names as well as English pokemon names.

Example input: `/dex Pikachu`


### Command: /willow

You can manage several things under this command and add/delete quests, rewards, encounters and quicklist entries.

# Debugging

Check your bot logfile and other related log files, e.g. apache/httpd log, php log, and so on.

# Updates

The bot has a version system and checks for updates to the database automatically.

The bot will send a message to the MAINTAINER_ID when an upgrade is required. In case the MAINTAINER_ID is not specified an error message is written to the error log of your webserver.

Required SQL upgrades files can be found under the `sql/upgrade` folder and need to be applied manually!

After any upgrade you need to make sure to change the bot version in your config.json as that version is used for comparison against the latest bot version in the `VERSION` file.

Updates to the config file are NOT checked automatically. Therefore always check for changes to the config.json.example and add new config variables to your own config.json then too!

# Git Hooks

In the needed core repository we provide a folder with git hooks which can be used to automate several processes. Copy them to the `.git/hooks/` folder of this bot and make them executable (e.g. `chmod +x .git/hooks/pre-commit`) to use them.

#### pre-commit

The pre-commit git hook will automatically update the VERSION file whenever you do a `git commit`.

The bot version is automatically generated when using the pre-commit hook according to the following scheme consisting of 4 parts separated by dots:
 - Current decade (1 char)
 - Current year (1 char)
 - Current day of the year (up to 3 chars)
 - Number of the commit at the current day of the year (1 or more chars)

To give a little example the bot version `1.9.256.4` means:
 - Decade was 20**1**0-20**1**9
 - Year was 201**9**
 - Day number **256** (from 365 days in 2019) was the 13th September 2019
 - There have been **4** commits at that day

This way it is easy to find out when a bot version was released and how old/new a version is.

# TODO

- Support pokemon forms!

# SQL Files

The following commands are used to create the pokemon-quest-bot.sql and quests-rewards-encounters.sql files. Make sure to replace USERNAME and DATABASENAME before executing the commands.

#### pokemon-quest-bot.sql

Export command: `mysqldump -u USERNAME -p --no-data --skip-add-drop-table --skip-add-drop-database --skip-comments DATABASENAME | sed 's/ AUTO_INCREMENT=[0-9]*\b/ AUTO_INCREMENT=100/' > sql/pokemon-quest-bot.sql`

#### quests-rewards-encounters.sql

Export command: `mysqldump -u USERNAME -p --skip-extended-insert --skip-comments DATABASENAME quick_questlist questlist encounterlist rewardlist > sql/quests-rewards-encounters.sql`

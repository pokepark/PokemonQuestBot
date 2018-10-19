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
For git 2.13 and above:
`git clone --recurse-submodules https://github.com/florianbecker/PokemonQuestBot.git`

If you're running an older version of git use the deprecated recursive command:
`git clone --recursive https://github.com/florianbecker/PokemonQuestBot.git`

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

Copy config.php.example to config.php and edit (values explained further).

Enter the details for the database connection to the config.php file.

## General config and log files

Set `DEBUG` to true, to enable the debug logfile.

Set `CONFIG_LOGFILE` to the location of the logfile, e.g. /var/log/tg-bots/dev-quest-bot.log. Make sure to create the log dir, e.g. /var/log/tg-bots/ and set it writeable by webserver.

Set `CONFIG_HASH` to the hashed value of your bot token (preferably lowercase) using a hash generator, e.g. https://www.miniwebtool.com/sha512-hash-generator/ 

Set `DDOS_MAXIMUM` to the amount of callback queries each user is allowed to do each minute. If the amount is reached, e.g. 10, any further callback query is rejected by the DDOS check.

Set `BRIDGE_MODE` to true when you're using the PokemonBotBridge. If you're not using the PokemonBotBridge keep the default false. PokemonBotBridge: https://github.com/florianbecker/PokemonBotBridge

## Proxy

Set `CURL_USEPROXY` to `true` in case you are running the bot behind a proxy server.

Set `CURL_PROXYSERVER` to the proxy server address and port.

Authentication against the proxy server by username and password is currently not supported!

## Webhooks

Set Telegram webhook via webhook.html, e.g. https://yourdomain.com/botdir/webhook.html

## Languages

You can set several languages for the bot. Available languages are (A-Z):
 - DE (German)
 - EN (English)
 - FR (French)
 - NL (Dutch)

Set `LANGUAGE` for the prefered language the bot will answer users when they chat with them. Leave blank that the bot will answer in the users language. If the users language is not supported, e.g. ZH-CN (Chinese), the bot will always use EN (English) as fallback language.

Set `QUEST_LANGUAGE` to the prefered language for quests.

So if you want to have the bot communication based on the users Telegram language, e.g. Dutch, and show the quest message in German for example:

`define('LANGUAGE', '');`
`define('QUEST_LANGUAGE', 'DE');`

## Timezone and Google maps API

Set `TIMEZONE` to the timezone you wish to use for the bot. Predefined value from the example config is "Europe/Berlin".

Optionally you can you use Google maps API to lookup addresses of gyms based on latitude and longitude

Therefore get a Google maps API key and set it as `GOOGLE_API_KEY` in your config.

To get a new API key, navigate to https://console.developers.google.com/apis/credentials and create a new API project, e.g. PokemonQuestBot

Once the project is created select "API key" from the "Create credentials" dropdown menu - a new API key is created.

After the key is created, you need to activate it for both: Geocoding and Timezone API

Therefore go to "Dashboard" on the left navigation pane and afterwards hit "Enable APIs and services" on top of the page.

Search for Geocoding and Timezone API and enable them. Alternatively use these links to get to Geocoding and Timezone API services:

https://console.developers.google.com/apis/library/timezone-backend.googleapis.com

https://console.developers.google.com/apis/library/geocoding-backend.googleapis.com

Finally check the dashboard again and make sure Google Maps Geocoding API and Google Maps Time Zone API are listed as enabled services.

## Quest sharing

You can share quests with any chat in Telegram via a share button.

Sharing quests can be restricted, so only moderators or users or both can be allowed to share a quest.

Therefore it is possible, via a comma-separated list, to specify the chats the quests can be shared with.

For the ID of a chat either forward a message from the chat to a bot like @RawDataBot or search the web for another method ;)

A few examples:

#### Restrict sharing for moderators and users to chats -100111222333 and -100444555666

`define('SHARE_MODERATORS', false);`

`define('SHARE_USERS', false);`

`define('SHARE_CHATS', '-100111222333,-100444555666');`

#### Allow moderators to share with any chat, restrict sharing for users to chat -100111222333

`define('SHARE_MODERATORS', true);`

`define('SHARE_USERS', false);`

`define('SHARE_CHATS', '-100111222333');`

## Quest creation

There are several options to customize the creation of quests:

Set `QUEST_VIA_LOCATION` to true to allow quest creation from a location shared with the bot.

Set `QUEST_LOCATION` to true to send back the location as message in addition to the quest.

Set `QUEST_STOPS_RADIUS` to the amount in meters the bot will search for pokestops around the location shared with the bot.

Set `QUEST_HIDE_REWARDS` to true to hide specific reward types, e.g. berries or revives. Specify the reward types you want to hide in `QUEST_HIDDEN_REWARDS` separated by comma. 

Example to hide pokeballs, berries, potions and revives: `define('QUEST_HIDDEN_REWARDS', '2,7,10,12');`

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

## Cleanup

The bot features an automatic cleanup of telegram messages as well as cleanup of the database (quests tables).

To activate cleanup you need to change the config and create a cronjob to trigger the cleanup process as follows:

Set the `CLEANUP` in the config to `true` and define a cleanup secret/passphrase under `CLEANUP_SECRET`.

Activate the cleanup of telegram messages and/or the database for quests by setting `CLEANUP_QUEST_TELEGRAM` / `CLEANUP_QUEST_DATABASE` to true.

The cleanup process will automatically detect old quests which are not from the present day.

Finally set up a cronjob to trigger the cleanup. You can also trigger telegram / database cleanup per cronjob: For no cleanup use 0, for cleanup use 1 and to use your config file use 2 or leave "telegram" and "database" out of the request data array. Please make sure to always specify the cleanup type which needs to be `quest`.

A few examples for quests - make sure to replace the URL with yours:

#### Cronjob using cleanup values from config.php for quests: Just the secret without telegram/database OR telegram = 2 and database = 2

`curl -k -d '{"cleanup":{"type":"quest","secret":"your-cleanup-secret/passphrase"}}' https://localhost/index.php?apikey=111111111:AABBCCDDEEFFGGHHIIJJKKLLMMNNOOPP123`

OR

`curl -k -d '{"cleanup":{"type":"quest","secret":"your-cleanup-secret/passphrase","telegram":"2","database":"2"}}' https://localhost/index.php?apikey=111111111:AABBCCDDEEFFGGHHIIJJKKLLMMNNOOPP123`

#### Cronjob to clean up telegram quest messages only: telegram = 1 and database = 0 

`curl -k -d '{"cleanup":{"type":"quest","secret":"your-cleanup-secret/passphrase","telegram":"1","database":"0"}}' https://localhost/index.php?apikey=111111111:AABBCCDDEEFFGGHHIIJJKKLLMMNNOOPP123`

#### Cronjob to clean up database and telegram quest messages: telegram = 1 and database = 1

`curl -k -d '{"cleanup":{"type":"quest","secret":"your-cleanup-secret/passphrase","telegram":"1","database":"1"}}' https://localhost/index.php?apikey=111111111:AABBCCDDEEFFGGHHIIJJKKLLMMNNOOPP123`

# Access permissions

## Public access

When no telegram id, group, supergroup or channel is specified in `BOT_ADMINS` and/or `BOT_ACCESS`, the bot will allow everyone to use it (public access).

Example for public access: `define('BOT_ACCESS', '');`

## Restricted access

With BOT_ADMINS and BOT_ACCESS being used to restrict access, there are several access roles / types. When you do not configure BOT_ACCESS, everyone will have access to your bot (public access).  

Set `BOT_ADMINS` and `BOT_ACCESS` to id (-100123456789) of one or multiple by comma separated individual telegram chat names/ids, groups, supergroups or channels.

Please note, when you're setting groups, supergroups or channels only administrators (not members!) from these chats will gain access to the bot! So make sure this requirement is fulfilled or add their individual telegram usernames/ids instead.

Example for restricted access:  
`define('BOT_ADMINS', '111222333,111555999');`

`define('BOT_ACCESS', '111222333,-100224466889,-100112233445,111555999');`

To allow members from groups, supergroups or channels:

Set `BOT_ALLOW_MEMBERS` to true, so members of a Telegram chat in addition to the administrators are considered during the access check and allowed to use the bot if they are a member of the respective chat.

Set `BOT_ALLOW_MEMBERS_CHAT` to the chats you wish to allow member access for.

Example to allow members of chat groups -100112233445 and -100224466889:
`define('BOT_ALLOW_MEMBERS', true);`

`define('BOT_ALLOW_MEMBERS_CHATS', '-100112233445, -100224466889');`


## Access overview

With your `MAINTAINER_ID` and as a member of `BOT_ADMINS` you have the permissions to do anything. **For performance improvements, it's recommended to add the MAINTAINER and all members of BOT_ADMINS as moderator via /mods command!** 

As a member of `BOT_ACCESS` you can create and share quests. `BOT_ACCESS` members who are moderators too, can delete their own quests and also quests from other users. Note that members of `BOT_ACCESS` are not allowed to see the available quests in DB by ID, only the `MAINTAINER_ID` and the `BOT_ADMINS` have the right to do so.

Telegram Users can only see on shared quests, but have no access to other bot functions (unless you configured it for public access).


| Access:   |            |                                  | MAINTAINER_ID | BOT_ADMINS | BOT_ACCESS | BOT_ACCESS | Telegram |
|-----------|------------|----------------------------------|---------------|------------|------------|------------|----------|
| Database: |            |                                  |               |            | Moderator  | User       | User     |
|           | **Area**   | **Action and /command**          |               |            |            |            |          |
|           |            |                                  |               |            |            |            |          |
|           | Moderators | List `/mods`                     | Yes           | Yes        |            |            |          |
|           |            | Add `/mods`                      | Yes           | Yes        |            |            |          |
|           |            | Delete `/mods`                   | Yes           | Yes        |            |            |          |
|           |            |                                  |               |            |            |            |          |
|           | Quests     | Create `/start` or `/new`        | Yes           | Yes        | Yes        | Yes        |          |
|           |            | List `/list`                     | Yes           | Yes        | Yes        | Yes        |          |
|           |            | Delete ALL quests `/delete`      | Yes           | Yes        |            |            |          |
|           |            | Delete OWN quests `/delete`      | Yes           | Yes        | Yes        | Yes        |          |
|           |            | Quests in DB by ID `/willow`     | Yes           | Yes        |            |            |          |
|           |            |                                  |               |            |            |            |          |
|           | Help       | Show `/help`                     | Yes           | Yes        | Yes        | Yes        |          |


# Usage

## Bot commands
### Command: No command - just send your location to the bot

The bot will guide you through the creation of a quest based on the settings in the config file and ask you for the quest type and quest action to be done and the reward which will be given upon quest fulfillment.

### Command: /start or /new

Create a quest by searching for the Pokestop name in the database. The bot will answer with all pokestops matching the name, e.g. "Brandenburger Tor".

Example input: `/quest Brandenburger Tor`


### Command: /delete

Delete an existing quest. With this command you can delete a quest from telegram and the database. Use with care!

Based on your access to the bot, you may can only delete quests you created yourself and cannot delete quests from other bot users.


### Command: /list

The bot will allow you to get a list of the quests from today, share and delete all quests.


### Command: /help

The bot will answer you "This is a private bot" so you can verify the bot is working and accepting input.


### Command: /mods

The bot allows you to set some users as moderators. You can list, add and delete moderators from the bot. Note that when you have restricted the access to your bot via BOT_ADMINS and BOT_ACCESS, you need to add the users as administrators of a chat or their Telegram IDs to either BOT_ADMINS or BOT_ACCESS. Otherwise they won't have access to the bot, even though you have added them as moderators! 


### Command: /willow

Get a list of all available quests and their ID from the database.

# Debugging

Check your bot logfile and other related log files, e.g. apache/httpd log, php log, and so on.

# Updates

Currently constantly new features, bug fixes and improvements are added to the bot. Since we do not have an update mechanism yet, when updating the bot, please always do the following:
 - Add new config variables which got added to the config.php.example to your own config.php!
 - If new tables and/or columns got added or changed inside pokemon-quest-bot.sql, please add/alter these tables/columns at your existing installation!

# SQL Files

The following commands are used to create the pokemon-quest-bot.sql and quests-rewards-encounters.sql files. Make sure to replace USERNAME and DATABASENAME before executing the commands.

#### pokemon-quest-bot.sql

Export command: `mysqldump -u USERNAME -p --no-data --skip-add-drop-table --skip-add-drop-database --skip-comments DATABASENAME | sed 's/ AUTO_INCREMENT=[0-9]*\b/ AUTO_INCREMENT=100/' > sql/pokemon-quest-bot.sql`

#### quests-rewards-encounters.sql

Export command: `mysqldump -u USERNAME -p --skip-extended-insert --skip-comments DATABASENAME questlist rewardlist encounterlist quick_questlist > sql/quests-rewards-encounters.sql`

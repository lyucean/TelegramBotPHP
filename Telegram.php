<?php

if (file_exists('TelegramErrorLogger.php')) {
    require_once 'TelegramErrorLogger.php';
}

/**
 * Telegram Bot Class.
 *
 * @author Gabriele Grillo <gabry.grillo@alice.it>
 */
class Telegram
{
    /**
     * Constant for type Inline Query.
     */
    const INLINE_QUERY = 'inline_query';
    /**
     * Constant for type Callback Query.
     */
    const CALLBACK_QUERY = 'callback_query';
    /**
     * Constant for type Edited Message.
     */
    const EDITED_MESSAGE = 'edited_message';
    /**
     * Constant for type Reply.
     */
    const REPLY = 'reply';
    /**
     * Constant for type Message.
     */
    const MESSAGE = 'message';
    /**
     * Constant for type Photo.
     */
    const PHOTO = 'photo';
    /**
     * Constant for type Video.
     */
    const VIDEO = 'video';
    /**
     * Constant for type Audio.
     */
    const AUDIO = 'audio';
    /**
     * Constant for type Voice.
     */
    const VOICE = 'voice';
    /**
     * Constant for type animation.
     */
    const ANIMATION = 'animation';
    /**
     * Constant for type sticker.
     */
    const STICKER = 'sticker';
    /**
     * Constant for type Document.
     */
    const DOCUMENT = 'document';
    /**
     * Constant for type Location.
     */
    const LOCATION = 'location';
    /**
     * Constant for type Contact.
     */
    const CONTACT = 'contact';
    /**
     * Constant for type Channel Post.
     */
    const CHANNEL_POST = 'channel_post';

    private $bot_token = '';
    private $data = [];
    private $updates = [];
    private $log_errors;
    private $proxy;

    /// Class constructor

    /**
     * Create a Telegram instance from the bot token
     * @param $bot_token int // the bot token
     * @param $log_errors bool // enable or disable the logging
     * @param $proxy array with the proxy configuration (url, port, type, auth)
     * return an instance of the class.
     */
    public function __construct($bot_token, $log_errors = true, array $proxy = [])
    {
        $this->bot_token = $bot_token;
        $this->data = $this->getData();
        $this->log_errors = $log_errors;
        $this->proxy = $proxy;
    }

    /// Do requests to Telegram Bot API

    /**
     * Contacts the various API's endpoints
     * @param $api // the API endpoint
     * @param $content // the request parameters as array
     * @param $post bool // tells if $content needs to be sends
     * @return string // the JSON Telegram's reply.
     */
    public function endpoint($api, array $content, $post = true)
    {
        $url = 'https://api.telegram.org/bot' . $this->bot_token . '/' . $api;
        if ($post) {
            $reply = $this->sendAPIRequest($url, $content);
        } else {
            $reply = $this->sendAPIRequest($url, [], false);
        }

        return json_decode($reply, true);
    }

    /// A method for testing your bot.

    /**
     * A simple method for testing your bot's auth token. Requires no parameters.
     * Returns basic information about the bot in form of a User object.
     * @return string // the JSON Telegram's reply.
     */
    public function getMe()
    {
        return $this->endpoint('getMe', [], false);
    }

    /// A method for responding http to Telegram.

    /**
     * @return string // the HTTP 200 to Telegram.
     */
    public function respondSuccess()
    {
        http_response_code(200);

        return json_encode(['status' => 'success']);
    }

    /// Send a message

    /**
     * Use this method to send text messages.
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function sendMessage(array $content)
    {
        return $this->endpoint('sendMessage', $content);
    }

    /// Forward a message

    /**
     * Use this method to forward messages of any kind. On success, the sent Message is returned
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function forwardMessage(array $content)
    {
        return $this->endpoint('forwardMessage', $content);
    }

    /// Send a photo

    /**
     * Use this method to send photos. On success, the sent Message is returned.
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function sendPhoto(array $content)
    {
        return $this->endpoint('sendPhoto', $content);
    }

    /// Send an audio

    /**
     * Use this method to send audio files, if you want Telegram clients to display them in the music player.
     * Your audio must be in the .mp3 format. On success, the sent Message is returned.
     * Bots can currently send audio files of up to 50 MB in size, this limit may be changed in the future.
     * For sending voice messages, use the sendVoice method instead.
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function sendAudio(array $content)
    {
        return $this->endpoint('sendAudio', $content);
    }

    /// Send a document

    /**
     * Use this method to send general files.
     * On success, the sent Message is returned. Bots can currently send files of any type of up to 50 MB in size, this limit may be changed in the future.
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function sendDocument(array $content)
    {
        return $this->endpoint('sendDocument', $content);
    }

    /// Send an animation

    /**
     * Use this method to send animation files (GIF or H.264/MPEG-4 AVC video without sound).
     * On success, the sent Message is returned. Bots can currently send animation files of up to 50 MB in size, this limit may be changed in the future.
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function sendAnimation(array $content)
    {
        return $this->endpoint('sendAnimation', $content);
    }

    /// Send a sticker

    /**
     * Use this method to send .webp stickers. On success, the sent Message is returned.
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function sendSticker(array $content)
    {
        return $this->endpoint('sendSticker', $content);
    }

    /// Send a video

    /**
     * Use this method to send video files, Telegram clients support mp4 videos (other formats may be sent as Document).
     * On success, the sent Message is returned.
     * Bots can currently send video files of up to 50 MB in size, this limit may be changed in the future.
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function sendVideo(array $content)
    {
        return $this->endpoint('sendVideo', $content);
    }

    /// Send a voice message

    /**
     *  Use this method to send audio files, if you want Telegram clients to display the file as a playable voice message.
     * For this to work, your audio must be in an .ogg file encoded with OPUS (other formats may be sent as Audio or Document).
     * On success, the sent Message is returned. Bots can currently send voice messages of up to 50 MB in size, this limit may be changed in the future.
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function sendVoice(array $content)
    {
        return $this->endpoint('sendVoice', $content);
    }

    /// Send a location

    /**
     *  Use this method to send point on the map. On success, the sent Message is returned.
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function sendLocation(array $content)
    {
        return $this->endpoint('sendLocation', $content);
    }

    /// Edit Message Live Location

    /**
     * Use this method to edit live location messages sent by the bot or via the bot inline
     * @link https://core.telegram.org/bots/api#inline-mode
     * A location can be edited until its live_period expires or editing is explicitly disabled by a call to stopMessageLiveLocation
     * On success, if the edited message was sent by the bot, the edited
     * @link https://core.telegram.org/bots/api#message
     * Message is returned, otherwise True is returned.
     * @param array $content
     * @return mixed
     */
    public function editMessageLiveLocation(array $content)
    {
        return $this->endpoint('editMessageLiveLocation', $content);
    }

    /// Stop Message Live Location

    /**
     * Use this method to stop updating a live location message sent by the bot or via the bot
     * (for inline bots) before live_period expires.
     * On success, if the message was sent by the bot, the sent https://core.telegram.org/bots/api#message" Message
     * is returned, otherwise True is returned.
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function stopMessageLiveLocation(array $content)
    {
        return $this->endpoint('stopMessageLiveLocation', $content);
    }

    /// Set Chat Sticker Set

    /**
     * Use this method to set a new group sticker set for a supergroup.
     * The bot must be an administrator in the chat for this to work and must have the appropriate admin rights.
     * Use the field can_set_sticker_set optionally returned in https://core.telegram.org/bots/api#getchat" getChat
     * requests to check if the bot can use this method. Returns True on success.
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function setChatStickerSet(array $content)
    {
        return $this->endpoint('setChatStickerSet', $content);
    }

    /// Delete Chat Sticker Set

    /**
     * Use this method to delete a group sticker set from a supergroup.
     * The bot must be an administrator in the chat for this to work and must have the appropriate admin rights.
     * Use the field can_set_sticker_set optionally returned in https://core.telegram.org/bots/api#getchat" getChat
     * requests to check if the bot can use this method. Returns True on success.
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function deleteChatStickerSet(array $content)
    {
        return $this->endpoint('deleteChatStickerSet', $content);
    }

    /// Send Media Group

    /**
     * Use this method to send a group of photos or videos as an album. On success, an array of the
     * sent https://core.telegram.org/bots/api#message" Messages is returned.
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function sendMediaGroup(array $content)
    {
        return $this->endpoint('sendMediaGroup', $content);
    }

    /// Send Venue

    /**
     * Use this method to send information about a venue. On success,
     * the sent https://core.telegram.org/bots/api#message" Message is returned.
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function sendVenue(array $content)
    {
        return $this->endpoint('sendVenue', $content);
    }

    //Send contact

    /**Use this method to send phone contacts. On success, the sent https://core.telegram.org/bots/api#message" Message is returned.
     *
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply
     */
    public function sendContact(array $content)
    {
        return $this->endpoint('sendContact', $content);
    }

    /// Send a chat action

    /**
     *  Use this method when you need to tell the user that something is happening on the bot's side. The status is set for 5 seconds or less (when a message arrives from your bot, Telegram clients clear its typing status).
     *
     * Example: The ImageBot needs some time to process a request and upload the image. Instead of sending a text message along the lines of “Retrieving image, please wait…”, the bot may use sendChatAction with action = upload_photo. The user will see a “sending photo” status for the bot.
     *
     * We only recommend using this method when a response from the bot will take a noticeable amount of time to arrive.
     *
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function sendChatAction(array $content)
    {
        return $this->endpoint('sendChatAction', $content);
    }

    /// Get a list of profile pictures for a user

    /**
     *  Use this method to get a list of profile pictures for a user. Returns a UserProfilePhotos object.
     *
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function getUserProfilePhotos(array $content)
    {
        return $this->endpoint('getUserProfilePhotos', $content);
    }

    /// Use this method to get basic info about a file and prepare it for downloading

    /**
     *  Use this method to get basic info about a file and prepare it for downloading. For the moment, bots can download
     * files of up to 20MB in size. On success, a File object is returned. The file can then be downloaded via the
     * link https://api.telegram.org/file/bot<token>/<file_path>, where <file_path> is taken from the response.
     * It is guaranteed that the link will be valid for at least 1 hour. When the link expires, a new one can be requested by calling getFile again.
     * @param $file_id String File identifier to get info about
     * @return string // the JSON Telegram's reply.
     */
    public function getFile($file_id)
    {
        $content = ['file_id' => $file_id];

        return $this->endpoint('getFile', $content);
    }

    /// Kick Chat Member

    /**
     * Use this method to kick a user from a group or a supergroup. In the case of supergroups, the user will not be
     * able to return to the group on their own using invite links, etc., unless https://core.telegram.org/bots/api#unbanchatmember"
     * unbanned first. The bot must be an administrator in the group for this to work. Returns True on success.
     *
     * Note: This will method only work if the \˜All Members Are Admins\' setting is off in the target group. Otherwise members may only be removed by the group&#39;s creator or by the member that added them.
     *
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function kickChatMember(array $content)
    {
        return $this->endpoint('kickChatMember', $content);
    }

    /// Leave Chat

    /**
     * Use this method for your bot to leave a group, supergroup or channel. Returns True on success.
     *
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function leaveChat(array $content)
    {
        return $this->endpoint('leaveChat', $content);
    }

    /// Unban Chat Member

    /**
     * Use this method to unban a previously kicked user in a supergroup. The user will <strong>not</strong> return to the group automatically, but will be able to join via link, etc. The bot must be an administrator in the group for this to work. Returns True on success.
     *
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function unbanChatMember(array $content)
    {
        return $this->endpoint('unbanChatMember', $content);
    }

    /// Get Chat Information

    /**
     * Use this method to get up to date information about the chat (current name of the user for one-on-one conversations, current username of a user, group or channel, etc.). Returns a https://core.telegram.org/bots/api#chat" Chat object on success.
     *
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function getChat(array $content)
    {
        return $this->endpoint('getChat', $content);
    }

    /**
     * Use this method to get a list of administrators in a chat. On success, returns an Array of https://core.telegram.org/bots/api#chatmember" ChatMember objects that contains information about all chat administrators except other bots. If the chat is a group or a supergroup and no administrators were appointed, only the creator will be returned.
     *
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function getChatAdministrators(array $content)
    {
        return $this->endpoint('getChatAdministrators', $content);
    }

    /**
     * Use this method to get the number of members in a chat. Returns Int on success.
     *
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function getChatMembersCount(array $content)
    {
        return $this->endpoint('getChatMembersCount', $content);
    }

    /**
     * Use this method to get information about a member of a chat. Returns a https://core.telegram.org/bots/api#chatmember" ChatMember object on success.
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function getChatMember(array $content)
    {
        return $this->endpoint('getChatMember', $content);
    }

    /**
     * Use this method to send answers to an inline query. On success, True is returned.
     * No more than <strong>50</strong> results per query are allowed.
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function answerInlineQuery(array $content)
    {
        return $this->endpoint('answerInlineQuery', $content);
    }

    /// Set Game Score

    /**
     * Use this method to set the score of the specified user in a game. On success, if the message was sent by the bot,
     * returns the edited Message, otherwise returns True. Returns an error, if the new score is not greater
     * than the users current score in the chat and force is False.
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function setGameScore(array $content)
    {
        return $this->endpoint('setGameScore', $content);
    }

    /// Answer a callback Query

    /**
     * Use this method to send answers to callback queries sent from inline keyboards. The answer will be displayed to
     * the user as a notification at the top of the chat screen or as an alert. On success, True is returned.
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function answerCallbackQuery(array $content)
    {
        return $this->endpoint('answerCallbackQuery', $content);
    }

    /**
     * Use this method to edit text messages sent by the bot or via the bot (for https://core.telegram.org/bots/api#inline-mode"
     * inline bots). On success, if edited message is sent by the bot, the edited https://core.telegram.org/bots/api#message"
     * Message is returned, otherwise True is returned.
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function editMessageText(array $content)
    {
        return $this->endpoint('editMessageText', $content);
    }

    /**
     * Use this method to edit captions of messages sent by the bot or via the bot (for https://core.telegram.org/bots/api#inline-mode" inline bots).
     * On success, if edited message is sent by the bot, the edited https://core.telegram.org/bots/api#message"
     * Message is returned, otherwise True is returned.
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function editMessageCaption(array $content)
    {
        return $this->endpoint('editMessageCaption', $content);
    }

    /**
     * Use this method to edit only the reply markup of messages sent by the bot or via the bot (for https://core.telegram.org/bots/api#inline-mode" inline bots).
     * On success, if edited message is sent by the bot, the edited https://core.telegram.org/bots/api#message" Message is returned, otherwise True is returned.
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function editMessageReplyMarkup(array $content)
    {
        return $this->endpoint('editMessageReplyMarkup', $content);
    }

    /// Use this method to download a file

    /**
     *  Use this method to to download a file from the Telegram servers.
     * @param $telegram_file_path String File path on Telegram servers
     * @param $local_file_path String File path where save the file.
     */
    public function downloadFile($telegram_file_path, $local_file_path)
    {
        $file_url = 'https://api.telegram.org/file/bot' . $this->bot_token . '/' . $telegram_file_path;
        $in = fopen($file_url, 'rb');
        $out = fopen($local_file_path, 'wb');

        while ($chunk = fread($in, 8192)) {
            fwrite($out, $chunk, 8192);
        }
        fclose($in);
        fclose($out);
    }

    /// Set a WebHook for the bot

    /**
     *  Use this method to specify a url and receive incoming updates via an outgoing webhook. Whenever there is an update
     * for the bot, we will send an HTTPS POST request to the specified url, containing a JSON-serialized Update.
     * In case of an unsuccessful request, we will give up after a reasonable amount of attempts.
     * If you'd like to make sure that the Webhook request comes from Telegram, we recommend using a secret path in the URL,
     * e.g. https://www.example.com/<token>. Since nobody else knows your bots token, you can be pretty sure its us.
     * @param $url
     * @param string $certificate
     * @return string // the JSON Telegram's reply.
     */
    public function setWebhook($url, $certificate = '')
    {
        if ($certificate == '') {
            $requestBody = ['url' => $url];
        } else {
            $requestBody = ['url' => $url, 'certificate' => "@$certificate"];
        }

        return $this->endpoint('setWebhook', $requestBody, true);
    }

    /// Delete the WebHook for the bot

    /**
     *  Use this method to remove webhook integration if you decide to switch back to
     * https://core.telegram.org/bots/api#getupdates" getUpdates. Returns True on success. Requires no parameters.
     * @return string // the JSON Telegram's reply.
     */
    public function deleteWebhook()
    {
        return $this->endpoint('deleteWebhook', [], false);
    }

    /// Get the data of the current message

    /** Get the POST request of a user in a Webhook or the message actually processed in a getUpdates() enviroment.
     * @return string // the JSON users's message.
     */
    public function getData()
    {
        if (empty($this->data)) {
            $rawData = file_get_contents('php://input');

            return json_decode($rawData, true);
        } else {
            return $this->data;
        }
    }

    /// Set the data currently used
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /// Get the text of the current message

    /**
     * @return string the String users's text.
     */
    public function Text()
    {
        $type = $this->getUpdateType();
        if ($type == self::CALLBACK_QUERY) {
            return @$this->data['callback_query']['data'];
        }
        if ($type == self::CHANNEL_POST) {
            return @$this->data['channel_post']['text'];
        }
        if ($type == self::EDITED_MESSAGE) {
            return @$this->data['edited_message']['text'];
        }

        return @$this->data['message']['text'];
    }

    public function Caption()
    {
        $type = $this->getUpdateType();
        if ($type == self::CHANNEL_POST) {
            return @$this->data['channel_post']['caption'];
        }
        return @$this->data['message']['caption'];
    }

    public function Photo()
    {
        if ($this->getUpdateType() != self::PHOTO) {
            return [];
        }
        return @$this->data['message']['photo'];
    }

    /// Get the chat_id of the current message

    /**
     * @return string the String users's chat_id.
     */
    public function ChatID()
    {
        $type = $this->getUpdateType();
        if ($type == self::CALLBACK_QUERY) {
            return @$this->data['callback_query']['message']['chat']['id'];
        }
        if ($type == self::CHANNEL_POST) {
            return @$this->data['channel_post']['chat']['id'];
        }
        if ($type == self::EDITED_MESSAGE) {
            return @$this->data['edited_message']['chat']['id'];
        }
        if ($type == self::INLINE_QUERY) {
            return @$this->data['inline_query']['from']['id'];
        }

        return $this->data['message']['chat']['id'];
    }

    /// Get the message_id of the current message

    /**
     * @return string the String message_id.
     */
    public function MessageID()
    {
        $type = $this->getUpdateType();
        if ($type == self::CALLBACK_QUERY) {
            return @$this->data['callback_query']['message']['message_id'];
        }
        if ($type == self::CHANNEL_POST) {
            return @$this->data['channel_post']['message_id'];
        }
        if ($type == self::EDITED_MESSAGE) {
            return @$this->data['edited_message']['message_id'];
        }

        return $this->data['message']['message_id'];
    }

    /// Get the reply_to_message message_id of the current message

    /**
     * @return string the String reply_to_message message_id.
     */
    public function ReplyToMessageID()
    {
        return $this->data['message']['reply_to_message']['message_id'];
    }

    /// Get the reply_to_message forward_from user_id of the current message

    /**
     * @return string the String reply_to_message forward_from user_id.
     */
    public function ReplyToMessageFromUserID()
    {
        return $this->data['message']['reply_to_message']['forward_from']['id'];
    }

    /// Get the inline_query of the current update

    /**
     * @return the Array inline_query.
     */
    public function Inline_Query()
    {
        return $this->data['inline_query'];
    }

    /// Get the callback_query of the current update

    /**
     * @return string the String callback_query.
     */
    public function Callback_Query()
    {
        return $this->data['callback_query'];
    }

    /// Get the callback_query id of the current update

    /**
     * @return string the String callback_query id.
     */
    public function Callback_ID()
    {
        return $this->data['callback_query']['id'];
    }

    /// Get the Get the data of the current callback

    /**
     * \deprecated Use Text() instead
     * @return string the String callback_data.
     */
    public function Callback_Data()
    {
        return $this->data['callback_query']['data'];
    }

    /// Get the Get the message of the current callback

    /**
     * @return the Message.
     */
    public function Callback_Message()
    {
        return $this->data['callback_query']['message'];
    }

    /// Get the Get the chati_id of the current callback

    /**
     * \deprecated Use ChatId() instead
     * @return string the String callback_query.
     */
    public function Callback_ChatID()
    {
        return $this->data['callback_query']['message']['chat']['id'];
    }

    /// Get the date of the current message

    /**
     * @return string the String message's date.
     */
    public function Date()
    {
        return $this->data['message']['date'];
    }

    /// Get the first name of the user
    public function FirstName()
    {
        $type = $this->getUpdateType();
        if ($type == self::CALLBACK_QUERY) {
            return @$this->data['callback_query']['from']['first_name'];
        }
        if ($type == self::CHANNEL_POST) {
            return @$this->data['channel_post']['from']['first_name'];
        }
        if ($type == self::EDITED_MESSAGE) {
            return @$this->data['edited_message']['from']['first_name'];
        }

        return @$this->data['message']['from']['first_name'];
    }

    /// Get the last name of the user
    public function LastName()
    {
        $type = $this->getUpdateType();
        if ($type == self::CALLBACK_QUERY) {
            return @$this->data['callback_query']['from']['last_name'];
        }
        if ($type == self::CHANNEL_POST) {
            return @$this->data['channel_post']['from']['last_name'];
        }
        if ($type == self::EDITED_MESSAGE) {
            return @$this->data['edited_message']['from']['last_name'];
        }
        if (array_key_exists('last_name', $this->data['message']['from'])) {
            return $this->data['message']['from']['last_name'];
        }

        return '';
    }

    /// Get the username of the user
    public function Username()
    {
        $type = $this->getUpdateType();
        if ($type == self::CALLBACK_QUERY) {
            return @$this->data['callback_query']['from']['username'];
        }
        if ($type == self::CHANNEL_POST) {
            return @$this->data['channel_post']['from']['username'];
        }
        if ($type == self::EDITED_MESSAGE) {
            return @$this->data['edited_message']['from']['username'];
        }

        return @$this->data['message']['from']['username'];
    }

    /// Get the location in the message
    public function Location()
    {
        return $this->data['message']['location'];
    }

    /// Get the update_id of the message
    public function UpdateID()
    {
        return $this->data['update_id'];
    }

    /// Get the number of updates
    public function UpdateCount()
    {
        return count($this->updates['result']);
    }

    /// Get user's id of current message
    public function UserID()
    {
        $type = $this->getUpdateType();
        if ($type == self::CALLBACK_QUERY) {
            return $this->data['callback_query']['from']['id'];
        }
        if ($type == self::CHANNEL_POST) {
            return $this->data['channel_post']['from']['id'];
        }
        if ($type == self::EDITED_MESSAGE) {
            return @$this->data['edited_message']['from']['id'];
        }

        return $this->data['message']['from']['id'];
    }

    /// Get user's id of current forwarded message
    public function FromID()
    {
        return $this->data['message']['forward_from']['id'];
    }

    /// Get chat's id where current message forwarded from
    public function FromChatID()
    {
        return $this->data['message']['forward_from_chat']['id'];
    }

    /// Tell if a message is from a group or user chat

    /**
     *  \return BOOLEAN true if the message is from a Group chat, false otherwise.
     */
    public function messageFromGroup()
    {
        if ($this->data['message']['chat']['type'] == 'private') {
            return false;
        }

        return true;
    }

    /// Get the title of the group chat

    /**
     *  \return a String of the title chat.
     */
    public function messageFromGroupTitle()
    {
        if ($this->data['message']['chat']['type'] != 'private') {
            return $this->data['message']['chat']['title'];
        }
        return '';
    }

    /// Set a custom keyboard

    /** This object represents a custom keyboard with reply options
     * @param $options Array of Array of String; Array of button rows, each represented by an Array of Strings
     * @param $onetime Boolean Requests clients to hide the keyboard as soon as it's been used. Defaults to false.
     * @param $resize Boolean Requests clients to resize the keyboard vertically for optimal fit (e.g., make the
     * keyboard smaller if there are just two rows of buttons). Defaults to false, in which case the custom keyboard
     * is always of the same height as the app's standard keyboard.
     * @param $selective Boolean Use this parameter if you want to show the keyboard to specific users only.
     * Targets:
     *  1) users that are @mentioned in the text of the Message object;
     *  2) if the bot's message is a reply (has reply_to_message_id), sender of the original message.
     * @return the requested keyboard as Json.
     */
    public function buildKeyBoard(array $options, $onetime = false, $resize = false, $selective = true)
    {
        $replyMarkup = [
            'keyboard' => $options,
            'one_time_keyboard' => $onetime,
            'resize_keyboard' => $resize,
            'selective' => $selective,
        ];
        return json_encode($replyMarkup, true);
    }

    /// Set an InlineKeyBoard

    /** This object represents an inline keyboard that appears right next to the message it belongs to.
     * @param $options Array of Array of InlineKeyboardButton; Array of button rows, each represented by an Array of InlineKeyboardButton
     * @return the requested keyboard as Json.
     */
    public function buildInlineKeyBoard(array $options)
    {
        $replyMarkup = [
            'inline_keyboard' => $options,
        ];
        return json_encode($replyMarkup, true);
    }

    /// Create an InlineKeyboardButton

    /** This object represents one button of an inline keyboard. You must use exactly one of the optional fields.
     * @param $text String; Array of button rows, each represented by an Array of Strings
     * @param $url String Optional. HTTP url to be opened when button is pressed
     * @param $callback_data String Optional. Data to be sent in a callback query to the bot when button is pressed
     * @param $switch_inline_query String Optional. If set, pressing the button will prompt the user to select one of
     * their chats, open that chat and insert the bot‘s username and the specified inline query in the input field.
     * Can be empty, in which case just the bot’s username will be inserted.
     * @param $switch_inline_query_current_chat String Optional. Optional. If set, pressing the button will insert the
     * bot‘s username and the specified inline query in the current chat's input field. Can be empty, in which case only the bot’s username will be inserted.
     * @param $callback_game  String Optional. Description of the game that will be launched when the user presses the button.
     * @param string $pay Boolean Optional. Specify True, to send a https://core.telegram.org/bots/api#payments" Pay button.
     * @return String[] requested button as Array.
     */
    public function buildInlineKeyboardButton(
        $text,
        $url = '',
        $callback_data = '',
        $switch_inline_query = null,
        $switch_inline_query_current_chat = null,
        $callback_game = '',
        $pay = ''
    ) {
        $replyMarkup = [
            'text' => $text,
        ];
        if ($url != '') {
            $replyMarkup['url'] = $url;
        } elseif ($callback_data != '') {
            $replyMarkup['callback_data'] = $callback_data;
        } elseif (!is_null($switch_inline_query)) {
            $replyMarkup['switch_inline_query'] = $switch_inline_query;
        } elseif (!is_null($switch_inline_query_current_chat)) {
            $replyMarkup['switch_inline_query_current_chat'] = $switch_inline_query_current_chat;
        } elseif ($callback_game != '') {
            $replyMarkup['callback_game'] = $callback_game;
        } elseif ($pay != '') {
            $replyMarkup['pay'] = $pay;
        }

        return $replyMarkup;
    }

    /// Create a KeyboardButton

    /** This object represents one button of an inline keyboard. You must use exactly one of the optional fields.
     * @param $text String; Array of button rows, each represented by an Array of Strings
     * @param $request_contact Boolean Optional. If True, the user's phone number will be sent as a contact when the button is pressed. Available in private chats only
     * @param $request_location Boolean Optional. If True, the user's current location will be sent when the button is pressed. Available in private chats only
     * @return array the requested button as Array.
     */
    public function buildKeyboardButton($text, $request_contact = false, $request_location = false)
    {
        return [
            'text' => $text,
            'request_contact' => $request_contact,
            'request_location' => $request_location,
        ];
    }

    /// Hide a custom keyboard

    /** Upon receiving a message with this object, Telegram clients will hide the current custom keyboard and display the default letter-keyboard. By default, custom keyboards are displayed until a new keyboard is sent by a bot. An exception is made for one-time keyboards that are hidden immediately after the user presses a button.
     * @param $selective Boolean Use this parameter if you want to show the keyboard to specific users only. Targets: 1) users that are @mentioned in the text of the Message object; 2) if the bot's message is a reply (has reply_to_message_id), sender of the original message.
     * @return string // the requested keyboard hide as Array.
     */
    public function buildKeyBoardHide($selective = true)
    {
        $replyMarkup = [
            'remove_keyboard' => true,
            'selective' => $selective,
        ];
        return json_encode($replyMarkup, true);
    }

    /// Display a reply interface to the user

    /** Upon receiving a message with this object, Telegram clients will display a reply interface to the user (act as if
     * the user has selected the bot‘s message and tapped ’Reply'). This can be extremely useful if you want to create user-friendly step-by-step interfaces without having to sacrifice privacy mode.
     * @param $selective Boolean Use this parameter if you want to show the keyboard to specific users only. Targets:
     * 1) users that are @mentioned in the text of the Message object;
     * 2) if the bot's message is a reply (has reply_to_message_id), sender of the original message.
     * @return string the requested force reply as Array
     */
    public function buildForceReply($selective = true)
    {
        $replyMarkup = [
            'force_reply' => true,
            'selective' => $selective,
        ];
        return json_encode($replyMarkup, true);
    }

    // Payments
    /// Send an invoice

    /**
     * Use this method to send invoices. On success, the sent https://core.telegram.org/bots/api#message" Message is returned.
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function sendInvoice(array $content)
    {
        return $this->endpoint('sendInvoice', $content);
    }

    /// Answer a shipping query

    /**
     * Once the user has confirmed their payment and shipping details, the Bot API sends the final confirmation in
     * the form of an https://core.telegram.org/bots/api#updates" Update with the field pre_checkout_query.
     * Use this method to respond to such pre-checkout queries. On success, True is returned.
     * The Bot API must receive an answer within 10 seconds after the pre-checkout query was sent.
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function answerShippingQuery(array $content)
    {
        return $this->endpoint('answerShippingQuery', $content);
    }

    /// Answer a PreCheckout query

    /**
     * Once the user has confirmed their payment and shipping details, the Bot API sends the final confirmation in the
     * form of an https://core.telegram.org/bots/api#" Update with the field pre_checkout_query. Use this method to
     * respond to such pre-checkout queries. On success, True is returned. The Bot API must receive an answer
     * within 10 seconds after the pre-checkout query was sent.
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function answerPreCheckoutQuery(array $content)
    {
        return $this->endpoint('answerPreCheckoutQuery', $content);
    }

    /// Send a video note

    /**
     * As of https://telegram.org/blog/video-messages-and-telescope" v.4.0, Telegram clients support rounded square
     * mp4 videos of up to 1 minute long. Use this method to send video messages.
     * On success, the sent https://core.telegram.org/bots/api#message" Message is returned.
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function sendVideoNote(array $content)
    {
        return $this->endpoint('sendVideoNote', $content);
    }

    /// Restrict Chat Member

    /**
     * Use this method to restrict a user in a supergroup. The bot must be an administrator in the supergroup for this
     * to work and must have the appropriate admin rights. Pass True for all boolean parameters to lift restrictions from a user. Returns True on success.
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function restrictChatMember(array $content)
    {
        return $this->endpoint('restrictChatMember', $content);
    }

    /// Promote Chat Member

    /**
     * Use this method to promote or demote a user in a supergroup or a channel. The bot must be an administrator
     * in the chat for this to work and must have the appropriate admin rights. Pass False for all boolean parameters
     * to demote a user. Returns True on success
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function promoteChatMember(array $content)
    {
        return $this->endpoint('promoteChatMember', $content);
    }

    //// Export Chat Invite Link

    /**
     * Use this method to export an invite link to a supergroup or a channel. The bot must be an administrator in the
     * chat for this to work and must have the appropriate admin rights. Returns exported invite link as String on success.
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function exportChatInviteLink(array $content)
    {
        return $this->endpoint('exportChatInviteLink', $content);
    }

    /// Set Chat Photo

    /**
     * Use this method to set a new profile photo for the chat. Photos can't be changed for private chats. The bot must
     * be an administrator in the chat for this to work and must have the appropriate admin rights. Returns True on success.
     * Note: In regular groups (non-supergroups), this method will only work if the ‘All Members Are Admins’ setting is off in the target group.
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function setChatPhoto(array $content)
    {
        return $this->endpoint('setChatPhoto', $content);
    }

    /// Delete Chat Photo

    /**
     * Use this method to delete a chat photo. Photos can't be changed for private chats. The bot must be an administrator
     * in the chat for this to work and must have the appropriate admin rights. Returns True on success.
     * Note: In regular groups (non-supergroups), this method will only work if the ‘All Members Are Admins’ setting is off in the target group.
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function deleteChatPhoto(array $content)
    {
        return $this->endpoint('deleteChatPhoto', $content);
    }

    /// Set Chat Title

    /**
     * Use this method to change the title of a chat. Titles can't be changed for private chats. The bot must be an
     * administrator in the chat for this to work and must have the appropriate admin rights. Returns True on success.
     * Note: In regular groups (non-supergroups), this method will only work if the ‘All Members Are Admins’ setting is off in the target group.
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function setChatTitle(array $content)
    {
        return $this->endpoint('setChatTitle', $content);
    }

    /// Set Chat Description

    /**
     * Use this method to change the description of a supergroup or a channel. The bot must be an administrator in the
     * chat for this to work and must have the appropriate admin rights. Returns True on success.
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function setChatDescription(array $content)
    {
        return $this->endpoint('setChatDescription', $content);
    }

    /// Pin Chat Message

    /**
     * Use this method to pin a message in a supergroup. The bot must be an administrator in the chat for this
     * to work and must have the appropriate admin rights. Returns True on success.
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function pinChatMessage(array $content)
    {
        return $this->endpoint('pinChatMessage', $content);
    }

    /// Unpin Chat Message

    /**
     * Use this method to unpin a message in a supergroup chat. The bot must be an administrator in the chat for
     * this to work and must have the appropriate admin rights. Returns True on success.
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function unpinChatMessage(array $content)
    {
        return $this->endpoint('unpinChatMessage', $content);
    }

    /// Get Sticker Set

    /**
     * Use this method to get a sticker set. On success, a StickerSet object is returned.
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function getStickerSet(array $content)
    {
        return $this->endpoint('getStickerSet', $content);
    }

    /// Upload Sticker File

    /**
     * Use this method to upload a .png file with a sticker for later use in createNewStickerSet and addStickerToSet
     * methods (can be used multiple times). Returns the uploaded File on success.
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function uploadStickerFile(array $content)
    {
        return $this->endpoint('uploadStickerFile', $content);
    }

    /// Create New Sticker Set

    /**
     * Use this method to create new sticker set owned by a user. The bot will be able to edit the created sticker set. Returns True on success.
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function createNewStickerSet(array $content)
    {
        return $this->endpoint('createNewStickerSet', $content);
    }

    /// Add Sticker To Set

    /**
     * Use this method to add a new sticker to a set created by the bot. Returns True on success.
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function addStickerToSet(array $content)
    {
        return $this->endpoint('addStickerToSet', $content);
    }

    /// Set Sticker Position In Set

    /**
     * Use this method to move a sticker in a set created by the bot to a specific position . Returns True on success.
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function setStickerPositionInSet(array $content)
    {
        return $this->endpoint('setStickerPositionInSet', $content);
    }

    /// Delete Sticker From Set

    /**
     * Use this method to delete a sticker from a set created by the bot. Returns True on success.
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function deleteStickerFromSet(array $content)
    {
        return $this->endpoint('deleteStickerFromSet', $content);
    }

    /// Delete a message

    /**
     * Use this method to delete a message. A message can only be deleted if it was sent less than 48 hours ago.
     * Any such recently sent outgoing message may be deleted. Additionally, if the bot is an administrator in a group chat,
     * it can delete any message. If the bot is an administrator in a supergroup, it can delete messages from any other user
     * and service messages about people joining or leaving the group (other types of service messages may only be removed
     * by the group creator). In channels, bots can only remove their own messages. Returns True on success.
     * @param $content // the request parameters as array
     * @return string // the JSON Telegram's reply.
     */
    public function deleteMessage(array $content)
    {
        return $this->endpoint('deleteMessage', $content);
    }

    /// Receive incoming messages using polling

    /** Use this method to receive incoming updates using long polling.
     * @param $offset Integer Identifier of the first update to be returned. Must be greater by one than the highest among
     * the identifiers of previously received updates. By default, updates starting with the earliest unconfirmed update
     * are returned. An update is considered confirmed as soon as getUpdates is called with an offset higher than its update_id.
     * @param $limit Integer Limits the number of updates to be retrieved. Values between 1—100 are accepted. Defaults to 100
     * @param $timeout Integer Timeout in seconds for long polling. Defaults to 0, i.e. usual short polling
     * @param $update Boolean If true updates the pending message list to the last update received. Default to true.
     * @return string the updates as Array.
     */
    public function getUpdates($offset = 0, $limit = 100, $timeout = 0, $update = true)
    {
        $content = ['offset' => $offset, 'limit' => $limit, 'timeout' => $timeout];
        $this->updates = $this->endpoint('getUpdates', $content);
        if ($update && array_key_exists('result', $this->updates)
            && is_array($this->updates['result'])
            && count($this->updates['result']) >= 1) { //for CLI working.

            $last_element_id = $this->updates['result'][count($this->updates['result']) - 1]['update_id'] + 1;
            $content = ['offset' => $last_element_id, 'limit' => '1', 'timeout' => $timeout];
            $this->endpoint('getUpdates', $content);
        }

        return $this->updates;
    }

    /// Serve an update

    /** Use this method to use the built-in function like Text() or Username() on a specific update.
     * @param $update Integer The index of the update in the updates array.
     */
    public function serveUpdate($update)
    {
        $this->data = $this->updates['result'][$update];
    }

    /// Return current update type

    /**
     * Return current update type `False` on failure.
     *
     * @return bool|string
     */
    public function getUpdateType()
    {
        $update = $this->data;
        if (isset($update['inline_query'])) {
            return self::INLINE_QUERY;
        }
        if (isset($update['callback_query'])) {
            return self::CALLBACK_QUERY;
        }
        if (isset($update['edited_message'])) {
            return self::EDITED_MESSAGE;
        }
        if (isset($update['message']['text'])) {
            return self::MESSAGE;
        }
        if (isset($update['message']['photo'])) {
            return self::PHOTO;
        }
        if (isset($update['message']['video'])) {
            return self::VIDEO;
        }
        if (isset($update['message']['audio'])) {
            return self::AUDIO;
        }
        if (isset($update['message']['voice'])) {
            return self::VOICE;
        }
        if (isset($update['message']['contact'])) {
            return self::CONTACT;
        }
        if (isset($update['message']['location'])) {
            return self::LOCATION;
        }
        if (isset($update['message']['reply_to_message'])) {
            return self::REPLY;
        }
        if (isset($update['message']['animation'])) {
            return self::ANIMATION;
        }
        if (isset($update['message']['sticker'])) {
            return self::STICKER;
        }
        if (isset($update['message']['document'])) {
            return self::DOCUMENT;
        }
        if (isset($update['channel_post'])) {
            return self::CHANNEL_POST;
        }

        return false;
    }

    private function sendAPIRequest($url, array $content, $post = true)
    {
        if (isset($content['chat_id'])) {
            $url = $url . '?chat_id=' . $content['chat_id'];
            unset($content['chat_id']);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($post) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
        }
        // 		echo "inside curl if";
        if (!empty($this->proxy)) {
            // 			echo "inside proxy if";
            if (array_key_exists('type', $this->proxy)) {
                curl_setopt($ch, CURLOPT_PROXYTYPE, $this->proxy['type']);
            }

            if (array_key_exists('auth', $this->proxy)) {
                curl_setopt($ch, CURLOPT_PROXYUSERPWD, $this->proxy['auth']);
            }

            if (array_key_exists('url', $this->proxy)) {
                // 				echo "Proxy Url";
                curl_setopt($ch, CURLOPT_PROXY, $this->proxy['url']);
            }

            if (array_key_exists('port', $this->proxy)) {
                // 				echo "Proxy port";
                curl_setopt($ch, CURLOPT_PROXYPORT, $this->proxy['port']);
            }
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        if ($result === false) {
            $result = json_encode(
                ['ok' => false, 'curl_error_code' => curl_errno($ch), 'curl_error' => curl_error($ch)]
            );
        }
        curl_close($ch);
        if ($this->log_errors) {
            if (class_exists('TelegramErrorLogger')) {
                $loggerArray = ($this->getData() == null) ? [$content] : [$this->getData(), $content];
                TelegramErrorLogger::log(json_decode($result, true), $loggerArray);
            }
        }

        return $result;
    }
}

// Helper for Uploading file using CURL
if (!function_exists('curl_file_create')) {
    function curl_file_create($filename, $mimetype = '', $postname = '')
    {
        return "@$filename;filename="
            . ($postname ?: basename($filename))
            . ($mimetype ? ";type=$mimetype" : '');
    }
}

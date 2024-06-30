<?php
$bot_token = '7351220568:AAFNuN_wnh-sWRjppR0I26zdykJ98vFTQo8';
$chat_id = '-1002233707621';
$member_limit = 1;

// Function to send a request to the Telegram API
function sendTelegramRequest($method, $data) {
    global $bot_token;
    $url = "https://api.telegram.org/bot$bot_token/$method";
    $options = [
        'http' => [
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($data),
        ],
    ];
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    return json_decode($result, true);
}

// Get the incoming update
$update = json_decode(file_get_contents('php://input'), true);

// Check if the update contains a message and the message text is '/gen'
if (isset($update['message'])) {
    $message = $update['message'];
    $text = $message['text'];
    $chat_id = $message['chat']['id'];

    if ($text === '/gen') {
        // Command 1: Create chat invite link
        $createChatInviteLinkResponse = sendTelegramRequest('createChatInviteLink', [
            'chat_id' => $chat_id,
            'member_limit' => $member_limit,
        ]);

        // Command 2: Send the invite link
        if ($createChatInviteLinkResponse && isset($createChatInviteLinkResponse['result']['invite_link'])) {
            $invite_link = $createChatInviteLinkResponse['result']['invite_link'];
            $message = "*🌟Private Link: $invite_link\n\n⚠️Note: above link is valid for only 1 member.*";
            sendTelegramRequest('sendMessage', [
                'chat_id' => $chat_id,
                'text' => $message,
                'parse_mode' => 'Markdown',
            ]);
        } else {
            // Handle error
            sendTelegramRequest('sendMessage', [
                'chat_id' => $chat_id,
                'text' => 'Failed to create an invite link.',
                'parse_mode' => 'Markdown',
            ]);
        }
    }
}
?>
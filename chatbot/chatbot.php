<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $message = isset($_POST['message']) ? $_POST['message'] : '';
    $response = '';

    switch (strtolower(trim($message))) {
        case 'hello':
            $response = 'Hi there! How can I assist you today?';
            break;
        case 'how are you?':
            $response = 'I\'m just a bot, but I\'m here to help you!';
            break;
        case 'what is your name?':
            $response = 'I\'m your friendly chatbot!';
            break;
        default:
            $response = 'Sorry, I don\'t understand that. Can you please rephrase?';
            break;
    }

    echo $response;
} else {
    echo 'Invalid request method.';
}
?>

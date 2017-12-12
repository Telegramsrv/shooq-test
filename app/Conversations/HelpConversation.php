<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\Drivers\Facebook\Extensions\ButtonTemplate;
use BotMan\Drivers\Facebook\Extensions\ElementButton;

class HelpConversation extends Conversation {

    public function askReason() {
        $this->ask($this->_createMessageHumanTemplate(), function(Answer $answer) {
            $response = $answer->getValue();
            $this->checkResponse($response);
        });
    }

    public function _createMessageHumanTemplate() {
        return ButtonTemplate::create('Hey I see you need some help. Shooq Humans are not available at the moment, but please choose an option below and we will get back to you as soon as possible.')
                        ->addButton(ElementButton::create('Send Chat Message')->type('postback')->payload('SEND_CHAT_MESSAGE'))
                        ->addButton(ElementButton::create('Leave Voicemail')->url('https://app.shooq.co/ajax_login'))
                        ->addButton(ElementButton::create('Web Contact Form')->url('https://www.shooq.co/contact/'));
    }

    public function _SendMessageTemplate($msg) {
        return ButtonTemplate::create('Here is your message: ' . $msg)
                        ->addButton(ElementButton::create('Send Now')->type('postback')->payload('SEND_NOW'))
                        ->addButton(ElementButton::create('Dont Send')->type('postback')->payload('DONT_SEND'));
    }

    public function checkResponse($response) {
        $botman = app('botman');
        switch ($response) {
            case 'SEND_CHAT_MESSAGE':
                $this->ask('What is Your Message?', function(Answer $answer) {
                    $msg = $answer->getText();
                    $this->ask($this->_SendMessageTemplate($msg), function(Answer $answer) {
                        $response = $answer->getValue();
                        $this->checkResponse($response);
                    });
                });
                break;

            case 'SEND_NOW':
                $this->say('Thanks your message has been sent!');
                break;

            case 'DONT_SEND':
                $this->say('Okay, I wont send your message');
                break;

            default:
                $botman->startConversation(new FallbackConversation());
        }
    }

    /**
     * Start the conversation
     */
    public function run() {
        $this->askReason();
    }

}

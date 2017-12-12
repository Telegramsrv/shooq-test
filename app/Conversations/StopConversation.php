<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\Drivers\Facebook\Extensions\ButtonTemplate;
use BotMan\Drivers\Facebook\Extensions\ElementButton;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;

class StopConversation extends Conversation {

    public function askReason() {
        return $this->ask($this->_createStopQuestions(), function (Answer $answer) {
                    if ($answer->isInteractiveMessageReply()) {
                        $response = $answer->getValue();
                        $this->checkResponse($response);
                    }
                });
    }

    public function _createStopTemplate() {
        return ButtonTemplate::create('If you would like to begin using Shooq again, please message the word ')
                        ->addButton(ElementButton::create('BEGIN')->payload('BEGIN')->type('postback'));
    }

    public function _createStopQuestions() {
        return $question = Question::create("Are you sure you want to stop receiving messages from Shooq℠ and ARTi℠ ? We
would be sad to see you go! 😢")
                ->fallback('Unable to ask question')
                ->callbackId('ask_reason')
                ->addButtons([
            Button::create('Stop Messages')->value('STOP_MESSAGES'),
            Button::create('I Want to stay')->value('WANT_TO_STAY'),
        ]);
    }

    public function checkResponse($response) {
        $botman = app('botman');
        switch ($response) {
            case 'STOP_MESSAGES':
                $this->say('So sorry to see you go! You will no longer receive messages from us.');
                $this->ask($this->_createStopTemplate(), function(Answer $answer) {
                    $response = $answer->getValue();
                    $this->checkResponse($response);
                });
                break;

            case 'WANT_TO_STAY':
                $this->say('Awesome 😀 

We are super excited to see you stay!');

                break;

            case 'BEGIN':
                $this->say('We are super excited that you came back to us!');

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

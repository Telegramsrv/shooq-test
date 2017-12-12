<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;

class FallbackConversation extends Conversation {

    public function askReason() {
        $this->ask('Is there anything else you would like me to do?', function(Answer $answer) {
            $response = $answer->getText();
            $this->checkResponse($response);
        });
    }

    public function checkResponse($response) {
        $botman = app('botman');
        switch ($response) {
            case 'Hi':
                $botman->startConversation(new helloConversation());
                break;

            case 'Hey':
                $botman->startConversation(new helloConversation());
                break;

            case 'HELLO':
                $botman->startConversation(new helloConversation());
                break;

            case 'Buy or Rent':
                $botman->startConversation(new BuyConversation());
                break;

            case 'Buy':
                $botman->startConversation(new BuyConversation());
                break;

            case 'Rent':
                $botman->startConversation(new BuyConversation());
                break;

            case 'Commands':
                $botman->startConversation(new OnboardConversation());
                break;

            case 'What can you do?':
                $botman->startConversation(new OnboardConversation());
                break;

            case 'Sell':
                $botman->startConversation(new SellConversation());
                break;

            case 'Sell books':
                $botman->startConversation(new SellConversation());
                break;

            case 'feedback':
                $botman->startConversation(new FeedbackConversation());
                break;

            case 'help':
                $botman->startConversation(new HelpConversation());
                break;

            default:
                $this->say('Sorry I did not understand these commands.');
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

<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\Drivers\Facebook\Extensions\ElementButton;
use BotMan\Drivers\Facebook\Extensions\ButtonTemplate;

class helloConversation extends Conversation {

    public function askReason() {
        $this->ask($this->_GreetingsTemplate(), function(Answer $answer) {
            $response = $answer->getValue();
            $this->checkResponse($response);
        });
        return $this->ask($this->_GreetingsQuestions(), function (Answer $answer) {
                    if ($answer->isInteractiveMessageReply()) {
                        $response = $answer->getValue();
                        $this->checkResponse($response);
                    }
                });
    }

    public function _GreetingsQuestions() {
        return $question = Question::create("You can also type 'Commands' to learn what you can do or message 'Help' to get a Shooq Human on the chat.")
                ->fallback('Unable to ask question')
                ->callbackId('ask_reason')
                ->addButtons([
            Button::create('Commands')->value('COMMANDS'),
            Button::create('Help')->value('HELP'),
        ]);
    }

    public function _GreetingsTemplate() {
        return ButtonTemplate::create('Hey there! I can help you buy, sell, and rent textbooks; I am super skilled with it. Try
pressing one of the options below: ')
                        ->addButton(ElementButton::create('Buy 📖')->type('postback')->payload('BUY_OR_RENT'))
                        ->addButton(ElementButton::create('Rent 🔄')->type('postback')->payload('BUY_OR_RENT'))
                        ->addButton(ElementButton::create('Sell 💵')->type('postback')->payload('SELL_BOOKS'));
    }

    public function checkResponse($response) {
        $botman = app('botman');
        switch ($response) {

            case 'BUY_OR_RENT':
                $botman->startConversation(new BuyConversation());
                break;

            case 'SELL_BOOKS':
                $botman->startConversation(new SellConversation());
                break;

            case 'COMMANDS':
                $botman->startConversation(new OnboardConversation());
                break;

            case 'HELP':
                $botman->startConversation(new HelpConversation());

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

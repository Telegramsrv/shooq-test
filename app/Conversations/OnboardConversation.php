<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\Drivers\Facebook\Extensions\ButtonTemplate;
use BotMan\Drivers\Facebook\Extensions\Element;
use BotMan\Drivers\Facebook\Extensions\ElementButton;
use BotMan\Drivers\Facebook\Extensions\GenericTemplate;
use BotMan\Drivers\Facebook\Extensions\ListTemplate;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;

class OnboardConversation extends Conversation {

    /**
     * First question
     */
    public function stopsConversation(IncomingMessage $message) {
        if ($message->getText() == 'CANCEL_SEARCH') {
            return true;
        }
        return false;
    }

    public function askReason() {
        $this->ask($this->_createFeaturesTemplate(), function(Answer $answer) {
            $response = $answer->getValue();
            $this->checkResponse($response);
        });
        return $this->ask($this->_createFeaturesQuestions(), function (Answer $answer) {
                    if ($answer->isInteractiveMessageReply()) {
                        $response = $answer->getValue();
                        $this->checkResponse($response);
                    }
                });
    }

    public function _createFeaturesTemplate() {
        return GenericTemplate::create()
                        ->addImageAspectRatio(GenericTemplate::RATIO_SQUARE)
                        ->addElements([
                            Element::create('Buy Or Rent')
                            ->subtitle('Find the best prices on new and used books')
                            ->image('https://external.fixc1-1.fna.fbcdn.net/safe_image.php?d=AQBaQ4SiDM3sZaiv&url=https%3A%2F%2Fwww.shooq.co%2Fwp-content%2Fuploads%2F2017%2F01%2Fbook.jpg&_nc_hash=AQDzVbFZC6yFx87Y')
                            ->addButton(ElementButton::create('Buy or Rent')
                                    ->payload('BUY_OR_RENT')->type('postback')),
                            Element::create('Sell')
                            ->subtitle('Sell Your used books')
                            ->image('https://external.fixc1-1.fna.fbcdn.net/safe_image.php?d=AQCdxgpeRiNVKaEg&url=https%3A%2F%2Fwww.shooq.co%2Fwp-content%2Fuploads%2F2017%2F01%2Fbooks.jpg&_nc_hash=AQC7KNz1TI17wNsj')
                            ->addButton(ElementButton::create('Sell')
                                    ->payload('SELL_BOOKS')->type('postback')),
                            Element::create('Orders')
                            ->subtitle('Check your Order details')
                            ->image('https://external-ams3-1.xx.fbcdn.net/safe_image.php?d=AQBIzB3wFCy4TpJG&url=https%3A%2F%2Fwww.shooq.co%2Fwp-content%2Fuploads%2F2016%2F10%2Fdocument.png&_nc_hash=AQDOMoBvxnF0cDt4"')
                            ->addButton(ElementButton::create('Orders')
                                    ->payload('ORDERS')->type('postback'))
        ]);
    }

    public function _createFeaturesQuestions() {
        return $question = Question::create("Or quickly select these :")
                ->fallback('Unable to ask question')
                ->callbackId('ask_reason')
                ->addButtons([
            Button::create('Buy or Rent 📚')->value('BUY_OR_RENT'),
            Button::create('Sell 📚')->value('SELL_BOOKS'),
            Button::create('Orders 📦')->value('ORDERS'),
            Button::create('Help 🆘')->value('HELP'),
            Button::create('Feedback 💡')->value('FEEDBACK'),
            Button::create('Login 🔓')->value('LOGIN'),
            Button::create('Wishlist 🌠')->value('ORDERS'),
            Button::create('Return 🔄')->value('RETURN'),
            Button::create('FAQs ❓')->value('FAQ'),
            Button::create('Stop ✋')->value('STOP'),
        ]);
    }

    public function _createLoginTemplate() {
        return ListTemplate::create()
                        ->useCompactView()
                        ->addElement(
                                Element::create('⬇️')
                                ->image('https://external-frt3-2.xx.fbcdn.net/safe_image.php?d=AQCZLzLF4MFaARZ0&url=https%3A%2F%2Fwww.shooq.co%2Fwp-content%2Fuploads%2F2015%2F08%2F6ccacd-shooq-brand-color-gradient.jpg&_nc_hash=AQCG6C-Ei5pGXi9z')
                                ->addButton(ElementButton::create('Press Here To Log In..')->url('https://app.shooq.co/ajax_login')))
                        ->addElement(
                                Element::create('Log In To Shooq')
                                ->subtitle('Manage Your Orders & Account')
                                ->image('https://external-frt3-2.xx.fbcdn.net/safe_image.php?d=AQCZLzLF4MFaARZ0&url=https%3A%2F%2Fwww.shooq.co%2Fwp-content%2Fuploads%2F2015%2F08%2F6ccacd-shooq-brand-color-gradient.jpg&_nc_hash=AQCG6C-Ei5pGXi9z')
                                ->addButton(ElementButton::create('Press Here To Log In..')
                                        ->url('https://app.shooq.co/ajax_login'))
        );
    }

    public function _createReturnTemplate() {
        return ButtonTemplate::create('Please select from the options below:')
                        ->addButton(ElementButton::create('Return Rental 🔄')->url('https://app.shooq.co/ajax_login'))
                        ->addButton(ElementButton::create('Return for Refund 💰')->url('https://app.shooq.co/ajax_login'));
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
            case 'ORDERS':
                $this->ask('Which phone number did you use to signup?', function(Answer $answer) {
                    
                });
                break;
            case 'FEEDBACK':
                $botman->startConversation(new FeedbackConversation());
                break;
            case 'LOGIN':
                $this->ask($this->_createLoginTemplate(), function(Answer $answer) {
                    $this->say('Thanks for your super awesome feedback!');
                });
                break;
            case 'RETURN':
                $this->ask($this->_createReturnTemplate(), function(Answer $answer) {
                    $this->say('Thanks for your super awesome feedback!');
                });
                break;
            case 'STOP':
                $botman->startConversation(new StopConversation());
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

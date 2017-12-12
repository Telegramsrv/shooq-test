<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\Drivers\Facebook\Extensions\Element;
use BotMan\Drivers\Facebook\Extensions\ElementButton;
use BotMan\Drivers\Facebook\Extensions\GenericTemplate;

class shooqConversation extends Conversation {

    public function askReason() {
        return $this->ask($this->_createTemplate(), function (Answer $answer) {
                    $response = $answer->getValue();
                    $this->checkResponse($response);
                });
    }

    public function _createTemplate() {
        return GenericTemplate::create()
                        ->addImageAspectRatio(GenericTemplate::RATIO_SQUARE)
                        ->addElements([
                            Element::create('Hi There, I am Arti!')
                            ->subtitle('Your textbook shopping Assistant')
                            ->image('https://external.fixc1-1.fna.fbcdn.net/safe_image.php?d=AQBG5PiB_6JBUx4Z&url=https%3A%2F%2Fwww.shooq.co%2Fwp-content%2Fuploads%2F2016%2F10%2FWave.png&_nc_hash=AQAlkhu3zz1076bg')
                            ->addButton(ElementButton::create('What can I do? 🗣️')
                                    ->payload('WHAT_I_CAN_DO')->type('postback'))
                            ->addButton(ElementButton::create('Buy or Rent Books 📚')
                                    ->payload('BUY_OR_RENT')->type('postback'))
                            ->addButton(ElementButton::create('Sell Books 📘')
                                    ->payload('SELL_BOOKS')->type('postback')),
                            Element::create('Help Options')
                            ->subtitle('Here afre some options if you need help...')
                            ->image('https://external.fixc1-1.fna.fbcdn.net/safe_image.php?d=AQDt9xx5qj9NgIeY&url=https%3A%2F%2Fwww.shooq.co%2Fwp-content%2Fuploads%2F2017%2F01%2FSOS.jpg&_nc_hash=AQBw-LEM8LtHd6EQ')
                            ->addButton(ElementButton::create('FAQs ❓')
                                    ->payload('FAQ')->type('postback'))
                            ->addButton(ElementButton::create('Message a Human 💬')
                                    ->payload('HELP')->type('postback'))
                            ->addButton(ElementButton::create('Call a Human 📞')->url('https://www.google.com')),
                            Element::create('View the Legal Mumbo Jumbo below')
                            ->subtitle('By messaging Arti℠ or Shooq℠, you agree to our Terms of Use, Privacy Policy, and...')
                            ->image('https://external.fixc1-1.fna.fbcdn.net/safe_image.php?d=AQBIzB3wFCy4TpJG&url=https%3A%2F%2Fwww.shooq.co%2Fwp-content%2Fuploads%2F2016%2F10%2Fdocument.png&_nc_hash=AQDOMoBvxnF0cDt4')
                            ->addButton(ElementButton::create('Terms of Use ⚠️')->url('https://www.shooq.co/terms-of-use/'))
                            ->addButton(ElementButton::create('Privacy Policy 👁‍🗨')->url('https://www.shooq.co/privacy-policy/'))
                            ->addButton(ElementButton::create('Beta Test NDA 😶')->url('https://www.shooq.co/beta-nda/'))
        ]);
    }

    public function checkResponse($response) {
        $botman = app('botman');
        switch ($response) {
            case 'WHAT_I_CAN_DO':
                $botman->startConversation(new OnboardConversation());
                break;

            case 'BUY_OR_RENT':
                $botman->startConversation(new BuyConversation());
                break;

            case 'SELL_BOOKS':
                $botman->startConversation(new SellConversation());
                break;

            case 'FAQ':
                $this->ask('What question do you have?', function(Answer $answer) {
                    
                });
                break;

            case 'HELP':
                $botman->startConversation(new HelpConversation());
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

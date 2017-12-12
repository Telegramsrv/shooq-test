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
use BotMan\BotMan\Messages\Incoming\IncomingMessage;

class BuyConversation extends Conversation {

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
        $this->say('Good choice. I have some buying and renting super powers!

Please message me with one of the following:

• Book Title 📗
• ISBN number 🔢
• Picture of book barcode 📷');
        $this->ask($this->_createCancelTemplate(), function(Answer $answer) {
            if ($answer->getValue()) {
                $response = $answer->getValue();
                $this->checkResponse($response);
            } else {
                $this->say('One sec.. I am looking up that book title. 📘🔍');

                $response = $answer->getText();
                $this->ask($this->_createBooksTemplate($response), function(Answer $answer) {
                    $response = $answer->getValue();
                    $this->checkResponse($response);
                });
                return $this->ask($this->_createBookQuestions(), function (Answer $answer) {
                            if ($answer->isInteractiveMessageReply()) {
                                $response = $answer->getValue();
                                $this->checkResponse($response);
                            }
                        });
            }
        });
    }

    public function _createBookQuestions() {
        return $question = Question::create("Or select these options: ")
                ->fallback('Unable to ask question')
                ->callbackId('ask_reason')
                ->addButtons([
            Button::create('Cancel Search ✖️️')->value('CANCEL_SEARCH'),
            Button::create('New Search 🔄')->value('NEW_SEARCH'),
        ]);
    }

    public function _createCancelTemplate() {
        return ButtonTemplate::create('You can cancel your search below:')
                        ->addButton(ElementButton::create('Cancel Search')->type('postback')->payload('CANCEL_SEARCH'));
    }

    public function _createBooksElement($book) {
        return Element::create($book)
                        ->subtitle('Find the best prices on new and used books')
                        ->image('https://external.fixc1-1.fna.fbcdn.net/safe_image.php?d=AQBaQ4SiDM3sZaiv&url=https%3A%2F%2Fwww.shooq.co%2Fwp-content%2Fuploads%2F2017%2F01%2Fbook.jpg&_nc_hash=AQDzVbFZC6yFx87Y')
                        ->addButton(ElementButton::create('Yes, This Book')
                                ->payload('YES_THIS_BOOK')->type('postback'));
    }

    public function _createBooksTemplate($response) {
        $books = ['Harry Potter', 'As You Like It', 'Julius Caesar', 'Hamlet'];
        $isbn = ['23423423423', '3242322332432', '3242455646677', '46542424346535'];
        $elements = [];
        foreach ($books as $key => $book) {
            if (!empty(stristr($book, $response))) {
                array_push($elements, $this->_createBooksElement($book));
            }
        }
        if (count($elements) > 1) {
            $this->say("I found more than one book!");
        } else if (count($elements) == 1) {
            $this->say("I found your book!");
        } else {
            $this->say('Sorry No matching Book Found');
        }
        return GenericTemplate::create()
                        ->addImageAspectRatio(GenericTemplate::RATIO_SQUARE)
                        ->addElements(
                                $elements
        );
    }

    public function _createConditionTemplate() {
        return GenericTemplate::create()
                        ->addImageAspectRatio(GenericTemplate::RATIO_SQUARE)
                        ->addElements([
                            Element::create('$54 | Rent(Semester) | Condition: New')
                            ->subtitle('Fitzpatricks 8th Ed, St Albans')
                            ->image('https://external.fixc1-1.fna.fbcdn.net/safe_image.php?d=AQBaQ4SiDM3sZaiv&url=https%3A%2F%2Fwww.shooq.co%2Fwp-content%2Fuploads%2F2017%2F01%2Fbook.jpg&_nc_hash=AQDzVbFZC6yFx87Y')
                            ->addButton(ElementButton::create('I Want This')
                                    ->payload('I_WANT_THIS')->type('postback')),
                            Element::create('$51 | Rent(Semester) | Condition: Used-Fair')
                            ->subtitle('Fitzpatricks 8th Ed, St Albans')
                            ->image('https://external.fixc1-1.fna.fbcdn.net/safe_image.php?d=AQBaQ4SiDM3sZaiv&url=https%3A%2F%2Fwww.shooq.co%2Fwp-content%2Fuploads%2F2017%2F01%2Fbook.jpg&_nc_hash=AQDzVbFZC6yFx87Y')
                            ->addButton(ElementButton::create('I Want This')
                                    ->payload('I_WANT_THIS')->type('postback')),
                            Element::create('$56 | Rent(Quarter) | Condition: Used-Fair')
                            ->subtitle('Fitzpatricks 8th Ed, St Albans')
                            ->image('https://external.fixc1-1.fna.fbcdn.net/safe_image.php?d=AQBaQ4SiDM3sZaiv&url=https%3A%2F%2Fwww.shooq.co%2Fwp-content%2Fuploads%2F2017%2F01%2Fbook.jpg&_nc_hash=AQDzVbFZC6yFx87Y')
                            ->addButton(ElementButton::create('I Want This')
                                    ->payload('I_WANT_THIS')->type('postback')),
                            Element::create('$58 | Rent(60-Days Short Term) | Condition: Used-Fair')
                            ->subtitle('Fitzpatricks 8th Ed, St Albans')
                            ->image('https://external.fixc1-1.fna.fbcdn.net/safe_image.php?d=AQBaQ4SiDM3sZaiv&url=https%3A%2F%2Fwww.shooq.co%2Fwp-content%2Fuploads%2F2017%2F01%2Fbook.jpg&_nc_hash=AQDzVbFZC6yFx87Y')
                            ->addButton(ElementButton::create('I Want This')
                                    ->payload('I_WANT_THIS')->type('postback')),
                            Element::create('$45 | Buy | Condition: New')
                            ->subtitle('Fitzpatricks 8th Ed, St Albans')
                            ->image('https://external.fixc1-1.fna.fbcdn.net/safe_image.php?d=AQBaQ4SiDM3sZaiv&url=https%3A%2F%2Fwww.shooq.co%2Fwp-content%2Fuploads%2F2017%2F01%2Fbook.jpg&_nc_hash=AQDzVbFZC6yFx87Y')
                            ->addButton(ElementButton::create('I Want This')
                                    ->payload('I_WANT_THIS')->type('postback')),
                            Element::create('$77 | Buy | Condition: Used-Fair')
                            ->subtitle('Fitzpatricks 8th Ed, St Albans')
                            ->image('https://external.fixc1-1.fna.fbcdn.net/safe_image.php?d=AQBaQ4SiDM3sZaiv&url=https%3A%2F%2Fwww.shooq.co%2Fwp-content%2Fuploads%2F2017%2F01%2Fbook.jpg&_nc_hash=AQDzVbFZC6yFx87Y')
                            ->addButton(ElementButton::create('I Want This')
                                    ->payload('I_WANT_THIS')->type('postback')),
        ]);
    }

    public function _createCheckoutTemplate() {
        return ButtonTemplate::create('Would you like to add another book or checkout? ')
                        ->addButton(ElementButton::create('Add Book 📘')->payload('NEW_SEARCH')->type('postback'))
                        ->addButton(ElementButton::create('Checkout 🛒')->url('https://app.shooq.co/ajax_register/48d6f1fc-c878-47b6-a323-b252e39d8116/279'));
    }

    public function checkResponse($response) {
        $botman = app('botman');
        switch ($response) {
            case 'YES_THIS_BOOK':
                $this->ask($this->_createConditionTemplate(), function(Answer $answer) {
                    $response = $answer->getValue();
                    $this->checkResponse($response);
                });
                return $this->ask($this->_createBookQuestions(), function (Answer $answer) {
                            if ($answer->isInteractiveMessageReply()) {
                                $response = $answer->getValue();
                                $this->checkResponse($response);
                            }
                        });
                break;

            case 'NEW_SEARCH':
                $this->say('Please message me with one of the following:

• Book Title 📗
• ISBN number 🔢
• Picture of book barcode 📷');
                $this->ask($this->_createCancelTemplate(), function(Answer $answer) {
                    $this->say('One sec.. I am looking up that book title. 📘🔍');

                    $response = $answer->getText();
                    $this->ask($this->_createBooksTemplate($response), function(Answer $answer) {
                        
                    });
                    return $this->ask($this->_createBookQuestions(), function (Answer $answer) {
                                if ($answer->isInteractiveMessageReply()) {
                                    $response = $answer->getValue();
                                    $this->checkResponse($response);
                                }
                            });
                });
                break;

            case 'I_WANT_THIS':
                $this->say('Nice… You’ve added this title to your BookBag🎒: ');
                $this->ask($this->_createCheckoutTemplate(), function(Answer $answer) {
                    $response = $answer->getValue();
                    $this->checkResponse($response);
                });
                break;

            default:
                $botman->startConversation(new FallbackConversation());
        }
    }

    public function run() {
        $this->askReason();
    }

}

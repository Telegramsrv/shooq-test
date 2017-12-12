<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\Drivers\Facebook\Extensions\Element;
use BotMan\Drivers\Facebook\Extensions\ElementButton;
use BotMan\Drivers\Facebook\Extensions\GenericTemplate;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;

class SellConversation extends Conversation {

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
        $this->ask('Great! Please reply with one of the following:

• ISBN number 🔢
• Picture of book barcode 📷
• Book Title 📗

Separate multiple ISBNs with a comma, like this:
9780062301239, 9780753555200', function(Answer $answer) {
            $response = $answer->getText();
            $this->say('Hold on... I am looking up that book title. 📘🔍');
            $this->ask($this->_createSellBooksTemplate($response), function(Answer $answer) {
                $response = $answer->getValue();
                $this->checkResponse($response);
            });
            return $this->ask($this->_createSellBookQuestions(), function (Answer $answer) {
                        if ($answer->isInteractiveMessageReply()) {
                            $response = $answer->getValue();
                            $this->checkResponse($response);
                        }
                    });
        });
    }

    public function _createSellBooksElement($book) {

        return Element::create($book)
                        ->subtitle('Find the best prices on new and used books')
                        ->image('https://external.fixc1-1.fna.fbcdn.net/safe_image.php?d=AQBaQ4SiDM3sZaiv&url=https%3A%2F%2Fwww.shooq.co%2Fwp-content%2Fuploads%2F2017%2F01%2Fbook.jpg&_nc_hash=AQDzVbFZC6yFx87Y')
                        ->addButton(ElementButton::create('Yes, This Book')
                                ->payload('YES_SELL_THIS_BOOK')->type('postback'));
    }

    public function _createSellBookQuestions() {
        return $question = Question::create("Or select these options: ")
                ->fallback('Unable to ask question')
                ->callbackId('ask_reason')
                ->addButtons([
            Button::create('Cancel Search ✖️️')->value('CANCEL_SEARCH'),
            Button::create('New Search 🔄')->value('NEW_SEARCH_SELL'),
        ]);
    }

    public function _createSellBooksTemplate($response) {
        $books = ['Harry Potter', 'As You Like It', 'Julius Caesar', 'Hamlet'];
        $isbn = ['23423423423', '3242322332432', '3242455646677', '46542424346535'];
        $elements = [];
        foreach ($books as $key => $book) {
            if (!empty(stristr($book, $response))) {

                array_push($elements, $this->_createSellBooksElement($book));
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

    public function checkResponse($response) {
        $botman = app('botman');
        switch ($response) {
            case 'NEW_SEARCH_SELL':
                $this->ask('Please reply with a new Book Title, ISBN, or Barcode Image.

🔢 Separate multiple ISBN numbers with a comma.', function(Answer $answer) {
                    $response = $answer->getText();
                    $this->say('Hold on... I am looking up that book title. 📘🔍');
                    $this->ask($this->_createSellBooksTemplate($response), function(Answer $answer) {
                        $response = $answer->getValue();
                        $this->checkResponse($response);
                    });
                    return $this->ask($this->_createSellBookQuestions(), function (Answer $answer) {
                                if ($answer->isInteractiveMessageReply()) {
                                    $response = $answer->getValue();
                                    $this->checkResponse($response);
                                }
                            });
                });
                break;
            case 'YES_SELL_THIS_BOOK':
                $this->say('Sorry we are not purchasing this book.');
                return $this->ask($this->_createSellBookQuestions(), function (Answer $answer) {
                            if ($answer->isInteractiveMessageReply()) {
                                $response = $answer->getValue();
                                $this->checkResponse($response);
                            }
                        });
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

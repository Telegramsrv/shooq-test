<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;

class FeedbackConversation extends Conversation {

    public function askReason() {
        $this->say('I love feedback; it allows me to improve my skills and intelligence!');
        return $this->ask($this->_createFeedbackQuestions(), function (Answer $answer) {
                    if ($answer->isInteractiveMessageReply()) {
                        $response = $answer->getValue();
                        $this->checkResponse($response);
                    }
                });
    }

    public function _createFeedbackQuestions() {
        return $question = Question::create("Please reply with the applicable emoji regarding how you feel about Shooq:")
                ->fallback('Unable to ask question')
                ->callbackId('ask_reason')
                ->addButtons([
            Button::create('😃')->value('😃'),
            Button::create('😍')->value('😍'),
            Button::create('😡')->value('😡'),
            Button::create('😑')->value('😑'),
            Button::create('🤔')->value('🤔'),
            Button::create('😂')->value('😂'),
        ]);
    }

    public function checkResponse($response) {
        $botman = app('botman');
        switch ($response) {
            case '😃':
                $this->ask("Thanks for your feedback!
Why did you choose for " . $response . ' ?', function(Answer $answer) {
                    $this->say('Thanks for your super awesome feedback!');
                });
                break;

            case '😍':
                $this->ask("Thanks for your feedback!
Why did you choose for " . $response . ' ?', function(Answer $answer) {
                    $this->say('Thanks for your super awesome feedback!');
                });
                break;

            case '😡':
                $this->ask("Thanks for your feedback!
Why did you choose for " . $response . ' ?', function(Answer $answer) {
                    $this->say('Thanks for your super awesome feedback!');
                });
                break;

            case '😑':
                $this->ask("Thanks for your feedback!
Why did you choose for " . $response . ' ?', function(Answer $answer) {
                    $this->say('Thanks for your super awesome feedback!');
                });
                break;

            case '🤔':
                $this->ask("Thanks for your feedback!
Why did you choose for " . $response . ' ?', function(Answer $answer) {
                    $this->say('Thanks for your super awesome feedback!');
                });
                break;

            case '😂':
                $this->ask("Thanks for your feedback!
Why did you choose for " . $response . ' ?', function(Answer $answer) {
                    $this->say('Thanks for your super awesome feedback!');
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

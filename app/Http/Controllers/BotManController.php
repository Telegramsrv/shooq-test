<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
//use App\Conversations\ExampleConversation;
use App\Conversations\shooqConversation;
use App\Conversations\OnboardConversation;
use App\Conversations\SellConversation;
use App\Conversations\BuyConversation;
use App\Conversations\FeedbackConversation;
use App\Conversations\HelpConversation;
use App\Conversations\helloConversation;
use App\Conversations\FallbackConversation;

class BotManController extends Controller {

    /**
     * Place your BotMan logic here.
     */
    public function handle() {
        $botman = app('botman');

        $botman->hears('GET_STARTED', function (BotMan $bot) {
            $user = $bot->getUser();
            $firstname = $user->getFirstName();

            $bot->reply('Hey ' . $firstname . '! Thanks for dropping in. I am Arti, your super helpful textbook shopping assistant.');
            $bot->startConversation(new shooqConversation());
        });

        $botman->hears('Hi', function($bot) {
            $bot->startConversation(new helloConversation());
        });

        $botman->hears('Hello', function($bot) {
            $bot->startConversation(new helloConversation());
        });

        $botman->hears('Hey', function($bot) {
            $bot->startConversation(new helloConversation());
        });

        $botman->hears('Buy Or Rent', function($bot) {
            $bot->startConversation(new BuyConversation());
        });

        $botman->hears('Buy', function($bot) {
            $bot->startConversation(new BuyConversation());
        });

        $botman->hears('rent', function($bot) {
            $bot->startConversation(new BuyConversation());
        });

        $botman->hears('Commands', function($bot) {
            $bot->startConversation(new OnboardConversation());
        });

        $botman->hears('What can you do', function($bot) {
            $bot->startConversation(new OnboardConversation());
        });

        $botman->hears('sell', function($bot) {
            $bot->startConversation(new SellConversation());
        });

        $botman->hears('sell books', function($bot) {
            $bot->startConversation(new SellConversation());
        });

        $botman->hears('feedback', function($bot) {
            $bot->startConversation(new FeedbackConversation());
        });

        $botman->hears('help', function($bot) {
            $bot->startConversation(new HelpConversation());
        });

        $botman->hears('stop', function(BotMan $bot) {
            $bot->reply('stopped');
        })->stopsConversation();

        $botman->fallback(function($bot) {
            $bot->reply('Sorry I did not understand these commands.');
            $bot->startConversation(new FallbackConversation);
        });
        $botman->listen();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tinker() {
        return view('tinker');
    }

    /**
     * Loaded through routes/botman.php
     * @param  BotMan $bot
     */
}

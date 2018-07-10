<?php

namespace App\Http\Controllers;

use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use App\Conversations\ExampleConversation;
use App\Conversations\PointExistanceConversation;
use App\Point;
use App\Exceptions\PointFactoryException;

class BotManController extends Controller
{
    /**
     * Place your BotMan logic here.
     */
    public function handle()
    {
        $botman = app('botman');

        $botman->listen();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tinker()
    {
        return view('tinker');
    }

    /**
     * Loaded through routes/botman.php
     * @param \BotMan\BotMan\BotMan $bot
     */
    public function startConversation(BotMan $bot)
    {
        $bot->startConversation(new ExampleConversation());
    }

    /**
     * Loaded through routes/botman.php
     * @param \BotMan\BotMan\BotMan $bot
     */
    public function startPointConversation(BotMan $bot)
    {
        $payload = (object) $bot->getMessage()->getPayload();

        if (isset($payload->type)) {
            try {
                $point = Point::make($payload->type);
                $bot->startConversation(new PointExistanceConversation($point));
            } catch(PointFactoryException $exception) {
                $bot->reply($exception->getMessage());
            }
        } else {
            $bot->reply(__('botman/errors.required_type'));
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Point;
use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use App\Exceptions\PointFactoryException;
use App\Conversations\ExampleConversation;
use App\Conversations\PointCreationConversation;
use App\Conversations\PointExistanceConversation;

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

        if (isset($payload->point_id)) {
            try {
                $point = Point::findOrFail($payload->point_id);
                $bot->startConversation(new PointExistanceConversation($point));
            } catch(PointFactoryException $exception) {
                $bot->reply($exception->getMessage());
            }
        } else {
            $bot->reply(__('botman/errors.required_point_id'));
        }
    }

    /**
     * Empieza una nueva conversación para crear un punto nuevo.
     *
     * @param \BotMan\BotMan\BotMan $bot
     * @return void
     */
    public function startPointCreationConversation(BotMan $bot) {
        $payload = (object) $bot->getMessage()->getPayload();

        if (isset($payload->location)) {
            $bot->startConversation(new PointCreationConversation($payload->location));
        } else {
            $bot->reply(__('botman/errors.required_location'));
        }
    }

    /**
     * Se comunica con el servicio Dialogflow para captar
     * la intención del mensaje del usuario.
     *
     * @param \BotMan\BotMan\BotMan $bot
     * @return void
     */
    public function nlpHandler(Botman $bot) {
        $extras = $bot->getMessage()->getExtras();

        switch ($extras['apiIntent']) {
            case 'point_found': $this->startPointConversation($bot);
                break;
            case 'create_point': $this->startPointCreationConversation($bot);
                break;
            default: $bot->reply($extras['apiReply']);
                break;
        }
    }
}

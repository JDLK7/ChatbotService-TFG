<?php

use BotMan\BotMan\Middleware\ApiAi;
use App\Http\Controllers\BotManController;

$botman = resolve('botman');

$dialogflow = ApiAi::create('bc654b2b8b4a42a1b621609f9a0d5824')->listenForAction();
$botman->middleware->received($dialogflow);

$botman->hears('.*', BotManController::class.'@nlpHandler')->middleware($dialogflow);

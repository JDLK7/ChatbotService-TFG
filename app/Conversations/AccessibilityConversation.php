<?php

namespace App\Conversations;

use App\Actions\RatingAction;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Conversations\Conversation;

abstract class AccessibilityConversation extends Conversation
{
    /**
     * El punto encontrado
     *
     * @var \App\Point
     */
    protected $point;

    public function __construct($point) {
        $this->point = $point;
    }

    protected function askForRating() {
        $question = Question::create("Valora el grado de accesibilidad del {$this->point->type}")
            ->fallback('Unable to ask question')
            ->callbackId('ask_accessibility_rating')
            ->addAction(RatingAction::create('rating'));

        return $this->ask($question, function (Answer $answer) {
            $rating = $answer->getValue();

            $this->say("Puntuación: $rating");
            $this->say('Gracias por tu colaboración');
        });
    }

    /**
     * @return mixed
     */
    abstract public function run();
}

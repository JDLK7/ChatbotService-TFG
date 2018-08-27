<?php

namespace App\Conversations;

use App\PointVersion;
use App\Actions\RatingAction;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Conversations\Conversation;

abstract class AccessibilityConversation extends Conversation
{
    /**
     * El punto encontrado.
     *
     * @var \App\Point
     */
    protected $point;

    /**
     * Las propiedades del punto.
     *
     * @var object
     */
    protected $properties;
    
    /**
     * La valoración de accesibilidad del punto.
     *
     * @var int
     */
    protected $rating;

    /**
     * Crea una revisión, se la asigna al punto y se guarda.
     *
     * @return void
     */
    protected function reviewPoint() {
        $review = $this->point->makeVersion(auth()->user());
        $review->properties = $this->properties;
        $review->rating = $this->rating;
        $review->save();
    }

    public function __construct($point) {
        $this->point = $point;
    }

    protected function askForRating() {
        $question = Question::create("Valora el grado de accesibilidad del {$this->point->type}")
            ->fallback('Unable to ask question')
            ->callbackId('ask_accessibility_rating')
            ->addAction(RatingAction::create('rating'));

        return $this->ask($question, function (Answer $answer) {
            $this->rating = floatval($answer->getValue());
            $this->reviewPoint();

            $this->say("Puntuación: $this->rating");
            $this->say('Gracias por tu colaboración');
        });
    }

    /**
     * @return mixed
     */
    abstract public function run();
}

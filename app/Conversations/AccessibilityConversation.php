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
     * La revisión del punto.
     *
     * @var \App\PointVersion
     */
    protected $review;
    
    /**
     * Las propiedades de la revisión.
     *
     * @var object
     */
    protected $properties;

    /**
     * Asigna las propiedades recogidas a la revisión y la guarda.
     *
     * @return void
     */
    protected function reviewPoint() {
        $this->review->properties = $this->properties;
        $this->review->doesExist = true;
        $this->review->save();
    }

    public function __construct($point) {
        $this->point = $point;
        $this->review = $this->point->makeVersion(auth()->user());
    }

    protected function askForRating() {
        $question = Question::create(__('botman/questions.ask_accessibility_rating', [
                'type' => $this->point->type
            ]))
            ->fallback(__('botman/questions.fallback'))
            ->callbackId('ask_accessibility_rating')
            ->addAction(RatingAction::create('rating'));

        return $this->ask($question, function (Answer $answer) {
            $this->review->rating = floatval($answer->getValue());
            $this->reviewPoint();

            $this->say("Puntuación: {$this->review->rating}");
            $this->say(__('botman/questions.appreciation'));
        });
    }

    /**
     * @return mixed
     */
    abstract public function run();
}

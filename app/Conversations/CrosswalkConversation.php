<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use App\Conversations\AccessibilityConversation;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;

class CrosswalkConversation extends AccessibilityConversation
{
    /**
     * Las propiedades del punto: 
     *
     * @var object
     */
    protected $properties;

    protected function reviewPoint() {
        $review = $this->point->makeVersion(auth()->user());
        $review->properties = $this->properties;
        $review->save();
    }

    public function __construct($point) {
        $this->properties = (object) [
            'visibility' => null,
            'hasCurbRamps' => false,
            'hasAcousticSemaphore' => false,
        ];

        parent::__construct($point);
    }

    public function askAboutCurbRamps() {
        $question = Question::create('¿El paso de cebra cuenta con vados para acceder con silla de ruedas u otros?')
            ->fallback('Unable to ask question')
            ->callbackId('ask_semafore_existence')
            ->addButtons([
                Button::create('Si')->value('true'),
                Button::create('No')->value('false'),
            ]);

        return $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $this->properties->hasCurbRamps = ($answer->getValue() === 'true');

                $this->askAboutVisibility();
            }
        });
    }

    public function askAboutVisibility() {
        $question = Question::create('¿Cómo es la visibilidad desde el paso de cebra a la calzada?')
            ->fallback('Unable to ask question')
            ->callbackId('ask_semafore_existence')
            ->addButtons([
                Button::create('Buena')->value('good'),
                Button::create('Normal')->value('normal'),
                Button::create('Mala')->value('bad'),
            ]);

        return $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $this->properties->visibility = $answer->getValue();

                $this->askAboutSemaphore();
            }
        });
    }

    public function askAboutSemaphore() {
        $question = Question::create('¿La calle que cruza el paso de cebra tiene semáforos sonoros?')
            ->fallback('Unable to ask question')
            ->callbackId('ask_semafore_existence')
            ->addButtons([
                Button::create('Si')->value('true'),
                Button::create('No')->value('false'),
            ]);

        return $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $this->properties->hasSemaphore = ($answer->getValue() === 'true');

                $this->askForRating();
                $this->reviewPoint();
            }
        });
    }

    /**
     * Start the conversation.
     *
     * @return mixed
     */
    public function run()
    {
        $this->askAboutCurbRamps();
    }
}

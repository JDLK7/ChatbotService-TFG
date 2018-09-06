<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use App\Conversations\AccessibilityConversation;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;

class CrosswalkConversation extends AccessibilityConversation
{
    public function __construct($point) {
        $this->properties = (object) [
            'visibility' => null,
            'hasCurbRamps' => false,
            'hasAcousticSemaphore' => false,
        ];

        parent::__construct($point);
    }

    public function askAboutCurbRamps() {
        $question = Question::create(__('botman/questions.ask_crosswalk_curb_ramps'))
            ->fallback(__('botman/questions.fallback'))
            ->callbackId('ask_crosswalk_curb_ramps')
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
        $question = Question::create(__('botman/questions.ask_crosswalk_visibility'))
            ->fallback(__('botman/questions.fallback'))
            ->callbackId('ask_crosswalk_visibility')
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
        $question = Question::create(__('botman/questions.ask_crosswalk_semaphore'))
            ->fallback(__('botman/questions.fallback'))
            ->callbackId('ask_crosswalk_semaphore')
            ->addButtons([
                Button::create('Si')->value('true'),
                Button::create('No')->value('false'),
            ]);

        return $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $this->properties->hasSemaphore = ($answer->getValue() === 'true');

                $this->askForRating();
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

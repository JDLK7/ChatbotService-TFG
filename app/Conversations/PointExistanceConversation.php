<?php

namespace App\Conversations;

use App\Point;
use BotMan\BotMan\Messages\Incoming\Answer;
use App\Conversations\CrosswalkConversation;
use BotMan\BotMan\Messages\Outgoing\Question;
use App\Conversations\AccessibilityConversation;
use App\Conversations\CategorizationConversation;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;

class PointExistanceConversation extends AccessibilityConversation
{
    /**
     * First question
     */
    public function askIfPointExists()
    {
        $question = Question::create(__('botman/questions.existence', [
                'type' => $this->point->displayName,
            ]))
            ->fallback('Unable to ask question')
            ->callbackId('ask_existence')
            ->addButtons([
                Button::create('Si')->value('true'),
                Button::create('No')->value('false'),
            ]);

        return $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                if ($answer->getValue() === 'true') {
                    if (is_a($this->point, \App\ObstaclePoint::class)) {
                        $this->bot->startConversation(new CategorizationConversation($this->point));
                    } else if (is_a($this->point, \App\CrosswalkPoint::class)) {
                        $this->bot->startConversation(new CrosswalkConversation($this->point));
                    } else {
                        $this->askForRating();
                    }
                } else {
                    $this->say('Gracias por tu colaboración');
                }
            }
        });
    }

    /**
     * Start the conversation
     */
    public function run()
    {
        $this->askIfPointExists();
    }
}

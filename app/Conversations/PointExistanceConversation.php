<?php

namespace App\Conversations;

use App\Point;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

class PointExistanceConversation extends Conversation
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
                    $this->say('Necesito más info');
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

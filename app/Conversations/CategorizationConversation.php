<?php

namespace App\Conversations;

use App\Point;
use Illuminate\Support\Facades\DB;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use App\Conversations\AccessibilityConversation;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;

class CategorizationConversation extends AccessibilityConversation
{
    protected function createButtons() {
        $buttons = [];
        $obstacleTypes = DB::table('obstacle_types')->get();

        foreach ($obstacleTypes as $type) {
            $buttons[] = Button::create($type->name)->value($type->value);
        }

        return $buttons;
    }

    public function askPointSubtype()
    {
        $question = Question::create(__('botman/questions.ask_obstacle_type'))
            ->fallback(__('botman/questions.fallback'))
            ->callbackId('ask_obstacle_type')
            ->addButtons($this->createButtons());

        return $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $obstacleType = $answer->getValue();

                $this->askForRating();
            }
        });
    }

    public function run()
    {
        $this->askPointSubtype();
    }
}

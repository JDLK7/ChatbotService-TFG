<?php

namespace App\Conversations;

use App\Point;
use Illuminate\Support\Facades\DB;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;

class PointCreationConversation extends Conversation
{
    /**
     * Localización del punto a crear.
     *
     * @var object
     */
    protected $location;

    /**
     * Devuelve un array con los botones de cada 
     * uno de los tipos de punto definidos.
     *
     * @return array
     */
    protected function pointTypesButtons() {
        $buttons = [];

        foreach (__('points/types') as $type => $name) {
            $buttons[] = Button::create(ucfirst($name))->value($type);
        }

        return $buttons;
    }

    public function __construct(object $location) {
        if (isset($location->lat) && isset($location->lng)) {
            $this->location = $location;
        }
    }

    /**
     * Pregunta que tipo de punto se quiere crear y, en función 
     * de éste, se empieza una nueva conversación.
     *
     * @return void
     */
    protected function askPointType() {
        $question = Question::create(__('botman/questions.ask_point_type'))
            ->fallback(__('botman/questions.fallback'))
            ->callbackId('ask_point_type')
            ->addButtons($this->pointTypesButtons());

        return $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $type = $answer->getValue();

                $point = Point::make($type);
                $point->latitude = $this->location->lat;
                $point->longitude = $this->location->lng;
                $point->save();

                switch ($type) {
                    case 'crosswalk':
                        $this->bot->startConversation(new CrosswalkConversation($point));
                        break;
                    case 'urbanFurniture':
                        break;
                    case 'works':
                        break;
                    case 'obstacle':
                        $this->bot->startConversation(new CategorizationConversation($point));
                        break;
                    default:
                        break;
                }
            }
        });
    }

    public function run()
    {
        $this->askPointType();
    }
}

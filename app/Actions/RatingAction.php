<?php

namespace App\Actions;

use BotMan\BotMan\Interfaces\QuestionActionInterface;


class RatingAction  implements QuestionActionInterface
{

    /** @var string */
    protected $value;

    /** @var string */
    protected $name;

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $name
     *
     * @return static
     */
    public static function create($name)
    {
        return new static($name);
    }

    /**
     * Array representation of the question action.
     *
     * @return array
     */
    public function toArray() {
        return [
            'name' => $this->name,
            'value' => $this->value ?? 0,
        ];
    }
}

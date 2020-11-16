<?php

namespace App\Core;

/**
 * Class Logger
 *
 * @package App\Core
 */
class Logger
{
    /**
     * Used to identify the types of messages.
     */
    const MESSAGE_INFO = 'INFO';
    const MESSAGE_ERROR = 'ERROR';
    const MESSAGE_WARNING = 'WARNING';


    /**
     * The messages.
     *
     * @var array
     */
    public $messages = [];


    /**
     * Used to register a new message.
     *
     * @param Message $message
     * @return $this
     */
    public function addMessage(Message $message)
    {
        $this->messages[] = $message;

        return $this;
    }


    /**
     * Returns the messages.
     *
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }


    /**
     * Used to record generic information.
     *
     * @param $message
     */
    public function info($message)
    {
        $this->addMessage(
            new Message($message, self::MESSAGE_INFO)
        );
    }


    /**
     * Used to add a new warning message.
     *
     * @param $message
     */
    public function warning($message)
    {
        $this->addMessage(
            new Message($message, self::MESSAGE_WARNING)
        );
    }


    /**
     * Used to register a new error message.
     *
     * @param $message
     */
    public function error($message)
    {
        $this->addMessage(
            new Message($message, self::MESSAGE_ERROR)
        );
    }
}
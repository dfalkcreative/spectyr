<?php

namespace App\Core;

/**
 * Class Message
 *
 * @package App\Core
 */
class Message
{
    /**
     * The message error level.
     *
     * @var
     */
    protected $level;


    /**
     * The time in which the message was created.
     *
     * @var
     */
    protected $date;


    /**
     * The actual message contents.
     *
     * @var
     */
    protected $message;


    /**
     * Message constructor.
     *
     * @param string $message
     * @param string $level
     */
    public function __construct($message = '', $level = Logger::MESSAGE_INFO)
    {
        $this->setLevel($level)
            ->setMessage($message)
            ->setDate(new Date());
    }


    /**
     * Used to assign an error level to the message.
     *
     * @param string $level
     * @return $this
     */
    public function setLevel($level = Logger::MESSAGE_INFO)
    {
        $this->level = $level;

        return $this;
    }


    /**
     * Used to return an error level.
     *
     * @return mixed
     */
    public function getLevel()
    {
        return $this->level;
    }


    /**
     * Configures the date for the message.
     *
     * @param $date
     * @return $this
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }


    /**
     * Returns the date for the message.
     *
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
    }


    /**
     * Configures the message contents.
     *
     * @param $message
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }


    /**
     * Returns the message contents.
     *
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }
}
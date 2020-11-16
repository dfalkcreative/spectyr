<?php

namespace App\Core;

/**
 * Class Console
 *
 * @package App\Core
 */
class Console
{
    /**
     * The command line arguments.
     *
     * @var array
     */
    protected $arguments = [];


    /**
     * The registered commands.
     *
     * @var array
     */
    protected $commands = [];


    /**
     * Console constructor.
     */
    public function __construct()
    {

    }


    /**
     * Used to determine whether or not the application is running from the command line.
     *
     * @return bool
     */
    public function isRunningFromCommandLine()
    {
        return php_sapi_name() === 'cli';
    }


    /**
     * Used to register a new command.
     *
     * @param $action
     * @param Command $command
     * @return $this
     */
    public function addCommand($action, Command $command)
    {
        $this->commands[$action] = $command;

        return $this;
    }


    /**
     * Indicates whether or not a command has been registered.
     *
     * @param $action
     * @return bool
     */
    public function hasCommand($action)
    {
        return array_key_exists($action, $this->commands);
    }


    /**
     * Returns a command for a particular action.
     *
     * @param $action
     * @return Command|mixed
     */
    public function getCommand($action)
    {
        return $this->hasCommand($action) ? $this->commands[$action] : new Command();
    }


    /**
     * Used to assign the arguments.
     *
     * @param array $arguments
     * @return $this
     */
    public function setArguments($arguments = [])
    {
        $this->arguments = $arguments;

        return $this;
    }


    /**
     * Returns the provided arguments.
     *
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }


    /**
     * Indicates whether or not an argument exists.
     *
     * @param $argument
     * @return bool
     */
    public function hasArgument($argument)
    {
        return array_key_exists($argument, $this->getArguments());
    }


    /**
     * Used to retrieve an argument.
     *
     * @param $argument
     * @param string $default
     * @return mixed|string
     */
    public function getArgument($argument, $default = '')
    {
        return $this->hasArgument($argument) ? $this->arguments[$argument] : $default;
    }


    /**
     * Used to log a message.
     *
     * @param string $message
     * @return Console
     */
    public function log($message = '')
    {
        echo __($message) . "\n";

        return $this;
    }


    /**
     * Used to terminate the CLI process.
     */
    public function terminate()
    {
        die();
    }


    /**
     * Used to execute the command line action.
     */
    public function getResponse()
    {
        if(!$this->isRunningFromCommandLine()){
            $this->log('Not currently running from command line.')
                ->terminate();
        }

        $arguments = app()->getServer()->getArguments();
        array_shift($arguments);

        // Verify that we have arguments.
        if (!has($arguments)) {
            $this->log('No arguments provided.')->terminate();
        }

        // Prepare each argument and strip any command-line prefixes.
        foreach ($arguments as &$argument) {
            if (substr($argument, 0, 1) == '-') {
                while ($argument && substr($argument, 0, 1) == '-') {
                    $argument = substr($argument, 1, strlen($argument));
                }
            }
        }

        // Determine what the action is.
        $action = array_shift($arguments);

        if ($this->setArguments($arguments)->hasCommand($action)) {
            $this->log('Command found, attempting to execute...');

            // Execute the configured action.
            $this->getCommand($action)
                ->call($this);

            $this->log('Successfully executed action!')
                ->terminate();
        }

        $this->log('No command registered for the provided action.')
            ->terminate();
    }
}
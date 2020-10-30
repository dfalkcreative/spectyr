<?php

namespace App\Core;

/**
 * Class Parser
 *
 * @package App\Core
 */
class Parser
{
    /**
     * The contents to parse.
     *
     * @var
     */
    protected $contents;


    /**
     * Token rules for replacement.
     *
     * @var
     */
    protected $tokens;


    /**
     * Parser constructor.
     *
     * @param string $contents
     */
    public function __construct($contents = '')
    {

    }


    /**
     * Used to configure the contents.
     *
     * @param string $contents
     * @return $this
     */
    public function setContents($contents = '')
    {
        $this->contents = $contents;

        return $this;
    }


    /**
     * Used to return the contents.
     *
     * @return mixed
     */
    public function getContents()
    {
        return $this->contents;
    }


    /**
     * Used to configure the tokens to replace.
     *
     * @param array $tokens
     * @return $this
     */
    public function setTokens($tokens = [])
    {
        $this->tokens = $tokens;

        return $this;
    }


    /**
     * Returns the configured tokens.
     *
     * @return mixed
     */
    public function getTokens()
    {
        return $this->tokens;
    }


    /**
     * Used to process the contents.
     */
    public function run()
    {
        $parts = [];
        $value = $this->getContents();

        if ($value && strpos($value, '{') !== false) {
            // Handle opening brackets.
            while (strpos($value, '{') !== false) {
                $position = strpos($value, '{');
                $parts[] = substr($value, 0, $position);
                $value = substr($value, $position + 1, strlen($value));
            }

            // Add the final segment.
            $parts[] = $value;

            // Handle closing brackets.
            if (count($parts)) {
                $converted = [];

                foreach ($parts as $index => &$part) {
                    $components = [];

                    if (strpos($part, '}') !== false) {
                        while (strpos($part, '}') !== false) {
                            $position = strpos($part, '}');
                            $components[] = substr($part, 0, $position);
                            $part = substr($part, $position + 1, strlen($part));
                        }

                        if ($part) {
                            $components[] = $part;
                        }

                        $converted = array_merge($converted, $components);
                    } else {
                        $converted[] = $part;
                    }
                }

                $parts = $converted;
            }
        }

        if (count($parts)) {
            foreach ($parts as &$value) {
                // Determine whether the options contain a reference.
                if (is_string($value) && strpos($value, ServiceConfiguration::SERVICE_OBJECT) !== false) {
                    // Determine what the reference actually is.
                    $variable = explode(ServiceConfiguration::SERVICE_OBJECT, $value)[1];

                    if ($variable) {
                        $segments = explode('||', $variable);

                        if (count($segments)) {
                            $variable = trim($segments[0]);
                            $fallback = count($segments) > 1 ? $segments[1] : '';

                            $value = $object->getFieldOutput($variable, $model, false, false, $escape, $formatted);

                            if (!$value && $value !== 0) {
                                $value = $fallback;
                            }
                        } else {
                            $value = '';
                        }
                    }
                }
            }

            return implode('', $parts);
        }

        return $value;
    }
}
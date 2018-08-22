<?php

namespace RouterOS;

use RouterOS\Exceptions\Exception;
use RouterOS\Interfaces\ConfigInterface;

/**
 * Class Config
 * @package RouterOS
 * @since 0.1
 */
class Config implements ConfigInterface
{
    /**
     * Array of parameters (with defaults)
     * @var array
     */
    private $_parameters = [
        'legacy' => Client::LEGACY,
        'ssl' => Client::SSL,
        'timeout' => Client::TIMEOUT,
        'attempts' => Client::ATTEMPTS,
        'delay' => Client::ATTEMPTS_DELAY
    ];

    /**
     * Check if key in array
     *
     * @param   string $key
     * @param   array $array
     * @throws  Exception
     */
    private function keyAllowed(string $key, array $array)
    {
        // Check if parameter in list of allowed parameters
        if (!array_key_exists($key, $array)) {
            throw new Exception("Requested parameter '$key' not found in allowed list [" . implode(',',
                    array_keys($array)) . ']');
        }
    }

    /**
     * Set parameter into array
     *
     * @param   string $name
     * @param   mixed $value
     * @return  ConfigInterface
     * @throws  Exception
     */
    public function set(string $name, $value): ConfigInterface
    {
        // Check of key in array
        $this->keyAllowed($name, self::ALLOWED);

        $whatType = \gettype($value);
        $isType = self::ALLOWED[$name];

        // Check what type has this value
        if ($whatType !== $isType) {
            throw new Exception("Parameter '$name' has wrong type '$whatType'' but should be '$isType''");
        }

        // Save value to array
        $this->_parameters[$name] = $value;

        return $this;
    }

    /**
     * Return port number (get from defaults if port is not set by user)
     *
     * @param   string $parameter
     * @return  bool|int
     */
    private function getPort(string $parameter)
    {
        // If client need port number and port is not set
        if ($parameter === 'port' && !isset($this->_parameters['port'])) {
            // then use default with or without ssl encryption
            return (isset($this->_parameters['ssl']) && $this->_parameters['ssl'])
                ? Client::PORT_SSL
                : Client::PORT;
        }
        return null;
    }

    /**
     * Remove parameter from array by name
     *
     * @param   string $parameter
     * @return  ConfigInterface
     * @throws  Exception
     */
    public function delete(string $parameter): ConfigInterface
    {
        // Check of key in array
        $this->keyAllowed($parameter, self::ALLOWED);

        // Save value to array
        unset($this->_parameters[$parameter]);

        return $this;
    }

    /**
     * Return parameter of current config by name
     *
     * @param   string $parameter
     * @return  mixed
     * @throws  Exception
     */
    public function get(string $parameter)
    {
        // Check of key in array
        $this->keyAllowed($parameter, self::ALLOWED);

        return $this->getPort($parameter) ?? $this->_parameters[$parameter];
    }

    /**
     * Return array with all parameters of configuration
     *
     * @return  array
     */
    public function getParameters(): array
    {
        return $this->_parameters;
    }
}

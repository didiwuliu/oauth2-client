<?php

namespace League\OAuth2\Client\Exception;

class IDPException extends \Exception
{
    protected $result;

    public function __construct($result)
    {
        $this->result = $result;

        $code = isset($result['code']) ? $result['code'] : 0;

        if (isset($result['errcode'])) {

            // OAuth 2.0 Draft 10 style
            $message = $result['errcode'];

        } elseif (isset($result['errmsg'])) {

            // cURL style
            $message = $result['errmsg'];

        } else {

            $message = 'Unknown Error.';

        }

        parent::__construct($message, $code);
    }

    public function getType()
    {
        if (isset($this->result['errcode'])) {

            $message = $this->result['errcode'];

            if (is_string($message)) {
                // OAuth 2.0 Draft 10 style
                return $message;
            }
        }

        return 'Exception';
    }

    /**
     * To make debugging easier.
     *
     * @return string The string representation of the error.
     */
    public function __toString()
    {
        $str = $this->getType() . ': ';

        if ($this->code != 0) {
            $str .= $this->code . ': ';
        }

        return $str . $this->message;
    }
}

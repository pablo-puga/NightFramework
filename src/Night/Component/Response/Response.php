<?php

namespace Night\Component\Response;


use Night\Component\Response\Exception\InvalidRedirectCode;

abstract class Response
{
    const MOVED_PERMANENTLY = 301;
    const SEE_OTHER = 303;
    const TEMPORARY_REDIRECT = 307;

    protected $headers = array();

    protected function setContentType($contentType)
    {
        $this->headers['Content-type:'] = $contentType;
    }

    protected function setResponseStatus($status, $message)
    {
        $this->headers['HTTP/1.1'] = "$status $message";
    }

    protected function redirect($destinationURL, $redirectCode)
    {
        switch ($redirectCode) {
            case self::MOVED_PERMANENTLY:
                break;
            case self::SEE_OTHER:
                break;
            case self::TEMPORARY_REDIRECT:
                break;
            default:
                InvalidRedirectCode::throwDefault($redirectCode);
        }
        $this->headers['Location:'] = $destinationURL;
    }
}





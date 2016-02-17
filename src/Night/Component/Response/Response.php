<?php

namespace Night\Component\Response;


use Night\Component\Response\Exception\InvalidRedirectCode;

abstract class Response
{
    const MOVED_PERMANENTLY_CODE = 301;
    const SEE_OTHER_CODE = 303;
    const TEMPORARY_REDIRECT_CODE = 307;

    protected $headers = array();
    protected $content;

    public function setContentType($contentType)
    {
        $this->headers['Content-type'] = $contentType;
    }

    public function setResponseStatus($status, $message)
    {
        $this->headers['HTTP/1.1'] = "$status $message";
    }

    public function redirect($destinationURL, $redirectCode)
    {
        switch ($redirectCode) {
            case self::MOVED_PERMANENTLY_CODE:
                $message = 'Moved Permanently';
                break;
            case self::SEE_OTHER_CODE:
                $message = 'See Other';
                break;
            case self::TEMPORARY_REDIRECT_CODE:
                $message = 'Temporary Redirect';
                break;
            default:
                InvalidRedirectCode::throwDefault($redirectCode);
        }
        $this->setResponseStatus($redirectCode, $message);
        $this->headers['Location'] = $destinationURL;
    }

    public function setCustomHeader($headerName, $headerValue)
    {
        $this->headers["$headerName:"] = $headerValue;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function setContent($responseContent)
    {
        $this->content = $responseContent;
    }

    public function getContent()
    {
        return $this->content;
    }
}





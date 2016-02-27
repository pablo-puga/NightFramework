<?php

namespace Night\Component\Response;


use Night\Component\Profiling\Profiler;
use Night\Component\Response\Exception\InvalidRedirectCode;

class Response
{
    const MOVED_PERMANENTLY_CODE = 301;
    const SEE_OTHER_CODE = 303;
    const TEMPORARY_REDIRECT_CODE = 307;

    protected $headers = array();
    protected $status;
    protected $content;

    public function setContentType($contentType)
    {
        $this->headers['Content-type'] = $contentType;
    }

    public function setResponseStatus($status, $message)
    {
        $this->status = "HTTP/1.1 $status $message";
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

    public function setContent($responseContent)
    {
        $this->content = $responseContent;
    }

    private function sendHeaders()
    {
        foreach($this->headers as $header => $value) {
            header("$header: $value");
        }
        header($this->status);
    }

    private function sendContent()
    {
        echo $this->content;
    }

    public function send()
    {
        $this->sendHeaders();
        $this->sendContent();
        if (Profiler::getState()) {
            echo Profiler::getInstance()->getProfilerHTMLPanel();
        }
    }

    public function getStatus()
    {
        return $this->status;
    }
}





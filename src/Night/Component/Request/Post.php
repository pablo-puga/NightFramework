<?php

namespace Night\Component\Request;


use Night\Component\Request\Exception\InvalidRequestParam;

class Post
{
    private $globalPost;

    public function __construct(Array $globalPost)
    {
        $this->globalPost = $globalPost;
    }

    public function getParam($param)
    {
        if (!array_key_exists($param, $this->globalPost)) {
            InvalidRequestParam::throwDefault($param, get_class());
        }
        return $this->globalPost[$param];
    }
}


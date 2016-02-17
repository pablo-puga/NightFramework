<?php

namespace Night\Component\Request;


class Post
{
    private $globalPost;

    public function __construct(Array $globalPost)
    {
        $this->globalPost = $globalPost;
    }

    public function getParam($param)
    {
        return $this->globalPost[$param];
    }
}


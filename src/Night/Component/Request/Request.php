<?php

namespace Night\Component\Request;


class Request
{
    public $server;
    public $session;
    public $cookie;
    public $get;
    public $post;
    public $route;

    private function __construct(Array $server, Array $session, Array $cookie, Array $get, Array $post)
    {
        $this->server  = new Server($server);
        $this->session = new Session($session);
        $this->cookie  = new Cookie($cookie);
        $this->get     = new Get($get);
        $this->post    = new Post($post);
    }

    public static function newFromGlobals()
    {
        session_start();
        return new self($_SERVER, $_SESSION, $_COOKIE, $_GET, $_POST);
    }

    public static function newCustom(Array $server, Array $session, Array $cookie, Array $get, Array $post)
    {
        return new self($server, $session, $cookie, $get, $post);
    }

    public function getRequestUri()
    {
        return $this->server->getParam('REQUEST_URI');
    }
}


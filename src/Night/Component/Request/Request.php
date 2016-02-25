<?php

namespace Night\Component\Request;


class Request
{
    /** @var \Night\Component\Request\Server */
    public $server;
    /** @var \Night\Component\Request\Session */
    public $session;
    /** @var \Night\Component\Request\Cookie */
    public $cookie;
    /** @var \Night\Component\Request\Get */
    public $get;
    /** @var \Night\Component\Request\Post */
    public $post;
    /** @var \Night\Component\Request\Route */
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


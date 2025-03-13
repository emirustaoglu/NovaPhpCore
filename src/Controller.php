<?php

namespace NovaPhp\Core;

class Controller extends Bootstrap
{
    public function view($view, $data = [])
    {
        return $this->view->show($view, $data);
    }

    public function jsonData()
    {
        return new JsonData();
    }
}
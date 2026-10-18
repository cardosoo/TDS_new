<?php
namespace base;

class Mail  {
    public string|\TDS\App $app;

    public function __construct(){
        $this->app = \TDS\App::get();
    }

}
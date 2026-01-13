<?php

namespace app\tools\controller;
use think\Controller;

class Comment extends Controller
{ 
    public function index()
    {
        return $this->fetch('index');
    }
}
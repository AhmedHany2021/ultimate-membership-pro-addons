<?php

namespace MEMBERSHIPADDON\INCLUDES;

class InitPluginClass
{
    public function __construct()
    {
        add_shortcode("test", [$this, "test"]);
    }

    public function test()
    {
        echo "test";
    }

}
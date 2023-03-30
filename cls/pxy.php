<?php

final class pxy
{
    private $x;
    private $y;

    public function __construct($cx=null,$cy=null){

        if($cx !== null){
            $this->x = $cx;
        }

        if($cy !== null){
            $this->y = $cy;
        }

    }

    public function Cx($cx=null)
    {
        if($cx !== null){
            $this->x = $cx;
        }

        return $this->x;
    }

    public function Cy($cy=null)
    {
        if($cy !== null){
            $this->y = $cy;
        }

        return $this->y;
    }
}


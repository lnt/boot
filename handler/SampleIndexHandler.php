<?php

/*
 * To change this license header, choose License Headers in Project Properties.
* To change this template file, choose Tools | Templates
* and open the template in the editor.
*/

namespace app\handler;

/**
 * @author lalittanwar
 *
 * @Handler(index)
 */
class SampleIndexHandler extends AbstractHandler
{

    public function invokeHandler(Smarty $viewModel, Header $header,
                                  AbstractUser $user, $view = "empty")
    {
        $header->title(DEFAULT_TITLE);
        $header->import(DEFAULT_BUNDLE);
        return $view;
    }

}

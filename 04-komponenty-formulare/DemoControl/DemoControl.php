<?php

namespace App\Components\DemoControl;

use Nette\Application\UI\Control;

/**
 * Class DemoControl - ukázková, vlastně hrozně hloupá komponenta
 * @package App\Components\DemoControl
 */
class DemoControl extends Control{

  /** @var string $text */
  public string $text = 'DEMO';

  public function render():void {
    $this->template->text = $this->text;
    $this->template->render(__DIR__.'/default.latte');
  }

  public function renderHello(string $message):void {
    $this->template->text=$this->text;
    $this->template->message=$message;
    $this->template->render(__DIR__.'/hello.latte');
  }
  
}
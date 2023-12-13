<?php

namespace GWP;

require_once CONTROLLER_PATH . 'AbstractController.php';

class SharedController extends AbstractController {

  function start(): void {
    $it = $this->createIntegratedTemplate();
    $it->loadTemplatefile('start.html');

    $placeholder = [
      'randNumber' => rand(0,10)
    ];

    $it->setVariable($placeholder);
    $it->parseCurrentBlock();

    $this->html = $it->get();
  }
}
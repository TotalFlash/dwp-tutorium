<?php

namespace GWP;

require_once CONTROLLER_PATH . 'AbstractController.php';

class SharedController extends AbstractController {

  function start(): void {
    $it = $this->createIntegratedTemplate();
    // Sollte eine Seite keine Platzhalter oder blöcke enthalten, muss die Funktion $it->loadTemplateFile, wie folgt aufgerufen werden: $it->loadTemplatefile('start.html', false, false);
    $it->loadTemplatefile('start.html');

    $placeholder = [
      'randNumber' => rand(0,10)
    ];

    $it->setVariable($placeholder);
    $it->parseCurrentBlock();

    $this->html = $it->get();
  }
}

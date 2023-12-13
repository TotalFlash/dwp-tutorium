<?php
namespace GWP;

use HTML_Template_IT;

class AbstractController {
  protected string $actionName;
  protected string $controllerName;
  protected string $html = '';
  protected string $title = '';

  public function __construct(string $controllerName, string $actionName) {
    $this->actionName = $actionName;
    $this->controllerName = $controllerName;
  }

  final public function renderHTML(): void {
    $it = new HTML_Template_IT(__DIR__ . "/../views/");
    $it->loadTemplatefile('layout.html');
    $placeholder = [
      'title' => $this->title,
      'content' => $this->html
    ];
    $it->setVariable($placeholder);
    echo $it->get();
  }

  final public function createIntegratedTemplate(): HTML_Template_IT {
    return new HTML_Template_IT(__DIR__ . "/../views/{$this->controllerName}/");
  }
}
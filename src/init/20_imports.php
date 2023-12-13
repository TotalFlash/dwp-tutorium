<?php
// core
require_once 'config/config.php';
require_once 'core/HTMLGenerator.php';
require_once 'core/functions.php';

// Include Integrated Template Models
foreach(glob('assets/lib/Template/*.php') as $integratedTemplateFiles) {
    require_once $integratedTemplateFiles;
}

if (!is_dir('data/')) {
    mkdir('data/', 0777);
}

if (!is_dir('logs/')) {
  mkdir('logs/', 0777);
}

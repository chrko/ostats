<?php

use Prometheus\RenderTextFormat;

require_once 'vendor/autoload.php';

$registry = \ChrKo\Prometheus::getRegistry();

$renderer = new RenderTextFormat();
$result = $renderer->render($registry->getMetricFamilySamples());

header('Content-type: ' . RenderTextFormat::MIME_TYPE);
echo $result;

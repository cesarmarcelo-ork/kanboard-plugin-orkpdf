<?php
require_once __DIR__.'/i18n-bootstrap.php';
class UrlBrandStub {public function href($c,$a,$p){return '/orkpdf';}}
class BrandView {public $url; public function __construct(){$this->url=new UrlBrandStub();} public function render($type){$project=['name'=>'Projeto de Teste'];$data=$type==='detail'?['task'=>['id'=>1,'title'=>'Cartão de Teste'],'subtasks'=>[],'comments'=>[]]:['tasks'=>[]];$options=['fields'=>[],'order'=>[],'show_date'=>true];ob_start();include __DIR__.'/../Template/pdf/report.php';return ob_get_clean();}}
$v=new BrandView();
foreach(['detail','inventory'] as $type){$html=$v->render($type);foreach(['Gerado com OrkPdf | OrkFlowTech','www.orkflowtech.com.br','https://www.orkflowtech.com.br/'] as $needle){if(strpos($html,$needle)===false)throw new RuntimeException($type.' missing '.$needle);}if(strpos($html,'OrkPdf · Kanboard')!==false)throw new RuntimeException('Old header in '.$type);}
$preview=file_get_contents(__DIR__.'/../Template/pdf/index.php');if(strpos($preview,'OrkPdf · Kanboard')!==false||strpos($preview,'www.orkflowtech.com.br')===false)throw new RuntimeException('Preview branding mismatch');
echo "PASS: brand footer in detail, inventory and preview\n";

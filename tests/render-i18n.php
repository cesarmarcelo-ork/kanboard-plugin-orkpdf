<?php
require_once __DIR__.'/i18n-bootstrap.php';
$language = getenv('ORKPDF_TEST_LOCALE') ?: 'pt_BR';
class TestText { public function e($value) {return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');}}
class TestUrl { public function href($c,$a,$p) { return '/orkpdf'; }}
class TestView {
    public $text;public $url;
    public function __construct() {$this->text=new TestText();$this->url=new TestUrl();}
    public function index() {$projects=[8=>'Board example'];ob_start();include __DIR__.'/../Template/pdf/index.php';return ob_get_clean();}
    public function report() {$options=['fields'=>['project','description'],'order'=>['project'],'show_date'=>true];$project=['name'=>'Board example'];$type='detail';$data=['task'=>['id'=>13,'title'=>'Task example','description'=>'Hello'],'subtasks'=>[],'comments'=>[]];ob_start();include __DIR__.'/../Template/pdf/report.php';return ob_get_clean();}
}
$v=new TestView();$index=$v->index();$report=$v->report();
if ($language==='en_US') {
    foreach(['1. Report type','2. Selection','3. PDF fields','4. Presentation','Detailed task','Layout preview','Preview report'] as $x) if(!str_contains($index,$x)) throw new RuntimeException('Missing EN interface: '.$x);
    foreach(['Back to options','Print / Save as PDF','Full description','Generated on'] as $x) if(!str_contains($report,$x)) throw new RuntimeException('Missing EN report: '.$x);
    if(str_contains($index,'Marque os campos'))throw new RuntimeException('Portuguese leaked into EN interface');
} else {
    foreach(['1. Tipo de relatório','2. Seleção','3. Campos do PDF','4. Apresentação','Cartão detalhado','Pré-visualização do layout','Visualizar relatório real'] as $x) if(!str_contains($index,$x)) throw new RuntimeException('Missing PT interface: '.$x);
    foreach(['Voltar às opções','Imprimir / Salvar em PDF','Descrição completa','Gerado em'] as $x) if(!str_contains($report,$x)) throw new RuntimeException('Missing PT report: '.$x);
}
if (!str_contains($report, 'id="orkpdf-print-button"')) throw new RuntimeException('Print control missing');
if (str_contains($report, 'onclick=')) throw new RuntimeException('Inline print handler must not be used');
if(!str_contains($index,'data-i18n='))throw new RuntimeException('Missing JS translation payload');
echo "PASS: interface + report locale $language\n";

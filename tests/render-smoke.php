<?php
require_once __DIR__.'/i18n-bootstrap.php';
// Isolated rendering smoke test. No Kanboard instance is required.
class UrlStub { public function href($controller, $action, $params) { return '/index.php?controller='.rawurlencode($controller).'&action='.rawurlencode($action); } }
class ViewStub {
    public $url; public $text;
    public function __construct() { $this->url = new UrlStub(); $this->text = new class {function e($s) {return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');}}; }
    public function render($file, $project, $type, $data) {
         $options=array('fields'=>array('project','reference','description','subtasks','comments'),'order'=>array('project','reference'),'show_date'=>true,'orientation'=>'portrait','density'=>'normal');
        ob_start();
        include $file;
        return ob_get_clean();
    }
}
$view = new ViewStub();
$project = array('name' => '<script>alert(1)</script>');
$task = array('id'=>7,'title'=>'Teste <img src=x onerror=alert(1)>','project_id'=>2,'description'=>'Texto <script>alert(1)</script>','date_creation'=>0);
$detail = $view->render(__DIR__.'/../Template/pdf/report.php',$project,'detail',array('task'=>$task,'subtasks'=>array(array('title'=>'Subtarefa teste','status'=>1,'time_spent'=>0)),'comments'=>array(array('comment'=>'Comentário seguro','date_creation'=>0))));
$inv = $view->render(__DIR__.'/../Template/pdf/report.php',$project,'inventory',array('tasks'=>array($task)));
foreach (array($detail, $inv) as $html) {
    if (strpos($html,'<script>alert(1)</script>')!==false || strpos($html,'<img src=x onerror=alert(1)>')!==false) { fwrite(STDERR,"Unsafe output\n"); exit(1); }
    if (strpos($html,'&lt;script&gt;alert(1)&lt;/script&gt;')===false) { fwrite(STDERR,"Missing escaped content\n"); exit(2); }
}
foreach (array('Subtarefa teste','Comentário seguro','Descrição completa') as $term) {
    if (strpos($detail,$term)===false) { fwrite(STDERR,"Missing detail section $term\n"); exit(3); }
}
if(strpos($inv,'Inventário de cartões')===false){exit(4);}
echo "PASS: detail/inventory render, HTML escaping, sections\n";

<?php
require_once __DIR__.'/i18n-bootstrap.php';
class UrlStubForUser { public function href($a,$b,$c) { return '/'; }}
class ViewStubForUser {
 public $url;
 public function __construct() {$this->url = new UrlStubForUser();}
 public function go($data) {
  $type='detail'; $project=['name'=>'Teste'];
  $options=['fields'=>['designated','creator'],'order'=>['designated','creator']];
  ob_start(); include __DIR__.'/../Template/pdf/report.php'; return ob_get_clean();
 }
}
$v=new ViewStubForUser();
$html=$v->go(['task'=>['id'=>12,'title'=>'Título','owner_id'=>999,'creator_id'=>55], 'designated_name'=>'Maria Silva', 'creator_name'=>'João Souza']);
if(!str_contains($html,'Maria Silva') || !str_contains($html,'João Souza') || str_contains($html,'<td>999</td>') || str_contains($html,'<td>55</td>')) exit(1);
$html=$v->go(['task'=>['id'=>12,'title'=>'Título','owner_id'=>0,'creator_id'=>55], 'designated_name'=>'Não designado', 'creator_name'=>'João Souza']);
if(!str_contains($html,'Não designado')) exit(2);
echo "PASS: designated/creator names and unassigned fallback\n";

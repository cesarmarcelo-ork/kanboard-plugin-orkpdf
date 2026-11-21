<?php
$esc = static function ($v) { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); };
$dt = static function ($v) { return $v && is_numeric($v) ? date('d/m/Y H:i', (int)$v) : '-'; };
$opts = $options ?? array();
$chosen = $opts['fields'] ?? array();
$order = $opts['order'] ?? array();
$orientation = ($opts['orientation'] ?? '') === 'landscape' ? 'landscape' : 'portrait';
$density = ($opts['density'] ?? '') === 'compact' ? 'compact' : 'normal';
$labels = array('project'=>t('Board'),'creator'=>t('Creator'),'designated'=>t('Assignee'),'createdAt'=>t('Created on'),'priority'=>t('Priority'),'startDate'=>t('Started'),'complexity'=>t('Complexity'),'dueDate'=>t('Due date'),'category'=>t('Category'),'modifiedAt'=>t('Modified on'),'tags'=>t('Tags'),'completedAt'=>t('Completed on'),'reference'=>t('Reference'),'lane'=>t('Swimlane (ID)'),'status'=>t('Status'),'column'=>t('Column (ID)'));
$keys = array('project'=>'project_id','creator'=>'creator_id','designated'=>'owner_id','createdAt'=>'date_creation','priority'=>'priority','startDate'=>'date_started','complexity'=>'score','dueDate'=>'date_due','category'=>'category_id','modifiedAt'=>'date_modification','tags'=>'tags','completedAt'=>'date_completed','reference'=>'reference','lane'=>'swimlane_id','status'=>'is_active','column'=>'column_id');
$dateKeys = array('createdAt','startDate','dueDate','modifiedAt','completedAt');
$rawTitle = trim((string)($opts['report_title'] ?? ''));
$reportTitle = $rawTitle !== '' ? $rawTitle : ($type === 'inventory' ? t('Task inventory') : t('Detailed task report'));
?>
<style id="orkpdf-report-standalone-styles">
/* Embedded in the report deliberately: Kanboard layout plugin assets may be screen-only. */
.orkpdf-report-wrap{font-family:Arial,Helvetica,sans-serif;color:#1b2738;line-height:1.4}
.orkpdf-report-wrap .orkpdf-print-toolbar{display:flex;align-items:center;gap:10px;max-width:210mm;margin:12px auto}
.orkpdf-report-wrap .orkpdf-sheet{box-sizing:border-box;width:210mm;max-width:100%;min-height:297mm;margin:0 auto;padding:16mm;background:white;color:#1b2738;box-shadow:0 1px 12px #0002;font-size:11pt}
.orkpdf-report-wrap.orkpdf-landscape .orkpdf-sheet{width:297mm;min-height:210mm}
.orkpdf-report-wrap.orkpdf-compact .orkpdf-sheet{font-size:9pt}
.orkpdf-report-wrap .orkpdf-report-header{display:flex;align-items:center;gap:16px;border-bottom:2px solid #214a79;padding-bottom:12px}
.orkpdf-report-wrap .orkpdf-report-header>.fa{font-size:32px;color:#214a79}
.orkpdf-report-wrap .orkpdf-report-header h1{font-family:inherit;font-size:20pt;line-height:1.2;margin:4px 0}
.orkpdf-report-wrap .orkpdf-report-header p{margin:0}
.orkpdf-report-wrap .orkpdf-created{text-align:right;color:#6a7888;font-size:9pt}
.orkpdf-report-wrap .orkpdf-sheet h2{font-family:inherit;font-size:16pt;margin:16px 0 12px}
.orkpdf-report-wrap .orkpdf-sheet h3{font-family:inherit;font-size:12pt;margin:20px 0 10px}
.orkpdf-report-wrap .orkpdf-data{width:100%;border-collapse:collapse;table-layout:fixed}
.orkpdf-report-wrap .orkpdf-data th,.orkpdf-report-wrap .orkpdf-data td{border:1px solid #d7e0e8;text-align:left;padding:7px;vertical-align:top;overflow-wrap:anywhere}
.orkpdf-report-wrap .orkpdf-data th{background:#f0f4fa}
.orkpdf-report-wrap .orkpdf-data tbody th{width:35%}
.orkpdf-report-wrap.orkpdf-compact .orkpdf-data th,.orkpdf-report-wrap.orkpdf-compact .orkpdf-data td{padding:4px}
.orkpdf-report-wrap .orkpdf-description{white-space:normal;overflow-wrap:anywhere}
.orkpdf-report-wrap .orkpdf-report-section{margin:15px 0}
.orkpdf-report-wrap .orkpdf-comment{padding:10px;margin-bottom:8px;border:1px solid #d7e0e8;break-inside:avoid}
.orkpdf-report-wrap .orkpdf-comment small{float:right;color:#65748a}
.orkpdf-report-wrap .orkpdf-comment p{margin-bottom:0}
.orkpdf-report-wrap .orkpdf-brand-footer{display:flex;flex-wrap:wrap;justify-content:space-between;align-items:baseline;gap:5px 14px;margin-top:28px;border-top:1px solid #d7e0e8;padding-top:10px;color:#65748a;font-size:9pt}
.orkpdf-report-wrap .orkpdf-brand-footer a{color:inherit;text-decoration:none;overflow-wrap:anywhere}
@media print {
  @page {size:A4 <?= $orientation ?>;margin:12mm}
  html,body{background:white!important;margin:0!important;padding:0!important}
  /* Hide Kanboard chrome regardless of its layout/print stylesheet. */
  body *{visibility:hidden!important}
  .orkpdf-report-wrap,.orkpdf-report-wrap *{visibility:visible!important}
  .orkpdf-report-wrap{position:absolute!important;left:0!important;top:0!important;width:100%!important;max-width:none!important;margin:0!important;padding:0!important;float:none!important}
  .orkpdf-report-wrap .orkpdf-print-toolbar{display:none!important}
  .orkpdf-report-wrap .orkpdf-sheet,.orkpdf-report-wrap.orkpdf-landscape .orkpdf-sheet{box-sizing:border-box!important;width:100%!important;max-width:none!important;min-height:0!important;margin:0!important;padding:0!important;box-shadow:none!important;background:white!important;color:#1b2738!important}
  .orkpdf-report-wrap .orkpdf-data th{background:#f0f4fa!important;-webkit-print-color-adjust:exact!important;print-color-adjust:exact!important}
  .orkpdf-report-wrap .orkpdf-report-header,.orkpdf-report-wrap .orkpdf-report-section h3{break-after:avoid;page-break-after:avoid}
  .orkpdf-report-wrap .orkpdf-data thead{display:table-header-group}
  .orkpdf-report-wrap .orkpdf-data tr,.orkpdf-report-wrap .orkpdf-comment{break-inside:avoid;page-break-inside:avoid}
  .orkpdf-report-wrap .orkpdf-brand-footer a{color:inherit!important;text-decoration:none!important}
}
</style>
<div class="orkpdf-report-wrap <?= $density === 'compact' ? 'orkpdf-compact' : '' ?> <?= $orientation === 'landscape' ? 'orkpdf-landscape' : '' ?>">
  <div class="orkpdf-print-toolbar"><a class="btn" href="<?= $esc($this->url->href('OrkPdfController','index',array('plugin'=>'OrkPdf'))) ?>"><?= t('Back to options') ?></a> <button type="button" id="orkpdf-print-button" class="btn btn-blue"><i class="fa fa-print" aria-hidden="true"></i> <?= t('Print / Save as PDF') ?></button></div>
  <article class="orkpdf-sheet">
    <header class="orkpdf-report-header"><span class="fa fa-print" aria-hidden="true"></span><div><h1><?= $esc($reportTitle) ?></h1><p><?= $esc($project['name'] ?? '') ?></p></div></header>
    <?php if (!empty($opts['show_date'])): ?><p class="orkpdf-created"><?= $esc(t('Generated on')) . ' ' . $esc(date('d/m/Y H:i')) ?></p><?php endif ?>
    <?php if ($type === 'detail'): $task = $data['task']; ?>
      <h2>#<?= (int)$task['id'] ?> - <?= $esc($task['title']) ?></h2>
      <div class="orkpdf-report-meta"><table class="orkpdf-data"><tbody>
        <?php foreach ($order as $field): if (!in_array($field, $chosen, true) || !isset($labels[$field])) continue;
          $key=$keys[$field];
          $value=$field==='project' ? $project['name'] : ($task[$key] ?? '-');
          if ($field==='designated') $value=$data['designated_name'] ?? t('Unassigned');
          if ($field==='creator') $value=$data['creator_name'] ?? t('Not provided');
          if (in_array($field, $dateKeys, true)) $value=$dt($value);
          if ($field==='status') $value=!empty($value)?t('Active'):t('Inactive');
          if (is_array($value)) $value=implode(', ',array_map('strval',$value));
        ?><tr><th><?= $esc($labels[$field]) ?></th><td><?= $esc($value) ?></td></tr><?php endforeach ?>
      </tbody></table></div>
      <?php if (in_array('description',$chosen,true)): ?><section class="orkpdf-report-section"><h3><?= t('Full description') ?></h3><div class="orkpdf-description"><?= nl2br($esc($task['description'] ?? '')) ?></div></section><?php endif ?>
      <?php if (in_array('subtasks',$chosen,true)): ?><section class="orkpdf-report-section"><h3><?= t('Subtasks') ?> (<?= count($data['subtasks']) ?>)</h3>
      <table class="orkpdf-data"><thead><tr><th><?= t('Description') ?></th><th><?= t('State') ?></th><th><?= t('Time spent') ?></th></tr></thead><tbody>
      <?php foreach ($data['subtasks'] as $sub): ?><tr><td><?= $esc($sub['title'] ?? '') ?></td><td><?= $esc($sub['status'] ?? '') ?></td><td><?= $esc($sub['time_spent'] ?? '') ?></td></tr><?php endforeach ?>
      <?php if (empty($data['subtasks'])): ?><tr><td colspan="3"><?= t('No subtasks.') ?></td></tr><?php endif ?>
      </tbody></table></section><?php endif ?>
      <?php if (in_array('comments',$chosen,true)): ?><section class="orkpdf-report-section"><h3><?= t('Comment history') ?> (<?= count($data['comments']) ?>)</h3>
      <?php foreach ($data['comments'] as $comment): ?><div class="orkpdf-comment"><strong><?= $esc($comment['username'] ?? $comment['user_name'] ?? t('User')) ?></strong><small><?= $dt($comment['date_creation'] ?? 0) ?></small><p><?= nl2br($esc($comment['comment'] ?? '')) ?></p></div><?php endforeach ?>
      <?php if (empty($data['comments'])): ?><p><?= t('No comments.') ?></p><?php endif ?></section><?php endif ?>
    <?php else: ?>
      <p><?= t('Total tasks:') ?> <strong><?= count($data['tasks']) ?></strong></p>
      <table class="orkpdf-data"><thead><tr><th>ID</th><th><?= t('Title') ?></th><th><?= t('Reference') ?></th><th><?= t('Active') ?></th></tr></thead><tbody>
      <?php foreach ($data['tasks'] as $item): ?><tr><td><?= (int)$item['id'] ?></td><td><?= $esc($item['title']) ?></td><td><?= $esc($item['reference'] ?? '') ?></td><td><?= !empty($item['is_active']) ? t('Yes') : t('No') ?></td></tr><?php endforeach ?>
      <?php if (!$data['tasks']): ?><tr><td colspan="4"><?= t('No tasks found.') ?></td></tr><?php endif ?>
      </tbody></table>
    <?php endif ?>
    <footer class="orkpdf-brand-footer"><span><?= t('Generated with OrkPdf | OrkFlowTech') ?></span><a href="https://www.orkflowtech.com.br/" target="_blank" rel="noopener noreferrer">www.orkflowtech.com.br</a></footer>
  </article>
</div>

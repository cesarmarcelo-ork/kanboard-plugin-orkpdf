<?php
$defaultFields = array(
    'project' => t('Board'),
    'creator' => t('Creator'),
    'designated' => t('Assignee'),
    'createdAt' => t('Created'),
    'priority' => t('Priority'),
    'startDate' => t('Start date'),
    'complexity' => t('Complexity'),
    'dueDate' => t('Due date'),
    'category' => t('Category'),
    'modifiedAt' => t('Last modified'),
    'tags' => t('Tags'),
    'completedAt' => t('Completed'),
    'reference' => t('Reference'),
    'lane' => t('Swimlane'),
    'status' => t('Status'),
    'column' => t('Column'),
);
$orkpdfJsTranslations = array('detail_title'=>t('Detailed task report'), 'inventory_title'=>t('Task inventory'), 'description'=>t('Full description'), 'subtasks'=>t('Subtasks'), 'comments'=>t('Comment history'), 'inventory_columns'=>t('ID  |  Title  |  Reference  |  Active'));
$firstId = $projects ? (int) array_key_first($projects) : 0;
$reportBase = $this->url->href('OrkPdfController', 'report', array('plugin'=>'OrkPdf', 'project_id'=>$firstId ?: 999999));
?>
<div class="orkpdf-shell" id="orkpdf-root" data-i18n="<?= $this->text->e(json_encode($orkpdfJsTranslations, JSON_UNESCAPED_UNICODE | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG)) ?>">
  <header class="orkpdf-header"><span class="fa fa-print" aria-hidden="true"></span><div><h1>OrkPdf</h1><p><?= t('Configure, preview and print Kanboard reports') ?></p></div></header>
  <form id="orkpdf-form" method="get" action="<?= $this->text->e($reportBase) ?>" data-url-pattern="<?= $this->text->e($this->url->href('OrkPdfController','report',array('plugin'=>'OrkPdf','project_id'=>999999))) ?>">
    <div class="orkpdf-workspace">
      <div class="orkpdf-config">
        <section class="orkpdf-panel"><h2><?= t('1. Report type') ?></h2><div class="orkpdf-mode-picker">
          <label><input type="radio" name="type" value="detail" checked><strong> <?= t('Detailed task') ?></strong><small><?= t('Details, description, subtasks and comments') ?></small></label>
          <label><input type="radio" name="type" value="inventory"><strong> <?= t('Task inventory') ?></strong><small><?= t('List of tasks in the selected board') ?></small></label>
        </div></section>
        <section class="orkpdf-panel"><h2><?= t('2. Selection') ?></h2>
          <label class="orkpdf-field"><?= t('Board') ?>
            <select id="orkpdf-project" required <?= $projects ? '' : 'disabled' ?>>
            <?php if (!$projects): ?><option value=""><?= t('No accessible boards') ?></option><?php endif ?>
            <?php foreach ($projects as $id=>$name): ?><option value="<?= (int)$id ?>"><?= $this->text->e($name) ?></option><?php endforeach ?>
            </select>
          </label>
          <label class="orkpdf-field" id="orkpdf-task-field"><?= t('Task ID') ?>
            <input name="task_id" id="orkpdf-task" type="number" min="1" step="1" placeholder="<?= t('e.g. 123') ?>" required>
          </label>
          <p class="orkpdf-hint"><?= t('In this version, select the task by ID. Board lane, column, and task selection will be added later.') ?></p>
        </section>
        <section class="orkpdf-panel" id="orkpdf-fields-panel"><div class="orkpdf-section-title"><h2><?= t('3. PDF fields') ?></h2><div><button type="button" class="orkpdf-link" id="orkpdf-select-all"><?= t('All') ?></button><button type="button" class="orkpdf-link" id="orkpdf-clear-all"><?= t('Clear') ?></button></div></div>
          <p class="orkpdf-hint"><?= t('Select fields and drag to reorder. The preview updates automatically.') ?></p>
          <div class="orkpdf-sort-grid" id="orkpdf-sort-grid">
          <?php foreach ($defaultFields as $key=>$label): ?><label class="orkpdf-sort-field" draggable="true" data-key="<?= $key ?>"><span class="fa fa-bars" aria-hidden="true"></span><input type="checkbox" data-field="<?= $key ?>" checked><span><?= $this->text->e($label) ?></span></label><?php endforeach ?>
          </div>
          <div class="orkpdf-extra-fields">
            <label><input type="checkbox" data-field="description" checked> <?= t('Full description') ?></label>
            <label><input type="checkbox" data-field="subtasks" checked> <?= t('Subtasks') ?></label>
            <label><input type="checkbox" data-field="comments" checked> <?= t('Comment history') ?></label>
          </div>
          <input type="hidden" id="orkpdf-selected-fields" name="fields">
          <input type="hidden" id="orkpdf-field-order" name="order">
        </section>
        <section class="orkpdf-panel"><h2><?= t('4. Presentation') ?></h2>
          <label class="orkpdf-field"><?= t('Report title') ?><input type="text" name="report_title" id="orkpdf-title" maxlength="120" placeholder="<?= t('Default: task title or inventory') ?>"></label>
          <div class="orkpdf-choices"><label><?= t('Orientation') ?><select name="orientation" id="orkpdf-orientation"><option value="portrait"><?= t('Portrait') ?></option><option value="landscape"><?= t('Landscape') ?></option></select></label>
          <label><?= t('Density') ?><select name="density" id="orkpdf-density"><option value="normal"><?= t('Normal') ?></option><option value="compact"><?= t('Compact') ?></option></select></label></div>
          <label class="orkpdf-check"><input type="checkbox" id="orkpdf-show-date" checked> <?= t('Show generation date') ?></label>
          <input type="hidden" name="show_date" id="orkpdf-date-value" value="1">
        </section>
      </div>
      <aside class="orkpdf-preview-column"><div class="orkpdf-panel orkpdf-preview-sticky"><div class="orkpdf-section-title"><h2><?= t('Layout preview') ?></h2><span class="orkpdf-live-tag"><?= t('Schematic preview') ?></span></div>
        <div id="orkpdf-preview-sheet" class="orkpdf-preview-sheet"><div class="orkpdf-preview-head"><span class="fa fa-print"></span><div><h3 id="orkpdf-preview-title"><?= t('Detailed task report') ?></h3><span id="orkpdf-preview-project"></span></div></div><div id="orkpdf-preview-meta" class="orkpdf-preview-meta"></div><div id="orkpdf-preview-sections"></div><footer class="orkpdf-brand-footer"><span><?= t('Generated with OrkPdf | OrkFlowTech') ?></span><a href="https://www.orkflowtech.com.br/" target="_blank" rel="noopener noreferrer">www.orkflowtech.com.br</a></footer></div>
        <p class="orkpdf-hint"><?= t('This preview shows the structure and field order. Click below to open the report with real Kanboard data.') ?></p>
        <button class="btn btn-blue orkpdf-submit" type="submit" <?= $projects ? '' : 'disabled' ?>><span class="fa fa-eye"></span> <?= t('Preview report') ?></button>
      </div></aside>
    </div>
  </form>
</div>

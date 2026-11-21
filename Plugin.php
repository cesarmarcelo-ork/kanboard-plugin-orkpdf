<?php

namespace Kanboard\Plugin\OrkPdf;

use Kanboard\Core\Plugin\Base;
use Kanboard\Core\Security\Role;
use Kanboard\Core\Translator;

class Plugin extends Base
{
    public function initialize()
    {
        $this->applicationAccessMap->add('OrkPdfController', '*', Role::APP_USER);
        $this->route->addRoute('/orkpdf', 'OrkPdfController', 'index', 'OrkPdf');
        $this->route->addRoute('/orkpdf/report/:project_id', 'OrkPdfController', 'report', 'OrkPdf');
        $this->template->hook->attach('template:dashboard:sidebar', 'OrkPdf:dashboard/sidebar');
        $this->template->hook->attach('template:project:dropdown', 'OrkPdf:project/dropdown');
        $this->hook->on('template:layout:css', array('template' => 'plugins/OrkPdf/Assets/css/orkpdf.css'));
        $this->hook->on('template:layout:js', array('template' => 'plugins/OrkPdf/Assets/js/orkpdf.js'));
    }

    public function onStartup()
    {
        Translator::load($this->languageModel->getCurrentLanguage(), __DIR__ . '/Locale');
    }

    public function getPluginName() { return 'OrkPdf'; }
    public function getPluginDescription() { return t('OrkPdf - PDF reports and task inventories integrated into Kanboard'); }
    public function getPluginAuthor() { return 'OrkFlowTech'; }
    public function getPluginVersion() { return '0.2.7-alpha'; }
    public function getPluginHomepage() { return 'https://www.orkflowtech.com.br/'; }
    public function getCompatibleVersion() { return '>=1.2.50'; }
}

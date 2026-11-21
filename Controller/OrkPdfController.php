<?php

namespace Kanboard\Plugin\OrkPdf\Controller;

use Kanboard\Controller\BaseController;
use Kanboard\Core\Controller\AccessForbiddenException;

class OrkPdfController extends BaseController
{
    public function index()
    {
        $projects = $this->projectUserRoleModel->getActiveProjectsByUser($this->userSession->getId());
        $this->response->html($this->helper->layout->dashboard('OrkPdf:pdf/index', array(
            'user' => $this->userModel->getById($this->userSession->getId()),
            'title' => 'OrkPdf',
            'projects' => $projects,
        )));
    }

    public function report()
    {
        $projectId = (int) $this->request->getIntegerParam('project_id');
        if ($projectId <= 0 || ! $this->projectPermissionModel->isUserAllowed($projectId, $this->userSession->getId())) {
            throw new AccessForbiddenException();
        }

        $project = $this->projectModel->getById($projectId);
        if (empty($project)) {
            throw new AccessForbiddenException();
        }

        $type = $this->request->getStringParam('type', 'detail');
        $allowedFields = array('project','creator','designated','createdAt','priority','startDate','complexity','dueDate','category','modifiedAt','tags','completedAt','reference','lane','status','column','description','subtasks','comments');
        $requestedFields = explode(',', $this->request->getStringParam('fields', implode(',', $allowedFields)));
        $fields = array_values(array_intersect($requestedFields, $allowedFields));
        $order = array_values(array_unique(array_intersect(explode(',', $this->request->getStringParam('order', implode(',', $allowedFields))), $allowedFields)));
        $order = array_values(array_unique(array_merge($order, $allowedFields)));
        $options = array(
            'fields' => $fields,
            'order' => $order,
            'orientation' => $this->request->getStringParam('orientation') === 'landscape' ? 'landscape' : 'portrait',
            'density' => $this->request->getStringParam('density') === 'compact' ? 'compact' : 'normal',
            'report_title' => mb_substr($this->request->getStringParam('report_title', ''), 0, 120),
            'show_date' => $this->request->getStringParam('show_date', '1') === '1',
        );
        $taskId = (int) $this->request->getIntegerParam('task_id');
        $data = array();
        if ($type === 'detail') {
            $task = $this->taskFinderModel->getById($taskId);
            if (!$task || (int) $task['project_id'] !== $projectId) {
                throw new AccessForbiddenException();
            }
            // IDs internos não devem ser exibidos como nomes no PDF.
            $userName = function ($userId, $emptyLabel) {
                $userId = (int) $userId;
                if ($userId <= 0) return $emptyLabel;
                $user = $this->userModel->getById($userId);
                if (!$user) return t('Unavailable user');
                $name = trim((string) ($user['name'] ?? ''));
                return $name !== '' ? $name : (string) ($user['username'] ?? t('Unnamed user'));
            };
            $data = array(
                'task' => $task,
                'designated_name' => $userName($task['owner_id'] ?? 0, t('Unassigned')),
                'creator_name' => $userName($task['creator_id'] ?? 0, t('Not provided')),
                'subtasks' => $this->subtaskModel->getAll($taskId),
                'comments' => $this->commentModel->getAll($taskId),
            );
        } elseif ($type === 'inventory') {
            // Limit scope to a project explicitly authorized above. This alpha is read-only.
            $query = $this->db->table('tasks')->eq('project_id', $projectId);
            $laneId = (int) $this->request->getIntegerParam('lane_id');
            $columnId = (int) $this->request->getIntegerParam('column_id');
            if ($laneId > 0) $query->eq('swimlane_id', $laneId);
            if ($columnId > 0) $query->eq('column_id', $columnId);
            $data['tasks'] = $query->asc('id')->findAll();
        } else {
            throw new AccessForbiddenException();
        }

        $this->response->html($this->helper->layout->app('OrkPdf:pdf/report', array(
            'title' => 'OrkPdf - ' . $project['name'],
            'project' => $project,
            'type' => $type,
            'data' => $data,
            'options' => $options,
        )));
    }
}

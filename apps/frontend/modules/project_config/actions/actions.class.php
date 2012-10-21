<?php

/**
 * project_config actions.
 *
 * @package    MineVis
 * @subpackage project_config
 * @author     Patrick Fiaux
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class project_configActions extends sfActions {
    /**
     * Custom action for the project to be able to ajax Table fields.
     * Logic will come from MineVis util.
     * @param sfWebRequest $request
     */
    public function executeFields(sfWebRequest $request) {
        $table = $request->getParameter('table');
        $project_id = $request->getParameter('project_id');
        $fields = array();
        if ($table != '' and $project_id>0) {
            $project =  Doctrine_Core::getTable('Project')->find(array($project_id));
            $db = new MineVisDb($project->getExternalDatabase());
            $fields = $db->getFields($table);
            //add stuff for debug
            if (sfConfig::get('sf_environment') == 'dev' && !$this->getRequest()->isXmlHttpRequest()) {
                $fields['table'] = $table;
                $fields['pid'] = $project_id;
                $fields['projectdb'] = $project->getExternalDatabase();
            }
        }
        return $this->renderText(json_encode($fields));
    }

    public function executeIndex(sfWebRequest $request) {
        //get project_id
        $project_id = $request->getParameter('project_id');
        $config = new ProjectConfig();
        $config->setProjectId($project_id);
        //load form in here too for quick add
        $this->form = new ProjectConfigForm($config);

        //filter by project
        $this->project_configs = Doctrine_Core::getTable('ProjectConfig')->findBy('project_id', array($project_id));

    }

    public function executeShow(sfWebRequest $request) {
        $this->project_config = Doctrine_Core::getTable('ProjectConfig')->find(array($request->getParameter('id')));
        $this->forward404Unless($this->project_config);
    }

    public function executeNew(sfWebRequest $request) {
        //get project_id
        $project_id = $request->getParameter('project_id');
        $this->config = new ProjectConfig();
        $this->config->setProjectId($project_id);

        $this->form = new ProjectConfigForm($this->config);

    }

    public function executeCreate(sfWebRequest $request) {
        $this->forward404Unless($request->isMethod(sfRequest::POST));

        $this->form = new ProjectConfigForm();

        $this->processForm($request, $this->form);

        $project_id = $request->getParameter('project_id');
        $this->config = new ProjectConfig();
        $this->config->setProjectId($project_id);

        $this->setTemplate('new');
    }

    public function executeEdit(sfWebRequest $request) {
        $this->forward404Unless($project_config = Doctrine_Core::getTable('ProjectConfig')->find(array($request->getParameter('id'))), sprintf('Object project_config does not exist (%s).', $request->getParameter('id')));
        $this->form = new ProjectConfigForm($project_config);
        $this->project_config = $project_config;
    }

    public function executeUpdate(sfWebRequest $request) {
        $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
        $this->forward404Unless($project_config = Doctrine_Core::getTable('ProjectConfig')->find(array($request->getParameter('id'))), sprintf('Object project_config does not exist (%s).', $request->getParameter('id')));
        $this->form = new ProjectConfigForm($project_config);
        $this->project_config = $project_config;

        $this->processForm($request, $this->form);

        $this->setTemplate('edit');
    }

    public function executeDelete(sfWebRequest $request) {
        $request->checkCSRFProtection();

        $this->forward404Unless($project_config = Doctrine_Core::getTable('ProjectConfig')->find(array($request->getParameter('id'))), sprintf('Object project_config does not exist (%s).', $request->getParameter('id')));
        $project_config->delete();

        $this->redirect('@project_config_index?project_id='.$project_config->getProjectId());
    }

    protected function processForm(sfWebRequest $request, sfForm $form) {
        $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
        if ($form->isValid()) {
            $project_config = $form->save();

            $this->redirect('@project_config_index?project_id='.$project_config->getProjectId());
        }
    }
}

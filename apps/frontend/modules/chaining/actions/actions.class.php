<?php

/**
 * chaining actions.
 *
 * @package    MineVis
 * @subpackage chaining
 * @author     Patrick Fiaux
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class chainingActions extends sfActions {

    public function executeRun(sfWebRequest $request) {
        $chaining = Doctrine_Core::getTable('Chaining')->find(array($request->getParameter('id')));
        $this->forward404Unless($chaining);


        if ($chaining->getComplete() == false && $chaining->getStarted() == false && $chaining->getMining()->getComplete() == true) {
            /**
             * This executes the call to run the chaining
             * nohup helps with background too (no it doesn't using proc open close now)
             * >/dev/null makes sure we don't have to worry about output
             * & makes it run in the background (what we want)
             */
            $logName = '/cache/algorithms/minetree/chaining_' . $chaining->getId();

            $task = new BackgroundTask('runChaining', array($chaining->getId()));
            $task->setPipes($logName . '.log', $logName . '.err', null);
            $task->run();

            $chaining->setStarted(True);
            $chaining->save();
        }
        $this->redirect('chaining/' . $chaining->getId());
    }

    public function executeIndex(sfWebRequest $request) {
        $this->chainings = Doctrine_Core::getTable('Chaining')
                ->createQuery('a')
                ->execute();
    }

    public function executeShow(sfWebRequest $request) {
        $this->chaining = Doctrine_Core::getTable('Chaining')->find(array($request->getParameter('id')));
        $this->forward404Unless($this->chaining);

        // -- - - - - - - - - - - - - - - - -  - --  - - Temp stuff
        $connection = Doctrine_Manager::connection();

        //get unique types
        //union can't be done with doctrine...
        $query = "SELECT DISTINCT table_a AS chain_type FROM `project_config` WHERE project_id=1"
                . " UNION "
                . " SELECT DISTINCT table_b AS chain_type FROM `project_config` WHERE project_id=1;";
        $statement = $connection->execute($query);
        $resultset = $statement->fetchAll();

        $this->chainTypes = array();

        foreach ($resultset as $result) {
            //echo $result[0];
            $this->chainTypes[] = $result[0];
        }
    }

    public function executeNew(sfWebRequest $request) {
        $this->form = new ChainingForm();
    }

    public function executeCreate(sfWebRequest $request) {
        $this->forward404Unless($request->isMethod(sfRequest::POST));

        $this->form = new ChainingForm();

        $this->processForm($request, $this->form);

        $this->setTemplate('new');
    }

    public function executeEdit(sfWebRequest $request) {
        $this->forward404Unless($chaining = Doctrine_Core::getTable('Chaining')->find(array($request->getParameter('id'))), sprintf('Object chaining does not exist (%s).', $request->getParameter('id')));
        $this->form = new ChainingForm($chaining);
        if ($chaining->getStarted() == 1) {
            $this->form->enableNameOnly();
        }
    }

    public function executeUpdate(sfWebRequest $request) {
        $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
        $this->forward404Unless($chaining = Doctrine_Core::getTable('Chaining')->find(array($request->getParameter('id'))), sprintf('Object chaining does not exist (%s).', $request->getParameter('id')));
        $this->form = new ChainingForm($chaining);

        $this->processForm($request, $this->form);

        $this->setTemplate('edit');
    }

    public function executeDelete(sfWebRequest $request) {
        $request->checkCSRFProtection();

        $this->forward404Unless($chaining = Doctrine_Core::getTable('Chaining')->find(array($request->getParameter('id'))), sprintf('Object chaining does not exist (%s).', $request->getParameter('id')));
        $chaining->delete();

        $this->redirect('chaining/index');
    }

    protected function processForm(sfWebRequest $request, sfForm $form) {
        $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
        if ($form->isValid()) {
            //convert the max n and threshold to parameter array.
            //$max_n = $form->getValue('max_neighbors');
            //$tres = $form->getValue('threshold');
            //$form->s

            $chaining = $form->save();
            $this->chaining = $chaining;

            //Do automatic setup on creation.
            if (!$chaining->getConfigured()) {
                //chaining was created, now create the links for it:
                $chaining->setupLinkTypes();
                $chaining->setConfigured(True);
                $chaining->save();
            }
            $this->redirect('chaining/show?id=' . $chaining->getId());
        }
    }

}

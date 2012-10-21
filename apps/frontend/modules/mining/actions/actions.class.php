<?php

/**
 * mining actions.
 *
 * @package    MineVis
 * @subpackage mining
 * @author     Patrick Fiaux
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class miningActions extends sfActions {

    /**
     * This runs the document link task to generate the links
     * @param sfWebRequest $request
     */
    public function executeRundocumentlinks(sfWebRequest $request) {
        $mining = Doctrine_Core::getTable('Mining')->find(array($request->getParameter('id')));
        $this->forward404Unless($mining);

        $task = new BackgroundTask('generateDocumentLinks', array($mining->getId()));
        //No logging
        $task->run();
        $this->redirect('mining/show?id=' . $mining->getId());
    }

    /**
     * This runs the entity frequencies task to generate the frequency table
     * @param sfWebRequest $request
     */
    public function executeRunentityfrequencies(sfWebRequest $request) {
        $mining = Doctrine_Core::getTable('Mining')->find(array($request->getParameter('id')));
        $this->forward404Unless($mining);

        $task = new BackgroundTask('generateEntityFrequencies', array($mining->getId()));
        //No logging
        $task->run();
        $this->redirect('mining/show?id=' . $mining->getId());
    }

    /**
     * This runs the mining algorithm
     * @param sfWebRequest $request
     */
    public function executeRun(sfWebRequest $request) {
        $mining = Doctrine_Core::getTable('Mining')->find(array($request->getParameter('id')));
        $this->forward404Unless($mining);


        if ($mining->getComplete() == false and $mining->getStarted() == false) {
            //Set Mining as started
            $mining->setStarted(1);
            //Add all configs to job
            $mining->setupJobs();
            //set jobs setup as done
            $mining->setSetup(1);
            //save mining now
            $mining->save();

            /**
             * This executes the call to run the mining
             * nohup helps with background too (no it doesn't using proc open close now)
             * >/dev/null makes sure we don't have to worry about output
             * & makes it run in the background (what we want)
             */
            $algorithm = strtolower($mining->getAlgorithmName());
            $task = new BackgroundTask('runMining', array($mining->getId()));

            if ($algorithm::checkCache()) {
                $logs = $algorithm::getLogPaths($mining);
                $task->setPipes($logs['log'], $logs['err'], null);
                $task->run();
            } else {
                die('ERROR: Failed to confirm cache directory validity.');
            }

            //echo $task->command . "<br/>";
            //echo $task->pid . "<br/>";
        }
        //Comment next line to disable redirect for debug...
        $this->redirect('mining/show?id=' . $mining->getId());
        //return $this->renderText('woot no template here');
    }

    public function executeIndex(sfWebRequest $request) {
        $this->minings = Doctrine_Core::getTable('Mining')
                ->createQuery('a')
                ->execute();

        $this->types = Doctrine_Core::getTable('Mining')->getMiningTypes();
        $this->alorithms = Doctrine_Core::getTable('Mining')->getMiningAlgorithms();
    }

    public function executeShow(sfWebRequest $request) {
        $this->mining = Doctrine_Core::getTable('Mining')->find(array($request->getParameter('id')));
        $this->forward404Unless($this->mining);
        $this->types = Doctrine_Core::getTable('Mining')->getMiningTypes();
        $this->alorithms = Doctrine_Core::getTable('Mining')->getMiningAlgorithms();

        /*
         * Stuff to show config
         */
        $started = $this->mining->getStarted();
        $complete = $this->mining->getComplete();
        if ($started == 0 and $complete == 0) {
            $this->relationships = $this->mining->getProject()->getProjectConfig();
        } else if ($started == 1 and $complete == 0) {
            $this->jobMappings = $this->mining->getJobMappings();
        }

        /**
         * JSON STUFF
         */
//        $json = array();
//        $bics = $this->mining->getBiClusters();
//
//        foreach ($bics as $bic) {
//            $json [] = $bic->getMiniRaw();
//        }
//        return $this->renderText(json_encode($json));
    }

    public function executeNew(sfWebRequest $request) {

        $project_id = $request->getParameter('project_id');
        if ($project_id == 0) {
            $this->form = new MiningForm();
        } else {
            $mining = new Mining();
            $mining->setProjectId($project_id);
            $this->form = new MiningForm($mining);
        }
    }

    public function executeCreate(sfWebRequest $request) {
        $this->forward404Unless($request->isMethod(sfRequest::POST));

        $this->form = new MiningForm();

        $this->processForm($request, $this->form);

        $this->setTemplate('new');
    }

    public function executeEdit(sfWebRequest $request) {
        $this->forward404Unless($mining = Doctrine_Core::getTable('Mining')->find(array($request->getParameter('id'))), sprintf('Object mining does not exist (%s).', $request->getParameter('id')));
        $this->form = new MiningForm($mining);
        if ($mining->getStarted() == 1) {
            $this->form->enableNameOnly();
        }
    }

    public function executeUpdate(sfWebRequest $request) {
        $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
        $this->forward404Unless($mining = Doctrine_Core::getTable('Mining')->find(array($request->getParameter('id'))), sprintf('Object mining does not exist (%s).', $request->getParameter('id')));
        $this->form = new MiningForm($mining);
        if ($mining->getStarted() == 1) {
            $this->form->enableNameOnly();
        }
        $this->processForm($request, $this->form);

        $this->setTemplate('edit');
    }

    public function executeDelete(sfWebRequest $request) {
        $request->checkCSRFProtection();

        $this->forward404Unless($mining = Doctrine_Core::getTable('Mining')->find(array($request->getParameter('id'))), sprintf('Object mining does not exist (%s).', $request->getParameter('id')));
        $mining->delete();

        $this->redirect('mining/index');
    }

    protected function processForm(sfWebRequest $request, sfForm $form) {
        $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
        if ($form->isValid()) {

            $mining = $form->save();

            $this->redirect('mining/show?id=' . $mining->getId());
        }
    }

}

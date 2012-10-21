<?php

/**
 * mining_job_mapping actions.
 *
 * @package    MineVis
 * @subpackage mining_job_mapping
 * @author     Patrick Fiaux
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class mining_job_mappingActions extends sfActions {
    public function executeIndex(sfWebRequest $request) {
        $mining_id = $request->getParameter('mining_id');
        $this->forward404Unless($mining_id);
        $this->mining = Doctrine_Core::getTable('Mining')->find(array($mining_id));
        $this->mining_job_mappings = Doctrine_Core::getTable('MiningJobMapping')
                ->findBy('mining_id', array($mining_id));

        //now also run all of them
        foreach ($this->mining_job_mappings as $job) {
            $job->runJob();
        }
        //mark job as complete
        $this->mining->setComplete(true);
        $this->mining->save();
    }

    public function executeShow(sfWebRequest $request) {
        $this->mining_job_mapping = Doctrine_Core::getTable('MiningJobMapping')->find(array($request->getParameter('id')));
        $this->forward404Unless($this->mining_job_mapping);
        $this->mining = $this->mining_job_mapping->getMining();
        $this->config = $this->mining_job_mapping->getProjectConfig();

        $this->output = $this->mining_job_mapping->runJob();

        $charm_path = './charm/';
        $this->input_file = $charm_path . 'charmInput_config_id_' . $this->config->getId() . '.asc';
        $this->output_file = $charm_path . 'charmOutput_job_id_' . $this->mining_job_mapping->getId() . '.txt';
    }

}

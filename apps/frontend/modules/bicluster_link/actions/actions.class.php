<?php

/**
 * bicluster_link actions.
 *
 * @package    MineVis
 * @subpackage bicluster_link
 * @author     Patrick Fiaux
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class bicluster_linkActions extends sfActions {

    public function executeIndex(sfWebRequest $request) {
        $this->chaining_links = Doctrine_Core::getTable('ChainingLink')
                ->createQuery('a')
                ->execute();
    }

    public function executeShow(sfWebRequest $request) {
        $this->chaining_link = Doctrine_Core::getTable('ChainingLink')->find(array($request->getParameter('id')));
        $this->forward404Unless($this->chaining_link);

        //Load data for the vis here
    }

    public function executeGetvis(sfWebRequest $request) {
        $this->chaining_links = Doctrine_Core::getTable('ChainingLink')
                ->createQuery('a')
                ->execute();
        $vis = 'visBiClusterLink';
        $this->forwardUnless($link_id = $request->getParameter('id'), 'mining', 'index');

        $bi_cluster_link = Doctrine_Core::getTable('ChainingLink')->find(array($link_id));

        //if ($request->isXmlHttpRequest()) {
            return $this->renderPartial(
                            'getvis', array('link' => $bi_cluster_link, 'visualization' => $vis)
            );
        //}
    }

}

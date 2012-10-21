<?php

/**
 * bicluster actions.
 *
 * @package    MineVis
 * @subpackage bicluster
 * @author     Patrick Fiaux
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class biclusterActions extends sfActions {

    public function executeIndex(sfWebRequest $request) {
        $this->mining_bi_clusters = Doctrine_Core::getTable('MiningBiCluster')
                ->createQuery('a')
                ->execute();
    }

    public function executeShow(sfWebRequest $request) {
        $this->mining_bi_cluster = Doctrine_Core::getTable('MiningBiCluster')->find(array($request->getParameter('id')));
        $this->forward404Unless($this->mining_bi_cluster);

        $this->mining = $this->mining_bi_cluster->getMining();
    }

    public function executeGetvis(sfWebRequest $request) {
        $vis = 'miniBiCluster';
        $this->forwardUnless($bic_id = $request->getParameter('id'), 'mining', 'index');

        $mining_bi_cluster = Doctrine_Core::getTable('MiningBiCluster')->find(array($bic_id));

        //if ($request->isXmlHttpRequest()) {
            return $this->renderPartial(
                            'getvis', array('bicluster' => $mining_bi_cluster, 'visualization' => $vis)
            );
        //}
    }

}

<?php

/**
 * ChainingLink
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    MineVis
 * @subpackage model
 * @author     Patrick Fiaux
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class ChainingLink extends BaseChainingLink
{
    /**
     * Returns JSON data of all the bics in this link to visualize it.
     * Kinda like this:
     * $json = {
     *      'target' => bic->getMiniJSON();
     *      'destinations' => [
     *         bic->getMiniJSON();
     *          ...
     *      ]
     * }
     * @return JSON returns the json data to vis this link.
     */
    public function getJSON() {
        $target = $this->getTargetBiCluster()->getMiniRaw();
        $dests = array();
        foreach( $this->getDestinations() as $dest ) {
            $dests[] = $dest->getDestinationBiCluster()->getMiniRaw();
        }
        $link = array();
        $link['type'] = 'link';
        $link['target'] = $target;
        $link['destinations'] = $dests;
        return json_encode($link);
    }
}

<?php

/**
 * Chaining form.
 *
 * @package    MineVis
 * @subpackage form
 * @author     Patrick Fiaux
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class ChainingForm extends BaseChainingForm {

    public function configure() {
        //remove junk user shouldn't see
        unset(
                $this['process_id'], $this['created_at'], $this['updated_at'], $this['expires_at'], $this['is_activated'], $this['complete'], $this['configured'], $this['started'], $this['param']
        );
        //todo remove param field
        //add fields for maxNeighbors and threshold
        $this->widgetSchema['max_neighbors'] = new sfWidgetFormInputText();
        $this->validatorSchema['max_neighbors'] = new sfValidatorNumber(array('min' => 1, 'max' => 1000));
        $this->widgetSchema['distance_threshold'] = new sfWidgetFormInputText();
        $this->validatorSchema['distance_threshold'] = new sfValidatorNumber(array('min' => 0.0001, 'max' => 1));

        $max = '5';
        $dist = '0.5';
        if (!$this->isNew()) {
            $params = json_decode($this->getObject()->getParam(), true);
            $max = $params['max_neighbors'];
            $dist = $params['distance_threshold'];
        }


        $this->setDefaults(array(
            'name' => 'chaining',
            'max_neighbors' => $max,
            'distance_threshold' => $dist,
        ));
    }

    /**
     * This is a helper for the edit form.
     * if the mining has already started only allow editing of the name by disabling
     * the other widgets
     */
    public function enableNameOnly() {
        $this->widgetSchema ['mining_id']->setAttribute('onfocus', 'this.defaultIndex=this.selectedIndex;');
        $this->widgetSchema ['mining_id']->setAttribute('onchange', 'this.selectedIndex=this.defaultIndex;');
        $this->widgetSchema ['max_neighbors']->setAttribute('disabled', 'disable');
        $this->widgetSchema ['distance_threshold']->setAttribute('disabled', 'disable');
    }

    /**
     * Override doSave to convert maxn and tres to params...
     * @param type $values
     * @return type
     */
    public function updateObject($values = null) {
        $object = parent::updateObject($values);

        $max_neighbors = $this->getValue('max_neighbors');
        $distance_threshold = $this->getValue('distance_threshold');

        $param = array('max_neighbors' => $max_neighbors, 'distance_threshold' => $distance_threshold);

        $object->setParam(json_encode($param));

        return $object;
    }

}

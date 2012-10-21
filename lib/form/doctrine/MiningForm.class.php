<?php

/**
 * Mining form.
 *
 * @package    MineVis
 * @subpackage form
 * @author     Patrick Fiaux
 * @version    SVN: $Id: sfDoctrineFormTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class MiningForm extends BaseMiningForm {

    public function configure() {
        unset(
                $this['process_id'], $this['created_at'], $this['updated_at']
                , $this['expires_at'], $this['is_activated'], $this['complete']
                , $this['started'], $this['setup'], $this['param']
                , $this['document_link_status'], $this['entity_frequency_status']
        );

        //set custom widgets for drop down fields and custom fileds
        $this->widgetSchema['min_support'] = new sfWidgetFormInputText();
        $this->widgetSchema['min_columns'] = new sfWidgetFormInputText();
        $this->widgetSchema['type'] = new sfWidgetFormChoice(array(
                    'choices' => Doctrine_Core::getTable('Mining')->getMiningTypes(),
                    'expanded' => false,
                ));
        $this->widgetSchema['algorithm'] = new sfWidgetFormChoice(array(
                    'choices' => Doctrine_Core::getTable('Mining')->getMiningAlgorithms(),
                    'expanded' => false,
                ));

        //set up validators
        $this->validatorSchema['min_support'] = new sfValidatorNumber(array('min' => 1, 'max' => 1000000));
        $this->validatorSchema['min_columns'] = new sfValidatorNumber(array('min' => 1, 'max' => 1000000));

        $params = array('min_support' => 5, 'min_columns' => 2);
        if (!$this->isNew()) {
            $params = json_decode($this->getObject()->getParam(), true);
        }

        $this->setDefaults(array(
            'name' => 'default mining',
            'min_support' => $params['min_support'],
            'min_columns' => $params['min_columns'],
        ));
    }

    /**
     * This is a helper for the edit form.
     * if the mining has already started only allow editing of the name by disabling
     * the other widgets
     */
    public function enableNameOnly() {
        $this->widgetSchema ['project_id']->setAttribute('onfocus', 'this.defaultIndex=this.selectedIndex;');
        $this->widgetSchema ['project_id']->setAttribute('onchange', 'this.selectedIndex=this.defaultIndex;');
        $this->widgetSchema ['type']->setAttribute('onfocus', 'this.defaultIndex=this.selectedIndex;');
        $this->widgetSchema ['type']->setAttribute('onchange', 'this.selectedIndex=this.defaultIndex;');
        $this->widgetSchema ['algorithm']->setAttribute('onfocus', 'this.defaultIndex=this.selectedIndex;');
        $this->widgetSchema ['algorithm']->setAttribute('onchange', 'this.selectedIndex=this.defaultIndex;');
        $this->widgetSchema ['min_support']->setAttribute('readonly', 'readonly');
        $this->widgetSchema ['min_columns']->setAttribute('readonly', 'readonly');
    }

    /**
     * Override doSave to convert params to json and save them
     * @param type $values
     * @return type
     */
    public function updateObject($values = null) {
        $object = parent::updateObject($values);

        $param = array();

        $param['min_support'] = $this->getValue('min_support');
        $param['min_columns'] = $this->getValue('min_columns');

        $object->setParam(json_encode($param));

        return $object;
    }

}

<?php

/**
 * EntityFrequency form base class.
 *
 * @method EntityFrequency getObject() Returns the current form's model object
 *
 * @package    MineVis
 * @subpackage form
 * @author     Patrick Fiaux
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseEntityFrequencyForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'              => new sfWidgetFormInputHidden(),
      'mining_id'       => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Mining'), 'add_empty' => false)),
      'entity_id'       => new sfWidgetFormInputText(),
      'entity_name'     => new sfWidgetFormInputText(),
      'entity_type'     => new sfWidgetFormInputText(),
      'bicluster_count' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'              => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'mining_id'       => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Mining'))),
      'entity_id'       => new sfValidatorInteger(),
      'entity_name'     => new sfValidatorString(array('max_length' => 255)),
      'entity_type'     => new sfValidatorString(array('max_length' => 255)),
      'bicluster_count' => new sfValidatorInteger(),
    ));

    $this->widgetSchema->setNameFormat('entity_frequency[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'EntityFrequency';
  }

}

<?php

/**
 * MiningJobMapping form base class.
 *
 * @method MiningJobMapping getObject() Returns the current form's model object
 *
 * @package    MineVis
 * @subpackage form
 * @author     Patrick Fiaux
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseMiningJobMappingForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'          => new sfWidgetFormInputHidden(),
      'mining_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Mining'), 'add_empty' => false)),
      'config_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('ProjectConfig'), 'add_empty' => false)),
      'status_code' => new sfWidgetFormInputText(),
      'start_time'  => new sfWidgetFormInputText(),
      'end_time'    => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'          => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'mining_id'   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Mining'))),
      'config_id'   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('ProjectConfig'))),
      'status_code' => new sfValidatorInteger(array('required' => false)),
      'start_time'  => new sfValidatorPass(array('required' => false)),
      'end_time'    => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('mining_job_mapping[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'MiningJobMapping';
  }

}

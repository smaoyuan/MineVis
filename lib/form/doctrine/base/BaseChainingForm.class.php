<?php

/**
 * Chaining form base class.
 *
 * @method Chaining getObject() Returns the current form's model object
 *
 * @package    MineVis
 * @subpackage form
 * @author     Patrick Fiaux
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseChainingForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'         => new sfWidgetFormInputHidden(),
      'mining_id'  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Mining'), 'add_empty' => false)),
      'name'       => new sfWidgetFormInputText(),
      'param'      => new sfWidgetFormInputText(),
      'configured' => new sfWidgetFormInputCheckbox(),
      'started'    => new sfWidgetFormInputCheckbox(),
      'complete'   => new sfWidgetFormInputCheckbox(),
      'process_id' => new sfWidgetFormInputText(),
      'created_at' => new sfWidgetFormDateTime(),
      'updated_at' => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'         => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'mining_id'  => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Mining'))),
      'name'       => new sfValidatorString(array('max_length' => 255)),
      'param'      => new sfValidatorString(array('max_length' => 255)),
      'configured' => new sfValidatorBoolean(array('required' => false)),
      'started'    => new sfValidatorBoolean(array('required' => false)),
      'complete'   => new sfValidatorBoolean(array('required' => false)),
      'process_id' => new sfValidatorInteger(array('required' => false)),
      'created_at' => new sfValidatorDateTime(),
      'updated_at' => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('chaining[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Chaining';
  }

}

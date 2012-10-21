<?php

/**
 * Visualization form base class.
 *
 * @method Visualization getObject() Returns the current form's model object
 *
 * @package    MineVis
 * @subpackage form
 * @author     Patrick Fiaux
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseVisualizationForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'          => new sfWidgetFormInputHidden(),
      'project_id'  => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Project'), 'add_empty' => false)),
      'mining_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Mining'), 'add_empty' => false)),
      'chaining_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Chaining'), 'add_empty' => false)),
      'name'        => new sfWidgetFormInputText(),
      'jsondata'    => new sfWidgetFormTextarea(),
      'created_at'  => new sfWidgetFormDateTime(),
      'updated_at'  => new sfWidgetFormDateTime(),
    ));

    $this->setValidators(array(
      'id'          => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'project_id'  => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Project'))),
      'mining_id'   => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Mining'))),
      'chaining_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Chaining'))),
      'name'        => new sfValidatorString(array('max_length' => 255)),
      'jsondata'    => new sfValidatorString(),
      'created_at'  => new sfValidatorDateTime(),
      'updated_at'  => new sfValidatorDateTime(),
    ));

    $this->widgetSchema->setNameFormat('visualization[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Visualization';
  }

}

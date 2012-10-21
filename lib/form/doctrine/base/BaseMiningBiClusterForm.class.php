<?php

/**
 * MiningBiCluster form base class.
 *
 * @method MiningBiCluster getObject() Returns the current form's model object
 *
 * @package    MineVis
 * @subpackage form
 * @author     Patrick Fiaux
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseMiningBiClusterForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'        => new sfWidgetFormInputHidden(),
      'mining_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Mining'), 'add_empty' => false)),
      'config_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('ProjectConfig'), 'add_empty' => false)),
    ));

    $this->setValidators(array(
      'id'        => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'mining_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('Mining'))),
      'config_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('ProjectConfig'))),
    ));

    $this->widgetSchema->setNameFormat('mining_bi_cluster[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'MiningBiCluster';
  }

}

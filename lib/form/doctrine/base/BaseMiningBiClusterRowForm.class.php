<?php

/**
 * MiningBiClusterRow form base class.
 *
 * @method MiningBiClusterRow getObject() Returns the current form's model object
 *
 * @package    MineVis
 * @subpackage form
 * @author     Patrick Fiaux
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseMiningBiClusterRowForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'id'           => new sfWidgetFormInputHidden(),
      'bicluster_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('MiningBiCluster'), 'add_empty' => false)),
      'row_id'       => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'id'           => new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)),
      'bicluster_id' => new sfValidatorDoctrineChoice(array('model' => $this->getRelatedModelName('MiningBiCluster'))),
      'row_id'       => new sfValidatorInteger(),
    ));

    $this->widgetSchema->setNameFormat('mining_bi_cluster_row[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'MiningBiClusterRow';
  }

}

<?php

/**
 * MiningBiCluster filter form base class.
 *
 * @package    MineVis
 * @subpackage filter
 * @author     Patrick Fiaux
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseMiningBiClusterFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'mining_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Mining'), 'add_empty' => true)),
      'config_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('ProjectConfig'), 'add_empty' => true)),
    ));

    $this->setValidators(array(
      'mining_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Mining'), 'column' => 'id')),
      'config_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('ProjectConfig'), 'column' => 'id')),
    ));

    $this->widgetSchema->setNameFormat('mining_bi_cluster_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'MiningBiCluster';
  }

  public function getFields()
  {
    return array(
      'id'        => 'Number',
      'mining_id' => 'ForeignKey',
      'config_id' => 'ForeignKey',
    );
  }
}

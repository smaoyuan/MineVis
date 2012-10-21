<?php

/**
 * MiningBiClusterCol filter form base class.
 *
 * @package    MineVis
 * @subpackage filter
 * @author     Patrick Fiaux
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseMiningBiClusterColFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'bicluster_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('MiningBiCluster'), 'add_empty' => true)),
      'col_id'       => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'bicluster_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('MiningBiCluster'), 'column' => 'id')),
      'col_id'       => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('mining_bi_cluster_col_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'MiningBiClusterCol';
  }

  public function getFields()
  {
    return array(
      'id'           => 'Number',
      'bicluster_id' => 'ForeignKey',
      'col_id'       => 'Number',
    );
  }
}

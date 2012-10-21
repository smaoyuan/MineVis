<?php

/**
 * DocumentLink filter form base class.
 *
 * @package    MineVis
 * @subpackage filter
 * @author     Patrick Fiaux
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseDocumentLinkFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'mining_id'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Mining'), 'add_empty' => true)),
      'bicluster_id' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('MiningBiCluster'), 'add_empty' => true)),
      'document_id'  => new sfWidgetFormFilterInput(array('with_empty' => false)),
    ));

    $this->setValidators(array(
      'mining_id'    => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Mining'), 'column' => 'id')),
      'bicluster_id' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('MiningBiCluster'), 'column' => 'id')),
      'document_id'  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('document_link_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'DocumentLink';
  }

  public function getFields()
  {
    return array(
      'id'           => 'Number',
      'mining_id'    => 'ForeignKey',
      'bicluster_id' => 'ForeignKey',
      'document_id'  => 'Number',
    );
  }
}

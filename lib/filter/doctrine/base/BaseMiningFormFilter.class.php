<?php

/**
 * Mining filter form base class.
 *
 * @package    MineVis
 * @subpackage filter
 * @author     Patrick Fiaux
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseMiningFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'project_id'              => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Project'), 'add_empty' => true)),
      'type'                    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'algorithm'               => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'name'                    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'param'                   => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'started'                 => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'setup'                   => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'complete'                => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'process_id'              => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'document_link_status'    => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'entity_frequency_status' => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'created_at'              => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
      'updated_at'              => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'project_id'              => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('Project'), 'column' => 'id')),
      'type'                    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'algorithm'               => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'name'                    => new sfValidatorPass(array('required' => false)),
      'param'                   => new sfValidatorPass(array('required' => false)),
      'started'                 => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'setup'                   => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'complete'                => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'process_id'              => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'document_link_status'    => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'entity_frequency_status' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'created_at'              => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
      'updated_at'              => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('mining_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Mining';
  }

  public function getFields()
  {
    return array(
      'id'                      => 'Number',
      'project_id'              => 'ForeignKey',
      'type'                    => 'Number',
      'algorithm'               => 'Number',
      'name'                    => 'Text',
      'param'                   => 'Text',
      'started'                 => 'Boolean',
      'setup'                   => 'Boolean',
      'complete'                => 'Boolean',
      'process_id'              => 'Number',
      'document_link_status'    => 'Number',
      'entity_frequency_status' => 'Number',
      'created_at'              => 'Date',
      'updated_at'              => 'Date',
    );
  }
}

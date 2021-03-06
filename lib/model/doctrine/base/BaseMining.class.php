<?php

/**
 * BaseMining
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $project_id
 * @property integer $type
 * @property integer $algorithm
 * @property string $name
 * @property string $param
 * @property boolean $started
 * @property boolean $setup
 * @property boolean $complete
 * @property integer $process_id
 * @property integer $document_link_status
 * @property integer $entity_frequency_status
 * @property Project $Project
 * @property Doctrine_Collection $JobMappings
 * @property Doctrine_Collection $BiClusters
 * @property Doctrine_Collection $Chainings
 * @property Doctrine_Collection $Visualizations
 * @property Doctrine_Collection $DocumentLinks
 * @property Doctrine_Collection $EntityFrequencies
 * 
 * @method integer             getProjectId()               Returns the current record's "project_id" value
 * @method integer             getType()                    Returns the current record's "type" value
 * @method integer             getAlgorithm()               Returns the current record's "algorithm" value
 * @method string              getName()                    Returns the current record's "name" value
 * @method string              getParam()                   Returns the current record's "param" value
 * @method boolean             getStarted()                 Returns the current record's "started" value
 * @method boolean             getSetup()                   Returns the current record's "setup" value
 * @method boolean             getComplete()                Returns the current record's "complete" value
 * @method integer             getProcessId()               Returns the current record's "process_id" value
 * @method integer             getDocumentLinkStatus()      Returns the current record's "document_link_status" value
 * @method integer             getEntityFrequencyStatus()   Returns the current record's "entity_frequency_status" value
 * @method Project             getProject()                 Returns the current record's "Project" value
 * @method Doctrine_Collection getJobMappings()             Returns the current record's "JobMappings" collection
 * @method Doctrine_Collection getBiClusters()              Returns the current record's "BiClusters" collection
 * @method Doctrine_Collection getChainings()               Returns the current record's "Chainings" collection
 * @method Doctrine_Collection getVisualizations()          Returns the current record's "Visualizations" collection
 * @method Doctrine_Collection getDocumentLinks()           Returns the current record's "DocumentLinks" collection
 * @method Doctrine_Collection getEntityFrequencies()       Returns the current record's "EntityFrequencies" collection
 * @method Mining              setProjectId()               Sets the current record's "project_id" value
 * @method Mining              setType()                    Sets the current record's "type" value
 * @method Mining              setAlgorithm()               Sets the current record's "algorithm" value
 * @method Mining              setName()                    Sets the current record's "name" value
 * @method Mining              setParam()                   Sets the current record's "param" value
 * @method Mining              setStarted()                 Sets the current record's "started" value
 * @method Mining              setSetup()                   Sets the current record's "setup" value
 * @method Mining              setComplete()                Sets the current record's "complete" value
 * @method Mining              setProcessId()               Sets the current record's "process_id" value
 * @method Mining              setDocumentLinkStatus()      Sets the current record's "document_link_status" value
 * @method Mining              setEntityFrequencyStatus()   Sets the current record's "entity_frequency_status" value
 * @method Mining              setProject()                 Sets the current record's "Project" value
 * @method Mining              setJobMappings()             Sets the current record's "JobMappings" collection
 * @method Mining              setBiClusters()              Sets the current record's "BiClusters" collection
 * @method Mining              setChainings()               Sets the current record's "Chainings" collection
 * @method Mining              setVisualizations()          Sets the current record's "Visualizations" collection
 * @method Mining              setDocumentLinks()           Sets the current record's "DocumentLinks" collection
 * @method Mining              setEntityFrequencies()       Sets the current record's "EntityFrequencies" collection
 * 
 * @package    MineVis
 * @subpackage model
 * @author     Patrick Fiaux
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseMining extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('mining');
        $this->hasColumn('project_id', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));
        $this->hasColumn('type', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));
        $this->hasColumn('algorithm', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             ));
        $this->hasColumn('name', 'string', 255, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 255,
             ));
        $this->hasColumn('param', 'string', 255, array(
             'type' => 'string',
             'notnull' => true,
             'length' => 255,
             ));
        $this->hasColumn('started', 'boolean', null, array(
             'type' => 'boolean',
             'notnull' => true,
             'default' => 0,
             ));
        $this->hasColumn('setup', 'boolean', null, array(
             'type' => 'boolean',
             'notnull' => true,
             'default' => 0,
             ));
        $this->hasColumn('complete', 'boolean', null, array(
             'type' => 'boolean',
             'notnull' => true,
             'default' => 0,
             ));
        $this->hasColumn('process_id', 'integer', null, array(
             'type' => 'integer',
             'notnull' => true,
             'default' => 0,
             ));
        $this->hasColumn('document_link_status', 'integer', 1, array(
             'type' => 'integer',
             'notnull' => true,
             'default' => 0,
             'length' => 1,
             ));
        $this->hasColumn('entity_frequency_status', 'integer', 1, array(
             'type' => 'integer',
             'notnull' => true,
             'default' => 0,
             'length' => 1,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Project', array(
             'local' => 'project_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));

        $this->hasMany('MiningJobMapping as JobMappings', array(
             'local' => 'id',
             'foreign' => 'mining_id'));

        $this->hasMany('MiningBiCluster as BiClusters', array(
             'local' => 'id',
             'foreign' => 'mining_id'));

        $this->hasMany('Chaining as Chainings', array(
             'local' => 'id',
             'foreign' => 'mining_id'));

        $this->hasMany('Visualization as Visualizations', array(
             'local' => 'id',
             'foreign' => 'mining_id'));

        $this->hasMany('DocumentLink as DocumentLinks', array(
             'local' => 'id',
             'foreign' => 'mining_id'));

        $this->hasMany('EntityFrequency as EntityFrequencies', array(
             'local' => 'id',
             'foreign' => 'mining_id'));

        $timestampable0 = new Doctrine_Template_Timestampable();
        $this->actAs($timestampable0);
    }
}
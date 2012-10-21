# Converted with pg2mysql-1.9
# Converted on Mon, 08 Aug 2011 10:25:45 -0400
# Lightbox Technologies Inc. http://www.lightbox.ca

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone="+00:00";

CREATE TABLE featuregroup (
    featuregroup_id int(11) auto_increment NOT NULL,
    subject_id int(11) NOT NULL,
    object_id int(11) NOT NULL,
    group_id int(11) NOT NULL,
    srcfeature_id int(11),
    fmin int(11),
    fmax int(11),
    strand int(11),
    is_root int(11) DEFAULT 0 NOT NULL
, PRIMARY KEY(`featuregroup_id`)
) TYPE=MyISAM;

CREATE TABLE cvtermpath (
    cvtermpath_id int(11) auto_increment NOT NULL,
    type_id int(11),
    subject_id int(11) NOT NULL,
    object_id int(11) NOT NULL,
    cv_id int(11) NOT NULL,
    pathdistance int(11)
, PRIMARY KEY(`cvtermpath_id`)
) TYPE=MyISAM;

CREATE TABLE feature (
    feature_id int(11) auto_increment NOT NULL,
    dbxref_id int(11),
    organism_id int(11) NOT NULL,
    name varchar(255),
    uniquename text NOT NULL,
    residues text,
    seqlen int(11),
    md5checksum varchar(32),
    type_id int(11) NOT NULL,
    is_analysis bool DEFAULT 0 NOT NULL,
    is_obsolete bool DEFAULT 0 NOT NULL,
    timeaccessioned timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
    timelastmodified timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
, PRIMARY KEY(`feature_id`)
) TYPE=MyISAM;

CREATE TABLE featureloc (
    featureloc_id int(11) auto_increment NOT NULL,
    feature_id int(11) NOT NULL,
    srcfeature_id int(11),
    fmin int(11),
    is_fmin_partial bool DEFAULT 0 NOT NULL,
    fmax int(11),
    is_fmax_partial bool DEFAULT 0 NOT NULL,
    strand smallint,
    phase int(11),
    residue_info text,
    locgroup int(11) DEFAULT 0 NOT NULL,
    rank int(11) DEFAULT 0 NOT NULL
, PRIMARY KEY(`featureloc_id`)
) TYPE=MyISAM;

CREATE TABLE cvterm (
    cvterm_id int(11) auto_increment NOT NULL,
    cv_id int(11) NOT NULL,
    name text NOT NULL,
    definition text,
    dbxref_id int(11) NOT NULL,
    is_obsolete int(11) DEFAULT 0 NOT NULL,
    is_relationshiptype int(11) DEFAULT 0 NOT NULL
, PRIMARY KEY(`cvterm_id`)
) TYPE=MyISAM;

CREATE TABLE db (
    db_id int(11) auto_increment NOT NULL,
    name varchar(255) NOT NULL,
    description varchar(255),
    urlprefix varchar(255),
    url varchar(255)
, PRIMARY KEY(`db_id`)
) TYPE=MyISAM;

CREATE TABLE dbxref (
    dbxref_id int(11) auto_increment NOT NULL,
    db_id int(11) NOT NULL,
    accession varchar(255) NOT NULL,
    version varchar(255) DEFAULT '',
    description text
, PRIMARY KEY(`dbxref_id`)
) TYPE=MyISAM;

CREATE TABLE feature_cvterm (
    feature_cvterm_id int(11) auto_increment NOT NULL,
    feature_id int(11) NOT NULL,
    cvterm_id int(11) NOT NULL,
    pub_id int(11) NOT NULL,
    is_not bool DEFAULT 0 NOT NULL
, PRIMARY KEY(`feature_cvterm_id`)
) TYPE=MyISAM;

CREATE TABLE feature_dbxref (
    feature_dbxref_id int(11) auto_increment NOT NULL,
    feature_id int(11) NOT NULL,
    dbxref_id int(11) NOT NULL,
    is_current bool DEFAULT 1 NOT NULL
, PRIMARY KEY(`feature_dbxref_id`)
) TYPE=MyISAM;

CREATE TABLE feature_pub (
    feature_pub_id int(11) auto_increment NOT NULL,
    feature_id int(11) NOT NULL,
    pub_id int(11) NOT NULL
, PRIMARY KEY(`feature_pub_id`)
) TYPE=MyISAM;

CREATE TABLE feature_synonym (
    feature_synonym_id int(11) auto_increment NOT NULL,
    synonym_id int(11) NOT NULL,
    feature_id int(11) NOT NULL,
    pub_id int(11) NOT NULL,
    is_current bool DEFAULT 1 NOT NULL,
    is_internal bool DEFAULT 0 NOT NULL
, PRIMARY KEY(`feature_synonym_id`)
) TYPE=MyISAM;

CREATE TABLE featureprop (
    featureprop_id int(11) auto_increment NOT NULL,
    feature_id int(11) NOT NULL,
    type_id int(11) NOT NULL,
    value text,
    rank int(11) DEFAULT 0 NOT NULL
, PRIMARY KEY(`featureprop_id`)
) TYPE=MyISAM;

CREATE TABLE pub (
    pub_id int(11) auto_increment NOT NULL,
    title text,
    volumetitle text,
    volume varchar(255),
    series_name varchar(255),
    issue varchar(255),
    pyear varchar(255),
    pages varchar(255),
    miniref varchar(255),
    uniquename text NOT NULL,
    type_id int(11) NOT NULL,
    is_obsolete bool DEFAULT 0,
    publisher varchar(255),
    pubplace varchar(255)
, PRIMARY KEY(`pub_id`)
) TYPE=MyISAM;

CREATE TABLE synonym (
    synonym_id int(11) auto_increment NOT NULL,
    name varchar(255) NOT NULL,
    type_id int(11) NOT NULL,
    synonym_sgml varchar(255) NOT NULL
, PRIMARY KEY(`synonym_id`)
) TYPE=MyISAM;

CREATE TABLE gencode (
    gencode_id int(11) NOT NULL,
    organismstr text NOT NULL
) TYPE=MyISAM;

CREATE TABLE gencode_codon_aa (
    gencode_id int(11) NOT NULL,
    codon varchar(3) NOT NULL,
    aa varchar(1) NOT NULL
) TYPE=MyISAM;

CREATE TABLE gencode_startcodon (
    gencode_id int(11) NOT NULL,
    codon varchar(3)
) TYPE=MyISAM;

CREATE TABLE acquisition (
    acquisition_id int(11) auto_increment NOT NULL,
    assay_id int(11) NOT NULL,
    protocol_id int(11),
    channel_id int(11),
    acquisitiondate timestamp DEFAULT CURRENT_TIMESTAMP,
    name text,
    uri text
, PRIMARY KEY(`acquisition_id`)
) TYPE=MyISAM;

CREATE TABLE acquisition_relationship (
    acquisition_relationship_id int(11) auto_increment NOT NULL,
    subject_id int(11) NOT NULL,
    type_id int(11) NOT NULL,
    object_id int(11) NOT NULL,
    value text,
    rank int(11) DEFAULT 0 NOT NULL
, PRIMARY KEY(`acquisition_relationship_id`)
) TYPE=MyISAM;

CREATE TABLE acquisitionprop (
    acquisitionprop_id int(11) auto_increment NOT NULL,
    acquisition_id int(11) NOT NULL,
    type_id int(11) NOT NULL,
    value text,
    rank int(11) DEFAULT 0 NOT NULL
, PRIMARY KEY(`acquisitionprop_id`)
) TYPE=MyISAM;

CREATE TABLE analysis (
    analysis_id int(11) auto_increment NOT NULL,
    name varchar(255),
    description text,
    program varchar(255) NOT NULL,
    programversion varchar(255) NOT NULL,
    algorithm varchar(255),
    sourcename varchar(255),
    sourceversion varchar(255),
    sourceuri text,
    timeexecuted timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL
, PRIMARY KEY(`analysis_id`)
) TYPE=MyISAM;

CREATE TABLE analysisfeature (
    analysisfeature_id int(11) auto_increment NOT NULL,
    feature_id int(11) NOT NULL,
    analysis_id int(11) NOT NULL,
    rawscore double precision,
    normscore double precision,
    significance double precision,
    identity double precision
, PRIMARY KEY(`analysisfeature_id`)
) TYPE=MyISAM;

CREATE TABLE analysisprop (
    analysisprop_id int(11) auto_increment NOT NULL,
    analysis_id int(11) NOT NULL,
    type_id int(11) NOT NULL,
    value text
, PRIMARY KEY(`analysisprop_id`)
) TYPE=MyISAM;

CREATE TABLE arraydesign (
    arraydesign_id int(11) auto_increment NOT NULL,
    manufacturer_id int(11) NOT NULL,
    platformtype_id int(11) NOT NULL,
    substratetype_id int(11),
    protocol_id int(11),
    dbxref_id int(11),
    name text NOT NULL,
    version text,
    description text,
    array_dimensions text,
    element_dimensions text,
    num_of_elements int(11),
    num_array_columns int(11),
    num_array_rows int(11),
    num_grid_columns int(11),
    num_grid_rows int(11),
    num_sub_columns int(11),
    num_sub_rows int(11)
, PRIMARY KEY(`arraydesign_id`)
) TYPE=MyISAM;

CREATE TABLE arraydesignprop (
    arraydesignprop_id int(11) auto_increment NOT NULL,
    arraydesign_id int(11) NOT NULL,
    type_id int(11) NOT NULL,
    value text,
    rank int(11) DEFAULT 0 NOT NULL
, PRIMARY KEY(`arraydesignprop_id`)
) TYPE=MyISAM;

CREATE TABLE assay (
    assay_id int(11) auto_increment NOT NULL,
    arraydesign_id int(11) NOT NULL,
    protocol_id int(11),
    assaydate timestamp DEFAULT CURRENT_TIMESTAMP,
    arrayidentifier text,
    arraybatchidentifier text,
    operator_id int(11) NOT NULL,
    dbxref_id int(11),
    name text,
    description text
, PRIMARY KEY(`assay_id`)
) TYPE=MyISAM;

CREATE TABLE assay_biomaterial (
    assay_biomaterial_id int(11) auto_increment NOT NULL,
    assay_id int(11) NOT NULL,
    biomaterial_id int(11) NOT NULL,
    channel_id int(11),
    rank int(11) DEFAULT 0 NOT NULL
, PRIMARY KEY(`assay_biomaterial_id`)
) TYPE=MyISAM;

CREATE TABLE assay_project (
    assay_project_id int(11) auto_increment NOT NULL,
    assay_id int(11) NOT NULL,
    project_id int(11) NOT NULL
, PRIMARY KEY(`assay_project_id`)
) TYPE=MyISAM;

CREATE TABLE assayprop (
    assayprop_id int(11) auto_increment NOT NULL,
    assay_id int(11) NOT NULL,
    type_id int(11) NOT NULL,
    value text,
    rank int(11) DEFAULT 0 NOT NULL
, PRIMARY KEY(`assayprop_id`)
) TYPE=MyISAM;

CREATE TABLE biomaterial (
    biomaterial_id int(11) auto_increment NOT NULL,
    taxon_id int(11),
    biosourceprovider_id int(11),
    dbxref_id int(11),
    name text,
    description text
, PRIMARY KEY(`biomaterial_id`)
) TYPE=MyISAM;

CREATE TABLE biomaterial_dbxref (
    biomaterial_dbxref_id int(11) auto_increment NOT NULL,
    biomaterial_id int(11) NOT NULL,
    dbxref_id int(11) NOT NULL
, PRIMARY KEY(`biomaterial_dbxref_id`)
) TYPE=MyISAM;

CREATE TABLE biomaterial_relationship (
    biomaterial_relationship_id int(11) auto_increment NOT NULL,
    subject_id int(11) NOT NULL,
    type_id int(11) NOT NULL,
    object_id int(11) NOT NULL
, PRIMARY KEY(`biomaterial_relationship_id`)
) TYPE=MyISAM;

CREATE TABLE biomaterial_treatment (
    biomaterial_treatment_id int(11) auto_increment NOT NULL,
    biomaterial_id int(11) NOT NULL,
    treatment_id int(11) NOT NULL,
    unittype_id int(11),
    value real,
    rank int(11) DEFAULT 0 NOT NULL
, PRIMARY KEY(`biomaterial_treatment_id`)
) TYPE=MyISAM;

CREATE TABLE biomaterialprop (
    biomaterialprop_id int(11) auto_increment NOT NULL,
    biomaterial_id int(11) NOT NULL,
    type_id int(11) NOT NULL,
    value text,
    rank int(11) DEFAULT 0 NOT NULL
, PRIMARY KEY(`biomaterialprop_id`)
) TYPE=MyISAM;

CREATE TABLE channel (
    channel_id int(11) auto_increment NOT NULL,
    name text NOT NULL,
    definition text NOT NULL
, PRIMARY KEY(`channel_id`)
) TYPE=MyISAM;

CREATE TABLE contact (
    contact_id int(11) auto_increment NOT NULL,
    type_id int(11),
    name varchar(255) NOT NULL,
    description varchar(255)
, PRIMARY KEY(`contact_id`)
) TYPE=MyISAM;

CREATE TABLE contact_relationship (
    contact_relationship_id int(11) auto_increment NOT NULL,
    type_id int(11) NOT NULL,
    subject_id int(11) NOT NULL,
    object_id int(11) NOT NULL
, PRIMARY KEY(`contact_relationship_id`)
) TYPE=MyISAM;

CREATE TABLE control (
    control_id int(11) auto_increment NOT NULL,
    type_id int(11) NOT NULL,
    assay_id int(11) NOT NULL,
    tableinfo_id int(11) NOT NULL,
    row_id int(11) NOT NULL,
    name text,
    value text,
    rank int(11) DEFAULT 0 NOT NULL
, PRIMARY KEY(`control_id`)
) TYPE=MyISAM;

CREATE TABLE cv (
    cv_id int(11) auto_increment NOT NULL,
    name varchar(255) NOT NULL,
    definition text
, PRIMARY KEY(`cv_id`)
) TYPE=MyISAM;

CREATE TABLE cvterm_relationship (
    cvterm_relationship_id int(11) auto_increment NOT NULL,
    type_id int(11) NOT NULL,
    subject_id int(11) NOT NULL,
    object_id int(11) NOT NULL
, PRIMARY KEY(`cvterm_relationship_id`)
) TYPE=MyISAM;

CREATE TABLE cvterm_dbxref (
    cvterm_dbxref_id int(11) auto_increment NOT NULL,
    cvterm_id int(11) NOT NULL,
    dbxref_id int(11) NOT NULL,
    is_for_definition int(11) DEFAULT 0 NOT NULL
, PRIMARY KEY(`cvterm_dbxref_id`)
) TYPE=MyISAM;

CREATE TABLE cvtermprop (
    cvtermprop_id int(11) auto_increment NOT NULL,
    cvterm_id int(11) NOT NULL,
    type_id int(11) NOT NULL,
    value varchar(255) DEFAULT '',
    rank int(11) DEFAULT 0 NOT NULL
, PRIMARY KEY(`cvtermprop_id`)
) TYPE=MyISAM;

CREATE TABLE cvtermsynonym (
    cvtermsynonym_id int(11) auto_increment NOT NULL,
    cvterm_id int(11) NOT NULL,
    synonym text NOT NULL,
    type_id int(11)
, PRIMARY KEY(`cvtermsynonym_id`)
) TYPE=MyISAM;

CREATE TABLE dbxrefprop (
    dbxrefprop_id int(11) auto_increment NOT NULL,
    dbxref_id int(11) NOT NULL,
    type_id int(11) NOT NULL,
    value varchar(255) DEFAULT '',
    rank int(11) DEFAULT 0 NOT NULL
, PRIMARY KEY(`dbxrefprop_id`)
) TYPE=MyISAM;

CREATE TABLE eimage (
    eimage_id int(11) auto_increment NOT NULL,
    eimage_data text,
    eimage_type varchar(255) NOT NULL,
    image_uri varchar(255)
, PRIMARY KEY(`eimage_id`)
) TYPE=MyISAM;

CREATE TABLE element (
    element_id int(11) auto_increment NOT NULL,
    feature_id int(11),
    arraydesign_id int(11) NOT NULL,
    type_id int(11),
    dbxref_id int(11)
, PRIMARY KEY(`element_id`)
) TYPE=MyISAM;

CREATE TABLE element_relationship (
    element_relationship_id int(11) auto_increment NOT NULL,
    subject_id int(11) NOT NULL,
    type_id int(11) NOT NULL,
    object_id int(11) NOT NULL,
    value text,
    rank int(11) DEFAULT 0 NOT NULL
, PRIMARY KEY(`element_relationship_id`)
) TYPE=MyISAM;

CREATE TABLE elementresult (
    elementresult_id int(11) auto_increment NOT NULL,
    element_id int(11) NOT NULL,
    quantification_id int(11) NOT NULL,
    signal double precision NOT NULL
, PRIMARY KEY(`elementresult_id`)
) TYPE=MyISAM;

CREATE TABLE elementresult_relationship (
    elementresult_relationship_id int(11) auto_increment NOT NULL,
    subject_id int(11) NOT NULL,
    type_id int(11) NOT NULL,
    object_id int(11) NOT NULL,
    value text,
    rank int(11) DEFAULT 0 NOT NULL
, PRIMARY KEY(`elementresult_relationship_id`)
) TYPE=MyISAM;

CREATE TABLE environment (
    environment_id int(11) auto_increment NOT NULL,
    uniquename text NOT NULL,
    description text
, PRIMARY KEY(`environment_id`)
) TYPE=MyISAM;

CREATE TABLE environment_cvterm (
    environment_cvterm_id int(11) auto_increment NOT NULL,
    environment_id int(11) NOT NULL,
    cvterm_id int(11) NOT NULL
, PRIMARY KEY(`environment_cvterm_id`)
) TYPE=MyISAM;

CREATE TABLE expression (
    expression_id int(11) auto_increment NOT NULL,
    uniquename text NOT NULL,
    md5checksum varchar(32),
    description text
, PRIMARY KEY(`expression_id`)
) TYPE=MyISAM;

CREATE TABLE expression_cvterm (
    expression_cvterm_id int(11) auto_increment NOT NULL,
    expression_id int(11) NOT NULL,
    cvterm_id int(11) NOT NULL,
    rank int(11) DEFAULT 0 NOT NULL,
    cvterm_type_id int(11) NOT NULL
, PRIMARY KEY(`expression_cvterm_id`)
) TYPE=MyISAM;

CREATE TABLE expression_cvtermprop (
    expression_cvtermprop_id int(11) auto_increment NOT NULL,
    expression_cvterm_id int(11) NOT NULL,
    type_id int(11) NOT NULL,
    value text,
    rank int(11) DEFAULT 0 NOT NULL
, PRIMARY KEY(`expression_cvtermprop_id`)
) TYPE=MyISAM;

CREATE TABLE expression_image (
    expression_image_id int(11) auto_increment NOT NULL,
    expression_id int(11) NOT NULL,
    eimage_id int(11) NOT NULL
, PRIMARY KEY(`expression_image_id`)
) TYPE=MyISAM;

CREATE TABLE expression_pub (
    expression_pub_id int(11) auto_increment NOT NULL,
    expression_id int(11) NOT NULL,
    pub_id int(11) NOT NULL
, PRIMARY KEY(`expression_pub_id`)
) TYPE=MyISAM;

CREATE TABLE expressionprop (
    expressionprop_id int(11) auto_increment NOT NULL,
    expression_id int(11) NOT NULL,
    type_id int(11) NOT NULL,
    value text,
    rank int(11) DEFAULT 0 NOT NULL
, PRIMARY KEY(`expressionprop_id`)
) TYPE=MyISAM;

CREATE TABLE feature_cvterm_dbxref (
    feature_cvterm_dbxref_id int(11) auto_increment NOT NULL,
    feature_cvterm_id int(11) NOT NULL,
    dbxref_id int(11) NOT NULL
, PRIMARY KEY(`feature_cvterm_dbxref_id`)
) TYPE=MyISAM;

CREATE TABLE feature_cvterm_pub (
    feature_cvterm_pub_id int(11) auto_increment NOT NULL,
    feature_cvterm_id int(11) NOT NULL,
    pub_id int(11) NOT NULL
, PRIMARY KEY(`feature_cvterm_pub_id`)
) TYPE=MyISAM;

CREATE TABLE feature_cvtermprop (
    feature_cvtermprop_id int(11) auto_increment NOT NULL,
    feature_cvterm_id int(11) NOT NULL,
    type_id int(11) NOT NULL,
    value text,
    rank int(11) DEFAULT 0 NOT NULL
, PRIMARY KEY(`feature_cvtermprop_id`)
) TYPE=MyISAM;

CREATE TABLE feature_expression (
    feature_expression_id int(11) auto_increment NOT NULL,
    expression_id int(11) NOT NULL,
    feature_id int(11) NOT NULL,
    pub_id int(11) NOT NULL
, PRIMARY KEY(`feature_expression_id`)
) TYPE=MyISAM;

CREATE TABLE feature_expressionprop (
    feature_expressionprop_id int(11) auto_increment NOT NULL,
    feature_expression_id int(11) NOT NULL,
    type_id int(11) NOT NULL,
    value text,
    rank int(11) DEFAULT 0 NOT NULL
, PRIMARY KEY(`feature_expressionprop_id`)
) TYPE=MyISAM;

CREATE TABLE feature_genotype (
    feature_genotype_id int(11) auto_increment NOT NULL,
    feature_id int(11) NOT NULL,
    genotype_id int(11) NOT NULL,
    chromosome_id int(11),
    rank int(11) NOT NULL,
    cgroup int(11) NOT NULL,
    cvterm_id int(11) NOT NULL
, PRIMARY KEY(`feature_genotype_id`)
) TYPE=MyISAM;

CREATE TABLE feature_phenotype (
    feature_phenotype_id int(11) auto_increment NOT NULL,
    feature_id int(11) NOT NULL,
    phenotype_id int(11) NOT NULL
, PRIMARY KEY(`feature_phenotype_id`)
) TYPE=MyISAM;

CREATE TABLE feature_pubprop (
    feature_pubprop_id int(11) auto_increment NOT NULL,
    feature_pub_id int(11) NOT NULL,
    type_id int(11) NOT NULL,
    value text,
    rank int(11) DEFAULT 0 NOT NULL
, PRIMARY KEY(`feature_pubprop_id`)
) TYPE=MyISAM;

CREATE TABLE feature_relationship (
    feature_relationship_id int(11) auto_increment NOT NULL,
    subject_id int(11) NOT NULL,
    object_id int(11) NOT NULL,
    type_id int(11) NOT NULL,
    value text,
    rank int(11) DEFAULT 0 NOT NULL
, PRIMARY KEY(`feature_relationship_id`)
) TYPE=MyISAM;

CREATE TABLE feature_relationship_pub (
    feature_relationship_pub_id int(11) auto_increment NOT NULL,
    feature_relationship_id int(11) NOT NULL,
    pub_id int(11) NOT NULL
, PRIMARY KEY(`feature_relationship_pub_id`)
) TYPE=MyISAM;

CREATE TABLE feature_relationshipprop (
    feature_relationshipprop_id int(11) auto_increment NOT NULL,
    feature_relationship_id int(11) NOT NULL,
    type_id int(11) NOT NULL,
    value text,
    rank int(11) DEFAULT 0 NOT NULL
, PRIMARY KEY(`feature_relationshipprop_id`)
) TYPE=MyISAM;

CREATE TABLE feature_relationshipprop_pub (
    feature_relationshipprop_pub_id int(11) auto_increment NOT NULL,
    feature_relationshipprop_id int(11) NOT NULL,
    pub_id int(11) NOT NULL
, PRIMARY KEY(`feature_relationshipprop_pub_id`)
) TYPE=MyISAM;

CREATE TABLE featureloc_pub (
    featureloc_pub_id int(11) auto_increment NOT NULL,
    featureloc_id int(11) NOT NULL,
    pub_id int(11) NOT NULL
, PRIMARY KEY(`featureloc_pub_id`)
) TYPE=MyISAM;

CREATE TABLE featuremap (
    featuremap_id int(11) auto_increment NOT NULL,
    name varchar(255),
    description text,
    unittype_id int(11)
, PRIMARY KEY(`featuremap_id`)
) TYPE=MyISAM;

CREATE TABLE featuremap_pub (
    featuremap_pub_id int(11) auto_increment NOT NULL,
    featuremap_id int(11) NOT NULL,
    pub_id int(11) NOT NULL
, PRIMARY KEY(`featuremap_pub_id`)
) TYPE=MyISAM;

CREATE TABLE featurepos (
    featurepos_id int(11) auto_increment NOT NULL,
    featuremap_id int(11) auto_increment NOT NULL,
    feature_id int(11) NOT NULL,
    map_feature_id int(11) NOT NULL,
    mappos double precision NOT NULL
, PRIMARY KEY(`featurepos_id`)
, PRIMARY KEY(`featuremap_id`)
) TYPE=MyISAM;

CREATE TABLE featureprop_pub (
    featureprop_pub_id int(11) auto_increment NOT NULL,
    featureprop_id int(11) NOT NULL,
    pub_id int(11) NOT NULL
, PRIMARY KEY(`featureprop_pub_id`)
) TYPE=MyISAM;

CREATE TABLE featureprop_study (
    featureprop_study_id int(11) auto_increment NOT NULL,
    featureprop_id int(11) NOT NULL,
    study_id int(11) NOT NULL
, PRIMARY KEY(`featureprop_study_id`)
) TYPE=MyISAM;

CREATE TABLE featurerange (
    featurerange_id int(11) auto_increment NOT NULL,
    featuremap_id int(11) NOT NULL,
    feature_id int(11) NOT NULL,
    leftstartf_id int(11) NOT NULL,
    leftendf_id int(11),
    rightstartf_id int(11),
    rightendf_id int(11) NOT NULL,
    rangestr varchar(255)
, PRIMARY KEY(`featurerange_id`)
) TYPE=MyISAM;

CREATE TABLE genotype (
    genotype_id int(11) auto_increment NOT NULL,
    name text,
    uniquename text NOT NULL,
    description varchar(255)
, PRIMARY KEY(`genotype_id`)
) TYPE=MyISAM;

CREATE TABLE library (
    library_id int(11) auto_increment NOT NULL,
    organism_id int(11) NOT NULL,
    name varchar(255),
    uniquename text NOT NULL,
    type_id int(11) NOT NULL
, PRIMARY KEY(`library_id`)
) TYPE=MyISAM;

CREATE TABLE library_cvterm (
    library_cvterm_id int(11) auto_increment NOT NULL,
    library_id int(11) NOT NULL,
    cvterm_id int(11) NOT NULL,
    pub_id int(11) NOT NULL
, PRIMARY KEY(`library_cvterm_id`)
) TYPE=MyISAM;

CREATE TABLE library_feature (
    library_feature_id int(11) auto_increment NOT NULL,
    library_id int(11) NOT NULL,
    feature_id int(11) NOT NULL
, PRIMARY KEY(`library_feature_id`)
) TYPE=MyISAM;

CREATE TABLE library_pub (
    library_pub_id int(11) auto_increment NOT NULL,
    library_id int(11) NOT NULL,
    pub_id int(11) NOT NULL
, PRIMARY KEY(`library_pub_id`)
) TYPE=MyISAM;

CREATE TABLE library_synonym (
    library_synonym_id int(11) auto_increment NOT NULL,
    synonym_id int(11) NOT NULL,
    library_id int(11) NOT NULL,
    pub_id int(11) NOT NULL,
    is_current bool DEFAULT 1 NOT NULL,
    is_internal bool DEFAULT 0 NOT NULL
, PRIMARY KEY(`library_synonym_id`)
) TYPE=MyISAM;

CREATE TABLE libraryprop (
    libraryprop_id int(11) auto_increment NOT NULL,
    library_id int(11) NOT NULL,
    type_id int(11) NOT NULL,
    value text,
    rank int(11) DEFAULT 0 NOT NULL
, PRIMARY KEY(`libraryprop_id`)
) TYPE=MyISAM;

CREATE TABLE magedocumentation (
    magedocumentation_id int(11) auto_increment NOT NULL,
    mageml_id int(11) NOT NULL,
    tableinfo_id int(11) NOT NULL,
    row_id int(11) NOT NULL,
    mageidentifier text NOT NULL
, PRIMARY KEY(`magedocumentation_id`)
) TYPE=MyISAM;

CREATE TABLE mageml (
    mageml_id int(11) auto_increment NOT NULL,
    mage_package text NOT NULL,
    mage_ml text NOT NULL
, PRIMARY KEY(`mageml_id`)
) TYPE=MyISAM;

CREATE TABLE organism (
    organism_id int(11) auto_increment NOT NULL,
    abbreviation varchar(255),
    genus varchar(255) NOT NULL,
    species varchar(255) NOT NULL,
    common_name varchar(255),
    `comment` text
, PRIMARY KEY(`organism_id`)
) TYPE=MyISAM;

CREATE TABLE organism_dbxref (
    organism_dbxref_id int(11) auto_increment NOT NULL,
    organism_id int(11) NOT NULL,
    dbxref_id int(11) NOT NULL
, PRIMARY KEY(`organism_dbxref_id`)
) TYPE=MyISAM;

CREATE TABLE organismprop (
    organismprop_id int(11) auto_increment NOT NULL,
    organism_id int(11) NOT NULL,
    type_id int(11) NOT NULL,
    value text,
    rank int(11) DEFAULT 0 NOT NULL
, PRIMARY KEY(`organismprop_id`)
) TYPE=MyISAM;

CREATE TABLE phendesc (
    phendesc_id int(11) auto_increment NOT NULL,
    genotype_id int(11) NOT NULL,
    environment_id int(11) NOT NULL,
    description text NOT NULL,
    type_id int(11) NOT NULL,
    pub_id int(11) NOT NULL
, PRIMARY KEY(`phendesc_id`)
) TYPE=MyISAM;

CREATE TABLE phenotype (
    phenotype_id int(11) auto_increment NOT NULL,
    uniquename text NOT NULL,
    observable_id int(11),
    attr_id int(11),
    value text,
    cvalue_id int(11),
    assay_id int(11)
, PRIMARY KEY(`phenotype_id`)
) TYPE=MyISAM;

CREATE TABLE phenotype_comparison (
    phenotype_comparison_id int(11) auto_increment NOT NULL,
    genotype1_id int(11) NOT NULL,
    environment1_id int(11) NOT NULL,
    genotype2_id int(11) NOT NULL,
    environment2_id int(11) NOT NULL,
    phenotype1_id int(11) NOT NULL,
    phenotype2_id int(11),
    pub_id int(11) NOT NULL,
    organism_id int(11) NOT NULL
, PRIMARY KEY(`phenotype_comparison_id`)
) TYPE=MyISAM;

CREATE TABLE phenotype_comparison_cvterm (
    pub_id int(11) NOT NULL,
    phenotype_comparison_cvterm_id int(11) auto_increment NOT NULL,
    phenotype_comparison_id int(11) NOT NULL,
    cvterm_id int(11) NOT NULL,
    rank int(11) DEFAULT 0 NOT NULL
, PRIMARY KEY(`phenotype_comparison_cvterm_id`)
) TYPE=MyISAM;

CREATE TABLE phenotype_cvterm (
    phenotype_cvterm_id int(11) auto_increment NOT NULL,
    phenotype_id int(11) NOT NULL,
    cvterm_id int(11) NOT NULL,
    rank int(11) DEFAULT 0 NOT NULL
, PRIMARY KEY(`phenotype_cvterm_id`)
) TYPE=MyISAM;

CREATE TABLE phenstatement (
    phenstatement_id int(11) auto_increment NOT NULL,
    genotype_id int(11) NOT NULL,
    environment_id int(11) NOT NULL,
    phenotype_id int(11) NOT NULL,
    type_id int(11) NOT NULL,
    pub_id int(11) NOT NULL
, PRIMARY KEY(`phenstatement_id`)
) TYPE=MyISAM;

CREATE TABLE phylonode (
    phylonode_id int(11) auto_increment NOT NULL,
    phylotree_id int(11) NOT NULL,
    parent_phylonode_id int(11),
    left_idx int(11) NOT NULL,
    right_idx int(11) NOT NULL,
    type_id int(11),
    feature_id int(11),
    label varchar(255),
    distance double precision
, PRIMARY KEY(`phylonode_id`)
) TYPE=MyISAM;

CREATE TABLE phylonode_dbxref (
    phylonode_dbxref_id int(11) auto_increment NOT NULL,
    phylonode_id int(11) NOT NULL,
    dbxref_id int(11) NOT NULL
, PRIMARY KEY(`phylonode_dbxref_id`)
) TYPE=MyISAM;

CREATE TABLE phylonode_organism (
    phylonode_organism_id int(11) auto_increment NOT NULL,
    phylonode_id int(11) NOT NULL,
    organism_id int(11) NOT NULL
, PRIMARY KEY(`phylonode_organism_id`)
) TYPE=MyISAM;

CREATE TABLE phylonode_pub (
    phylonode_pub_id int(11) auto_increment NOT NULL,
    phylonode_id int(11) NOT NULL,
    pub_id int(11) NOT NULL
, PRIMARY KEY(`phylonode_pub_id`)
) TYPE=MyISAM;

CREATE TABLE phylonode_relationship (
    phylonode_relationship_id int(11) auto_increment NOT NULL,
    subject_id int(11) NOT NULL,
    object_id int(11) NOT NULL,
    type_id int(11) NOT NULL,
    rank int(11),
    phylotree_id int(11) NOT NULL
, PRIMARY KEY(`phylonode_relationship_id`)
) TYPE=MyISAM;

CREATE TABLE phylonodeprop (
    phylonodeprop_id int(11) auto_increment NOT NULL,
    phylonode_id int(11) NOT NULL,
    type_id int(11) NOT NULL,
    value varchar(255) DEFAULT '',
    rank int(11) DEFAULT 0 NOT NULL
, PRIMARY KEY(`phylonodeprop_id`)
) TYPE=MyISAM;

CREATE TABLE phylotree (
    phylotree_id int(11) auto_increment NOT NULL,
    dbxref_id int(11) NOT NULL,
    name varchar(255),
    type_id int(11),
    analysis_id int(11),
    `comment` text
, PRIMARY KEY(`phylotree_id`)
) TYPE=MyISAM;

CREATE TABLE phylotree_pub (
    phylotree_pub_id int(11) auto_increment NOT NULL,
    phylotree_id int(11) NOT NULL,
    pub_id int(11) NOT NULL
, PRIMARY KEY(`phylotree_pub_id`)
) TYPE=MyISAM;

CREATE TABLE project (
    project_id int(11) auto_increment NOT NULL,
    name varchar(255) NOT NULL,
    description varchar(255) NOT NULL
, PRIMARY KEY(`project_id`)
) TYPE=MyISAM;

CREATE TABLE protocol (
    protocol_id int(11) auto_increment NOT NULL,
    type_id int(11) NOT NULL,
    pub_id int(11),
    dbxref_id int(11),
    name text NOT NULL,
    uri text,
    protocoldescription text,
    hardwaredescription text,
    softwaredescription text
, PRIMARY KEY(`protocol_id`)
) TYPE=MyISAM;

CREATE TABLE protocolparam (
    protocolparam_id int(11) auto_increment NOT NULL,
    protocol_id int(11) NOT NULL,
    name text NOT NULL,
    datatype_id int(11),
    unittype_id int(11),
    value text,
    rank int(11) DEFAULT 0 NOT NULL
, PRIMARY KEY(`protocolparam_id`)
) TYPE=MyISAM;

CREATE TABLE pub_dbxref (
    pub_dbxref_id int(11) auto_increment NOT NULL,
    pub_id int(11) NOT NULL,
    dbxref_id int(11) NOT NULL,
    is_current bool DEFAULT 1 NOT NULL
, PRIMARY KEY(`pub_dbxref_id`)
) TYPE=MyISAM;

CREATE TABLE pub_relationship (
    pub_relationship_id int(11) auto_increment NOT NULL,
    subject_id int(11) NOT NULL,
    object_id int(11) NOT NULL,
    type_id int(11) NOT NULL
, PRIMARY KEY(`pub_relationship_id`)
) TYPE=MyISAM;

CREATE TABLE pubauthor (
    pubauthor_id int(11) auto_increment NOT NULL,
    pub_id int(11) NOT NULL,
    rank int(11) NOT NULL,
    editor bool DEFAULT 0,
    surname varchar(100) NOT NULL,
    givennames varchar(100),
    suffix varchar(100)
, PRIMARY KEY(`pubauthor_id`)
) TYPE=MyISAM;

CREATE TABLE pubprop (
    pubprop_id int(11) auto_increment NOT NULL,
    pub_id int(11) NOT NULL,
    type_id int(11) NOT NULL,
    value text NOT NULL,
    rank int(11)
, PRIMARY KEY(`pubprop_id`)
) TYPE=MyISAM;

CREATE TABLE quantification (
    quantification_id int(11) auto_increment NOT NULL,
    acquisition_id int(11) NOT NULL,
    operator_id int(11),
    protocol_id int(11),
    analysis_id int(11) NOT NULL,
    quantificationdate timestamp DEFAULT CURRENT_TIMESTAMP,
    name text,
    uri text
, PRIMARY KEY(`quantification_id`)
) TYPE=MyISAM;

CREATE TABLE quantification_relationship (
    quantification_relationship_id int(11) auto_increment NOT NULL,
    subject_id int(11) NOT NULL,
    type_id int(11) NOT NULL,
    object_id int(11) NOT NULL
, PRIMARY KEY(`quantification_relationship_id`)
) TYPE=MyISAM;

CREATE TABLE quantificationprop (
    quantificationprop_id int(11) auto_increment NOT NULL,
    quantification_id int(11) NOT NULL,
    type_id int(11) NOT NULL,
    value text,
    rank int(11) DEFAULT 0 NOT NULL
, PRIMARY KEY(`quantificationprop_id`)
) TYPE=MyISAM;

CREATE TABLE stock (
    stock_id int(11) auto_increment NOT NULL,
    dbxref_id int(11),
    organism_id int(11) NOT NULL,
    name varchar(255),
    uniquename text NOT NULL,
    description text,
    type_id int(11) NOT NULL,
    is_obsolete bool DEFAULT 0 NOT NULL
, PRIMARY KEY(`stock_id`)
) TYPE=MyISAM;

CREATE TABLE stock_cvterm (
    stock_cvterm_id int(11) auto_increment NOT NULL,
    stock_id int(11) NOT NULL,
    cvterm_id int(11) NOT NULL,
    pub_id int(11) NOT NULL
, PRIMARY KEY(`stock_cvterm_id`)
) TYPE=MyISAM;

CREATE TABLE stock_dbxref (
    stock_dbxref_id int(11) auto_increment NOT NULL,
    stock_id int(11) NOT NULL,
    dbxref_id int(11) NOT NULL,
    is_current bool DEFAULT 1 NOT NULL
, PRIMARY KEY(`stock_dbxref_id`)
) TYPE=MyISAM;

CREATE TABLE stock_genotype (
    stock_genotype_id int(11) auto_increment NOT NULL,
    stock_id int(11) NOT NULL,
    genotype_id int(11) NOT NULL
, PRIMARY KEY(`stock_genotype_id`)
) TYPE=MyISAM;

CREATE TABLE stock_pub (
    stock_pub_id int(11) auto_increment NOT NULL,
    stock_id int(11) NOT NULL,
    pub_id int(11) NOT NULL
, PRIMARY KEY(`stock_pub_id`)
) TYPE=MyISAM;

CREATE TABLE stock_relationship (
    stock_relationship_id int(11) auto_increment NOT NULL,
    subject_id int(11) NOT NULL,
    object_id int(11) NOT NULL,
    type_id int(11) NOT NULL,
    value text,
    rank int(11) DEFAULT 0 NOT NULL
, PRIMARY KEY(`stock_relationship_id`)
) TYPE=MyISAM;

CREATE TABLE stock_relationship_pub (
    stock_relationship_pub_id int(11) auto_increment NOT NULL,
    stock_relationship_id int(11) NOT NULL,
    pub_id int(11) NOT NULL
, PRIMARY KEY(`stock_relationship_pub_id`)
) TYPE=MyISAM;

CREATE TABLE stockcollection (
    stockcollection_id int(11) auto_increment NOT NULL,
    type_id int(11) NOT NULL,
    contact_id int(11),
    name varchar(255),
    uniquename text NOT NULL
, PRIMARY KEY(`stockcollection_id`)
) TYPE=MyISAM;

CREATE TABLE stockcollection_stock (
    stockcollection_stock_id int(11) auto_increment NOT NULL,
    stockcollection_id int(11) NOT NULL,
    stock_id int(11) NOT NULL
, PRIMARY KEY(`stockcollection_stock_id`)
) TYPE=MyISAM;

CREATE TABLE stockcollectionprop (
    stockcollectionprop_id int(11) auto_increment NOT NULL,
    stockcollection_id int(11) NOT NULL,
    type_id int(11) NOT NULL,
    value text,
    rank int(11) DEFAULT 0 NOT NULL
, PRIMARY KEY(`stockcollectionprop_id`)
) TYPE=MyISAM;

CREATE TABLE stockprop (
    stockprop_id int(11) auto_increment NOT NULL,
    stock_id int(11) NOT NULL,
    type_id int(11) NOT NULL,
    value text,
    rank int(11) DEFAULT 0 NOT NULL
, PRIMARY KEY(`stockprop_id`)
) TYPE=MyISAM;

CREATE TABLE stockprop_pub (
    stockprop_pub_id int(11) auto_increment NOT NULL,
    stockprop_id int(11) NOT NULL,
    pub_id int(11) NOT NULL
, PRIMARY KEY(`stockprop_pub_id`)
) TYPE=MyISAM;

CREATE TABLE study (
    study_id int(11) auto_increment NOT NULL,
    contact_id int(11) NOT NULL,
    pub_id int(11),
    dbxref_id int(11),
    name text NOT NULL,
    description text
, PRIMARY KEY(`study_id`)
) TYPE=MyISAM;

CREATE TABLE study_assay (
    study_assay_id int(11) auto_increment NOT NULL,
    study_id int(11) NOT NULL,
    assay_id int(11) NOT NULL
, PRIMARY KEY(`study_assay_id`)
) TYPE=MyISAM;

CREATE TABLE studydesign (
    studydesign_id int(11) auto_increment NOT NULL,
    study_id int(11) NOT NULL,
    description text
, PRIMARY KEY(`studydesign_id`)
) TYPE=MyISAM;

CREATE TABLE studydesignprop (
    studydesignprop_id int(11) auto_increment NOT NULL,
    studydesign_id int(11) NOT NULL,
    type_id int(11) NOT NULL,
    value text,
    rank int(11) DEFAULT 0 NOT NULL
, PRIMARY KEY(`studydesignprop_id`)
) TYPE=MyISAM;

CREATE TABLE studyfactor (
    studyfactor_id int(11) auto_increment NOT NULL,
    studydesign_id int(11) NOT NULL,
    type_id int(11),
    name text NOT NULL,
    description text
, PRIMARY KEY(`studyfactor_id`)
) TYPE=MyISAM;

CREATE TABLE studyfactorvalue (
    studyfactorvalue_id int(11) auto_increment NOT NULL,
    studyfactor_id int(11) NOT NULL,
    assay_id int(11) NOT NULL,
    factorvalue text,
    name text,
    rank int(11) DEFAULT 0 NOT NULL
, PRIMARY KEY(`studyfactorvalue_id`)
) TYPE=MyISAM;

CREATE TABLE studyprop (
    studyprop_id int(11) auto_increment NOT NULL,
    study_id int(11) NOT NULL,
    type_id int(11) NOT NULL,
    value text,
    rank int(11) DEFAULT 0 NOT NULL
, PRIMARY KEY(`studyprop_id`)
) TYPE=MyISAM;

CREATE TABLE studyprop_feature (
    studyprop_feature_id int(11) auto_increment NOT NULL,
    studyprop_id int(11) NOT NULL,
    feature_id int(11) NOT NULL
, PRIMARY KEY(`studyprop_feature_id`)
) TYPE=MyISAM;

CREATE TABLE tableinfo (
    tableinfo_id int(11) auto_increment NOT NULL,
    name varchar(30) NOT NULL,
    primary_key_column varchar(30),
    is_view int(11) DEFAULT 0 NOT NULL,
    view_on_table_id int(11),
    superclass_table_id int(11),
    is_updateable int(11) DEFAULT 1 NOT NULL,
    modification_date date DEFAULT now() NOT NULL
, PRIMARY KEY(`tableinfo_id`)
) TYPE=MyISAM;

CREATE TABLE treatment (
    treatment_id int(11) auto_increment NOT NULL,
    rank int(11) DEFAULT 0 NOT NULL,
    biomaterial_id int(11) NOT NULL,
    type_id int(11) NOT NULL,
    protocol_id int(11),
    name text
, PRIMARY KEY(`treatment_id`)
) TYPE=MyISAM;


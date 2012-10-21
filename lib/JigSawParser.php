<?php

/**
 * Represents a general jigsaw entity.
 */
class Entity {
    /**
     * Predicted db id
     * @var int
     */
    var $id = 0;

    /**
     * name of entity
     * @var string
     */
    var $name = '';

    /**
     * type of entity
     * @var string
     */
    var $type = '';
}

/**
 * This represents a flexible jigsaw document. It's used for prasing.
 */
class Document {
    /**
     * predicted database id number.
     * @var int
     */
    var $id;

    /**
     * Document name (usually a file name)
     * @var string
     */
    var $name;

    /**
     * document date
     * @var string
     */
    var $date;

    /**
     * document source
     * @var string
     */
    var $source;

    /**
     * document text content
     * @var string
     */
    var $text;

    /**
     * Entities contained in this document by type.
     * @var array[type][entity]
     */
    var $entities;

    /**
     * Initializes everything to blank.
     */
    public function __construct() {
        $this->id = 0;
        $this->name = '';
        $this->date = '';
        $this->source = '';
        $this->text = '';
        $this->entities = array();
    }

    /**
     * Add entity helper, it's a dynamic 2d array.
     * 1st dimension is entity type
     * 2nd is entities for that type.
     * This function creates new arrays on the 2nd dimension as needed. Allowing
     * handling of an unknown number of entities from jigsaw (custom ones and
     * all...)
     * @param string $type type of entity
     * @param string $value entity value
     */
    public function addEntity($type, $value) {
        if ($this->entities[$type] == null) {
            $this->entities[$type] = array();
        }
        $this->entities[$type][] = $value;
    }

}

/**
 * This class helps parsing Jigsaw data files into a relational database
 * for use with MineVis.
 */
class JigsawParser {

    private $jig_file_path;
    private $documents;
    private $doc_count;
    private $entity_types;
    private $entities;

    /**
     * This jsut consturcts the stuff and init the connection.
     * @param string $db Name of the database to connect to.
     */
    public function __construct() {
        $this->jig_file_path = '';
        $this->documents = array();
        $this->entity_types = array();
        $this->entities = array();
        $this->doc_count = 0;
        $this->ent_count = 0;
    }

    /**
     * Takes in the path of a jig saw file and parses it.
     * @param string $file absolute or relative path to file to parse
     */
    public function parse($file) {
        $this->doc_count = 0;
        $this->jig_file_path = $file;
        //dont load empty file
        if ($file != null and $file != '') {
            libxml_use_internal_errors(true);
            $xml = simplexml_load_file($file);
            //make sure file is correct xml
            if ($xml) {
                //Loop through all the documents
                foreach ($xml->document as $document) {
                    //if (count($this->documents) > 2000) {
                    //return; //This is a debug option to skip out some docs
                    //}
                    $this->documents[] = $this->parseXML($document);
                }
                foreach (libxml_get_errors() as $error) {
                    echo "#\t", $error->message . ".\n";
                }
                $this->parseEntityIndexes();
            } else {
                echo "Failed loading XML\n";
                foreach (libxml_get_errors() as $error) {
                    echo "\t", $error->message . ".\n";
                }
            }
        }
    }

    /**
     * parse helper, parses a single document given the simple_xml object for a
     * document.
     * @param SimpleXMLElement $document document xml object
     */
    private function parseXML($document) {
        $elements = $document->children();
        $doc = new Document();
        $this->doc_count++;
        $doc->id = $this->doc_count;
        foreach ($elements as $element) {
            if ($element->getName() == 'docID') {
                $doc->name = (string) $element;
            } else if ($element->getName() == 'docDate') {
                $doc->date = (string) $element;
            } else if ($element->getName() == 'docSource') {
                $doc->source = (string) $element;
            } else if ($element->getName() == 'docText') {
                $doc->text = (string) $element;
            } else {
                $this->addEntity($element, $doc);
            }
        }
        return $doc;
    }

    /**
     * This function loops through the entities based on their types and
     * generates their ID indexes.
     * It used to be done on sql generation but this would require generating
     * entities sql everytime. Doing it here allows generation of other sql code
     * separately from entities.
     */
    private function parseEntityIndexes() {
        $ent_count = 0;
        foreach ($this->entities as $type => $entities) {
            $ent_count = 0;
            foreach ($entities as $entity) {
                $ent_count++;
                $entity->id = $ent_count;
            }
        }
    }

    /**
     * This parse helper handles adding entities. First it checks the type
     * and does whats needed if it's a new type. It then adds it to the document,
     * and the entity list...
     * @param SimpleXMLElement $element entity to add
     * @param Document document it should be tied too.
     */
    private function addEntity($element, $doc) {
        $type = strtolower($element->getName());
        $this->updateEntityTypes($type);
        $entity = $this->entities[$type][(string) $element];
        if ($entity == null) {
            $entity = new Entity();
            $entity->name = (string) $element;
            $this->entities[$type][$entity->name] = $entity;
        }
        $doc->addEntity($type, $entity);
    }

    /**
     * This updates the entity types with this type.
     * If it doesn't exist it create it allowing adding entities of that type
     * and later generation of a table for that type.
     * This function is what allows dynymic parsing of types and handling of
     * any custom jig saw types as opposed to only standard ones.
     * @param String $type entity type.
     */
    private function updateEntityTypes($type) {
        if (!in_array($type, $this->entity_types)) {
            $this->entity_types[] = $type;
            $this->entities[$type] = array();
        }
    }

    /**
     * Returns SQL code to instert into a database for this jig file.
     * Places the code that creates the tables at the top, first data tables then
     * many to many tables.
     * Next it adds the insert statements to populate the database.
     * Running the sql file should build the whole db without need for any
     * extra functions or scripts to be run.
     *
     * @param file_handle to write to...
     *
     */
    public function writeSQL($output) {
        echo "generating drop if exist statements...\n";
        fwrite($output, "#\n");
        fwrite($output, "# Drop tables if existing but in reverse order\n");
        fwrite($output, "#\n");
        fwrite($output, $this->getDropSQL() . "\n");

        echo "generating create table statements...\n";
        fwrite($output, "#\n");
        fwrite($output, "# Creating tables for documents and each entitytypes\n");
        fwrite($output, "#\n");
        fwrite($output, $this->getCreateDocumentSQL());
        foreach ($this->entity_types as $type) {
            fwrite($output, $this->getCreateTableSQL($type));
        }
        fwrite($output, $this->getCreateEntDocTablesSQL());
        fwrite($output, $this->getCreateEntEntTablesSQL());

        echo "generating document insert statements...\n";
        fwrite($output, "#\n");
        fwrite($output, "# Inserting data now\n");
        fwrite($output, "#\n");
        foreach ($this->documents as $doc) {
            fwrite($output, $this->getInsertDocumentSQL($doc));
        }

        echo "generating entity insert statements...\n";
        fwrite($output, $this->getInsertEntitiesSQL());
        echo "generating entity to documents insert statements...\n";
        fwrite($output, $this->getInsertDocEntSQL());
        echo "generating entity to entities insert statements...\n";
        $this->getInsertEntEntSQL($output);
    }

    /**
     * This helper puts all the drop statements at the begining.
     * It's needed because of the foreign keys, the tables have to be
     * droped in reverse order.
     * @return string SQL that drops tables in the right order
     */
    private function getDropSQL() {
        $sql = "";

        for ($outer = 0; $outer < count($this->entity_types); $outer++) {
            $typeA = $this->entity_types[$outer];
            $typeB = '';
            for ($inner = $outer + 1; $inner < count($this->entity_types); $inner++) {
                $typeB = $this->entity_types[$inner];
                $sql .= "DROP TABLE IF EXISTS " . $typeA . "_" . $typeB . ";\n";
            }
        }
        foreach ($this->entity_types as $type) {
            $sql .= "DROP TABLE IF EXISTS document_" . $type . ";\n";
            $sql .= "DROP TABLE IF EXISTS $type;\n";
        }

        $sql .= "DROP TABLE IF EXISTS document;\n";

        return $sql;
    }

    /**
     * Generates the SQL code to create the document table
     * @return string SQL for creating doc table
     */
    private function getCreateDocumentSQL() {
        $sql = "";
        //$sql .= "DROP TABLE IF EXISTS document;\n";
        $sql .= "CREATE TABLE document\n";
        $sql .= "(\n";
        $sql .= "`id` INT NOT NULL AUTO_INCREMENT,\n";
        $sql .= "`name` VARCHAR(255) NOT NULL,\n";
        $sql .= "`date` VARCHAR(255),\n";
        $sql .= "`source` VARCHAR(255),\n";
        $sql .= "`text` BLOB,\n";
        $sql .= "`text_short` VARCHAR(255),\n";
        $sql .= "`text_preview` VARCHAR(25),\n";
        $sql .= "PRIMARY KEY (id)\n";
        $sql .= ") ENGINE=INNODB;\n";
        $sql .= "\n";

        return $sql;
    }

    /**
     * Generates the SQL code to create a specific entity type table.
     * @param string $entity_type name of entity type to create table for.
     * @return string SQL for creating the table.
     */
    private function getCreateTableSQL($entity_type) {
        $sql = '';
        //$sql .= "DROP TABLE IF EXISTS $entity_type;\n";
        $sql .= "CREATE TABLE $entity_type";
        $sql .= "(\n";
        $sql .= "`id` INT NOT NULL AUTO_INCREMENT,\n";
        $sql .= "`name` VARCHAR(255) NOT NULL,\n";
        //$sql .= "`doc_id` INT NOT NULL,\n";
        $sql .= "PRIMARY KEY (id),\n";
        $sql .= "KEY doc_name (name)\n";
        //$sql .= "FOREIGN KEY (doc_id) REFERENCES document(id) ON DELETE CASCADE\n";
        $sql .= ") ENGINE=INNODB;\n";
        $sql .= "\n";
        return $sql;
    }

    /**
     * This functions creates tables for the relations ships between documents
     * and entities.
     * @return string SQL create table statements
     */
    private function getCreateEntDocTablesSQL() {
        $sql = "";
        foreach ($this->entity_types as $type) {
            $sql .= "CREATE TABLE document_" . $type;
            $sql .= "(\n";
            $sql .= "`id` INT NOT NULL AUTO_INCREMENT,\n";
            $sql .= "`doc_id` INT NOT NULL,\n";
            $sql .= "`" . $type . "_id` INT NOT NULL,\n";
            $sql .= "PRIMARY KEY (id),\n";
            $sql .= "FOREIGN KEY (doc_id) REFERENCES document(id) ON DELETE CASCADE,\n";
            $sql .= "FOREIGN KEY (" . $type . "_id) REFERENCES " . $type . "(id) ON DELETE CASCADE\n";
            $sql .= ") ENGINE=INNODB;\n";
            $sql .= "\n";
        }
        return $sql;
    }

    /**
     * This function creates the relationship tables for relationships between
     * entities and other entities of different type.
     * @return string SQL create table statements
     */
    private function getCreateEntEntTablesSQL() {
        $sql = "";
        for ($outer = 0; $outer < count($this->entity_types); $outer++) {
            $typeA = $this->entity_types[$outer];
            $typeB = '';
            for ($inner = $outer + 1; $inner < count($this->entity_types); $inner++) {
                $typeB = $this->entity_types[$inner];
                $sql .= "CREATE TABLE " . $typeA . "_" . $typeB;
                $sql .= "(\n";
                $sql .= "`id` INT NOT NULL AUTO_INCREMENT,\n";
                $sql .= "`doc_id` INT NOT NULL,\n";
                $sql .= "`" . $typeA . "_id` INT NOT NULL,\n";
                $sql .= "`" . $typeB . "_id` INT NOT NULL,\n";
                $sql .= "PRIMARY KEY (id),\n";
                $sql .= "FOREIGN KEY (doc_id) REFERENCES document(id) ON DELETE CASCADE,\n";
                $sql .= "FOREIGN KEY (" . $typeA . "_id) REFERENCES " . $typeA . "(id) ON DELETE CASCADE,\n";
                $sql .= "FOREIGN KEY (" . $typeB . "_id) REFERENCES " . $typeB . "(id) ON DELETE CASCADE\n";
                $sql .= ") ENGINE=INNODB;\n";
                $sql .= "\n";
            }
        }
        return $sql;
    }

    /**
     * Get the sql insert statement for a spesific document.
     * @param Document $doc document to get insert for
     * @return string SQL insert statement
     */
    private function getInsertDocumentSQL($doc) {
        $sql = "INSERT INTO document ";
        $sql .= " VALUES (";
        $sql .= $doc->id . ", ";
        $sql .= "'" . mysql_escape_string($doc->name) . "', ";
        $sql .= "'" . mysql_escape_string($doc->date) . "', ";
        $sql .= "'" . mysql_escape_string($doc->source) . "', ";
        $sql .= "'" . mysql_escape_string($doc->text) . "',";
        $sql .= "'" . mysql_escape_string(substr($doc->text, 0, 255)) . "',";
        $sql .= "'" . mysql_escape_string(substr($doc->text, 0, 25)) . "'";
        $sql .= ");\n";
        return $sql;
    }

    /**
     * This generates the insetions for all the entities into their
     * respective tables.
     * @return string SQL insert statements
     */
    private function getInsertEntitiesSQL() {
        $sql = "";
        foreach ($this->entities as $type => $entities) {
            foreach ($entities as $entity) {
                $sql .= "INSERT INTO " . $type . " VALUES (";
                $sql .= $entity->id . ", ";
                $sql .= "'" . mysql_escape_string($entity->name) . "'";
                $sql .= ");\n";
            }
        }
        return $sql;
    }

    /**
     * Returns the SQL for inserting the relationships (document_entitytype,
     * entity_tyeptoentitytype.
     *
     * Basically loops through the documents and entities and builds the links.
     * @return string SQL inserts
     */
    private function getInsertDocEntSQL() {
        $sql = "";
        foreach ($this->entity_types as $type) {
            $next_id = 1;
            foreach ($this->documents as $doc) {
                foreach ($doc->entities[$type] as $entity) {
                    $sql .= "INSERT INTO document_" . $type . " VALUES (";
                    $sql .= $next_id . ", ";
                    $sql .= $doc->id . ", ";
                    $sql .= $entity->id;
                    $sql .= ");\n";
                    $next_id++;
                }
            }
        }
        return $sql;
    }

    /**
     * This generates the relations from entities to entities based on documents.
     *
     * It loops through documents
     * Then through outer entities, if one isn't contained skip it.
     * Then through inner entities, if one isn't contained skip it.
     * then for each document calls a helper to handle it for typeA and B...
     * @return string Insert SQL for entity to entity relations
     */
    private function getInsertEntEntSQL($outstream = null) {

        foreach ($this->documents as $doc) {
            for ($outer = 0; $outer < count($this->entity_types); $outer++) {
                $typeA = $this->entity_types[$outer];
                if ($doc->entities[$typeA] != null) {
                    $typeB = '';
                    for ($inner = $outer + 1; $inner < count($this->entity_types); $inner++) {
                        $typeB = $this->entity_types[$inner];
                        if ($doc->entities[$typeA] != null) {
                            $this->getInsertEntEntDocSQL($typeA, $typeB, $doc, $outstream);
                        }
                    }
                }
            }
        }
    }

    /**
     * This is a helper method, given typeA typeB it extracts the entities of 2
     * types from a given document into insert statemnts
     * @param string $typeA entity type a
     * @param string $typeB entity type b
     * @param Document $doc document to extra inserts for.
     * @return string Inerst SQL Lines
     */
    private function getInsertEntEntDocSQL($typeA, $typeB, $doc, $outstream) {
        $sql = '';
        foreach ($doc->entities[$typeA] as $entA) {
            foreach ($doc->entities[$typeB] as $entB) {
                $sql = '';
                $sql .= "INSERT INTO " . $typeA . "_" . $typeB . " (";
                $sql .= "`doc_id`, `" . $typeA . "_id`, `" . $typeB . "_id`";
                $sql .= ") VALUES (";
                $sql .= $doc->id . ", ";
                $sql .= $entA->id . ", ";
                $sql .= $entB->id;
                $sql .= ");\n";
                fwrite($outstream, $sql);
            }
        }
    }

    /**
     * Returns a list of all the unique entity types found in this jig file.
     * @return array[string] list of all the different entity types
     */
    public function getEntityTypes() {
        return $this->entity_types;
    }

    /**
     * Returns a count of the number of documents parsed in jig file.
     * @return int total number of documents parsed
     */
    public function getDocumentCount() {
        return count($this->documents);
    }

}

?>

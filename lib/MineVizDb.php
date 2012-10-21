<?php

/**
 * Description of MineVisDb
 *
 * @author Patrick Fiaux
 */
class MineVisDb {

    private $db_name;
    private $connection;

    /**
     * This jsut consturcts the stuff and init the connection.
     * @param string $db Name of the database to connect to.
     */
    public function __construct($db) {
        $this->db_name = $db;
        $this->connection = mysql_connect('localhost', 'root', 'root');
        $this->setDatabase();
    }

    /**
     * This returns a list of the tables in the database.
     * @return String[] $tables Array of strings containing the tables
     */
    public function getTables() {
        $query = mysql_query('SHOW TABLES FROM ' . $this->db_name, $this->connection);
        $tables = array();
        if (mysql_num_rows($query) > 0) {
            $tables[''] = "Select a table";
            while ($row = mysql_fetch_row($query)) {
                $tables[$row[0]] = $row[0];
            }
        } else {
            $tables[] = "No tables on Database";
        }
        return $tables;
    }

    /**
     * This returns a list of field for a given table.
     * @param String $table Name of the table for which to load fields.
     * @return String[] $fields list of the fields in that table.
     */
    public function getFields($table) {
        $query = 'SHOW COLUMNS FROM `' . $table . '` FROM ' . $this->db_name;
        $result = mysql_query($query, $this->connection);
        $fields = array();
        if ($result) {
            while ($row = mysql_fetch_row($result)) {
                $fields[$row[0]] = $row[0];
            }
        }
        return $fields;
    }

    /**
     * Set the database to connect to.
     * This is a helper it just calls select db with the $db set at
     * construction time.
     */
    private function setDatabase() {
        mysql_select_db($this->db_name);
    }

    /**
     * Gets a list of the documents for this database (assuming it's a jigsaw db)
     * @return array index is doc id and content is doc name
     */
    public function getDocumentList() {
        $query = 'SELECT id, name FROM ' . $this->db_name . '.`document` ORDER BY name DESC';
        $result = mysql_query($query, $this->connection);
        $docs = array();
        if ($result) {
            while ($row = mysql_fetch_row($result)) {
                $docs[$row[0]] = $row[1];
            }
        }
        return $docs;
    }

    /**
     * Gets a list of the documents for this database (assuming it's a jigsaw db)
     * filter it by the given list tho.
     * @param array $id_list list of the documents to be selected.
     * @return array index is doc id and content is doc name
     */
    public function getDocumentListWhere($id_list) {
        $query = 'SELECT id, name FROM ' . $this->db_name . '.`document` WHERE ';
        $first = true;
        foreach ($id_list as $id) {
            if ($first == true) {
                $query .= 'id = ' . $id;
                $first = false;
            } else {
                $query .= ' OR id = ' . $id;
            }
        }
        $result = mysql_query($query, $this->connection);
        $docs = array();
        if ($result) {
            while ($row = mysql_fetch_row($result)) {
                $docs[$row[0]] = $row[1];
            }
        }
        return $docs;
    }

    /**
     * Loads a single document and returns it.
     * @param int $id id of the document to load
     * @return Array associative array with data for that document
     */
    public function getDocument($id) {
        $query = 'SELECT id, name, text FROM ' . $this->db_name . '.`document` WHERE id=' . $id . ' LIMIT 1';
        $result = mysql_query($query, $this->connection);
        $document = array();
        if ($result) {
            $document = mysql_fetch_assoc($result);
        }
        return $document;
    }

    /**
     * Looks through document text for a matching string...
     * @param string $term string to lookup in documents
     * @return a list of document names and ids of documents matching the string
     */
    public function getDocumentMatching($term) {
        $documents = array();

        $query = "SELECT id, `name` FROM document WHERE text LIKE '%" . $term . "%'";
        //echo $query;
        $result = mysql_query($query, $this->connection);

        if ($result) {
            while ($row = mysql_fetch_row($result)) {
                $documents[] = array('id' => $row[0], 'name' => $row[1]);
            }
        }

        return $documents;
    }

    /**
     * Gets documents containing an entity
     * @param int $entity_id
     * @param string $entity_type
     * @param ProjectConfig $config
     */
    public function getDocumentsContaining($entity_id, $entity_type, $config) {
        /**
         * Process:
         * - Get the MySQL field id
         * - lookup document id from relations where the entity search matches
         *   the field id of the row
         * - return the resutls in an array of the following format:
         *   { [id: #, name: string], ... }
         */
        $documents = array();

        $type_id = ($entity_type == $config->getTableA()) ? $config->getTableAxbTableAIdField() : $config->getTableAxbTableBIdField();

        $query = "SELECT d.id, d.name FROM " . $config->getTableAxb() .
                " AS l JOIN document AS d ON l.doc_id = d.id WHERE l." . $type_id . "=" . $entity_id;
        //echo $query;
        $result = mysql_query($query, $this->connection);

        if ($result) {
            while ($row = mysql_fetch_row($result)) {
                $documents[] = array('id' => $row[0], 'name' => $row[1]);
            }
        }

        return $documents;
    }

    /**
     * This queries all the entity types for a particular chaining and
     * gets all the entities like the given query.
     *
     * USEAGE: search auto complete
     *
     * @param string $term
     * @param Chaining $chaining
     * @return JSON array of [entity name, entity type]
     */
    public function getEntitiesLike($term, $chaining) {
        $entities = array();

        $types = $chaining->getLinkTypes();

        foreach ($types as $type) {
            $table = $type->getName();

            $query = 'SELECT id, `name` FROM ' . $table . " WHERE `name` LIKE '%" . $term . "%'";
            //echo $query . "\n";
            $result = mysql_query($query, $this->connection);

            if ($result) {
                while ($row = mysql_fetch_row($result)) {
                    $entities[] = array('label' => $row[1], 'category' => $table, 'id' => $row[0]);
                }
            }
        }

        return $entities;
    }

    /**
     * Gets the document Ids for a given BiCluster.
     * @param ProjectConfig $config
     * @param array $bic [0] is an array of rows [1] is an array of cols
     * @return array of document ids
     */
    public function getBiclusterDocuments($config, $bic) {
        $query = 'SELECT doc_id FROM ' . $config->getTableAxb() . ' WHERE (';
        /**
         * Add and inclusive OR for the rows
         */
        $title = $config->getTableA() . '_id';
        $list = $bic[0];
        $first = true;
        foreach ($list as $id) {
            if ($first == true) {
                $query .= $title . ' = ' . $id;
                $first = false;
            } else {
                $query .= ' OR ' . $title . ' = ' . $id;
            }
        }
        /**
         * Second part of the WHERE
         */
        $query .= ') AND (';
        /**
         * Add an inclusive OR for the cols
         */
        $title = $config->getTableB() . '_id';
        $list = $bic[1];
        $first = true;
        foreach ($list as $id) {
            if ($first == true) {
                $query .= $title . ' = ' . $id;
                $first = false;
            } else {
                $query .= ' OR ' . $title . ' = ' . $id;
            }
        }
        $query .= ')';
        $result = mysql_query($query, $this->connection);
        $docs = array();
        if ($result) {
            while ($row = mysql_fetch_row($result)) {
                $docs[$row[0]] = $row[0];
            }
        }
        return $docs;
    }

    /**
     * This loads the a relationship table and returns the relationships (pairs)
     * in the array format the charm needs for input:
     * based on the pairs
     * [
     * [#a_id1 #a_id1 #count #b_id1 #b_id2 #b_id...] //#a_id1
     * [#a_id2 #a_id2 #count #b_id1 #b_id2 #b_id...] //#a_id2
     * [#a_id... #a_id... #count #b_id1 #b_id2 #b_id...] //#a_id...
     * ]
     * @param String $table Name of the table to query from
     * @param String $a_id Name of the first id field to query
     * @param String $b_id Name of the second id field to query
     * @param File open file stream to write to
     * @return int[][] Array in the format described above.
     */
    public function getCharmArray($table, $a_id, $b_id, $file) {
        //$this->setDatabase();
        $query = 'SELECT `' . $a_id . '`, `' . $b_id . '` FROM `' . $table . '` ORDER BY `' . $a_id . '`,`' . $b_id . '` ASC';
        //echo $query;
        $result = mysql_query($query, $this->connection);
        //$mappings = array();
        if ($result) {
            $line = '';
            $count = 0;
            $last_id = 0;
            //$outer_index = 0;
            while ($row = mysql_fetch_row($result)) {
                //charm format: Aid Aid Bid_count Bid1 Bid2 ...
                if ($row[0] == $last_id) {
                    //add to current line
                    $count++;
                    $line .= ' ' . $row[1];
                } else {
                    //write last line but not a 0 0 0 line.
                    if ($last_id != 0) {
                        fwrite($file, $last_id . ' ' . $last_id . ' ' . $count . $line . "\n");
                    }
                    //start new line
                    $last_id = $row[0];
                    $count = 1;
                    $line = ' ' . $row[1];
                }
            }
        } else {
            echo 'TODO loading of data failed';
            echo 'Res count' . count($result);
        }
        return $mappings;
    }

    /**
     * This loads the a relationship table and returns the relationships (pairs)
     * in the array format the charm needs for input:
     * based on the pairs
     * File: (only items, one transacion perline)
     * [
     * [#b_id1 #b_id2 #b_id...] //#a_id1 line 0
     * [#b_id1 #b_id2 #b_id...] //#a_id2 line 1
     * [#b_id1 #b_id2 #b_id...] //#a_id... line 2
     * ]
     * File 2: (only transactions, one perline to complement above file)
     * [
     * #a_id1, //line 0
     * #a_id2, //line 1
     * #a_id... //line 2
     * ]
     * @param String $table Name of the table to query from
     * @param String $a_id Name of the first id field to query
     * @param String $b_id Name of the second id field to query
     * @param File open file stream to write items to.
     * @param File open file to write json formated transactions to
     * @return int[][] Array in the format described above.
     */
    public function getLCMInput($table, $a_id, $b_id, $file, $file_2) {
        //$this->setDatabase();
        $query = 'SELECT `' . $a_id . '`, `' . $b_id . '` FROM `' . $table . '` ORDER BY `' . $a_id . '`,`' . $b_id . '` ASC';
        //echo $query;
        $result = mysql_query($query, $this->connection);
        //$mappings = array();
        if ($result) {
            /**
             * Set up counters
             */
            $line = '';
            $last_id = 0;
            $first = true;

            /**
             * Write begining format
             */
            fwrite($file_2, '[');

            /**
             * Print all resutls
             */
            while ($row = mysql_fetch_row($result)) {
                //charm format: Aid Aid Bid_count Bid1 Bid2 ...
                if ($row[0] == $last_id) {
                    //add to current line
                    $line .= ' ' . $row[1];
                } else {
                    if ($last_id != 0) {
                        //write last line but not a 0 0 0 line.
                        fwrite($file, $line . "\n");
                        //Also Add that Id to the translation array:
                        if ($first) {
                            fwrite($file_2, $last_id);
                            $first = false;
                        } else {
                            fwrite($file_2, "," . $last_id);
                        }
                    }
                    //start new line
                    $last_id = $row[0];
                    $line = ' ' . $row[1];
                }
            }
            /**
             * Write End format
             */
            fwrite($file_2, ']');
        } else {
            echo 'TODO loading of data failed';
            echo 'Res count' . count($result);
        }
        return;
    }

    /**
     * This loads the a relationship table and returns the relationships (pairs)
     * in a simple array format:
     * [
     * [#a_id1, #b_id1]
     * [#a_id2, #b_id2]
     * [#...,  #...]
     * ]
     * @param String $table Name of the table to query from
     * @param String $a_id Name of the first id field to query
     * @param String $b_id Name of the second id field to query
     * @return int[][] Array in the format described above.
     */
    public function getSimpleArray($table, $a_id, $b_id) {
        //$this->setDatabase();
        $query = 'SELECT `' . $a_id . '`, `' . $b_id . '` FROM ' . $table . '`';
        //echo $query;
        $result = mysql_query($query, $this->connection);
        $mappings = array();

        if ($result) {
            $i = 0;
            while ($row = mysql_fetch_row($result)) {
                $mappings[$i] = array();
                $mappings[$i][] = $row[0];
                $mappings[$i][] = $row[1];
                $i++;
            }
        } else {
            echo 'TODO loading of data failed';
            echo 'Res count' . count($result);
        }
        return $mappings;
    }

    /**
     * This loads the a relationship table and returns the relationships (pairs)
     * in a simple array format:
     * [
     * [#a_id1, #b_id1]
     * [#a_id2, #b_id2]
     * [#...,  #...]
     * ]
     * @param String $table Name of the table to query from
     * @param String $a_id Name of the first id field to query
     * @param String $b_id Name of the second id field to query
     * @return bool[][] $matrix Array in the format described above.
     */
    public function getMappingMatrix($table, $a_id, $b_id) {
        //$this->setDatabase();
        $query = 'SELECT `' . $a_id . '`, `' . $b_id . '` FROM ' . $table . '`';
        //echo $query;
        $result = mysql_query($query, $this->connection);
        $matrix = array();
        if ($result) {
            while ($row = mysql_fetch_row($result)) {
                if (isset($matrix)) {
                    $matrix[$row[0]][$row[1]] = true;
                } else {
                    $matrix[$row[0]] = array();
                    $matrix[$row[0]][$row[1]] = true;
                }
            }
        } else {
            echo 'TODO loading of data failed';
            echo 'Res count' . count($result);
        }
        return $matrix;
    }

    /**
     * @deprecated
     * @param type $table
     * @param type $id
     * @param type $description
     * @return type
     */
    public function getFieldDescriptionArray($table, $id, $description) {
        $this->setDatabase();
        $query = 'SELECT `' . $id . '`, `' . $description . '` FROM `' . $table . '`';
        //echo $query;
        $descriptions = $this->runDescriptionQuery($query);
        return $descriptions;
    }

    /**
     * This gets ALL the descriptions...
     * WARNING this can overload on a big table
     * @param Project_Config $project_config
     * @return array<String>
     */
    public function getTableADescription($config, $limit = 0) {
        $query = 'SELECT `' . $config->getTableAIdField() .
                '`, `' . $config->getTableADescriptionField() .
                '` FROM `' . $config->getTableA() . '`';
        if ($limit > 0) {
            $query .= ' LIMIT ' . $limit;
        }
        //echo $query;
        $descriptions = $this->runDescriptionQuery($query);
        return $descriptions;
    }

    /**
     * This gets ALL the descriptions...
     * WARNING this can overload on a big table
     * @param Project_Config $project_config
     * @return array<String>
     */
    public function getTableBDescription($config, $limit = 0) {
        $query = 'SELECT `' . $config->getTableBIdField() .
                '`, `' . $config->getTableBDescriptionField() .
                '` FROM `' . $config->getTableB() . '`';
        if ($limit > 0) {
            $query .= ' LIMIT ' . $limit;
        }
        //echo $query;
        $descriptions = $this->runDescriptionQuery($query);
        return $descriptions;
    }

    /**
     * Get table description but only the ones needed.
     * @param ProjectConfig $config Config of table A to get description for
     * @param array $where which entires to get descriptions for
     * @return array list of descriptions
     */
    public function getTableADescriptionWhere($config, $where) {
        $query = 'SELECT `' . $config->getTableAIdField() .
                '`, `' . $config->getTableADescriptionField() .
                '` FROM `' . $config->getTableA() . '` ' .
                'WHERE ' . $this->getWhereString($config->getTableAIdField(), $where);
        //echo "\n" .  $query . "\n";
        $descriptions = $this->runDescriptionQuery($query);
        return $descriptions;
    }

    /**
     * Get table description but only the ones needed.
     * @param ProjectConfig $config Config of table B to get description for
     * @param array $where which entires to get descriptions for
     * @return array list of descriptions
     */
    public function getTableBDescriptionWhere($config, $where) {
        $query = 'SELECT `' . $config->getTableBIdField() .
                '`, `' . $config->getTableBDescriptionField() .
                '` FROM `' . $config->getTableB() . '` ' .
                'WHERE ' . $this->getWhereString($config->getTableBIdField(), $where);
        //echo $query;
        $descriptions = $this->runDescriptionQuery($query);
        return $descriptions;
    }

    /**
     * Get the count of the entities in a specific table
     * @return int total number of entities in that table
     */
    public function getTableEntityCount($table) {
        $query = 'SELECT COUNT(*) FROM ' . $table;
        $result = mysql_query($query, $this->connection);
        if ($result) {
            $row = mysql_fetch_row($result);
            //var_dump($row);
            return $row[0];
        } else {
            return 0;
        }
    }

    /**
     * This returns the list of entities. but to avoid loading them all into memory
     * it basically returns the resource to use as an interator to fetch rows
     * @param string $table table to get entities for
     * @return resource mysql resource
     */
    public function getTableEntityIterator($table) {
        $query = 'SELECT * FROM ' . $table;
        $result = mysql_query($query, $this->connection);

        return $result;
    }

    /**
     * Turns a list of ids into an SQL where clause.
     * @param string $id_field Field we're looking to match
     * @param array(int) $id_list list of the ids
     * @return string where close
     */
    public function getWhereString($id_field, $id_list) {
        $where = '';
        $first = true;
        foreach ($id_list as $id) {
            if ($first) {
                $first = false;
                $where .= $id_field . '=' . $id;
            } else {
                $where .= ' OR ' . $id_field . '=' . $id;
            }
        }
        return $where;
    }

    /**
     * helper to avoid repeating too much code.
     * @param String $query SQL query
     * @return Array([INT,String])
     */
    private function runDescriptionQuery($query) {
        $result = mysql_query($query, $this->connection);
        $descriptions = array();

        if ($result) {
            $i = 0;
            while ($row = mysql_fetch_row($result)) {
                $descriptions[$row[0]] = array();
                $descriptions[$row[0]][] = $row[0];
                $descriptions[$row[0]][] = $row[1];
                $i++;
            }
        } else {
            echo 'loading of data failed <br>' . "\n";
            echo $query . "\n";
            echo '<br>Res count' . count($result) . "\n";
        }
        return $descriptions;
    }

}

?>

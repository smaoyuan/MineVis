<?php

/**
 * This class allows for connection to an external database
 * through doctrine, this way the queries are still counted in the performance
 * stats and all...
 *
 * @author Pat
 */
class ExternalDb {

    /**
     * External Database name
     * @var string
     */
    private $db_name;

    /**
     * Connection to the external database
     * @var sfMySQLDatabase
     */
    private $connection;

    /**
     * This jsut consturcts the stuff and init the connection.
     * @param string $db Name of the database to connect to.
     */
    public function __construct($db, $usr, $pass) {
        $this->db_name = $db;
        //echo $db;

        $params = array();
        $params['database'] = $db;
        $params['username'] = $usr;
        $params['password'] = $pass;

        $this->connection = new sfMySQLDatabase($params);
        $this->connection->connect();

        //var_dump($this->connection->getConnection());
    }

    public function getDatabase() {
        return $this->connection;
    }

    public function getConnection() {
        if ($this->connection!= null) {
            return $this->connection->getConnection();
        } else {
            return 'connection missing';
        }
    }

    public function getName() {
        return $this->db_name;
    }

}

?>

<?php

namespace Models;


use Exception;
use Utils\App;
use Utils\Database;

abstract class BaseModel {

    protected $db;

    abstract public function up();
    abstract public function down();

    public function __construct() {
        $this->db = App::resolve(Database::class);
    }


    /**
     * Executes a database query.
     * @param string $sql The SQL query
     * @param array $data The data to bind to the query (for prepared statements)
     * @throws Exception
     * @return mixed The query result
     */
    protected function query(string $sql)
    {
        $this->db->query($sql);
    }

    // Optional altering method
    public function alter(string $alteration)
    {
        // Code for altering the table (if needed)
        $this->query("ALTER TABLE " . static::TABLE_NAME . " $alteration"); // Reminder: Use with caution
    }
}

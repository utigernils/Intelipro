<?php
class Project {
    private $db_conn;
    public function __construct($db_conn) {
        $this->db_conn = $db_conn->getConnection();
    }
    public function get($id = null, $orderBy = null, $desc = false) {
        $allowedFields = ['id', 'state', null];
        
        if (!in_array($orderBy, $allowedFields)) {
            return false;
        }

        if (is_null($id)) {
            $sql = "SELECT * FROM projects";

            if (!is_null($orderBy)) {
                $sql .= " ORDER BY " . $orderBy;
                if ($desc) {
                    $sql .= " DESC";
                }
            }
        } else {
            $sql = "SELECT * FROM projects WHERE id = '$id'";
        }
        
        $result = $this->db_conn->query($sql);
        $projects = array();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $projects[] = $row;
        }

        return $projects;
    }

}
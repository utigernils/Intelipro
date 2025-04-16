<?php
class Project {
    private $db_conn;
    public function __construct($db_conn) {
        $this->db_conn = $db_conn->getConnection();
    }

    public function create($name, $isActive, $currentExpo = null, $currentQuiz = null) {
        $sql = "INSERT INTO console (currentExpo, currentQuiz, name, isActive) VALUES (:currentExpo, :currentQuiz, :name, :isActive)";
        $stmt = $this->db_conn->prepare($sql);
        
        $stmt->bindValue(':currentExpo', $currentExpo);
        $stmt->bindValue(':currentQuiz', $currentQuiz);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':isActive', $isActive);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function get($id = null, $orderBy = null, $desc = false) {
        $allowedFields = ['currentExpo', 'currentQuiz', 'name', 'isActive', null];
        
        if (!in_array($orderBy, $allowedFields)) {
            return false;
        }

        if (is_null($id)) {
            $sql = "SELECT * FROM console";

            if (!is_null($orderBy)) {
                $sql .= " ORDER BY " . $orderBy;
                if ($desc) {
                    $sql .= " DESC";
                }
            }
        } else {
            $sql = "SELECT * FROM console WHERE id = '$id'";
        }
        
        $result = $this->db_conn->query($sql);
        $consoles = array();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $consoles[] = $row;
        }

        return $consoles;
    }

    public function set($id, $field, $value) {
        $allowedFields = ['currentExpo', 'currentQuiz', 'name', 'isActive', null];
        
        if (!in_array($field, $allowedFields)) {
            return false;
        }

        $sql = "UPDATE Console SET $field = :value WHERE id = :id";
        $stmt = $this->db_conn->prepare($sql);
        
        $stmt->bindValue(':value', $value);
        $stmt->bindValue(':id', $id);

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function delete($id) {
        $sql = "SELECT id FROM Console WHERE id = :id";
        $stmt = $this->db_conn->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();

        if ($stmt && $stmt->rowCount() > 0) {
            $deleteSql = "DELETE FROM Console WHERE id = :id";
            $deleteStmt = $this->db_conn->prepare($deleteSql);
            $deleteStmt->bindValue(':id', $id);
            $deleteStmt->execute();
            return true;
        }

        return false;
    }
}
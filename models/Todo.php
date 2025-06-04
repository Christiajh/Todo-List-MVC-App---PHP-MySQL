<?php
require_once(__DIR__ . '/../config.php');

class Todo {
    private $conn;

    public function __construct() {
        $this->conn = getConnection();
    }

    public function getAllTodos() {
        $result = $this->conn->query("SELECT * FROM todo ORDER BY created_at DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function createTodo($activity) {
        $stmt = $this->conn->prepare("INSERT INTO todo (activity) VALUES (?)");
        $stmt->bind_param("s", $activity);
        $stmt->execute();
    }

    public function updateTodo($id, $activity, $status) {
        $stmt = $this->conn->prepare("UPDATE todo SET activity = ?, status = ? WHERE id = ?");
        $stmt->bind_param("sii", $activity, $status, $id);
        $stmt->execute();
    }

    public function deleteTodo($id) {
        $stmt = $this->conn->prepare("DELETE FROM todo WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
}

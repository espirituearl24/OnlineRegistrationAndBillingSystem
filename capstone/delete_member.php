<?php
require 'dbconnection.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // Fetch the old image path
        $query = "SELECT image_path FROM team_members WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Delete the image file if it exists
        if ($result && !empty($result['image_path']) && file_exists($result['image_path'])) {
            unlink($result['image_path']);
        }

        // Delete the record
        $query = "DELETE FROM team_members WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->execute([':id' => $id]);

        // Redirect after deletion
        header("Location: useradmin.php");
        exit;
    } catch (PDOException $e) {
        die("Error deleting team member: " . $e->getMessage());
    }
}
?>

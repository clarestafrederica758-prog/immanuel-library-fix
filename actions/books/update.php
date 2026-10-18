<?php
if (isset($_POST['update']) && $_SERVER['REQUEST_METHOD'] === "POST") {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $isbn = $_POST['isbn'];
    $year = $_POST['year'];
    $stock = $_POST['stock'];
    $categoryId = $_POST['category_id'];
    $description = $_POST['description'];
    $authorIds = isset($_POST['author_ids']) ? $_POST['author_ids'] : [];

    print_r($_POST);
}

?><?php
require_once __DIR__ . "/../../config/database;.php";

if (isset($_POST['update']) && $_SERVER['REQUEST_METHOD'] === "POST") {
try {
    $pdo->beginTransaction();

    $id = htmlspecialchars($_POST['id']);
    $title = htmlspecialchars($_POST['title']);
    $isbn = htmlspecialchars($_POST['isbn']);
    $year = htmlspecialchars($_POST['year']);
    $stock = htmlspecialchars($_POST['stock']);
    $categoryId = htmlspecialchars($_POST['category_id']);
    $description = htmlspecialchars($_POST['description']);


    $query = "UPDATE books SET title = :title, isbn = :isbn,
            year = :year, stock = :stock, category_id = :category_id,
            description = :description WHERE id = :id";

    $stmt = $pdo->prepare($query);            
    $stmt->execute([
        'title' => $title,
        'isbn' => $isbn,
        'year' => $year,
        'stock' => $stock,
        'category_id' => $categoryId,
        'description' => $description,
        'id' => $id
    ]);

    $query = "DELETE FROM book_author WHERE book_id = :book_id";
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'book_id' => $id
    ]);

    $query = "INSERT INTO book_author (book_id, author_id) VALUES (:book_id, :author_id)";
    $stmt = $pdo->prepare($query);
    $authorIds = isset($_POST['author_ids']) ? $_POST['author_ids'] : [];

    foreach ($authorIds as $authorId) {
        $stmt->execute([
            'book_id' => $id,
            'author_id' => htmlspecialchars($authorId)
        ]);
    }

    $pdo->commit();
    header('Location: ../../pages/books/index.php');

    print_r($_POST);
    } catch (PDOException $e) {
        $pdo->rollBack();
        throw $e;
    }
}

?>

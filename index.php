<?php
require_once "db.php";

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

$error = "";
$success = "";
$editBook = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";

    if ($action === "save") {
        $id = (int)($_POST["id"] ?? 0);
        $title = trim($_POST["title"] ?? "");
        $author = trim($_POST["author"] ?? "");
        $category = trim($_POST["category"] ?? "");
        $isbn = trim($_POST["isbn"] ?? "");
        $total = filter_var($_POST["total_copies"] ?? null, FILTER_VALIDATE_INT);
        $available = filter_var($_POST["available_copies"] ?? null, FILTER_VALIDATE_INT);

        if ($title === "" || $author === "" || $category === "" || $isbn === "" ||
            $total === false || $available === false || $total < 0 ||
            $available < 0 || $available > $total) {
            $error = "Enter all fields correctly. Available copies cannot exceed total copies.";
        } else {
            try {
                if ($id > 0) {
                    $stmt = $pdo->prepare("UPDATE books SET title=?, author=?, category=?, isbn=?, total_copies=?, available_copies=? WHERE id=?");
                    $stmt->execute([$title, $author, $category, $isbn, $total, $available, $id]);
                    $success = "Book record updated successfully.";
                } else {
                    $stmt = $pdo->prepare("INSERT INTO books (title, author, category, isbn, total_copies, available_copies) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$title, $author, $category, $isbn, $total, $available]);
                    $success = "Book added successfully.";
                }
            } catch (PDOException $ex) {
                $error = "Could not save the book. Check that the ISBN is unique.";
            }
        }
    }

    if ($action === "delete") {
        $id = (int)($_POST["id"] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare("DELETE FROM books WHERE id=?");
            $stmt->execute([$id]);
            $success = "Book deleted successfully.";
        }
    }
}

if (isset($_GET["edit"])) {
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id=?");
    $stmt->execute([(int)$_GET["edit"]]);
    $editBook = $stmt->fetch();
}

$search = trim($_GET["search"] ?? "");
if ($search !== "") {
    $term = "%$search%";
    $stmt = $pdo->prepare("SELECT * FROM books WHERE title LIKE ? OR author LIKE ? OR category LIKE ? OR isbn LIKE ? ORDER BY id DESC");
    $stmt->execute([$term, $term, $term, $term]);
    $books = $stmt->fetchAll();
} else {
    $books = $pdo->query("SELECT * FROM books ORDER BY id DESC")->fetchAll();
}

$totalTitles = (int)$pdo->query("SELECT COUNT(*) FROM books")->fetchColumn();
$totalCopies = (int)$pdo->query("SELECT COALESCE(SUM(total_copies),0) FROM books")->fetchColumn();
$availableCopies = (int)$pdo->query("SELECT COALESCE(SUM(available_copies),0) FROM books")->fetchColumn();
$avgCopies = (float)$pdo->query("SELECT COALESCE(AVG(total_copies),0) FROM books")->fetchColumn();

$form = $editBook ?: ["id"=>"", "title"=>"", "author"=>"", "category"=>"", "isbn"=>"", "total_copies"=>"", "available_copies"=>""];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Library Book Management</title>
<style>
*{box-sizing:border-box}body{margin:0;background:#f3f5f9;color:#202938;font-family:Arial,sans-serif}
header{background:#183b56;color:white;padding:25px max(5%,calc((100% - 1150px)/2))}
header h1{margin:0 0 7px;font-size:27px}header p{margin:0;color:#d6e5ef}
main{max-width:1150px;margin:25px auto;padding:0 18px}
.stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:22px}
.card,.panel{background:white;border-radius:10px;padding:18px;box-shadow:0 3px 14px #1b355010}
.card span{display:block;color:#667085;font-size:13px}.card strong{display:block;font-size:25px;margin-top:8px;color:#183b56}
.layout{display:grid;grid-template-columns:330px 1fr;gap:18px}.panel h2{margin:0 0 16px;font-size:19px}
label{display:block;font-size:13px;font-weight:bold;margin:12px 0 6px}
input{width:100%;padding:10px;border:1px solid #ccd4df;border-radius:6px;font-size:14px}
button,.btn{border:0;border-radius:6px;padding:10px 13px;background:#1976a5;color:white;cursor:pointer;text-decoration:none;font-size:13px}
button:hover,.btn:hover{background:#125b80}.secondary{background:#667085}.danger{background:#bd3e42}
.actions{display:flex;gap:6px;align-items:center}.actions form{margin:0}
table{width:100%;border-collapse:collapse;font-size:13px}th,td{text-align:left;padding:11px 8px;border-bottom:1px solid #e8edf2}th{background:#f7f9fb;color:#526170}
.search{display:flex;gap:8px;margin-bottom:13px}.search input{flex:1}
.notice{padding:11px;border-radius:6px;margin-bottom:14px}.ok{background:#e5f5eb;color:#176b37}.err{background:#fdeaea;color:#9c2525}
.muted{color:#667085;font-size:12px}
@media(max-width:850px){.layout{grid-template-columns:1fr}.stats{grid-template-columns:repeat(2,1fr)}.tablewrap{overflow-x:auto}}
</style>
</head>
<body>
<header><h1>Library Book Management System</h1><p>Manage book records, search the catalogue and monitor available copies.</p></header>
<main>
<?php if($success): ?><div class="notice ok"><?=e($success)?></div><?php endif; ?>
<?php if($error): ?><div class="notice err"><?=e($error)?></div><?php endif; ?>

<section class="stats">
<div class="card"><span>Book titles</span><strong><?=$totalTitles?></strong></div>
<div class="card"><span>Total copies</span><strong><?=$totalCopies?></strong></div>
<div class="card"><span>Available copies</span><strong><?=$availableCopies?></strong></div>
<div class="card"><span>Average copies per title</span><strong><?=number_format($avgCopies,2)?></strong></div>
</section>

<div class="layout">
<section class="panel">
<h2><?=$editBook ? "Update Book" : "Add a Book"?></h2>
<form method="post">
<input type="hidden" name="action" value="save">
<input type="hidden" name="id" value="<?=e($form["id"])?>">
<label>Book title</label><input name="title" maxlength="150" required value="<?=e($form["title"])?>">
<label>Author</label><input name="author" maxlength="120" required value="<?=e($form["author"])?>">
<label>Category</label><input name="category" maxlength="80" required value="<?=e($form["category"])?>">
<label>ISBN</label><input name="isbn" maxlength="20" required value="<?=e($form["isbn"])?>">
<label>Total copies</label><input type="number" name="total_copies" min="0" required value="<?=e($form["total_copies"])?>">
<label>Available copies</label><input type="number" name="available_copies" min="0" required value="<?=e($form["available_copies"])?>">
<br><br><button type="submit"><?=$editBook ? "Save Changes" : "Add Book"?></button>
<?php if($editBook): ?> <a class="btn secondary" href="index.php">Cancel</a><?php endif; ?>
</form>
<p class="muted">ISBN must be unique. Available copies must not exceed total copies.</p>
</section>

<section class="panel">
<h2>Book Catalogue</h2>
<form class="search" method="get">
<input name="search" placeholder="Search title, author, category or ISBN" value="<?=e($search)?>">
<button type="submit">Search</button><a class="btn secondary" href="index.php">Clear</a>
</form>
<div class="tablewrap">
<table>
<thead><tr><th>ID</th><th>Title / Author</th><th>Category</th><th>ISBN</th><th>Copies</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach($books as $book): ?>
<tr>
<td><?=e($book["id"])?></td>
<td><strong><?=e($book["title"])?></strong><br><span class="muted"><?=e($book["author"])?></span></td>
<td><?=e($book["category"])?></td><td><?=e($book["isbn"])?></td>
<td><?=e($book["available_copies"])?> / <?=e($book["total_copies"])?></td>
<td><div class="actions">
<a class="btn" href="?edit=<?=(int)$book["id"]?>">Edit</a>
<form method="post" onsubmit="return confirm('Delete this book record?');">
<input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?=(int)$book["id"]?>">
<button class="danger" type="submit">Delete</button>
</form>
</div></td>
</tr>
<?php endforeach; ?>
<?php if(!$books): ?><tr><td colspan="6">No matching books found.</td></tr><?php endif; ?>
</tbody></table>
</div>
</section>
</div>
</main>
</body></html>

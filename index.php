<?php 
include('layout/header.php'); 

$host = 'sql11.freesqldatabase.com';
$database = 'sql11704334';
$username = 'sql11704334';
$password = '3MmRgAY1P9';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}


if(isset($_POST['add-books'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $genre = $_POST['genre'];
    $publication_year = $_POST['publication_year'];

    try {
        $query = "INSERT INTO books (title, author, genre, publication_year) VALUES (:title, :author, :genre, :publication_year)";
        $statement = $pdo->prepare($query);
        $statement->execute(array(
            ':title' => $title,
            ':author' => $author,
            ':genre' => $genre,
            ':publication_year' => $publication_year
        ));
        echo 'Book added successfully!';
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
}

if(isset($_POST['edit-books'])) {
    $book_id = $_POST['book_id'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $genre = $_POST['genre'];
    $publication_year = $_POST['publication_year'];

    try {
        $query = "UPDATE books SET title = :title, author = :author, genre = :genre, publication_year = :publication_year WHERE id = :book_id";
        $statement = $pdo->prepare($query);
        $statement->execute(array(
            ':book_id' => $book_id,
            ':title' => $title,
            ':author' => $author,
            ':genre' => $genre,
            ':publication_year' => $publication_year
        ));
        
        echo 'Book updated successfully!';
        exit();
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
}

if(isset($_POST['delete-book'])) {
    $book_id = $_POST['book_id'];

    try {
        $query = "DELETE FROM books WHERE id = :book_id";
        $statement = $pdo->prepare($query);
        $statement->execute(array(':book_id' => $book_id));
        
        echo '<div class="alert alert-success" role="alert">Book deleted successfully!</div>';        
    } catch(PDOException $e) {
        
        echo "Error: " . $e->getMessage();
        exit();
    }
}
?>
<h1 class="text-center pt-3 mb-4">Crud In PHP</h1>
<div class="container pt-4">
    <div class="mb-4" style="display: flex; justify-content:space-between;align-items:center">
        <h3>All Books</h3>
        <div id="alertContainer"></div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">Add Books</button>
    </div>
    <table class="table table-bordered table-striped table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>TITLE</th>
                <th>AUTHOR</th> 
                <th>GENRE</th>
                <th>PUBLICATION YEAR</th>
                <th>ACTIONS</th>
            </tr>
        </thead>
        <tbody>
        <?php 
            $query = "SELECT * FROM `books`";

            try {
                $statement = $pdo->query($query);
                while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    echo "<td>{$row['id']}</td>";
                    echo "<td>{$row['title']}</td>";
                    echo "<td>{$row['author']}</td>";
                    echo "<td>{$row['genre']}</td>";
                    echo "<td>{$row['publication_year']}</td>";
                    echo "<td>
                    <button type='button' class='btn btn-primary btn-sm' onclick='populateForm(".json_encode($row).")'>Edit</button>

                    <button type='button' onclick='confirmDelete({$row['id']})' class='btn btn-danger btn-sm'>Delete</button>
                  </td>";
                    echo "</tr>";
                }
            } catch(PDOException $e) {
                die("Query failed: " . $e->getMessage());
            }
            ?>
        </tbody>
    </table>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Add a new Book record</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                <form action="" method="POST" id="bookForm">
                        <input type="hidden" name="book_id" id="book_id">
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" name="title" id="title" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="author">Author</label>
                            <input type="text" name="author" id="author" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="genre">Genre</label>
                            <input type="text" name="genre" id="genre" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="publication_year">Publication Year</label>
                            <input type="date" name="publication_year" id="publication_year" class="form-control">
                        </div>
                        
                        <div style="display: flex; justify-content:space-between;align-items:center">
                            <input type="submit" value="Add Book" name="add-books" class="btn btn-primary mt-4">

                            <input type="submit" value="Update Books" name="edit-books" class="btn btn-primary mt-4">
                        </div>
                    </form>
                </div>            
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
function populateForm(row) {
    document.getElementById('book_id').value = row.id;
    document.getElementById('title').value = row.title;
    document.getElementById('author').value = row.author;
    document.getElementById('genre').value = row.genre;
    document.getElementById('publication_year').value = row.publication_year;
    $('#exampleModal').modal('show'); 
}

function handleResponse(response) {
    if (response.success) {
        $('#alertContainer').html('<div class="alert alert-success" role="alert">' + response.message + '</div>');
    } else {
        $('#alertContainer').html('<div class="alert alert-danger" role="alert">' + response.message + '</div>');
    }
}

function addBook() {
    $.ajax({
        type: 'POST',
        url: 'index.php',
        data: $('#bookForm').serialize() + '&add-books=true',
        success: function(response) {            
            $('#alertContainer').html('<div class="alert alert-success" role="alert">' + response + '</div>');
        },
        error: function(xhr, status, error) {                
            alert("Error: " + xhr.responseText);
        }
    });
}


function editBook() {
    $.ajax({
        type: 'POST',
        url: 'index.php',
        data: $('#bookForm').serialize() + '&edit-books=true',
        success: function(response) {                
            $('#alertContainer').html('<div class="alert alert-success" role="alert">' + response + '</div>');
        },
        error: function(xhr, status, error) {                
            alert("Error: " + xhr.responseText);
        }
    });
}

function confirmDelete(id) {
    if(confirm("Are you sure you want to delete this book?")) {     
        $.ajax({
            type: 'POST',
            url: 'index.php',
            data: { 'delete-book': true, 'book_id': id },
            success: function(response) {                
                $('#alertContainer').html('<div class="alert alert-success" role="alert">Book deleted successfully!</div>'); 
                $('#row_' + id).remove(); 
            },
            error: function(xhr, status, error) {                
                alert("Error: " + xhr.responseText);
            }
        });
    }
}

</script>
<?php include('layout/footer.php'); ?>

<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $user_id = $_SESSION['user_id'] ?? 0;
    $book_id = $_POST['book_id'];
    $title = $_POST['title'];
    $price = $_POST['price'];

    $stmt = $pdo->prepare("INSERT INTO carts (user_id, book_id, title, price, quantity) 
                           VALUES (?, ?, ?, ?, 1) 
                           ON DUPLICATE KEY UPDATE quantity = quantity + 1");
    $stmt->execute([$user_id, $book_id, $title, $price]);

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    if (isset($_SESSION['cart'][$book_id])) {
        $_SESSION['cart'][$book_id]['quantity']++;
    } else {
        $_SESSION['cart'][$book_id] = [
            'title' => $title,
            'price' => $price,
            'quantity' => 1
        ];
    }

    echo json_encode(['success' => true]);
    exit;
}

$success_message = '';
if (isset($_SESSION['contact_success'])) {
    $success_message = $_SESSION['contact_success'];
    unset($_SESSION['contact_success']);
}

// Fetch distinct genres
$query = "SELECT DISTINCT genre FROM books";
$stmt = $pdo->query($query);
$genres = $stmt->fetchAll(PDO::FETCH_COLUMN);
$genres = array_merge(['All'], $genres);

// Fetch featured books based on selected genre
$genre = isset($_GET['genre']) ? $_GET['genre'] : '';
$query = "SELECT id, title, author, genre, description, price, image_url FROM books";
if (!empty($genre) && $genre !== 'All') {
    $query .= " WHERE genre = :genre";
}
$query .= " LIMIT 32";

$stmt = $pdo->prepare($query);
if (!empty($genre) && $genre !== 'All') {
    $stmt->bindValue(':genre', $genre, PDO::PARAM_STR);
}
$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeoStore - Modern Bookshop</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-gray-100">

 <!-- Navbar -->
<nav class="bg-gray-900 p-4">
    <div class="container mx-auto">
        <div class="flex justify-between items-center">
            <div class="-mx-4">
                <a href="index.php" class="text-white text-2xl font-bold px-4">NeoStore</a>
            </div>
            
            <div class="hidden md:flex items-center space-x-6">
                <a href="index.php" class="text-white hover:text-gray-300">Home</a>
                <a href="./contact.php" class="text-white hover:text-gray-300">Contact</a>
                <a href="./about.php" class="text-white hover:text-gray-300">About</a>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="cart.php" class="text-white hover:text-gray-300">Cart</a>
                    <a href="edit-profile.php" class="text-white hover:text-gray-300">Edit Profile</a>
                    <a href="logout.php" class="text-white hover:text-gray-300">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="text-white hover:text-gray-300">Login</a>
                    <a href="register.php" class="text-white hover:text-gray-300">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>


  
<!-- Hero Section with Carousel -->
<section class="relative w-full" style="padding-top: 47.62%;">
    <div class="absolute inset-0 z-10 flex items-center justify-center text-white">
        <div class="text-center">
            <h1 class="text-4xl md:text-6xl font-bold mb-4">Welcome to NeoStore</h1>
            <p class="text-xl md:text-2xl">Discover your next favorite book</p>
        </div>
    </div>
    <div class="carousel absolute top-0 left-0 w-full h-full">
        <div class="carousel-item absolute w-full h-full transition-opacity duration-1000">
            <div class="overlay absolute inset-0 bg-black opacity-50"></div> <!-- Added overlay -->
            <img src="./herosection/1.jpeg" alt="Book 1" class="w-full h-full object-cover">
        </div>
        <div class="carousel-item absolute w-full h-full transition-opacity duration-1000 opacity-0">
            <div class="overlay absolute inset-0 bg-black opacity-50"></div> <!-- Added overlay -->
            <img src="./herosection/3.jpeg" alt="Book 2" class="w-full h-full object-cover">
        </div>
        <div class="carousel-item absolute w-full h-full transition-opacity duration-1000 opacity-0">
            <div class="overlay absolute inset-0 bg-black opacity-50"></div> <!-- Added overlay -->
            <img src="./herosection/4.jpeg" alt="Book 3" class="w-full h-full object-cover">
        </div>
        <div class="carousel-item absolute w-full h-full transition-opacity duration-1000 opacity-0">
            <div class="overlay absolute inset-0 bg-black opacity-50"></div> <!-- Added overlay -->
            <img src="./herosection/5.jpeg" alt="Book 4" class="w-full h-full object-cover">
        </div>
    </div>
</section>


<script>// Carousel functionality
let currentSlide = 0;
const slides = document.querySelectorAll('.carousel-item');

function showSlide(index) {
    slides.forEach((slide, i) => {
        slide.style.opacity = i === index ? '1' : '0';
    });
}

function nextSlide() {
    currentSlide = (currentSlide + 1) % slides.length;
    showSlide(currentSlide);
}

setInterval(nextSlide, 3000);
showSlide(0);
</script>
 <!-- Genre Selection Section -->
<section class="py-8">
    <div class="container mx-auto text-center">
        <h2 class="text-2xl font-bold mb-4">Browse by Genre</h2>
        <div class="flex flex-wrap -mx-2 justify-center">
            <?php foreach ($genres as $genre): ?>
                <div class="px-2 mb-4">
                <a class="genre-link bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-full transition-colors duration-300" data-genre="<?php echo $genre; ?>">
    <?php echo $genre; ?>
</a>


                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


   <!-- Featured Books Section -->
   <section class="py-14 m-4 px-3 md:px-0">
        <div class="container mx-auto">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6 text-center" id="featured-books">Books</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <?php if (count($books) > 0): ?>
                    <?php foreach ($books as $book): ?>
                        <div class="bg-white shadow-md rounded-lg overflow-hidden transition-transform transform hover:scale-105">
                            <img class="w-full h-48 object-cover lazyload" src="uploads/<?php echo $book['image_url']; ?>" alt="<?php echo $book['title']; ?>">
                            <div class="p-4 text-center">
                                <h3 class="text-lg font-bold text-gray-900 hover:underline cursor-pointer book-title" data-description="<?php echo htmlspecialchars($book['description']); ?>"><?php echo $book['title']; ?></h3>
                                <p class="mt-2 text-gray-600"><?php echo $book['author']; ?></p>
                                <p class="text-indigo-600 font-bold text-lg mb-4">Rs. <?php echo number_format($book['price'], 2); ?></p>
                                <?php if(isset($_SESSION['user_id'])): ?>
                                    <form action="index.php" method="POST" class="add-to-cart-form">
                                        <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
                                        <input type="hidden" name="title" value="<?php echo htmlspecialchars($book['title']); ?>">
                                        <input type="hidden" name="price" value="<?php echo $book['price']; ?>">
                                        <input type="hidden" name="add_to_cart" value="1">
                                        <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition duration-300">Add to Cart</button>
                                    </form>
                                <?php else: ?>
                                    <button class="w-full bg-gray-500 text-white px-4 py-2 rounded-md" onclick="window.location.href='login.php'">Add to Cart</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center text-gray-600">No books found for the selected genre.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>



    <!-- Footer Section -->
    <footer class="bg-gray-800 text-white py-7">
        <div class="container mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-3 m-4 md:mb-0">
                    <h3 class="text-lg md:text-xl font-semibold">NeoStore</h3>
                    <p class="text-sm text-gray-400">Your gateway to endless stories.</p>
                </div>
                <ul class="flex m-4 space-x-3">
                    <li><a href="index.php" class="text-sm text-gray-400 hover:text-white">Home</a></li>
                    <li><a href="./contact.php" class="text-sm text-gray-400 hover:text-white">Contact</a></li>
                    <li><a href="./about.php" class="text-sm text-gray-400 hover:text-white">About</a></li>
                </ul>
            </div>
            <div class="mt-6 md:mt-8 m-4 border-t border-gray-700 pt-3">
                <p class="text-xs md:text-sm text-gray-400">&copy; 2024 NeoStore. All rights reserved.</p>
            </div>
        </div>
    </footer>

     <!-- Book Description Modal -->
     <div id="bookDescriptionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div id="bookDescriptionContent"></div>
            <button id="closeModal" class="mt-3 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Close
            </button>
        </div>
    </div>

    <script>
        
    function Gotologin() {
    header("Location: login.php");
    exit();
}


        $(document).ready(function(){
            $('.add-to-cart-form').on('submit', function(e){
                e.preventDefault();
                
                var $form = $(this);
                var $button = $form.find('button[type="submit"]');
                
                $.ajax({
                    type: 'POST',
                    url: 'index.php',
                    data: $form.serialize(),
                    dataType: 'json',
                    success: function(response){
                        if(response.success) {
                            $button.text('Added!').addClass('bg-green-600').removeClass('bg-indigo-600');
                            setTimeout(function() {
                                $button.text('Add to Cart').removeClass('bg-green-600').addClass('bg-indigo-600');
                            }, 2000);
                        }
                    },
                    error: function(){
                        $button.text('Error').addClass('bg-red-600').removeClass('bg-indigo-600');
                        setTimeout(function() {
                            $button.text('Add to Cart').removeClass('bg-red-600').addClass('bg-indigo-600');
                        }, 2000);
                    }
                });
            });

            $('#contactForm').on('submit', function(e){
                e.preventDefault();
                
                $.ajax({
                    type: 'POST',
                    url: 'contact_form.php',
                    data: $(this).serialize(),
                    success: function(response){
                        alert('Message sent successfully!');
                    },
                    error: function(){
                        alert('An error occurred. Please try again.');
                    }
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('navbarSearchInput');
            searchInput.addEventListener('input', searchBooks);
        });
        
        function searchBooks() {
            const searchInput = document.getElementById('navbarSearchInput');
            const searchTerm = searchInput.value.toLowerCase();
            const bookItems = document.querySelectorAll('.bg-white.shadow-md.rounded-lg');
        
            bookItems.forEach(item => {
                const title = item.querySelector('h3').textContent.toLowerCase();
                const author = item.querySelector('p').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || author.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }
 document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('bookDescriptionModal');
            const modalContent = document.getElementById('bookDescriptionContent');
            const closeButton = document.getElementById('closeModal');

            document.querySelectorAll('.book-title').forEach(title => {
                title.addEventListener('click', function() {
                    modalContent.textContent = this.dataset.description;
                    modal.classList.remove('hidden');
                });
            });

            closeButton.addEventListener('click', function() {
                modal.classList.add('hidden');
            });

            window.addEventListener('click', function(event) {
                if (event.target === modal) {
                    modal.classList.add('hidden');
                }
            });
        });
  // Add event listeners for genre links
document.querySelectorAll('.genre-link').forEach(function(link) {
    link.addEventListener('click', function(e) {
        e.preventDefault(); // Prevent the default link behavior
        const genre = this.dataset.genre;
        const url = new URL(window.location.href);
        url.searchParams.set('genre', genre);
        window.location.href = url.toString(); // Update the URL and reload the page
    });
});

    </script>
</body>
</html>

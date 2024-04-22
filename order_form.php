<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Form</title>
    <style>
        
        @import url('https://fonts.googleapis.com/css2?family=Merienda+One&family=Nunito:wght@200;300;400;500;600&display=swap');

        :root {
            --main-color: #be9c79;
            --black: #333;
            --white: #fff;
            --light-color: #666;
            --border: 0.1rem solid var(--black);
            --box-shadow: 0 .2rem .5rem rgba(0, 0, 0, .1);
            --form-border-radius: 1rem; /* Adjust the value for roundness */
        }

        body {
            font-family: 'Nunito', sans-serif;
            margin: 0;
            padding: 0;
            background-image: url('images/coffee-bg.jpg');
            background-size: cover;
            background-repeat: no-repeat;
        }

        h3 {
            font-family: 'Merienda One', cursive;
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: whitesmoke;
            text-align: center; /* Center the heading */
        }

        form {
            width: 50%;
            margin: 0 auto;
            padding: 1rem;
            border: var(--light-color) 0.1rem solid;
            border-radius: var(--form-border-radius);
            text-align: center;
            background-color: var(--white);
            box-shadow: var(--box-shadow);
        }

        label {
            font-size: 1.4rem;
            color: var(--black);
            display: block;
            margin-bottom: 0.5rem;
            text-align: left; /* Align labels to the left */
        }

        input[type="text"] {
            width: calc(100% - 2rem); /* Subtract padding from width */
            padding: 0.8rem;
            font-size: 1.4rem;
            border: var(--border);
            margin-bottom: 1rem;
            box-sizing: border-box; /* Include padding in width calculation */
        }

        input[type="submit"] {
            background-color: var(--main-color);
            color: var(--white);
            padding: 0.8rem 1.5rem;
            font-size: 1.4rem;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: block; /* Ensure submit button takes full width */
            margin: 0 auto; /* Center the submit button */
        }

        p {
            font-size: 1.2rem;
            color: var(--white);
            text-align: center; /* Center the note */
        }

        .message {
            position: sticky;
            top: 0;
            z-index: 1100;
            background: var(--main-color);
            padding: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            max-width: 90%;
            margin: 0 auto;
        }

        .message span {
            color: var(--white);
            font-size: 1.6rem;
        }

        .message i {
            font-size: 1.8rem;
            color: var(--white);
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h3>Order your desired coffee</h3>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <input type="hidden" name="item" value="<?php echo isset($_GET['item']) ? $_GET['item'] : ''; ?>">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required><br><br>
        <label for="address">Address:</label>
        <input type="text" id="address" name="address" required><br><br>
        <input type="submit" value="Place Order">
    </form>
    <p>Note: Payment should be made physically to the delivery person.</p>

    <?php
    // Database connection parameters
    $db_host = 'localhost';
    $db_name = 'coffee2_db';
    $username = 'root';
    $password = '';

    try {
        // Connect to MySQL database
        $conn = new PDO("mysql:host=$db_host;dbname=$db_name", $username, $password);
        // Set PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Handle form submission
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Retrieve data from the form
            $item = $_POST['item'];
            $name = $_POST['name'];
            $address = $_POST['address'];

            // Prepare SQL statement to insert the order into the database
            $stmt = $conn->prepare("INSERT INTO orders (item, name, address) VALUES (:item, :name, :address)");
            // Bind parameters
            $stmt->bindParam(':item', $item);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':address', $address);

            // Execute the SQL statement
            $stmt->execute();

            // Display order placed message
            $message = array("Order placed successfully!");
            foreach ($message as $message) {
                echo '
                <div class="message">
                    <span>' . $message . '</span>
                    <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
                </div>
                ';
            }
        }
    } catch(PDOException $e) {
        // Display error message
        echo "Connection failed: " . $e->getMessage();
    }
    ?>
</body>
</html>

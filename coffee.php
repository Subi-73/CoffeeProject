<?php
$db_name = 'mysql:host=localhost;dbname=coffee2_db';
$username = 'root';
$password = '';

$conn = new PDO($db_name, $username, $password);

$total_tables = 250;
$remaining_tables = $total_tables; 

if (isset($_POST['send'])) {
    $name = $_POST['name'];
    $number = $_POST['number'];
    $guests = $_POST['guests'];
    $tables = $_POST['tables'];
    if ($guests > 30 || $tables > 5) {
        $message[] = 'Sorry, a person cannot have more than 30 guests and 5 tables.';
    } else {
        $today_date = date("Y-m-d");
        $select_booking = $conn->prepare("SELECT * FROM coffee_form2 WHERE number = ? AND DATE(created_at) = ?");
        $select_booking->execute([$number, $today_date]);

        if ($select_booking->rowCount() > 0) {
            $message[] = 'You have already booked for today!';
        } else {
           
            $two_hours_ago = date("Y-m-d H:i:s", strtotime("-2 hours"));
            $select_booking_within_2_hours = $conn->prepare("SELECT * FROM coffee_form2 WHERE number = ? AND created_at >= ?");
            $select_booking_within_2_hours->execute([$number, $two_hours_ago]);

            if ($select_booking_within_2_hours->rowCount() > 0) {
                $message[] = 'You have exceeded the booking limit. Please try again later.';
            } else {
                
                $insert_booking = $conn->prepare("INSERT INTO coffee_form2 (name, number, guests, tables) VALUES (?, ?, ?, ?)");
                $insert_booking->execute([$name, $number, $guests, $tables]);

               
                $remaining_tables -= $tables;

                $message[] = 'Booking successful!';
            }
        }
    }
}


if (isset($_POST['cancel'])) {
    $cancel_name = $_POST['cancel_name'];
    $cancel_number = $_POST['cancel_number'];

  
    $two_hours_ago = date("Y-m-d H:i:s", strtotime("-2 hours"));
    $select_booking_within_2_hours = $conn->prepare("SELECT * FROM coffee_form2 WHERE name = ? AND number = ? AND created_at >= ?");
    $select_booking_within_2_hours->execute([$cancel_name, $cancel_number, $two_hours_ago]);

    if ($select_booking_within_2_hours->rowCount() > 0) {
        
        $cancelled_order = $select_booking_within_2_hours->fetch(PDO::FETCH_ASSOC);
        $cancelled_tables = $cancelled_order['tables'];

        $update_booking = $conn->prepare("UPDATE coffee_form2 SET cancelled_at = CURRENT_TIMESTAMP WHERE name = ? AND number = ? AND created_at >= ?");
        $update_booking->execute([$cancel_name, $cancel_number, $two_hours_ago]);

        
        $remaining_tables += $cancelled_tables;

        $message[] = 'Order cancelled!';
    } else {
        $message[] = 'Cannot cancel order. Please check your name and number or you have exceeded the time limit.';
    }
}
?>




<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width,intial-scale=0.1">
        <title>Bean Bunny Cafe</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
        <link rel="stylesheet" href="coffeeStyle.css">
    </head>
    <body>

        <?php 
       if(isset($message)){
        foreach($message as $message){
            echo '
            <div class="message" style="position:sticky;
            top:0;
            z-index:1100;
            background:var(--main-color);
            padding:2rem;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:1.5rem;
            max-width:1200px;
            margin:0 auto;">
            <span style="color:var(--white);
            font-size:2rem;">'.$message.'</span>
            <i class="fas fa-times" style="font-size:2.5rem;
            color:var(--white);
            cursor:pointer;"onclick="this.parentElement.remove();"></i>
            </div>
            ';
        }
    }
        ?>
        <header class="header">
            <section class="flex">
                <a href="#home" class="logo"><img src="images/logo.png" alt=""></a>
                <nav class="navbar">
                    <a href="#home">home</a>
                    <a href="#about">about</a>
                    <a href="#menu">menu</a>
                    <a href="#gallery">gallery</a>
                    <a href="#team">team</a>
                    <a href="#contact">contact</a>
                </nav>
                <div id="menu-btn" class="fas fa-bars"></div>
            </section>
        </header>
        <div class="home-bg">
            <section class="home" id="home">
                <div class="content">
                    <h3>Bean Bunny Cafe</h3>
                    <p>"Sip, savor, and stay a while in our cozy corner of caffeine heaven. Indulge in the perfect brew while soaking in the ambiance of community and creativity."</p>
                    <a href="#about" class="btn">about us</a>
                </div>
            </section>
        </div>
        <section class="about" id="about">
            <div class="image">
                <img src="images/coffee.png" alt="">
            </div>
            <div class="content">
                <h3>A cup of coffee can complete your day</h3>
                <p>"At our coffee shop, we believe in more than just a cup of joe; it's a ritual, a moment of connection, and a daily dose of inspiration. Join us in celebrating the artistry of coffee and the joy of shared moments."</p>
                <a href="#menu" class="btn">our menu</a>
            </div>
        </section>
        <section class="facility">
            <div class="heading">
                <img src="images/heading-img.png" alt="">
                <h3>our facility</h3>
            </div>
            <div class="box-container">
                <div class="box">
                    <img src="images/icon-1.png" alt="">
                    <h3>varieties of coffees</h3>
                    <p>Explore a World of Aroma: Every Coffee Imaginable, Available Right Here!</p>
                </div>
                <div class="box">
                    <img src="images/icon-2.png" alt="">
                    <h3>coffee beans</h3>
                    <p>From Earth's Rich Bounty to Your Cup: Every Coffee Bean Imaginable Awaits Your Discovery!</p>
                </div>
                <div class="box">
                    <img src="images/icon-3.png" alt="">
                    <h3>breakfast ans sweets</h3>
                    <p>Start Your Day with a Feast: From Classic Comforts to Exotic Delights, Breakfast Dreams Await!</p>
                </div>
                <div class="box">
                    <img src="images/icon-4.png" alt="">
                    <h3>ready to go coffees</h3>
                    <p>On the Go, On Point: Your Perfect Cup, Ready to Travel with You, Every Time!</p>
                </div>
            </div>
        </section>
        <section class="menu" id="menu">
        <div class="heading">
            <img src="images/heading-img.png" alt="">
            <h3>Popular Menu</h3>
        </div>
        <div class="box-container">
            <div class="box">
                <a href="order_form.php?item=latte_art">
                    <img src="images/menu-1.jpg" alt="">
                </a>
                <h3>Latte Art</h3>
            </div>
            <div class="box">
                <a href="order_form.php?item=cappuccino">
                    <img src="images/menu-2.jpg" alt="">
                </a>
                <h3>Cappuccino</h3>
            </div>
            <div class="box">
                <a href="order_form.php?item=matcha_coffee">
                    <img src="images/menu-3.jpg" alt="">
                </a>
                <h3>Matcha Coffee</h3>
            </div>
            <div class="box">
                <a href="order_form.php?item=frappuccino">
                    <img src="images/menu-4.jpg" alt="">
                </a>
                <h3>Frappuccino</h3>
            </div>
            <div class="box">
                <a href="order_form.php?item=black_coffee">
                    <img src="images/menu-5.jpg" alt="">
                </a>
                <h3>Black Coffee</h3>
            </div>
            <div class="box">
                <a href="order_form.php?item=iced_americano">
                    <img src="images/menu-6.jpg" alt="">
                </a>
                <h3>Iced Americano</h3>
            </div>
        </div>
    </section>
        <section class="gallery" id="gallery">
            <div class="heading">
                <img src="images/heading-img.png" alt="">
                <h3>our gallery</h3>
            </div>
            <div class="box-container">
                <img src="images/gallery-1.jpg" alt="">
                <img src="images/gallery-2.jpg" alt="">
                <img src="images/gallery-3.jpg" alt="">
                <img src="images/gallery-4.jpg" alt="">
                <img src="images/gallery-5.jpg" alt="">
                <img src="images/gallery-6.jpg" alt="">
            </div>
        </section>
        <section class="team" id="team">
            <div class="heading">
                <img src="images/heading-img.png" alt="">
                <h3>our team</h3>
            </div>
            <div class="box-container">
                <div class="box">
                    <img src="images/team-1.png" alt="">
                    <h3>john deo</h3>
                </div>
                <div class="box">
                    <img src="images/team-2.jpg" alt="">
                    <h3>johnathan deo</h3>
                </div>
                <div class="box">
                    <img src="images/team-3.jpg" alt="">
                    <h3>jenna deo</h3>
                </div>
                <div class="box">
                    <img src="images/team-4.jpg" alt="">
                    <h3>james deo</h3>
                </div>
                <div class="box">
                    <img src="images/team-5.jpg" alt="">
                    <h3>jennifer deo</h3>
                </div>
                <div class="box">
                    <img src="images/team-6.png" alt="">
                    <h3>jenny deo</h3>
                </div>
            </div>
        </section>
        <section class="contact" id="contact">
            <div class="heading">
                <img src="images/heading-img.png" alt="">
                <h3>contact us</h3>
            </div>
            <div class="row">
            <form action="" method="post">
    <h3>Book a Table</h3>
    <input type="text" name="name" required class="box" maxlength="20" placeholder="Enter your name">
    <input type="tel" name="number" required class="box" maxlength="20" placeholder="Enter your number" pattern="[0-9]{10}">
    <input type="number" name="guests" required class="box" maxlength="20" placeholder="Number of guests" min="0" max="30">
    <input type="number" name="tables" required class="box" maxlength="20" placeholder="Number of tables" min="0" max="5">
    <input type="submit" name="send" value="Send Message" class="btn">
</form>
<form action="" method="post">
    <h3>Cancel Order</h3>
    <input type="text" name="cancel_name" required class="box" maxlength="20" placeholder="Enter your name">
    <input type="tel" name="cancel_number" required class="box" maxlength="20" placeholder="Enter your number" pattern="[0-9]{10}">
    <input type="submit" name="cancel" value="Cancel Order" class="btn">
</form>
            </div>
            <div class="total-tables">
    Total number of tables available: <?php echo  $remaining_tables; ?>
</div>
        </section>
        <section class="footer">
            <div class="box-container">
                <div class="box">
                    <i class="fas fa-envelope"></i>
                    <h3>our email</h3>
                    <p>subiksha.sri.s7@gmail.com</p>
                    <p>priya.aishu18@gmail.com</p>
                </div>
                <div class="box">
                    <i class="fas fa-clock"></i>
                    <h3>Shop location</h3>
                    <p>Nalatinputhur,Kovilpatti - 627503</p>
                </div>
                <div class="box">
                    <i class="fas fa-map-marker-alt"></i>
                    <h3>opening hours</h3>
                    <p>7:00 am to 10:00 pm</p>
                </div>
                <div class="box">
                    <i class="fas fa-phone"></i>
                    <h3>our number</h3>
                    <p>+91 9345642045</p>
                    <p>+91 6379551804</p>
                </div>
            </div>
            <div class="credit">&copy; copyright @ 2024 by <span>S.Subiksha Sri and U.Priyadharshini</span> || all rights reserved!</div>
        </section>





















































































        <script src="coffeeScript.js"></script>
    </body>
</html>
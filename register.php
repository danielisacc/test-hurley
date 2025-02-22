<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hurley Piano Sign-In</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php include("header.html"); ?>
    <main>
        <div class="tile signin-form signup">
            <h1>Parent Registration</h1>
            <p class="error" id="error"></p>
            <form action="<?php htmlspecialchars($_SERVER['PHP_SELF'])?>" method="POST">
                    <div class="form-inner">
                        <label for="first-name">First Name</label>
                        <input type="text" id="first-name" name="first-name">
                    </div>
                    <div class="form-inner">
                        <label for="last-name">Last Name</label>
                        <input type="text" id="last-name" name="last-name">
                    </div>

                    <div class="form-inner">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email">
                    </div>
                
                    <div class="form-inner">
                        <label for="password">Password</label>
                        <input type="password" id="register-pass" name="password">
                    </div>
                    <div class="pass-req" id="pass-req">
                         <p><span id="length"></span> 8 Characters</p>
                         <p><span id="upper"></span> 1 Uppercase</p>
                         <p><span id="lower"></span> 1 Lowercase</p>
                         <p><span id="number"></span> 1 Number</p>
                         <p><span id="symbol"></span> 1 Symbol</p>
                     </div>
                    <div class="form-inner">
                        <label for="password-conf">Confirm Password</label>
                        <p id="conf-match"></p>
                        <input type="password" id="password-conf" name="password-conf">
                    </div>
                    <div class="form-inner">
                        <label for="birth">Date of Birth</label>
                        <input type="date" id="birth" name="birth">
                    </div>

                    <div class="form-inner">
                        <label for="country">Country</label>
                        <!-- <input type="" id="country" name="country"> -->
                        <select id="country" name="country">
                            <div id="countries"></div>
                        </select>
                    </div>
                <a href="../html/members.html" style="text-decoration: none;">
                    <input type="submit" value="Sign Up">
                </a>
            </form>
        </div>
        <p class="centered-text nowrap-text">Already have an account?<br><a href="sign-in.php">Sign In</a></p>
    </main>
    <?php include("footer.html"); ?>
    <script src="test.js"></script> 
</body>
</html>

<?php
include_once 'database.php';
include_once 'test.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    add_user($conn);
}

mysqli_close($conn);
?>
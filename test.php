<?php
require "email_script.php";

function user_signin_validation($connection) {
    // Create Sanitized Variables
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);

    // If the email field is empty send warning
    if (empty($email)) {
        echo"<script>
                document.getElementById('error').insertAdjacentHTML('beforeend', `⚠️ Please Enter an Email`)
            </script>";
    }
    // If the password field is empty send warning
    else if (empty($password)) {
        echo"<script>
                document.getElementById('error').insertAdjacentHTML('beforeend', `⚠️ Please Enter a Password`);
            </script>";
    }
    // If all fields filled out begin query
    else {
        $sql = "SELECT password FROM parent_users WHERE email = ?;";
        $stmt = mysqli_stmt_init($connection);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            echo"<script>
                        document.getElementById('error').insertAdjacentHTML('beforeend', `😨 User Not Found!`);
                    </script>";
        }
        else {
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if(mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $hash = $row["password"];
                if (password_verify($password, $hash)) {
                    ob_clean();
                    echo"<script>window.location.replace('index.php')</script>";
                }
                else {
                    echo"<script>
                        document.getElementById('error').insertAdjacentHTML('beforeend', `😨 Incorrect Email or Password!`);
                    </script>";
                }
            }
            else {
                echo"<script>
                        document.getElementById('error').insertAdjacentHTML('beforeend', `😨 Incorrect Email or Password!`);
                    </script>";
            }

        }    
    } 
}

function add_user($connection) {
    // Create Variables
    $first_name = filter_input(INPUT_POST, "first-name", FILTER_SANITIZE_SPECIAL_CHARS);
    $last_name = filter_input(INPUT_POST, "last-name", FILTER_SANITIZE_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);
    $password_conf = filter_input(INPUT_POST, "password-conf", FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    $country = filter_input(INPUT_POST, "country", FILTER_SANITIZE_SPECIAL_CHARS);
    $birth = filter_input(INPUT_POST, "birth", FILTER_SANITIZE_SPECIAL_CHARS);

    //Error Messages
    $error_messages = array('first-name' => '😨 First Name Not Entered',
                            'last-name' => '😨 Last Name Not Entered',
                            'email' => '😨 Email Not Entered',
                            'password' => '😨 Password Not Entered',
                            'password-conf' => '😨 Confirm Password Not Matching',
                            'country' => '😨 Country Not Entered',
                            'birth' => '😨 Birthday Not Entered');
    
    // If password and Confimation password Don't match
    if ($password != $password_conf) {
        echo"<script>
                    document.getElementById('error').insertAdjacentHTML('beforeend', '😨 Confirm Password Not Matching');
                </script>";
    }
    // If the Password doesn't meet minimum requirements
    else if ($_COOKIE['valid_password'] != 'true') {
        echo"<script>
                    document.getElementById('error').insertAdjacentHTML('beforeend', '😨 Password Doesn't Meet Requirements');
                </script>";
    }
    else {
        // All field filled out boolean
        $no_empty_field = true;

        // Iterate through the inputs using the error message key to test if the input is empty output error message
        foreach ($error_messages as $key => $value) {
            if (empty($_POST[$key])){
                echo"<script>
                        document.getElementById('error').insertAdjacentHTML('beforeend', '$value');
                    </script>";
                $no_empty_field = false;
                break;
            } 
        }
        // If all the Fields are Filled out
        if ($no_empty_field) {

            $sql = "INSERT INTO `parent_users` (`parent_id`, `first_name`, `last_name`, `email`,
                                                `password`, `country`, `birth`, `activation_date`,
                                                `reset_token_hash`, `reset_token_expires_at`)
                            VALUES (NULL, ?, ?, ?, ?, ?, ?, current_timestamp(), NULL, NULL);";

            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_stmt_init($connection);
            if (!mysqli_stmt_prepare($stmt, $sql)) {
                echo"<script>
                            document.getElementById('error').insertAdjacentHTML('beforeend', '😨 Unable to Create User!');
                        </script>";
            }
            else {
                try {
                mysqli_stmt_bind_param($stmt, "ssssss", $first_name, $last_name, $email, $hash, $country, $birth);
                mysqli_stmt_execute($stmt);
                ob_clean();
                echo"<script>window.location.replace('index.php')</script>";
                }
                catch(mysqli_sql_exception) {
                    echo"<script>
                            document.getElementById('error').insertAdjacentHTML('beforeend', '😨 Email Already In Use!');
                        </script>";
                }
            }
        }
    }
}

function forgot_pass_token_creation($connection) {
    // Email variable creation
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    // Token creation
    $token = bin2hex(random_bytes(16));
    $token_hash = hash("sha256", $token);
    // Token expiration created
    $expiry = date("Y-m-d H:i:s", time() + 60 * 30);

    $sql = "UPDATE parent_users
            SET reset_token_hash = ?, 
                reset_token_expires_at = ?
            WHERE email = ?";
    $stmt = mysqli_stmt_init($connection);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        try {
            mysqli_stmt_bind_param($stmt, "sss", $token_hash, $expiry, $email);
            mysqli_stmt_execute($stmt);
            
            if (mysqli_stmt_num_rows($stmt) > 0) {

                    ////////////////////////////
                    // ENTER THE WEBSITE URL BELOW
                    $link = "https://localhost/Test-Website/reset-password.php?token=$token";
                    ////////////////////////////

                    $message = "Click <a href='$link'>Here</a> to reset your password.";
                    $subject = "Hurley Piano: Password Reset";
                    $response = sendMail($email, $subject, $message);
                    echo"$response";
            }
        }
        catch (mysqli_sql_exception) {

        }
    
    }
    echo"<script>
             document.getElementById('error').insertAdjacentHTML('beforeend', `Password reset link sent
                                                                  <br>to email if email exists.
                                                                  <br>Please Check your Inbox`);
        </script>";
    
}
?>
<?php include "dbConnection.php"; ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Login</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="utf-8">
        
        <link rel="stylesheet" href="stylesheet_login.css">
        <link rel="shortcut icon" href="https://icon-library.com/images/tree-icon/tree-icon-23.jpg">
    </head>

    <body>
        <main>
            <div class="wrapper fadeInDown">
                <div id="formContent">
                  <!-- Tabs Titles -->
                  <h2 class="active"> Sign In </h2>
                  <h2 class="inactive underlineHover">Sign Up </h2>
              
                  <!-- Icon -->
                  <div class="fadeIn first">
                    <img src="https://icon-library.com/images/username-icon/username-icon-11.jpg" id="icon" alt="User Icon" />
                  </div>
              
                  <!-- Login Form -->
                  <form>
                    <input type="text" id="login" class="fadeIn second" name="login" placeholder="LOGIN">
                    <input type="text" id="password" class="fadeIn third" name="login" placeholder="PASSWORD">
                    <input type="submit" class="fadeIn fourth" value="Log In">
                  </form>
              
                  <!-- Remind Passowrd -->
                  <div id="formFooter">
                    <a class="underlineHover" href="#">Forgot Password?</a>
                  </div>
              
                </div>
              </div>
            
        </main>

        <footer>
            
        </footer>
    </body>
</html>
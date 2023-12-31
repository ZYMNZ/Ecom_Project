<?php
include_once 'Views/General/session.php';
session_unset();
session_destroy();
?>
<html>
    <head>
        <title>Login</title>
        <!--Keep general styles css file at the top to let sub css files override the general one-->
        <link rel="stylesheet" type="text/css" href="Views/styles/generalstyles.css">
        <link rel="stylesheet" type="text/css" href="Views/styles/navbar.css">
        <link rel="stylesheet" type="text/css" href="Views/styles/login.css">
        <link rel="stylesheet" type="text/css" href="Views/styles/footer.css">
    </head>
    <body>
        <div class="mainContentWrapper">
            <main>
                <?php
                include_once 'Views/General/navbar.php';
                ?>

                <section>
                    <header class="header">
                        <label class="headerFont">Welcome</label>
                    </header>
                    <form action="/?controller=login&action=validation" method="POST">
                        <section class="loginRegistrationSection marginAuto">
                            <label class="hintLabel displayBlock">Email:</label>
                            <input type="text" name="email" class="inputField width100Percent" required> <br/>
                            <label class="hintLabel displayBlock">Password:</label>
                            <input type="password" name="password" class="inputField width100Percent" required> <br/>
                        </section>
                        <section class="signButtons marginAuto">
                            <input type="submit" name="submit" value="Sign in" class="defaultButtonStyling cursorPointer width100Percent borderNone"> <br/>
                            <a href="/?controller=registration&action=registration">
                                <input type="button" name="signUp" value="Sign up" class="defaultButtonStyling cursorPointer width100Percent borderNone">
                            </a>
                        </section>
                    </form>
                </section>
            </main>
        </div>

        <?php
            include_once "Views/General/footer.php";
        ?>
    </body>
</html>
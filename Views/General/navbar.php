<nav>
    <section id="navBar" class="width100Percent displayFlex">
        <section id="logo" class="height100Percent"></section>
        <?php
            function addHomeButtonNavBar() {
                global $controllerPrefix;
                global $action;
                if($controllerPrefix == "login" && $action == "login") {
        ?>
                    <section>
                        <input type="button" href="" value="Home" class="defaultButtonStyling borderNone navBarButton cursorPointer signButtons">
                    </section>
                    <?php
                }
            }
                    ?>
        <?php
            addHomeButtonNavBar();
        ?>

    </section>
</nav>

$(document).ready(
    function() {
        // When the document is ready, set up any event handler
        setUpEventHandlers();
    }
);



function setUpEventHandlers() {
    var productForm = $("#editBuyerForm");
    $("[name='submit']").on("click", function (event) {
            event.preventDefault();

            // Check values inside the text fields before you submit the form
            var firstName = $("[name='firstName']");
            var firstNameErrorLabel = $("[name='firstNameErrorLabel']");
            var firstName = firstNameCheck(firstName,firstNameErrorLabel);

            var lastName = $("[name='lastName']");
            var lastNameErrorLabel = $("[name='lastNameErrorLabel']");
            var lastName = lastNameCheck(lastName,lastNameErrorLabel);

            var email = $("[name='email']");
            var emailNameErrorLabel = $("[name='emailErrorLabel']");
            var email = emailCheck(email,emailNameErrorLabel);

            if (email && firstName && lastName) {
                /*
                Prevent the submit from happening
                if the values are wrong
                */
                $(this).off("click");
                productForm.submit();
            }
        }
    );
}


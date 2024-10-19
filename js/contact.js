$(document).ready(function () {
    // Initialize reCAPTCHA
    grecaptcha.ready(function () {
        grecaptcha.execute("6LeIz2UqAAAAAPKFZ7a2-SE2E1fR04Wlcar1GeqV", {
            action: "homepage"
        }).then(function (responseToken) {
            $("#contactForm").prepend('<input type="hidden" name="g-recaptcha-response" value="' + responseToken + '">');
        });
    });

    // jQuery Validation
    $("#contactForm").validate({
        rules: {
            name: {
                required: true,
                minlength: 2
            },
            email: {
                required: true,
                email: true
            },
            phone: {
                required: true,
                digits: true,
                minlength: 10,
                maxlength: 15
            },
            subject: {
                required: true,
                minlength: 5
            },
            message: {
                required: true,
                minlength: 10
            }
        },
        messages: {
            name: {
                required: "Please enter your name",
                minlength: "Your name must consist of at least 2 characters"
            },
            email: {
                required: "Please enter your email address",
                email: "Please enter a valid email address"
            },
            phone: {
                required: "Please provide your phone number",
                digits: "Please enter a valid phone number",
                minlength: "Phone number must be at least 10 digits",
                maxlength: "Phone number must not exceed 15 digits"
            },
            subject: {
                required: "Please provide a subject",
                minlength: "Subject must be at least 5 characters"
            },
            message: {
                required: "Please enter your message",
                minlength: "Message must be at least 10 characters long"
            }
        },
        errorElement: "div",
        errorPlacement: function (error, element) {
            error.addClass("error");
            element.closest(".form-floating").append(error); // Add error message next to the field
        },
        submitHandler: function (form) {
            var formData = $(form).serialize();

            // Show loader
            $("#loader").removeClass("d-none");

            // Hide any previous alert message
            $("#alert-message").addClass("d-none");

            $.ajax({
                url: 'contact.php', // PHP script to handle form submission
                type: "POST",
                data: formData + '&action=contact-2', // Add action for backend processing
                dataType: 'json',
                success: function (response) {
                    // Hide loader
                    $("#loader").addClass("d-none");

                    if (response.status == 1) {
                        // Show success message
                        $("#alert-message").removeClass("d-none alert-danger").addClass("alert-success");
                        $("#alert-message").text("Your message has been sent successfully!");

                        // Reset form fields
                        $(form)[0].reset();
                    } else {
                        // Show error message
                        $("#alert-message").removeClass("d-none alert-success").addClass("alert-danger");
                        $("#alert-message").text("Failed to send your message. Please try again.");
                    }
                },
                error: function () {
                    // Hide loader
                    $("#loader").addClass("d-none");

                    // Show error alert
                    $("#alert-message").removeClass("d-none alert-success").addClass("alert-danger");
                    $("#alert-message").text("Something went wrong. Please try again.");
                }
            });
        }
    });
});

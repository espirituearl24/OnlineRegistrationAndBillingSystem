<?php
session_start(); 
$email = isset($_GET['email']) ? urldecode($_GET['email']) : '';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Email</title>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* Reset some default styling */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        
        /* Body Styling */
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-color: #f4f4f9;
        }

        /* Form Container */
        .form-container {
            background-color: #ffffff;
            padding: 20px 30px;
            border-radius: 8px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        /* Form Heading */
        .form-container h2 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        /* Form Label and Input Styling */
        label {
            font-weight: bold;
            color: #555;
            margin-bottom: 5px;
            display: block;
        }

        input[type="email"], input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: border-color 0.3s;
        }

        input[type="email"]:focus, input[type="text"]:focus, textarea:focus {
            border-color: #007bff;
            outline: none;
        }

        textarea {
            resize: none; /* Prevent resizing */
            font-family: Arial, sans-serif; /* Ensure consistent font */
        }

        /* Button Styling */
        button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        /* Responsive Design */
        @media (max-width: 400px) {
            .form-container {
                padding: 15px;
            }
            input[type="email"], input[type="text"], textarea {
                padding: 8px;
            }
            button {
                padding: 8px;
            }
        }
    </style>
</head>
<body>

<?php
    if (isset($_SESSION['notification'])) {
        $notification = $_SESSION['notification'];
        echo "
        <script>
        Swal.fire({
            icon: '{$notification['type']}',
            title: '{$notification['message']}',
            showConfirmButton: false,
            timer: 3000
        });
        </script>
        ";
        unset($_SESSION['notification']); // Clear the notification after displaying
    }
    ?>
    
    <div class="form-container">
        <h2>Send Email</h2>
        <form action="send.php" method="post">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?= $email ?>" required readonly>
            <label for="subject">Subject</label>
            <input type="text" name="subject" id="subject" required>

            <label for="message">Message</label>
            <textarea name="message" id="message" rows="5" required></textarea>

            <button type="submit" name="send">Send</button>
        </form>
    </div>

</body>
</html>

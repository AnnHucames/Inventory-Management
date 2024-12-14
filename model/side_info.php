<?php
$sq = "SELECT * FROM users_info WHERE id='$userid'";
$thisUser = mysqli_fetch_assoc($conn->query($sq));
?>

<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=10">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="../css/product.css">
    </head>
    <body>
        <div class="rightcolumn">
            <div class="card text-center">
                <h2>About User</h2>
                <p>
                    <h4><?php echo htmlspecialchars($thisUser['name']); ?></h4> is working here since <h4><?php echo date('F j, Y', strtotime($thisUser['created_at'])); ?></h4>
                </p>
            </div>
        </div>
    </body>
</html>

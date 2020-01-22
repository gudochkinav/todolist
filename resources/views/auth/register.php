<!DOCTYPE html>
<html>
    <head>
        <title>Registration Page</title>
        <link rel="stylesheet" type="text/css" href="/css/auth.css">
    </head>
    <body>
        <form method="post" action="/auth/register/submit">

            <?php foreach ($errors as $error) { ?>
                <div class="input-group">
                    <label class="error"><?php echo $error; ?></label>
                </div>
            <?php } ?>

            <div class="input-group">
                <label>Name</label>
                <input type="text" name="name" value="<?php echo $name; ?>">
            </div>
            <div class="input-group">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo $email; ?>">
            </div>
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password">
            </div>
            <div class="input-group">
                <label>Confirm password</label>
                <input type="password" name="confirm_password">
            </div>
            <div class="input-group">
                <button type="submit" class="btn" name="reg_user">Register</button>
            </div>
            <p>
                Already a member? <a href="/auth/login">Sign in</a>
            </p>
        </form>
    </body>
</html>
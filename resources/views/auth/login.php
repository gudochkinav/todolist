<!DOCTYPE html>
<html>
    <head>
        <title>Login Page</title>
        <link rel="stylesheet" type="text/css" href="/css/auth.css">
    </head>
    <body>
        <form method="post" action="/auth/login/submit">

            <?php foreach ($errors as $error) { ?>
                <div class="input-group ">
                    <label class="error"><?php echo $error; ?></label>
                </div>
            <?php } ?>

            <div class="input-group">
                <label>Email</label>
                <input type="email" name="email" value="<?php echo $email; ?>">
            </div>
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password">
            </div>

            <div class="input-group">
                <button type="submit" class="btn" name="reg_user">Login</button>
            </div>
            <p>
                Already a member? <a href="/auth/register">Registration</a>
            </p>
        </form>
    </body>
</html>
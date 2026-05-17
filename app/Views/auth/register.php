<?php
/**
 * @var string $title
 * @var string $name
 * @var string $email
 * @var string $password
 * @var string $confirm_password
 * @var string $name_err
 * @var string $email_err
 * @var string $password_err
 * @var string $confirm_password_err
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?> | <?= SITENAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans text-gray-800 antialiased flex flex-col min-h-screen">

    <?php require APPROOT . '/Views/partials/navbar.php'; ?>

    <div class="flex-grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
            <div>
                <h2 class="mt-2 text-center text-3xl font-extrabold text-gray-900">
                    Create your account
                </h2>
                <p class="mt-2 text-center text-sm text-gray-600">
                    Already have an account?
                    <a href="<?= URLROOT ?>/auth/login" class="font-medium text-green-600 hover:text-green-500 transition-colors">
                        Sign in here
                    </a>
                </p>
            </div>
            
            <form class="mt-8 space-y-6" action="<?= URLROOT ?>/auth/register" method="POST">
                <div class="space-y-4">
                    
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                        <div class="mt-1">
                            <input id="name" name="name" type="text" autocomplete="name" required value="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>"
                                class="appearance-none block w-full px-3 py-3 border <?= !empty($name_err) ? 'border-red-300 ring-red-500' : 'border-gray-300 focus:ring-green-500 focus:border-green-500' ?> rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-1 sm:text-sm transition-colors">
                        </div>
                        <?php if (!empty($name_err)): ?>
                            <p class="mt-2 text-sm text-red-600"><?= $name_err ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                        <div class="mt-1">
                            <input id="email" name="email" type="email" autocomplete="email" required value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>"
                                class="appearance-none block w-full px-3 py-3 border <?= !empty($email_err) ? 'border-red-300 ring-red-500' : 'border-gray-300 focus:ring-green-500 focus:border-green-500' ?> rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-1 sm:text-sm transition-colors">
                        </div>
                        <?php if (!empty($email_err)): ?>
                            <p class="mt-2 text-sm text-red-600"><?= $email_err ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <div class="mt-1">
                            <input id="password" name="password" type="password" autocomplete="new-password" required value="<?= htmlspecialchars($password, ENT_QUOTES, 'UTF-8') ?>"
                                class="appearance-none block w-full px-3 py-3 border <?= !empty($password_err) ? 'border-red-300 ring-red-500' : 'border-gray-300 focus:ring-green-500 focus:border-green-500' ?> rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-1 sm:text-sm transition-colors">
                        </div>
                        <?php if (!empty($password_err)): ?>
                            <p class="mt-2 text-sm text-red-600"><?= $password_err ?></p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                        <div class="mt-1">
                            <input id="confirm_password" name="confirm_password" type="password" autocomplete="new-password" required value="<?= htmlspecialchars($confirm_password, ENT_QUOTES, 'UTF-8') ?>"
                                class="appearance-none block w-full px-3 py-3 border <?= !empty($confirm_password_err) ? 'border-red-300 ring-red-500' : 'border-gray-300 focus:ring-green-500 focus:border-green-500' ?> rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-1 sm:text-sm transition-colors">
                        </div>
                        <?php if (!empty($confirm_password_err)): ?>
                            <p class="mt-2 text-sm text-red-600"><?= $confirm_password_err ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                        Register
                    </button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
<?php
/**
 * @var string $title
 * @var App\Models\User[] $users
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?> | <?= SITENAME ?> Admin</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans text-gray-800 antialiased min-h-screen flex flex-col">

    <?php 
        $backUrl = URLROOT . '/admin/dashboard';
        $backText = 'Back to Dashboard';
        require APPROOT . '/Views/partials/navbar.php'; 
    ?>

    <div class="flex-grow max-w-6xl mx-auto px-4 mt-12 mb-16 w-full">
        
        <div class="mb-10 border-b border-gray-200 pb-6">
            <h1 class="text-3xl font-extrabold text-gray-900">
                Manage Users
            </h1>
            <p class="mt-2 text-gray-600">View registered students and manage their access to the platform.</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-900">All Students</h2>
                <span class="bg-gray-200 text-gray-700 text-sm font-bold px-3 py-1 rounded-full"><?= count($users) ?> Total</span>
            </div>
            
            <?php if (empty($users)): ?>
                <div class="p-12 text-center">
                    <p class="text-gray-500">No students have registered yet.</p>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($users as $u): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        #<?= $u->id ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-gray-900"><?= htmlspecialchars($u->name, ENT_QUOTES, 'UTF-8') ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500"><?= htmlspecialchars($u->email, ENT_QUOTES, 'UTF-8') ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?= date('M j, Y', strtotime($u->created_at)) ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if ($u->is_banned): ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Banned
                                            </span>
                                        <?php else: ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Active
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <form action="<?= URLROOT ?>/admin/toggle_ban/<?= $u->id ?>" method="POST" class="inline-block">
                                            <?php if ($u->is_banned): ?>
                                                <input type="hidden" name="ban_status" value="0">
                                                <button type="submit" class="text-green-600 hover:text-green-900 font-bold">Unban</button>
                                            <?php else: ?>
                                                <input type="hidden" name="ban_status" value="1">
                                                <button type="submit" class="text-red-600 hover:text-red-900 font-bold" onclick="return confirm('Are you sure you want to ban this user? They will no longer be able to log in.');">Ban User</button>
                                            <?php endif; ?>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

    </div>
</body>
</html>
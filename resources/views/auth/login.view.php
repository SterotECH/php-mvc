<?php
include base_path('resources/views/partials/_base.php');
?>
<section class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden md:flex w-4/6  md:items-center">
        <div class="md:w-1/2 px-6 py-8 md:border-r">
            <h2 class="text-2xl font-bold mb-6 text-center">Stero MVC</h2>
            <p class="text-gray-600 mb-6 text-center">Sign in to your account</p>
            <form action="/auth/login" method="POST">
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input
                            type="email" id="email" name="email"
                            class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            required value="<?= $_POST['email'] ?? '' ?>"
                    />
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" id="password" name="password"
                           class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                           required value="<?= $_POST['password'] ?? '' ?>"
                    />
                </div>
                <div class="text-right mb-4">
                    <a href="/auth/forgot-password" class="text-sm text-indigo-600">Forgot password?</a>
                </div>
                <div class="mb-6">
                    <button type="submit"
                            class="w-full bg-indigo-500 text-white py-2 rounded-md hover:bg-indigo-600 focus:outline-none focus:bg-indigo-600">
                        Sign in
                    </button>
                </div>
                <p class="text-sm text-gray-600">Don't have an account?
                    <a href="/auth/register" class="text-indigo-600">Register</a>
                </p>
            </form>
            <?php if (isset($errors['email'])): ?>
                <p class="mt-2 text-sm text-red-600"><?= $errors['email'] ?></p>
            <?php endif; ?>
            <?php if (isset($errors['password'])): ?>
                <p class="mt-2 text-sm text-red-600"><?= $errors['password'] ?></p>
            <?php endif; ?>
        </div>
        <div class="md:w-1/2 hidden md:block">
            <img src="<?= url('/image/login-bg.jpg') ?>" alt="Login Image" class="w-full h-[550px] object-cover">
        </div>
    </div>
</section>
<?php include base_path('resources/views/partials/footer.php') ?>

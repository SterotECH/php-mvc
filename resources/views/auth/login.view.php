<?php
include base_path('resources/views/partials/_base.php');
?>
<section class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden md:flex w-4/6  md:items-center">
        <div class="md:w-1/2 px-6 py-8 md:border-r">
            <h2 class="text-2xl font-bold mb-6 text-center">Stero MVC</h2>
            <p class="text-gray-600 mb-6 text-center">Sign in to your account</p>
            <?php if (isset($errors['email'])) : ?>
                <?= displayError($errors['email']); ?>
            <?php endif; ?>

            <?php if (isset($errors['password'])) : ?>
                <?= displayError($errors['password']); ?>
            <?php endif; ?>
            <form action="/auth/login" method="POST">
                <?= csrf_field() ?>
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email" name="email" class="input" required value="<?= old('email') ?>"/>
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" id="password" name="password" class="input" required/>
                </div>
                <div class="flex items-center justify-between">
                    <div class="text-left mb-4">
                        <input type="checkbox" name="remember" id="remember"/>
                        <label for="remember" class="text-sm text-gray-700"> Remember Me</label>
                    </div>

                    <div class="text-right mb-4">
                        <a href="/auth/forgot-password" class="text-sm text-indigo-600">Forgot password?</a>
                    </div>
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

        </div>
        <div class="md:w-1/2 hidden md:block">
            <img src="<?= url('/image/login-bg.jpg') ?>" alt="Login Image" class="w-full h-[550px] object-cover">
        </div>
    </div>
</section>
<?php include base_path('resources/views/partials/footer.php') ?>

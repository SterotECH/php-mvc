<?php include base_path('resources/views/partials/_base.php'); ?>

<section class="bg-gray-100 bg-cover bg-center bg-fixed h-screen" style="background-image: url('<?= url('/image/header.jpg') ?>');">
    <div class="max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow sm:rounded-lg items-center justify-center">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-2xl font-medium leading-6 text-gray-900">Create an account</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">
                    Already have an account?
                    <a href="/auth/login" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Sign in here
                    </a>
                </p>
            </div>
            <div class="border-t border-gray-200">
                <form action="/auth/register" method="POST" class="px-4 py-5 sm:p-6">
                    <?= csrf_field() ?>
                    <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-8">
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                            <input type="text" name="username" id="username" class="input" value="<?= old('username') ?>" required />
                            <?php if (isset($errors['username'])) : ?>
                                <?= displayError($errors['username']); ?>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label for="first_name" class="block text-sm font-medium text-gray-700">First Name</label>
                            <input type="text" name="first_name" id="first_name" class="input" value="<?= old('first_name') ?>" required>
                            <?php if (isset($errors['first_name'])) : ?>
                                <?= displayError($errors['first_name']); ?>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name</label>
                            <input type="text" name="last_name" id="last_name" class="input" value="<?= old('last_name') ?>" required>
                            <?php if (isset($errors['last_name'])) : ?>
                                <?= displayError($errors['last_name']); ?>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label for="other_name" class="block text-sm font-medium text-gray-700">Other Name</label>
                            <input type="text" name="other_name" id="other_name" class="input" value="<?= old('other_name') ?>">
                            <?php if (isset($errors['other_name'])) : ?>
                                <?= displayError($errors['other_name']); ?>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" id="email" class="input" value="<?= old('email') ?>" required>
                            <?php if (isset($errors['email'])) : ?>
                                <?= displayError($errors['email']); ?>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone
                                Number</label>
                            <input type="tel" name="phone_number" id="phone_number" class="input" value="<?= old('phone_number') ?>" required>
                            <?php if (isset($errors['phone_number'])) : ?>
                                <?= displayError($errors['phone_number']); ?>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                            <input type="password" name="password" id="password" class="input" required>
                            <?php if (isset($errors['password'])) : ?>
                                <?= displayError($errors['password']); ?>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm
                                Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="input" required>
                            <?php if (isset($errors['password_confirmation'])) : ?>
                                <?= displayError($errors['password_confirmation']); ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="mt-6">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Create Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include base_path('resources/views/partials/footer.php'); ?>

<?php include base_path('resources/views/partials/nav.view.php') ?>
<?php include base_path('resources/views/partials/header.view.php') ?>
<div class="flex min-h-full flex-col justify-center px-6 py-8 lg:px-8">
    <div class="w-full mx-auto ">
        <form action="/users" method="POST" class="mx-auto grid grid-cols-2 gap-4 mb-12">
            <div class="relative z-0 w-full mb-5 group">
                <label for="username" class="label">Username</label>
                <input type="text" id="username" name="username" value="<?= $_POST['username'] ?? '' ?>"
                       class="input "/>
                <?php if (isset($errors['username'])): ?>
                    <span class="text-red-500 text-xs"><?php echo $errors['username']; ?></span>
                <?php endif; ?>
            </div>
            <div class="relative z-0 w-full mb-5 group">
                <label for="first_name" class="label">First Name</label>
                <input type="text" id="first_name" name="first_name" value="<?= $_POST['first_name'] ?? '' ?>"
                       class="input"/>
                <?php if (isset($errors['first_name'])): ?>
                    <span class="text-red-500 text-xs"><?php echo $errors['first_name']; ?></span>
                <?php endif; ?>
            </div>
            <div class="relative z-0 w-full mb-5 group">
                <label for="last_name" class="label">Last Name</label>
                <input type="text" id="last_name" name="last_name" value="<?= $_POST['last_name'] ?? '' ?>"
                       class="input"/>
                <?php if (isset($errors['last_name'])): ?>
                    <span class="text-red-500 text-xs"><?php echo $errors['last_name']; ?></span>
                <?php endif; ?>
            </div>
            <div class="relative z-0 w-full mb-5 group">
                <label for="other_name" class="label">Other Name</label>
                <input type="text" id="other_name" name="other_name" value="<?= $_POST['other_name'] ?? '' ?>"
                       class="input"/>
                <?php if (isset($errors['other_name'])): ?>
                    <span class="text-red-500 text-xs"><?php echo $errors['other_name']; ?></span>
                <?php endif; ?>
            </div>
            <div class="relative z-0 w-full mb-5 group">
                <label for="phone_number" class="label">Phone Number</label>
                <input type="text" id="phone_number" name="phone_number" value="<?= $_POST['phone_number'] ?? '' ?>"
                       class="input"/>
                <?php if (isset($errors['phone_number'])): ?>
                    <span class="text-red-500 text-xs"><?php echo $errors['phone_number']; ?></span>
                <?php endif; ?>
            </div>
            <div class="relative z-0 w-full mb-5 group">
                <label for="email" class="label">Email</label>
                <input type="email" id="email" name="email" value="<?= $_POST['email'] ?? '' ?>" class="input"/>
                <?php if (isset($errors['email'])): ?>
                    <span class="text-red-500 text-xs"><?php echo $errors['email']; ?></span>
                <?php endif; ?>
            </div>
            <div class="relative z-0 w-full mb-5 group">
                <label for="password" class="label">Password</label>
                <input type="password" id="password" name="password" value="<?= $_POST['password'] ?? '' ?>"
                       class="input"/>
                <?php if (isset($errors['password'])): ?>
                    <span class="text-red-500 text-xs"><?php echo $errors['password']; ?></span>
                <?php endif; ?>
            </div>
            <div class="flex items-center justify-between col-span-2">
                <button
                        type="submit"
                        class="w-full text-white bg-violet-600 hover:bg-violet-700 focus:ring-4 focus:outline-none focus:ring-violet-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-violet-600 dark:hover:bg-violet-700 dark:focus:ring-violet-800">
                    Submit
                </button>
            </div>
        </form>
    </div>
</div>
<?php include base_path('resources/views/partials/footer.php') ?>

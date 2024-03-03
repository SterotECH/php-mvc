<?php include base_path('resources/views/partials/_base.php') ?>
<form action="/users" method="POST" class="w-full max-w-lg">
    <div class="mb-4">
        <label for="username" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Username</label>
        <input type="text" id="username" name="username" placeholder="Username" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline">
    </div>
    <div class="mb-4">
        <label for="first_name" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">First Name</label>
        <input type="text" id="first_name" name="first_name" placeholder="First Name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline">
    </div>
    <div class="mb-4">
        <label for="last_name" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Last Name</label>
        <input type="text" id="last_name" name="last_name" placeholder="Last Name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline">
    </div>
    <div class="mb-4">
        <label for="other_name" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Other Name</label>
        <input type="text" id="other_name" name="other_name" placeholder="Other Name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline">
    </div>
    <div class="mb-4">
        <label for="phone_number" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Phone Number</label>
        <input type="text" id="phone_number" name="phone_number" placeholder="Phone Number" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline">
    </div>
    <div class="mb-4">
        <label for="email" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Email</label>
        <input type="email" id="email" name="email" placeholder="Email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline">
    </div>
    <div class="mb-4">
        <label for="password" class="block text-gray-700 dark:text-gray-300 font-bold mb-2">Password</label>
        <input type="password" id="password" name="password" placeholder="Password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300 leading-tight focus:outline-none focus:shadow-outline">
    </div>
    <div class="flex items-center justify-between">
        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">Submit</button>
    </div>
</form>
<?php include base_path('resources/views/partials/footer.php') ?>

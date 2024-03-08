<?php
/**
 * @var User $users;
 */

use App\Models\User;

?>
<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
    <thead class="bg-gray-50 dark:bg-gray-800">
    <tr>
        <th scope="col"
            class="py-3.5 px-4 text-sm font-normal text-left rtl:text-right text-gray-500 dark:text-gray-400">
            Username
        </th>

        <th scope="col"
            class="px-12 py-3.5 text-sm font-normal text-left rtl:text-right text-gray-500 dark:text-gray-400">
            Name
        </th>

        <th scope="col"
            class="px-4 py-3.5 text-sm font-normal text-left rtl:text-right text-gray-500 dark:text-gray-400">
            Email
        </th>

        <th scope="col"
            class="px-4 py-3.5 text-sm font-normal text-left rtl:text-right text-gray-500 dark:text-gray-400">
            Phone
        </th>

        <th scope="col"
            class="px-4 py-3.5 text-sm font-normal text-left rtl:text-right text-gray-500 dark:text-gray-400">
            Date Joined
        </th>

        <th scope="col" class="relative py-3.5 px-4">
            <span class="sr-only">Edit</span>
        </th>
    </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200 dark:divide-gray-700 dark:bg-gray-900">
    <?php if (empty($users)): ?>
        <tr>
            <td rowspan="6">No Users</td>
        </tr>
    <?php else: ?>
        <?php foreach ($users as $user): ?>
            <tr>
                <td class="px-4 text-sm font-medium whitespace-nowrap">
                    <div class="inline px-3 py-1 text-sm font-normal rounded-full text-emerald-500 gap-x-2 bg-emerald-100/60 dark:bg-gray-800">
                        <?= htmlspecialchars($user->username) ?>
                    </div>
                </td>
                <td class="text-sm font-medium whitespace-nowrap">
                    <p>
                        <?= htmlspecialchars($user->first_name) ?>  <?= htmlspecialchars($user->last_name) ?>
                    </p>
                </td>
                <td class="px-4 py-4 text-sm whitespace-nowrap">
                    <h4 class="text-gray-700 dark:text-gray-200">
                        <?= htmlspecialchars($user->email) ?>
                    </h4>
                </td>
                <td class="px-4 py-4 text-sm whitespace-nowrap">
                    <?= htmlspecialchars($user->phone_number) ?>
                </td>

                <td class="px-4 py-4 text-sm whitespace-nowrap">
                    <?php
                    $dateFromDatabase = $user->created_at;

                    // Create a DateTime object from the database date string
                    try {
                        $date = new DateTime($dateFromDatabase);
                    } catch (Exception $e) {
                        echo null;
                    }

                    // Format the date as a human-readable string
                    $humanReadableDate = $date->format('F j, Y');

                    echo $humanReadableDate;

                    ?>
                </td>

                <td class="px-4 py-4 text-sm whitespace-nowrap flex items-center justify-center">
                    <a href="/users/<?= $user->id ?>" class="mr-4"> View</a>
                    <a href="/users/<?=  $user->id ?>/edit" class="mr-4"> Edit</a>
                    <form action="users/<?=  $user->id ?>" method="POST">
                        <input type="hidden" name="_request_method" value="DELETE">
                        <input type="hidden" name="id" value="<?=  $user->id ?>">
                        <button type="submit" class="bg-red-700 rounded px-3 py-2 text-white">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>

        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

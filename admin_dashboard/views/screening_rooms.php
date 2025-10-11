<section class="mb-10">
    <h2 class="text-5xl font-[Limelight] text-[var(--primary)] mb-6">Screening Rooms</h2>

    <!-- Screening Rooms Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full border-t-4 border-[var(--primary)] text-black">
            <thead class="font-[Limelight] text-[var(--primary)] text-lg">
                <tr>
                    <th class="px-4 py-2 text-left">ID</th>
                    <th class="px-4 py-2 text-left">Name</th>
                    <th class="px-4 py-2 text-left">Capacity</th>
                    <th class="px-4 py-2 text-left">Seats</th>
                    <th class="px-4 py-2 text-left">Actions</th>
                </tr>
            </thead>
            <tbody>
    <?php foreach ($screeningRooms as $room): ?>
        <tr class="hover:text-black transition-colors duration-300 align-top">
            <td class="px-4 py-4"><?= $room['id'] ?></td>
            <td class="px-4 py-4"><?= htmlspecialchars($room['name']) ?></td>
            <td class="px-4 py-4"><?= $room['capacity'] ?></td>
            <td class="px-4 py-4">
                <div class="flex flex-col gap-3"> <!-- Increased gap between seat rows -->
                    <?php
                    $seats = getSeatsByRoom($db, $room['id']);
                    $groupedSeats = [];

                    foreach ($seats as $seat) {
                        $groupedSeats[$seat['row_number']][] = $seat['seat_number'];
                    }

                    foreach ($groupedSeats as $row => $seatNumbers) {
                        echo '<div class="flex gap-2">';
                        foreach ($seatNumbers as $number) {
                            echo '<div class="w-8 h-8 bg-[var(--secondary)] rounded-full flex items-center justify-center text-white text-xs shadow">'
                                 . htmlspecialchars($row . $number) .
                                 '</div>';
                        }
                        echo '</div>';
                    }
                    ?>
                </div>
            </td>

            <td class="px-4 py-4">
                <button class="bg-[var(--primary)] text-white px-3 py-1 rounded shadow hover:bg-[var(--secondary)] text-sm">Edit</button>
                <button class="bg-red-500 text-white px-3 py-1 rounded shadow hover:bg-red-600 text-sm">Delete</button>
            </td>
        </tr>
    <?php endforeach; ?>
</tbody>

        </table>
    </div>
</section>

<!-- <?php
        require '../php/config.php'; // Include DB connection

        $sql = "SELECT 
            cars.id AS car_id,
            cars.user_id,
            cars.make,
            cars.model,
            cars.daily_rate,
            cars.location,
            cars.transmission,
            cars.seats,
            car_images.image_path
        FROM cars
        LEFT JOIN car_images ON cars.id = car_images.car_id AND car_images.is_primary = 1
        WHERE cars.is_active = 1";

        $result = mysqli_query($conn, $sql);

        $cars = [];
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $cars[] = $row;
            }
        }
        ?> -->

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/general.css">
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>

<body>
    <div class="home-container">
        <div class="home-header">
            <h1>Find the perfect car to rent</h1>
            <p style="color: gray;">Choose from thousands of cars shared by local hosts across</p>
        </div>
        <div class="search-car-container">
            <form action="post">
                <div class="input-container">
                    <label for="location">Location</label>
                    <input type="text" id="location" placeholder="City, airport or address">
                </div>
                <div class="input-container">
                    <label for="from">From</label>
                    <input type="date" id="from">
                </div>
                <div class="input-container">
                    <label for="to">To</label>
                    <input type="date" id="to">
                </div>
                <div class="input-container">
                    <button>Search Cars</button>
                </div>
            </form>
        </div>
        <!-- <div class="car-container">
            <?php foreach ($cars as $car): ?>
                <a href="/car-details?id=<?= htmlspecialchars($car['car_id']) ?>" class="car-link">
                    <div class="car-card">
                        <div class="car-image">
                            <img src="/php/car-images/<?= htmlspecialchars($car['car_id']) ?>/<?= htmlspecialchars($car['image_path']) ?>" alt="<?= htmlspecialchars($car['make'] . ' ' . $car['model']) ?>">
                            <div class="price-tag">₱<?= htmlspecialchars($car['daily_rate']) ?>/day</div>
                        </div>
                        <div class="car-info">
                            <div class="car-title-container">
                                <h2 class="car-title"><?= htmlspecialchars($car['make']) . ' ' . htmlspecialchars($car['model']) ?></h2>
                            </div>
                            <p class="location"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($car['location']) ?></p>
                            <div class="car-details">
                                <span><i class="fa-solid fa-users"></i> <?= htmlspecialchars($car['seats']) ?> seats</span>
                                <span><i class="fa-solid fa-gear"></i> <?= htmlspecialchars($car['transmission']) ?></span>
                            </div>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div> -->
        <div class="car-container" id="car-container">

        </div>
        <?php
        include_once "../footer.php";
        ?>
    </div>
    <script>
        (() => {
            function loadCars() {
                const container = document.getElementById('car-container');
                if (!container) return;

                fetch('/php/get-cars.php')
                    .then(response => response.json())
                    .then(cars => {
                        const tempContainer = document.createElement('div');

                        cars.forEach(car => {
                            const carCard = document.createElement('a');
                            carCard.href = `/car-details?id=${car.car_id}`;
                            carCard.className = 'car-link';
                            carCard.innerHTML = `
                    <div class="car-card">
                        <div class="car-image">
                            <img src="/php/car-images/${car.car_id}/${car.image_path}" alt="${car.make} ${car.model}">
                            <div class="price-tag">₱${car.daily_rate}/day</div>
                        </div>
                        <div class="car-info">
                            <div class="car-title-container">
                                <h2 class="car-title">${car.make} ${car.model}</h2>
                            </div>
                            <p class="location"><i class="fa-solid fa-location-dot"></i> ${car.location}</p>
                            <div class="car-details">
                                <span><i class="fa-solid fa-users"></i> ${car.seats} seats</span>
                                <span><i class="fa-solid fa-gear"></i> ${car.transmission}</span>
                            </div>
                        </div>
                    </div>
                `;
                            tempContainer.appendChild(carCard);
                        });

                        container.innerHTML = tempContainer.innerHTML;
                    })
                    .catch(error => {
                        console.error('Error loading cars:', error);
                    });
            }

            const observer = new MutationObserver(() => {
                const carContainer = document.getElementById('car-container');
                if (carContainer) {
                    loadCars();
                    observer.disconnect();
                    setInterval(loadCars, 30000);
                }
            });

            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        })();
    </script>

</body>

</html>
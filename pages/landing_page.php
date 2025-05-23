<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../style/landingPage.css">
</head>

<body>
    <header>
        <div class="section__container header__container" id="home">
            <div class="header__image">
                <img src="assets/ford.png" alt="header" />
            </div>
            <div class="header__content">
                <h1>Discover Veehive Peer-to-Peer Car Rentals in the Philippines</h1>
                <p>
                    Veehive is a peer-to-peer car rental platform that connects car owners directly with renters across the Philippines. Whether you're looking to earn extra income by listing your car or searching for affordable rental options, Veehive makes it easy and secure.
                </p>
                <div class="header__links">
                    <a href="#">
                        <img src="assets/store.jpg" alt="app store" />
                    </a>
                    <a href="#">
                        <img src="assets/play.png" alt="play" />
                    </a>
                </div>
            </div>
        </div>
    </header>

    <section class="section__container steps__container" id="rent">
        <p class="section__subheader">HOW IT WORKS</p>
        <h2 class="section__header">following 3 working steps</h2>
        <div class="steps__grid">
            <div class="steps__card">
                <span><i class="ri-map-pin-fill"></i></span>
                <h4>Choose a location</h4>
                <p>
                    Select your desired rental location from our extensive network of
                    car rental spots.
                </p>
            </div>
            <div class="steps__card">
                <span><i class="ri-calendar-check-fill"></i></span>
                <h4>Pick-up date</h4>
                <p>
                    Specify the date and time you wish to pick up your car with flexible
                    scheduling options.
                </p>
            </div>
            <div class="steps__card">
                <span><i class="ri-bookmark-3-fill"></i></span>
                <h4>Book your car</h4>
                <p>
                    Browse through our wide range of vehicles and choose the one that
                    best suits your needs.
                </p>
            </div>
        </div>
    </section>

    <section class="section__container service__container" id="service">
        <div class="service__image">
            <img src="assets/ranger.png" alt="service" />
        </div>
        <div class="service__content">
            <p class="section__subheader">BEST SERVICES</p>
            <h2 class="section__header">
                Feel the best experience with our rental deals
            </h2>
            <ul class="service__list">
                <li>
                    <span><i class="ri-price-tag-3-fill"></i></span>
                    <div>
                        <h4>Deals for every budget</h4>
                        <p>
                            From economy cars to luxury vehicles, we have something for
                            everyone, ensuring you get the best value for your money.
                        </p>
                    </div>
                </li>
                <li>
                    <span><i class="ri-wallet-fill"></i></span>
                    <div>
                        <h4>Best price guarantee</h4>
                        <p>
                            We ensure you get competitive rates in the market, so you can
                            book with confidence knowing you're getting the best deal.
                        </p>
                    </div>
                </li>
                <li>
                    <span><i class="ri-customer-service-fill"></i></span>
                    <div>
                        <h4>Support 24/7</h4>
                        <p>
                            Our dedicated team is available 24/7 to assist you with any
                            questions or concerns, ensuring a smooth rental experience.
                        </p>
                    </div>
                </li>
            </ul>
        </div>
    </section>

    <section class="section__container experience__container" id="ride">
        <p class="section__subheader">CUSTOMER EXPERIENCE</p>
        <h2 class="section__header">
            We are ensuring the best customer experience
        </h2>
        <div class="experience__content">
            <div class="experience__card">
                <span><i class="ri-price-tag-3-fill"></i></span>
                <h4>Competitive pricing</h4>
            </div>
            <div class="experience__card">
                <span><i class="ri-money-rupee-circle-fill"></i></span>
                <h4>Easier Rent On Your Budget</h4>
            </div>
            <div class="experience__card">
                <span><i class="ri-bank-card-fill"></i></span>
                <h4>Most Felxible Payment Plans</h4>
            </div>
            <div class="experience__card">
                <span><i class="ri-award-fill"></i></span>
                <h4>The Best Extended Auto Warranties</h4>
            </div>
            <div class="experience__card">
                <span><i class="ri-customer-service-2-fill"></i></span>
                <h4>Roadside Assistance 24/7</h4>
            </div>
            <div class="experience__card">
                <span><i class="ri-car-fill"></i></span>
                <h4>Your Choice Of Mechanic</h4>
            </div>
            <img src="assets/experience.png" alt="experience" />
        </div>
    </section>

    <section class="section__container download__container" id="contact">
        <div class="download__grid">
            <div class="download__content">
                <h2 class="section__header">Download the free Veehive app</h2>
                <p>
                    Download the Veehive app to manage your bookings, find exclusive
                    deals, and access 24/7 support, all from your mobile device.
                </p>
                <div class="download__links">
                    <a href="#">
                        <img src="assets/store.jpg" alt="app store" />
                    </a>
                    <a href="#">
                        <img src="assets/play.png" alt="play" />
                    </a>
                </div>
            </div>
            <div class="download__image">
                <img src="assets/download.png" alt="download" />
            </div>
        </div>
    </section>

    <?php include '../footer.php'; ?>

    <script src="https://unpkg.com/scrollreveal" defer></script>
    <script src="javascript/main.js"></script>
</body>

</html>
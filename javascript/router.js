(() => {
    const route = (event) => {
        event = event || window.event;
        event.preventDefault();
        window.history.pushState({}, "", event.target.href);
        handleLocation();

        document.querySelectorAll('#menuList li a').forEach(link => {
            link.classList.remove('active');
        });

        event.currentTarget.classList.add('active');
    };

    const routes = {
        "/": "/pages/home.php",
        "/Cars": "/pages/listCars.php",
        "/MyBookings": "/pages/bookings.php",
        "/MyCars": "/pages/myCars.php",
        "/login": "login.php",
        "/signup": "signup.php",
        "/message": "users.php",
        "/chat": "chat.php",
        "/listCar": "/pages/list-your-car.php",
        "/car-details": "/pages/car_details_booking.php",
        "/booking-confirmation": "/pages/booking-confirmation.php",
        "/google-signup": "/signup_google.php",
        "404": "/pages/404.php",
    };

    const getBasePath = (pathname) => {

        return pathname.split("?")[0];
    };

    const handleLocation = async () => {
        let fullPath = window.location.pathname + window.location.search;
        let path = getBasePath(window.location.pathname);

        if (!routes[path]) {
            window.history.replaceState({}, "", "/");
            path = "/";
        }

        const routePath = routes[path] || routes["404"];

        try {
            const html = await fetch(routePath + window.location.search).then(response => {
                if (!response.ok) throw new Error("Network response was not ok");
                return response.text();
            });

            const mainPage = document.getElementById("main-page");
            mainPage.innerHTML = html;

            const scripts = mainPage.querySelectorAll("script");
            scripts.forEach(oldScript => {
                const newScript = document.createElement("script");
                if (oldScript.src) {
                    newScript.src = oldScript.src;
                    newScript.type = oldScript.type || "text/javascript";
                    document.body.appendChild(newScript);
                } else {
                    newScript.textContent = oldScript.textContent;
                    document.body.appendChild(newScript);
                }
                oldScript.remove();
            });

            updateActiveLink(path);
        } catch (error) {
            console.error("Failed to load the page:", error);
            if (path !== "/") {
                window.history.replaceState({}, "", "/");
                handleLocation();
            }
        }
    };
    
    // let isLoading = false;

    // const handleLocation = async () => {
    //     if (isLoading) return;
    //     isLoading = true;

    //     const mainPage = document.getElementById("main-page");
    //     mainPage.style.opacity = "0.4"; // visually fade out

    //     let path = getBasePath(window.location.pathname);
    //     if (!routes[path]) {
    //         window.history.replaceState({}, "", "/");
    //         path = "/";
    //     }

    //     const routePath = routes[path] || routes["404"];

    //     try {
    //         const response = await fetch(routePath + window.location.search);
    //         if (!response.ok) throw new Error("Network error");

    //         const html = await response.text();

    //         // Create temporary container
    //         const tempDiv = document.createElement("div");
    //         tempDiv.innerHTML = html;

    //         // Extract scripts before injecting
    //         const scripts = tempDiv.querySelectorAll("script");
    //         scripts.forEach(script => script.remove());

    //         // Inject clean HTML
    //         mainPage.innerHTML = tempDiv.innerHTML;

    //         // Inject scripts (re-run JS)
    //         scripts.forEach(oldScript => {
    //             const newScript = document.createElement("script");
    //             if (oldScript.src) {
    //                 newScript.src = oldScript.src;
    //                 newScript.type = oldScript.type || "text/javascript";
    //             } else {
    //                 newScript.textContent = oldScript.textContent;
    //             }
    //             document.body.appendChild(newScript);
    //         });

    //         updateActiveLink(path);

    //         // Wait a short moment, then fade in
    //         setTimeout(() => {
    //             mainPage.style.transition = "opacity 0.2s ease";
    //             mainPage.style.opacity = "1";
    //         }, 10);

    //     } catch (err) {
    //         console.error("Page load error:", err);
    //     } finally {
    //         isLoading = false;
    //     }
    // };


    const updateActiveLink = (currentPath) => {
        document.querySelectorAll('#menuList li a').forEach(link => {
            link.classList.remove('active');

            let linkPath = link.getAttribute('href');

            if (linkPath !== '/' && !linkPath.startsWith('/')) {
                linkPath = '/' + linkPath;
            }

            if (currentPath === linkPath) {
                link.classList.add('active');
            }
        });
    };

    window.onpopstate = handleLocation;
    window.route = route;

    document.addEventListener("DOMContentLoaded", () => {
        // Don't force redirect to home page on load
        // Keep the current path from the URL
        handleLocation();
    });

})();


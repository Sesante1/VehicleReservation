(() => {
    // const route = (event) => {
    //     event = event || window.event;
    //     event.preventDefault();
    //     window.history.pushState({}, "", event.target.href);
    //     handleLocation();

    //     document.querySelectorAll('#menuList li a').forEach(link => {
    //         link.classList.remove('active');
    //     });

    //     event.currentTarget.classList.add('active');
    // };

    // const routes = {
    //     "/": "/pages/home.php",
    //     "/Cars": "/pages/listCars.php",
    //     "/MyBookings": "/pages/bookings.php",
    //     "/MyCars": "/pages/myCars.php",
    //     "/login": "login.php",
    //     "/signup": "signup.php",
    //     "/message": "users.php",
    //     "/chat": "chat.php",
    //     "/listCar": "/pages/list-your-car.php",
    //     "/car-details": "/pages/car_details_booking.php",
    //     "/booking-confirmation": "/pages/booking-confirmation.php",
    //     "/google-signup": "/signup_google.php",
    //     "/landingPage": "/landing_page.php",
    //     "/update-car": "/pages/update-car-listing.php",
    //     "404": "/pages/404.php",
    // };

    // const getBasePath = (pathname) => {
    //     return pathname.split("?")[0];
    // };

    // const handleLocation = async () => {
    //     let fullPath = window.location.pathname + window.location.search;
    //     let path = getBasePath(window.location.pathname);

    //     if (!routes[path]) {
    //         window.history.replaceState({}, "", "/");
    //         path = "/";
    //     }

    //     const routePath = routes[path] || routes["404"];

    //     try {
    //         const html = await fetch(routePath + window.location.search).then(response => {
    //             if (!response.ok) throw new Error("Network response was not ok");
    //             return response.text();
    //         });

    //         const mainPage = document.getElementById("main-page");
    //         mainPage.innerHTML = html;

    //         const scripts = mainPage.querySelectorAll("script");
    //         scripts.forEach(oldScript => {
    //             const newScript = document.createElement("script");
    //             if (oldScript.src) {
    //                 newScript.src = oldScript.src;
    //                 newScript.type = oldScript.type || "text/javascript";
    //                 document.body.appendChild(newScript);
    //             } else {
    //                 newScript.textContent = oldScript.textContent;
    //                 document.body.appendChild(newScript);
    //             }
    //             oldScript.remove();
    //         });

    //         updateActiveLink(path);
    //     } catch (error) {
    //         console.error("Failed to load the page:", error);
    //         if (path !== "/") {
    //             window.history.replaceState({}, "", "/");
    //             handleLocation();
    //         }
    //     }
    // };

    // const updateActiveLink = (currentPath) => {
    //     document.querySelectorAll('#menuList li a').forEach(link => {
    //         link.classList.remove('active');

    //         let linkPath = link.getAttribute('href');

    //         if (linkPath !== '/' && !linkPath.startsWith('/')) {
    //             linkPath = '/' + linkPath;
    //         }

    //         if (currentPath === linkPath) {
    //             link.classList.add('active');
    //         }
    //     });
    // };

    // window.onpopstate = handleLocation;
    // window.route = route;

    // document.addEventListener("DOMContentLoaded", () => {
    //     handleLocation();
    // });

    const isAdmin = window.location.pathname.includes("admin.php") || window.location.pathname.startsWith("/dashboard") 
                    || window.location.pathname.startsWith("/pendingUsers");

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

    const userRoutes = {
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
        "/landingPage": "/landing_page.php",
        "/update-car": "/pages/update-car-listing.php",
        "404": "/pages/404.php",
    };

    const adminRoutes = {
        "/dashboard": "/pages/adminDashboard.php",
        "/pendingUsers": "/pages/adminPendingUsers.php",
        "404": "/pages/404.php"
    };

    const routes = isAdmin ? adminRoutes : userRoutes;

    const getBasePath = (pathname) => pathname.split("?")[0];

    const handleLocation = async () => {
        let path = getBasePath(window.location.pathname);

        if (!routes[path]) {
            window.history.replaceState({}, "", isAdmin ? "/admin.php" : "/");
            path = isAdmin ? "/dashboard" : "/";
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
            if (path !== (isAdmin ? "/dashboard" : "/")) {
                window.history.replaceState({}, "", isAdmin ? "/admin.php" : "/");
                handleLocation();
            }
        }
    };

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
        handleLocation();
    });

    
    // Check if we're in admin context - this now checks for both admin.php and admin routes
    // Check if we're in admin context - this now checks for both admin.php and admin routes

    // const isAdmin = window.location.pathname.includes("admin.php") ||
    //     window.location.pathname.startsWith("/admin/") ||
    //     window.location.pathname === "/admin" ||
    //     window.location.href.includes("admin.php") ||
    //     document.documentElement.getAttribute('data-context') === 'admin' ||
    //     document.querySelector('script[src*="admin"]') !== null;

    // const route = (event) => {
    //     event = event || window.event;
    //     event.preventDefault();

    //     // Get the href from the clicked link
    //     let href = event.target.href || event.currentTarget.href;

    //     // If we're in admin context and the href doesn't start with /admin, add /admin prefix
    //     if (isAdmin && href && !href.includes('/admin/') && !href.includes('admin.php')) {
    //         const url = new URL(href);
    //         const path = url.pathname;

    //         // Add /admin prefix to the path
    //         if (!path.startsWith('/admin/')) {
    //             url.pathname = '/admin' + path;
    //             href = url.toString();
    //         }
    //     }

    //     window.history.pushState({}, "", href);
    //     handleLocation();

    //     document.querySelectorAll('#menuList li a').forEach(link => {
    //         link.classList.remove('active');
    //     });

    //     event.currentTarget.classList.add('active');
    // };

    // const userRoutes = {
    //     "/": "/pages/home.php",
    //     "/Cars": "/pages/listCars.php",
    //     "/MyBookings": "/pages/bookings.php",
    //     "/MyCars": "/pages/myCars.php",
    //     "/login": "login.php",
    //     "/signup": "signup.php",
    //     "/message": "users.php",
    //     "/chat": "chat.php",
    //     "/listCar": "/pages/list-your-car.php",
    //     "/car-details": "/pages/car_details_booking.php",
    //     "/booking-confirmation": "/pages/booking-confirmation.php",
    //     "/google-signup": "/signup_google.php",
    //     "/landingPage": "/landing_page.php",
    //     "/update-car": "/pages/update-car-listing.php",
    //     "404": "/pages/404.php",
    // };

    // const adminRoutes = {
    //     "/": "/pages/adminDashboard.php",           // Root admin route
    //     "/dashboard": "/pages/adminDashboard.php",
    //     "/pendingUsers": "/pages/adminPendingUsers.php",
    //     "404": "/pages/404.php"
    // };

    // const routes = isAdmin ? adminRoutes : userRoutes;

    // const getBasePath = (pathname) => pathname.split("?")[0];

    // // Function to normalize paths for admin context
    // const normalizePath = (pathname) => {
    //     let path = getBasePath(pathname);

    //     // If we're in admin context, remove /admin prefix for route matching
    //     if (isAdmin && path.startsWith('/admin/')) {
    //         path = path.replace('/admin', '');
    //     }

    //     // If path is empty, set to root
    //     if (!path || path === '/admin') {
    //         path = '/';
    //     }

    //     return path;
    // };

    // const handleLocation = async () => {
    //     let path = normalizePath(window.location.pathname);

    //     // Debug logging
    //     console.log('handleLocation - Full pathname:', window.location.pathname);
    //     console.log('handleLocation - Normalized path:', path);
    //     console.log('handleLocation - isAdmin:', isAdmin);

    //     // Check if route exists, if not redirect to appropriate default
    //     if (!routes[path]) {
    //         console.log('Route not found for path:', path);
    //         const defaultUrl = isAdmin ? "/admin/dashboard" : "/";
    //         console.log('Redirecting to default:', defaultUrl);
    //         window.history.replaceState({}, "", defaultUrl);
    //         path = isAdmin ? "/dashboard" : "/";
    //     }

    //     const routePath = routes[path] || routes["404"];
    //     console.log('Loading file:', routePath);

    //     try {
    //         const html = await fetch(routePath + window.location.search).then(response => {
    //             if (!response.ok) throw new Error("Network response was not ok");
    //             return response.text();
    //         });

    //         const mainPage = document.getElementById("main-page");
    //         mainPage.innerHTML = html;

    //         const scripts = mainPage.querySelectorAll("script");
    //         scripts.forEach(oldScript => {
    //             const newScript = document.createElement("script");
    //             if (oldScript.src) {
    //                 newScript.src = oldScript.src;
    //                 newScript.type = oldScript.type || "text/javascript";
    //                 document.body.appendChild(newScript);
    //             } else {
    //                 newScript.textContent = oldScript.textContent;
    //                 document.body.appendChild(newScript);
    //             }
    //             oldScript.remove();
    //         });

    //         updateActiveLink(path);
    //     } catch (error) {
    //         console.error("Failed to load the page:", error);
    //         // On error, redirect to appropriate default
    //         const defaultUrl = isAdmin ? "/admin/dashboard" : "/";
    //         if (window.location.pathname !== defaultUrl) {
    //             window.history.replaceState({}, "", defaultUrl);
    //             handleLocation();
    //         }
    //     }
    // };

    // const updateActiveLink = (currentPath) => {
    //     document.querySelectorAll('#menuList li a').forEach(link => {
    //         link.classList.remove('active');
    //         let linkPath = link.getAttribute('href');

    //         // Normalize the link path for comparison
    //         if (linkPath && !linkPath.startsWith('/')) {
    //             linkPath = '/' + linkPath;
    //         }

    //         // For admin context, we need to compare normalized paths
    //         let comparePath = currentPath;
    //         if (isAdmin && linkPath && linkPath.startsWith('/admin/')) {
    //             // Remove /admin prefix from link path for comparison
    //             linkPath = linkPath.replace('/admin', '');
    //         }

    //         if (currentPath === linkPath) {
    //             link.classList.add('active');
    //         }
    //     });
    // };

    // window.onpopstate = handleLocation;
    // window.route = route;

    // document.addEventListener("DOMContentLoaded", () => {
    //     handleLocation();
    // });
})();

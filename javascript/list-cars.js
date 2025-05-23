// Tab functions
function showTab(tabNumber) {

    document.querySelectorAll('.content').forEach(c => c.classList.remove('active'));

    document.querySelectorAll('.btn').forEach(b => b.classList.remove('active'));

    document.getElementById('content' + tabNumber).classList.add('active');

    document.getElementById('btn' + tabNumber).classList.add('active');
}

function closeModal() {
    document.getElementById("carStatusModal").style.display = "none";
}

function openDeclineModal(button) {
    // document.getElementById("declineModal").style.display = "flex";
    const bookingId = button.getAttribute('data-id');
    const modal = document.getElementById('declineModal');
    const reasonSelect = modal.querySelector('.reason-select');
    const cancelBtn = modal.querySelector('.declineBook');

    modal.style.display = "flex";

    reasonSelect.selectedIndex = 0;

    const newCancelBtn = cancelBtn.cloneNode(true);
    cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);

    newCancelBtn.addEventListener('click', function () {
        const reason = reasonSelect.value;

        if (!reason || reason === 'Select a reason...') {
            alert('Please select a reason.');
            return;
        }

        fetch('/php/cancel_booking.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                booking_id: bookingId,
                reason: reason
            })
        })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    alert('Booking cancelled successfully.');
                    modal.style.display = 'none';
                } else {
                    alert('Cancellation failed: ' + result.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });
}

function closeDeclineModal() {
    document.getElementById("declineModal").style.display = "none";
}

function openCancelBookingModal(button) {
    const bookingId = button.getAttribute('data-id');
    const modal = document.getElementById('cancel-booking');
    const reasonSelect = modal.querySelector('.reason-select');
    const cancelBtn = modal.querySelector('.cancelBook');

    modal.style.display = "flex";

    reasonSelect.selectedIndex = 0;

    const newCancelBtn = cancelBtn.cloneNode(true);
    cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);

    newCancelBtn.addEventListener('click', function () {
        const reason = reasonSelect.value;

        if (!reason || reason === 'Select a reason...') {
            alert('Please select a reason.');
            return;
        }

        fetch('/php/cancel_booking.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                booking_id: bookingId,
                reason: reason
            })
        })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    alert('Booking cancelled successfully.');
                    modal.style.display = 'none';
                } else {
                    alert('Cancellation failed: ' + result.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });
}

function closeCancelBookingModal() {
    document.getElementById("cancel-booking").style.display = "none";

    currentBookingId = null;
    document.querySelector('.reason-select').selectedIndex = 0;
}

(() => {
    // Modal functions
    function openCarReturnedStatusModal(bookingId, statusType) {
        const modal = document.getElementById("carStatusModal");
        const confirmBtn = document.getElementById("modalConfirmBtn");

        const title = 'Car returned';
        const description = 'Are you sure you want to mark this booking as completed? This action cannot be undone.';

        document.getElementById('modalTitle').textContent = title;
        document.getElementById('modalDescription').textContent = description;
        confirmBtn.textContent = statusType === 'confirmed' ? 'Confirm' : 'Continue';

        confirmBtn.classList.remove('btn-green', 'btn-orange');
        if (statusType === 'confirmed') {
            confirmBtn.classList.add('btn-green');
        } else {
            confirmBtn.classList.add('btn-orange');
        }

        const newConfirmBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);

        newConfirmBtn.addEventListener('click', () => {
            updateBookingStatus(bookingId, statusType);
            closeModal();
        });

        modal.style.display = "flex";
    }

    function openBookingStatusModal(bookingId, statusType) {
        const modal = document.getElementById("carStatusModal");
        const confirmBtn = document.getElementById("modalConfirmBtn");

        const titleMap = {
            confirmed: 'Approve Booking?',
            completed: 'Car Returned?',
            default: 'Update Booking Status'
        };

        const descriptionMap = {
            confirmed: 'Are you sure you want to approve this booking? Approving will mark this booking as confirmed.',
            completed: 'Are you sure this car has been returned by the customer? This will mark the booking as completed.',
            default: 'Are you sure you want to change this booking\'s status?'
        };

        const title = titleMap[statusType] || titleMap.default;
        const description = descriptionMap[statusType] || descriptionMap.default;

        document.getElementById('modalTitle').textContent = title;
        document.getElementById('modalDescription').textContent = description;
        confirmBtn.textContent = statusType === 'confirmed' ? 'Confirm' : (statusType === 'completed' ? 'Mark as Returned' : 'Continue');

        confirmBtn.classList.remove('btn-green', 'btn-orange');

        if (statusType === 'confirmed') {
            confirmBtn.classList.add('btn-green');
        } else if (statusType === 'completed') {
            confirmBtn.classList.add('btn-orange');
        }

        const newConfirmBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);

        newConfirmBtn.addEventListener('click', () => {
            updateBookingStatus(bookingId, statusType);  // Passes 'completed'
            closeModal();
        });

        modal.style.display = "flex";
    }

    function openStatusModal(statusType, carId) {
        const modal = document.getElementById("carStatusModal");
        const confirmBtn = document.getElementById("modalConfirmBtn");

        const title = statusType === 'maintenance' ? 'Set Car to Maintenance' : 'Set Car to Active';
        const description = statusType === 'maintenance' ?
            'Are you sure you want to set this car to maintenance mode? It will not be visible to customers or available for bookings.' :
            'Are you sure you want to activate this car? It will be visible and available for bookings.';

        document.getElementById('modalTitle').textContent = title;
        document.getElementById('modalDescription').textContent = description;
        confirmBtn.textContent = statusType === 'maintenance' ? 'Confirm' : 'Activate';

        confirmBtn.classList.remove('btn-green', 'btn-orange');
        if (statusType === 'maintenance') {
            confirmBtn.classList.add('btn-orange');
        } else {
            confirmBtn.classList.add('btn-green');
        }

        // remove previous event listeners
        const newConfirmBtn = confirmBtn.cloneNode(true);
        confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);

        newConfirmBtn.addEventListener('click', () => {
            const status = statusType === 'maintenance' ? 0 : 1;
            updateCarStatus(carId, status);
            closeModal();
        });

        modal.style.display = "flex";
    }

    // Load My Cars
    function loadMyCars() {
        const container = document.getElementById('my-car-list');
        if (!container) return;

        fetch('/php/fetch_my_cars.php')
            .then(res => res.json())
            .then(cars => {
                container.innerHTML = '';

                if (cars.length === 0) {
                    container.innerHTML = `
                        <div class="no-cars-container">
                            <div class="no-cars-icon">🚗</div>
                            <div class="co-cars-heading">No cars listed</div>
                            <div class="no-cars-description">You haven't listed any cars for rent yet.</div>
                            <button class="no-cars-btn" onclick="window.location.href='/listCar'">List Your First Car</button>
                        </div>
                    `;
                    return;
                }

                cars.forEach(car => {
                    const earnings = typeof car.earnings === 'number' && !isNaN(car.earnings) ? car.earnings : 0;
                    const isActive = car.is_active == 1;

                    container.innerHTML += `
                        <div class="my-car-card" data-car-id="${car.car_id}">
                            <div class="car-image-container">
                                <img src="/php/car-images/${car.car_id}/${car.image_path}" alt="${car.make} ${car.model}">
                                ${isActive
                            ? `<div class="status-badge-active">Active</div>`
                            : `<div class="status-badge-maintenance">Maintenance</div>`
                        }
                            </div>
                            <div class="my-content">
                                <div class="my-car-title">${car.make} ${car.model} (${car.year})</div>
                                <div class="my-car-location"><i class="fa-solid fa-location-dot"></i> ${car.location}</div>
                                <div class="my-car-rating-price">
                                    <div class="car-rating"><i class="fa-solid fa-star"></i> ${car.rating || 'N/A'}</div>
                                    <div class="car-price">₱${car.daily_rate} / day</div>
                                </div>
                            </div>
                            <div class="stats">
                                <div><div class="label">Bookings</div><div><strong>${car.bookings}</strong></div></div>
                                <div><div class="label">Earnings</div><div><strong>₱${earnings.toFixed(2)}</strong></div></div>
                            </div>
                            <div class="actions">
                                ${isActive
                            ? `<button class="set-maintenance" data-id="${car.car_id}">Set to Maintenance</button>`
                            : `<button class="set-active" data-id="${car.car_id}">Set to Active</button>`
                        }
                                <button class="view-details">
                                    <a href="/car-details?id=${car.car_id}" onclick="route()">View Details</a>
                                </button>
                            </div>
                            <div class="edit-link">
                                <a class="edit-link" href="/update-car?id=${car.car_id}">Edit Listing</a>
                            </div>
                            
                        </div>
                    `;
                });

                container.querySelectorAll('.set-active').forEach(btn => {
                    btn.addEventListener('click', () => {
                        openStatusModal('active', btn.dataset.id);
                    });
                });

                container.querySelectorAll('.set-maintenance').forEach(btn => {
                    btn.addEventListener('click', () => {
                        openStatusModal('maintenance', btn.dataset.id);
                    });
                });
            })
            .catch(err => {
                console.error("Failed to fetch cars:", err);
            });
    }

    function updateCarStatus(carId, newStatus) {
        fetch('/php/update_car_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                car_id: carId,
                is_active: newStatus
            })
        })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    loadMyCars();
                } else {
                    alert('Failed to update status.');
                }
            })
            .catch(err => console.error("Status update failed:", err));
    }

    function updateBookingStatus(bookingId, newStatus) {
        const formData = new FormData();
        formData.append('booking_id', bookingId);
        formData.append('status', newStatus);

        fetch('/php/update_booking_status.php', {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    alert(response.message);
                    // loadCancelledBookings();
                } else {
                    alert("Failed: " + response.message);
                }
            })
            .catch(err => {
                console.error("Error updating booking:", err);
                alert("Something went wrong.");
            });
    }

    const carListObserver = new MutationObserver(() => {
        const myCarList = document.getElementById('my-car-list');
        if (myCarList) {
            loadMyCars();
            loadMyBookings();
            loadBookingRequests();
            loadCancelledBookings()
            loadCompletedBookings();
            const interval = setInterval(() => {
                if (document.body.contains(myCarList)) {
                    loadMyCars();
                    loadMyBookings();
                    loadBookingRequests();
                    loadCancelledBookings();
                    loadCompletedBookings();
                } else {
                    clearInterval(interval);
                }
            }, 1000);
            carListObserver.disconnect();
        }
    });

    carListObserver.observe(document.body, {
        childList: true,
        subtree: true
    });

    // Load My Bookings
    function loadMyBookings() {
        fetch('/php/fetch_bookings.php')
            .then(res => res.json())
            .then(bookings => {
                const list = document.getElementById('my-booking-list');
                list.innerHTML = '';

                if (bookings.length === 0) {
                    list.innerHTML = `
                        <div class="no-cars-container">
                            <div class="no-cars-icon">🚗</div>
                            <div class="co-cars-heading">No Booking listed</div>
                            <div class="no-cars-description">You haven't booked any cars yet.</div>
                            <button class="no-cars-btn" onclick="window.location.href='/'">Book now</button>
                        </div>
                    `;
                    return;
                }

                bookings.forEach(booking => {

                    const isPending = booking.booking_status === 'Pending';

                    list.innerHTML += `
                                    <div class="booking-card">
                                        <img src="/php/car-images/${booking.car_id}/${booking.image_path || 'default.jpg'}" alt="${booking.make} ${booking.model}" class="booking-car-image">
                                        <div class="booking-details">
                                            <h3>${booking.make} ${booking.model} 
                                                ${isPending
                            ? `<span class="pending-status">${booking.booking_status}</span>`
                            : `<span class="confirm-status">${booking.booking_status}</span>`
                        }
                                            </h3>
                                            <div class="booking-dates"><strong>Dates:</strong> ${booking.start_date} - ${booking.end_date}</div>
                                            <div class="booking-price"><strong>Total Price:</strong> ₱${booking.total_price}</div>
                                            <div class="booking-actions">
                                                <div>

                                                    <p><i class="fa-regular fa-clock"></i></i>Car owner responds within an hour</p>
                                                    <a href="/chat?user_id=${booking.owner_unique_id}"><i class="fa-solid fa-phone-volume"></i>&nbsp;&nbsp;Contact owner</a>
                                                </div>
                                                
                                                <div>
                                                    <a href="/car-details?id=${booking.car_id}">View Car Details</a>
                                                    <span class="cancel-booking" data-id="${booking.booking_id}" onclick="openCancelBookingModal(this)">Cancel Booking</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                });
            })
            .catch(err => {
                console.error("Failed to load bookings:", err);
            });
    }

    function loadCancelledBookings() {
        fetch('/php/fetch_cancelled_bookings.php')
            .then(res => res.json())
            .then(bookings => {
                const list = document.getElementById('cancelled-booking-list');
                list.innerHTML = '';

                if (bookings.length === 0) {
                    list.innerHTML = `
                        <div class="no-cars-container">
                            <div class="no-cars-icon">🚗</div>
                            <div class="co-cars-heading">No Booking listed</div>
                            <div class="no-cars-description">You haven't booked any cars yet.</div>
                            <button class="no-cars-btn" onclick="window.location.href='/'">Book now</button>
                        </div>
                    `;
                    return;
                }

                bookings.forEach(booking => {

                    const isPending = booking.booking_status === 'Pending';

                    list.innerHTML += `
                                    <div class="booking-card">
                                        <img src="/php/car-images/${booking.car_id}/${booking.image_path || 'default.jpg'}" alt="${booking.make} ${booking.model}" class="booking-car-image">
                                        <div class="booking-details">
                                            <h3>${booking.make} ${booking.model} 
                                                ${isPending
                            ? `<span class="pending-status">${booking.booking_status}</span>`
                            : `<span class="cancelled-status">${booking.booking_status}</span>`
                        }
                                            </h3>
                                            <div class="booking-dates"><strong>Dates:</strong> ${booking.start_date} - ${booking.end_date}</div>
                                            <div class="booking-price"><strong>Total Price:</strong> ₱${booking.total_price}</div>
                                            <div class="booking-actions">
                                                <a href="/chat?user_id=${booking.owner_unique_id}"><i class="fa-solid fa-phone-volume"></i>&nbsp;&nbsp;Contact Host</a>
                                                <div>
                                                    <a href="/car-details?id=${booking.car_id}">View Car Details</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                });
            })
            .catch(err => {
                console.error("Failed to load bookings:", err);
            });
    }

    function loadCompletedBookings() {
        fetch('/php/fetch_completed_bookings.php')
            .then(res => res.json())
            .then(bookings => {
                const list = document.getElementById('completed-booking-list');
                list.innerHTML = '';

                if (bookings.length === 0) {
                    list.innerHTML = `
                        <div class="no-cars-container">
                            <div class="no-cars-icon">🚗</div>
                            <div class="co-cars-heading">No Booking listed</div>
                            <div class="no-cars-description">You haven't booked any cars yet.</div>
                            <button class="no-cars-btn" onclick="window.location.href='/'">Book now</button>
                        </div>
                    `;
                    return;
                }

                bookings.forEach(booking => {

                    const isPending = booking.booking_status === 'Pending';

                    list.innerHTML += `
                                    <div class="booking-card">
                                        <img src="/php/car-images/${booking.car_id}/${booking.image_path || 'default.jpg'}" alt="${booking.make} ${booking.model}" class="booking-car-image">
                                        <div class="booking-details">
                                            <h3>${booking.make} ${booking.model} 
                                                ${isPending
                            ? `<span class="pending-status">${booking.booking_status}</span>`
                            : `<span class="cancelled-status">${booking.booking_status}</span>`
                        }
                                            </h3>
                                            <div class="booking-dates"><strong>Dates:</strong> ${booking.start_date} - ${booking.end_date}</div>
                                            <div class="booking-price"><strong>Total Price:</strong> ₱${booking.total_price}</div>
                                            <div class="booking-actions">
                                                <a href="#"><i class="fa-regular fa-star"></i>&nbsp;&nbsp;Leave a review</a>
                                                <div>
                                                    <a href="/car-details?id=${booking.car_id}">Rent again</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                });
            })
            .catch(err => {
                console.error("Failed to load bookings:", err);
            });
    }

    // Load pending requests
    function loadBookingRequests() {
        fetch('/php/fetch_bookings_request.php')
            .then(res => res.json())
            .then(requests => {
                const container = document.getElementById('booking-request-list');
                container.innerHTML = '';

                if (requests.length === 0) {
                    container.innerHTML = `
                        <div class="no-cars-container">
                            <div class="no-cars-icon">🚗</div>
                            <div class="co-cars-heading">No Booking listed</div>
                            <div class="no-cars-description">You haven't booked any cars yet.</div>
                            <button class="no-cars-btn" onclick="window.location.href='/'">Book now</button>
                        </div>
                    `;
                    return;
                }

                requests.forEach(request => {
                    container.innerHTML += `
                            <div class="request-card">
                                <div class="request-image">
                                    <img src="php/car-images/${request.car_id}/${request.image_path || 'default.jpg'}" alt="${request.make} ${request.model}" />
                                </div>
                                <div class="request-info">
                                    <h3 class="request-title">${request.make} ${request.model}</h3>
                                    <p class="request-dates"><i class="fa-solid fa-calendar-week"></i>&nbsp;&nbsp;Pick up: &nbsp;${request.start_date} to &nbsp;<i class="fa-solid fa-calendar-week"></i>&nbsp;&nbsp;Return:&nbsp;&nbsp;${request.end_date}</p>
                                    <div class="request-user">
                                        <img src="php/images/${request.user_image || 'default-user.png'}" alt="User" class="request-avatar" />
                                        <div class="request-user-details">
                                            <strong>${request.user_name}</strong>
                                            <i class="fa-regular fa-star"><span>&nbsp;4.7</span></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="request-actions">
                                    <span class="pending-status">${request.status}</span>
                                    <p class="request-total"><strong>Total:</strong> ₱${request.total_price}</p>
                                    <div class="request-buttons">
                                        ${request.status === 'Confirmed'
                            ? `<button class="request-return-btn" data-id="${request.booking_id}">Car Returned</button>`
                            : `
                                                    <button class="request-decline-btn" data-id="${request.booking_id}" onclick="openDeclineModal(this)">Decline</button>
                                                    <button class="request-approve-btn" data-id="${request.booking_id}">Approve</button>
                                                    `
                        }
                                    </div>
                                </div>
                            </div>
                        `;
                });
                document.querySelectorAll('.request-approve-btn').forEach(button => {
                    button.addEventListener('click', () => {
                        const bookingId = button.dataset.id;
                        openBookingStatusModal(bookingId, 'confirmed');
                    });
                });

                document.querySelectorAll('.request-return-btn').forEach(button => {
                    button.addEventListener('click', () => {
                        const bookingId = button.dataset.id;
                        openBookingStatusModal(bookingId, 'completed');
                    });
                });
            })
            .catch(error => {
                console.error('Failed to load booking requests:', error);
                document.getElementById('booking-request-list').innerHTML = '<p>Error loading requests.</p>';
            });
    }
})();
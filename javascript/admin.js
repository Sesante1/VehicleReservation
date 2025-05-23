(function () {
    let carModal = document.getElementById('carModal');
    let carModalBody = document.getElementById('modalBody');
    let carModalTitle = document.getElementById('modalTitle');
    let carCloseBtn = document.getElementsByClassName('close')[0];

    let userModal = document.getElementById('userModal');
    let userModalBody = document.getElementById('userModalBody');
    let userModalTitle = document.getElementById('userModalTitle');
    let userCloseBtn = document.getElementById('userModalClose');

    carCloseBtn.onclick = function () {
        carModal.style.display = 'none';
    }

    userCloseBtn.onclick = function () {
        userModal.style.display = 'none';
    }

    window.onclick = function (event) {
        if (event.target == carModal) {
            carModal.style.display = 'none';
        }
        if (event.target == userModal) {
            userModal.style.display = 'none';
        }
    }

    // Car Modal Functions
    window.openCarModal = function (carId) {
        carModal.style.display = 'block';
        carModalBody.innerHTML = '<div class="loading">Loading car details...</div>';

        fetch(`?get_car_details=1&car_id=${carId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayCarDetails(data.car, data.images, data.documents);
                } else {
                    carModalBody.innerHTML = `<div class="error-message">Error: ${data.message}</div>`;
                }
            })
            .catch(error => {
                carModalBody.innerHTML = `<div class="error-message">Error loading car details: ${error.message}</div>`;
            });
    };

    function displayCarDetails(car, images, documents) {
        carModalTitle.textContent = `${car.make} ${car.model} (${car.year})`;

        let featuresHtml = '';
        if (car.features && Array.isArray(car.features)) {
            featuresHtml = car.features.join(', ');
        } else if (car.features) {
            featuresHtml = car.features;
        } else {
            featuresHtml = 'N/A';
        }

        let imagesHtml = '';
        if (images && images.length > 0) {
            imagesHtml = images.map(img => `
                <div class="image-item">
                    <img src="php/car-images/${car.id}/${img.image_path}" alt="Car Image" onerror="this.src='php/images/no-image.jpg'">
                </div>
            `).join('');
        } else {
            imagesHtml = '<p>No images available</p>';
        }

        let documentsHtml = '';
        if (documents && documents.length > 0) {
            documentsHtml = documents.map(doc => `
                <div class="document-item">
                    <img class="document-img" src="php/documents/${car.id}/${doc.image_path}" alt="${doc.document_type} Document" onerror="this.src='php/images/no-document.jpg'">
                    <div class="document-label">${doc.document_type} Document</div>
                </div>
            `).join('');
        } else {
            documentsHtml = '<p>No documents available</p>';
        }

        carModalBody.innerHTML = `
            <div class="car-details-grid">
                <div class="car-detail-item">
                    <div class="car-detail-label"><i class="fa-solid fa-user"></i> &nbsp;&nbsp;Owner</div>
                    <div class="car-detail-value">${car.fname} ${car.lname}</div>
                </div>
                <div class="car-detail-item">
                    <div class="car-detail-label"><i class="fa-solid fa-envelope"></i> &nbsp;&nbsp;Email</div>
                    <div class="car-detail-value">${car.email}</div>
                </div>
                <div class="car-detail-item">
                    <div class="car-detail-label"><i class="fa-solid fa-phone"></i> &nbsp;&nbsp;Phone</div>
                    <div class="car-detail-value">${car.phone || 'N/A'}</div>
                </div>
                <div class="car-detail-item">
                    <div class="car-detail-label"><i class="fa-solid fa-gas-pump"></i> &nbsp;&nbsp;Car Type</div>
                    <div class="car-detail-value">${car.car_type}</div>
                </div>
                <div class="car-detail-item">
                    <div class="car-detail-label"><i class="fa-solid fa-tag"></i> &nbsp;&nbsp;Daily Rate</div>
                    <div class="car-detail-value">₱ ${parseInt(car.daily_rate)}</div>
                </div>
                <div class="car-detail-item">
                    <div class="car-detail-label"><i class="fa-solid fa-location-dot"></i> &nbsp;&nbsp;Location</div>
                    <div class="car-detail-value">${car.location}</div>
                </div>
                <div class="car-detail-item">
                    <div class="car-detail-label"><i class="fa-solid fa-gears"></i> &nbsp;&nbsp;Transmission</div>
                    <div class="car-detail-value">${car.transmission}</div>
                </div>
                <div class="car-detail-item">
                    <div class="car-detail-label"><i class="fa-solid fa-users"></i> &nbsp;&nbsp;Seats</div>
                    <div class="car-detail-value">${car.seats}</div>
                </div>
                <div class="car-detail-item">
                    <div class="car-detail-label"><i class="fa-solid fa-calendar-week"></i> &nbsp;&nbsp;Available From</div>
                    <div class="car-detail-value">${car.available_from}</div>
                </div>
                <div class="car-detail-item">
                    <div class="car-detail-label"><i class="fa-solid fa-calendar-week"></i> &nbsp;&nbsp;Available Until</div>
                    <div class="car-detail-value">${car.available_until || 'N/A'}</div>
                </div>
                <div class="car-detail-item" style="grid-column: 1 / -1;">
                    <div class="car-detail-label">Description</div>
                    <div class="car-detail-value">${car.description}</div>
                </div>
                <div class="car-detail-item" style="grid-column: 1 / -1;">
                    <div class="car-detail-label">Features</div>
                    <div class="car-detail-value">${featuresHtml}</div>
                </div>
            </div>

            <div class="images-section">
                <h3>Car Images</h3>
                <div class="images-grid">
                    ${imagesHtml}
                </div>
            </div>

            <div class="documents-section">
                <h3>Car Documents</h3>
                <div class="documents-grid">
                    ${documentsHtml}
                </div>
            </div>

            ${car.verified === 'Pending' ? `
            <div class="action-buttons">
                <button class="btn btn-approve" onclick="approveDeclineCar(${car.id}, 'approve')">
                    <i class="fa-solid fa-check-to-slot"></i> Approve
                </button>
                <button class="btn btn-decline" onclick="approveDeclineCar(${car.id}, 'decline')">
                    <i class="fa-solid fa-rectangle-xmark"></i> Decline
                </button>
            </div>
            ` : ''}
        `;
    }

    window.approveDeclineCar = function (carId, action) {
        if (!confirm(`Are you sure you want to ${action} this car?`)) {
            return;
        }

        const actionButtons = document.querySelector('.action-buttons');
        actionButtons.innerHTML = '<div class="loading">Processing...</div>';

        fetch('', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=${action}&car_id=${carId}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    carModal.style.display = 'none';
                    location.reload();
                } else {
                    alert(`Error: ${data.message}`);
                    actionButtons.innerHTML = `
                    <button class="btn btn-approve" onclick="approveDeclineCar(${carId}, 'approve')">
                        <i class="fa-solid fa-check-to-slot"></i> Approve
                    </button>
                    <button class="btn btn-decline" onclick="approveDeclineCar(${carId}, 'decline')">
                        <i class="fa-solid fa-rectangle-xmark"></i> Decline
                    </button>
                `;
                }
            })
            .catch(error => {
                alert(`Error: ${error.message}`);
                actionButtons.innerHTML = `
                <button class="btn btn-approve" onclick="approveDeclineCar(${carId}, 'approve')">
                    <i class="fa-solid fa-check-to-slot"></i> Approve
                </button>
                <button class="btn btn-decline" onclick="approveDeclineCar(${carId}, 'decline')">
                    <i class="fa-solid fa-rectangle-xmark"></i> Decline
                </button>
            `;
            });
    };

    // User Modal Functions
    window.openUserModal = function (userId) {
        userModal.style.display = 'block';
        userModalBody.innerHTML = '<div class="loading">Loading user details...</div>';

        fetch(`?get_user_details=1&user_id=${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayUserDetails(data.user, data.license);
                } else {
                    userModalBody.innerHTML = `<div class="error-message">Error: ${data.message}</div>`;
                }
            })
            .catch(error => {
                userModalBody.innerHTML = `<div class="error-message">Error loading user details: ${error.message}</div>`;
            });
    };

    function displayUserDetails(user, license) {
        userModalTitle.textContent = `${user.fname} ${user.lname} - User Details`;

        let licensePhotosHtml = '';
        if (license) {
            licensePhotosHtml = `
                <div class="license-photos">
                    ${license.license_front_photo ? `
                        <div class="license-item">
                            <img class="document-img" src="php/uploads/licenses/${license.license_front_photo}" alt="License Front" onerror="this.src='php/images/no-document.jpg'">
                            <div class="document-label">License Front</div>
                        </div>
                    ` : ''}
                    ${license.license_back_photo ? `
                        <div class="license-item">
                            <img class="document-img" src="php/uploads/licenses/${license.license_back_photo}" alt="License Back" onerror="this.src='php/images/no-document.jpg'">
                            <div class="document-label">License Back</div>
                        </div>
                    ` : ''}
                    ${license.license_with_owner_photo ? `
                        <div class="license-item">
                            <img class="document-img" src="php/uploads/licenses/${license.license_with_owner_photo}" alt="License with Owner" onerror="this.src='php/images/no-document.jpg'">
                            <div class="document-label">License with Owner</div>
                        </div>
                    ` : ''}
                </div>
            `;
        } else {
            licensePhotosHtml = '<p>No license photos uploaded</p>';
        }

        userModalBody.innerHTML = `
            <div class="car-details-grid">
                <div class="car-detail-item">
                    <div class="car-detail-label"><i class="fa-solid fa-user"></i> &nbsp;&nbsp;Full Name</div>
                    <div class="car-detail-value">${user.fname} ${user.lname}</div>
                </div>
                <div class="car-detail-item">
                    <div class="car-detail-label"><i class="fa-solid fa-envelope"></i> &nbsp;&nbsp;Email</div>
                    <div class="car-detail-value">${user.email}</div>
                </div>
                <div class="car-detail-item">
                    <div class="car-detail-label"><i class="fa-solid fa-phone"></i> &nbsp;&nbsp;Phone</div>
                    <div class="car-detail-value">${user.phone || 'N/A'}</div>
                </div>
                <div class="car-detail-item">
                    <div class="car-detail-label"><i class="fa-solid fa-calendar"></i> &nbsp;&nbsp;Date of Birth</div>
                    <div class="car-detail-value">${user.date_of_birth || 'N/A'}</div>
                </div>
                <div class="car-detail-item">
                    <div class="car-detail-label"><i class="fa-solid fa-id-card"></i> &nbsp;&nbsp;Driver License Number</div>
                    <div class="car-detail-value">${user.driver_license_number || 'N/A'}</div>
                </div>
                <div class="car-detail-item">
                    <div class="car-detail-label"><i class="fa-solid fa-calendar-plus"></i> &nbsp;&nbsp;Account Created</div>
                    <div class="car-detail-value">${new Date(user.created_at).toLocaleDateString()}</div>
                </div>
                <div class="car-detail-item">
                    <div class="car-detail-label"><i class="fa-solid fa-shield-halved"></i> &nbsp;&nbsp;Status</div>
                    <div class="car-detail-value">${user.status || 'Active'}</div>
                </div>
                <div class="car-detail-item">
                    <div class="car-detail-label"><i class="fa-solid fa-user-tag"></i> &nbsp;&nbsp;Role</div>
                    <div class="car-detail-value">${user.role || 'User'}</div>
                </div>
            </div>

            <div class="profile-section">
                <h3>Profile Photo</h3>
                <div class="profile-photo">
                    <img src="${user.img ? 'php/images/' + user.img : 'php/images/default-profile.jpg'}" alt="Profile Photo" style="max-width: 200px; max-height: 200px; border-radius: 10px; border: 2px solid #ddd;">
                </div>
            </div>

            <div class="documents-section">
                <h3>Driver License Documents</h3>
                <div class="documents-flex">
                    ${licensePhotosHtml}
                </div>
            </div>

            ${(!user.verified || user.verified === 'Pending') ? `
            <div class="action-buttons">
                <button class="btn btn-approve" onclick="approveDeclineUser(${user.user_id}, 'approve_user')">
                    <i class="fa-solid fa-check-to-slot"></i> Approve User
                </button>
                <button class="btn btn-decline" onclick="approveDeclineUser(${user.user_id}, 'decline_user')">
                    <i class="fa-solid fa-rectangle-xmark"></i> Decline User
                </button>
            </div>
            ` : ''}
        `;
    }

    window.approveDeclineUser = function (userId, action) {
        if (!confirm(`Are you sure you want to ${action.replace('_user', '')} this user?`)) {
            return;
        }

        const actionButtons = document.querySelector('#userModal .action-buttons');
        actionButtons.innerHTML = '<div class="loading">Processing...</div>';

        fetch('', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=${action}&user_id=${userId}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    userModal.style.display = 'none';
                    location.reload();
                } else {
                    alert(`Error: ${data.message}`);
                    actionButtons.innerHTML = `
                    <button class="btn btn-approve" onclick="approveDeclineUser(${userId}, 'approve_user')">
                        <i class="fa-solid fa-check-to-slot"></i> Approve User
                    </button>
                    <button class="btn btn-decline" onclick="approveDeclineUser(${userId}, 'decline_user')">
                        <i class="fa-solid fa-rectangle-xmark"></i> Decline User
                    </button>
                `;
                }
            })
            .catch(error => {
                alert(`Error: ${error.message}`);
                actionButtons.innerHTML = `
                <button class="btn btn-approve" onclick="approveDeclineUser(${userId}, 'approve_user')">
                    <i class="fa-solid fa-check-to-slot"></i> Approve User
                </button>
                <button class="btn btn-decline" onclick="approveDeclineUser(${userId}, 'decline_user')">
                    <i class="fa-solid fa-rectangle-xmark"></i> Decline User
                </button>
            `;
            });
    };

    // Car search functionality
    document.getElementById('searchForm').addEventListener('submit', function (e) {
        e.preventDefault();
        performCarSearch();
    });

    document.getElementById('searchInput').addEventListener('input', function () {
        performCarSearch();
    });

    function performCarSearch() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const rows = document.querySelectorAll('#carsTableBody tr');

        rows.forEach(row => {
            const owner = row.querySelector('td:first-child p')?.textContent.toLowerCase() || '';
            const car = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';

            if (owner.includes(searchTerm) || car.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    // User search functionality
    document.getElementById('userSearchForm').addEventListener('submit', function (e) {
        e.preventDefault();
        performUserSearch();
    });

    document.getElementById('userSearchInput').addEventListener('input', function () {
        performUserSearch();
    });

    function performUserSearch() {
        const searchTerm = document.getElementById('userSearchInput').value.toLowerCase();
        const rows = document.querySelectorAll('#usersTableBody tr');

        rows.forEach(row => {
            const userName = row.querySelector('td:first-child p')?.textContent.toLowerCase() || '';
            const email = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';

            if (userName.includes(searchTerm) || email.includes(searchTerm)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

})();

document.addEventListener("click", function (event) {
    if (event.target && event.target.classList.contains("document-img")) {
        const img = event.target;

        const overlay = document.createElement("div");
        overlay.style.position = "fixed";
        overlay.style.top = 0;
        overlay.style.left = 0;
        overlay.style.width = "100vw";
        overlay.style.height = "100vh";
        overlay.style.backgroundColor = "rgba(0,0,0,0.9)";
        overlay.style.display = "flex";
        overlay.style.alignItems = "center";
        overlay.style.justifyContent = "center";
        overlay.style.zIndex = 9999;

        const bigImg = document.createElement("img");
        bigImg.src = img.src;
        bigImg.style.maxWidth = "90vw";
        bigImg.style.maxHeight = "90vh";
        bigImg.style.border = "1px solid white";
        bigImg.style.borderRadius = "10px";

        overlay.appendChild(bigImg);

        overlay.addEventListener("click", () => {
            overlay.remove();
        });

        document.body.appendChild(overlay);
    }
});
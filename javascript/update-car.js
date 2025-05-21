(() => {
    function initUpdateCarPage() {
        if (window.updateCarPageInitialized) {
            return;
        }
        window.updateCarPageInitialized = true;

        let existingImages = [];
        let newCarImages = [];
        let imagesToDelete = [];
        let newOrImage = null;
        let newCrImage = null;
        let car_id = null;

        // Initialize the page immediately since DOM is already loaded
        function init() {
            const urlParams = new URLSearchParams(window.location.search);
            car_id = urlParams.get('id');
            console.log('Car ID:', car_id);

            if (car_id) {
                document.getElementById('car_id').value = car_id;
                loadCarData(car_id);
            } else {
                showMessage('No car ID provided', 'error');
            }

            setupEventListeners();
        }

        // Setup event listeners with proper cleanup
        function setupEventListeners() {
            // Clean up any existing listeners first
            cleanupEventListeners();

            // Car images
            const carAddBtn = document.getElementById('car-add-image-btn');
            const carFileInput = document.getElementById('car-file-input');
            const orAddBtn = document.getElementById('or-add-image-btn');
            const orFileInput = document.getElementById('or-file-input');
            const crAddBtn = document.getElementById('cr-add-image-btn');
            const crFileInput = document.getElementById('cr-file-input');
            const updateForm = document.getElementById('UpdateCarForm');

            if (carAddBtn) {
                carAddBtn.addEventListener('click', function () {
                    carFileInput.click();
                });
            }

            if (carFileInput) {
                carFileInput.addEventListener('change', function (e) {
                    handleCarImageSelection(e.target.files);
                });
            }

            // OR document
            if (orAddBtn) {
                orAddBtn.addEventListener('click', function () {
                    orFileInput.click();
                });
            }

            if (orFileInput) {
                orFileInput.addEventListener('change', function (e) {
                    if (e.target.files[0]) {
                        newOrImage = e.target.files[0];
                        displayNewOrImage();
                    }
                });
            }

            // CR document
            if (crAddBtn) {
                crAddBtn.addEventListener('click', function () {
                    crFileInput.click();
                });
            }

            if (crFileInput) {
                crFileInput.addEventListener('change', function (e) {
                    if (e.target.files[0]) {
                        newCrImage = e.target.files[0];
                        displayNewCrImage();
                    }
                });
            }

            // Form submission
            if (updateForm) {
                updateForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    updateCar();
                });
            }
        }

        function cleanupEventListeners() {
            // Clear file inputs
            const carFileInput = document.getElementById('car-file-input');
            const orFileInput = document.getElementById('or-file-input');
            const crFileInput = document.getElementById('cr-file-input');

            if (carFileInput) carFileInput.value = '';
            if (orFileInput) orFileInput.value = '';
            if (crFileInput) crFileInput.value = '';

            // Reset arrays
            existingImages = [];
            newCarImages = [];
            imagesToDelete = [];
            newOrImage = null;
            newCrImage = null;
        }

        // Load existing car data
        async function loadCarData(carId) {
            try {
                const response = await fetch(`/php/get-car-details.php?id=${carId}`);
                const data = await response.json();

                if (data.success) {
                    populateForm(data.car);
                    loadExistingImages(data.car.images);
                    loadExistingDocuments(data.car.documents);
                } else {
                    showMessage(data.message || 'Failed to load car data', 'error');
                }
            } catch (error) {
                console.error('Error loading car data:', error);
                showMessage('Error loading car data', 'error');
            }
        }

        // Populate form with existing data
        function populateForm(car) {
            console.log('Populating form with:', car);

            const fields = [
                'make', 'model', 'year', 'car_type', 'description',
                'daily_rate', 'location', 'transmission', 'seats',
                'available_from', 'available_until'
            ];

            fields.forEach(field => {
                const element = document.getElementById(field);
                if (element && car[field] !== undefined) {
                    element.value = car[field];
                }
            });

            // Handle features
            if (car.features) {
                let features;
                try {
                    features = typeof car.features === 'string' ? JSON.parse(car.features) : car.features;

                    features.forEach(feature => {
                        const checkbox = document.querySelector(`input[name="${feature}"]`);
                        if (checkbox) {
                            checkbox.checked = true;
                        }
                    });
                } catch (e) {
                    console.error('Error parsing features:', e);
                }
            }
        }

        // Load existing images
        function loadExistingImages(images) {
            existingImages = images || [];
            const gallery = document.getElementById('car-image-gallery');
            const addButton = gallery.querySelector('.add-image-container');

            // Remove existing previews
            const existingPreviews = gallery.querySelectorAll('.image-preview');
            existingPreviews.forEach(preview => preview.remove());

            existingImages.forEach(image => {
                const imagePreview = createImagePreview(`/php/car-images/${car_id}/${image.image_path}`, image.image_id, true);
                gallery.insertBefore(imagePreview, addButton);
            });

            updateCarImageCount();
        }

        // Load existing documents
        function loadExistingDocuments(documents) {
            documents = documents || [];

            const orDoc = documents.find(doc => doc.document_type === 'OR');
            if (orDoc) {
                const orContainer = document.getElementById('existing-or-container');
                orContainer.innerHTML = '';
                const orPreview = createDocumentPreview(`/php/documents/${car_id}/${orDoc.image_path}`, 'OR');
                orContainer.appendChild(orPreview);
            }

            const crDoc = documents.find(doc => doc.document_type === 'CR');
            if (crDoc) {
                const crContainer = document.getElementById('existing-cr-container');
                crContainer.innerHTML = '';
                const crPreview = createDocumentPreview(`/php/documents/${car_id}/${crDoc.image_path}`, 'CR');
                crContainer.appendChild(crPreview);
            }
        }

        function handleCarImageSelection(files) {
            Array.from(files).forEach(file => {
                if (file && file.type.startsWith('image/')) {
                    newCarImages.push(file);
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const imagePreview = createImagePreview(e.target.result, null, false);
                        const gallery = document.getElementById('car-image-gallery');
                        const addButton = gallery.querySelector('.add-image-container');
                        gallery.insertBefore(imagePreview, addButton);
                    };
                    reader.readAsDataURL(file);
                }
            });

            updateCarImageCount();
        }

        function createImagePreview(src, imageId, isExisting) {
            const div = document.createElement('div');
            div.className = 'image-preview';
            div.innerHTML = `
                    <img src="${src}" alt="Car image">
                    <button type="button" class="remove-btn" data-image-id="${imageId || ''}" data-existing="${isExisting}">
                        <i class="fa-solid fa-times"></i>
                    </button>
                `;

            div.querySelector('.remove-btn').addEventListener('click', function () {
                removeCarImage(this, imageId, isExisting);
            });

            return div;
        }

        function createDocumentPreview(src, type) {
            const div = document.createElement('div');
            div.className = 'document-preview';
            div.innerHTML = `
                    <img src="${src}" alt="${type} document">
                    <button type="button" class="remove-btn">
                        <i class="fa-solid fa-times"></i>
                    </button>
                `;

            div.querySelector('.remove-btn').addEventListener('click', function () {
                removeDocument(type);
            });

            return div;
        }

        function removeCarImage(button, imageId, isExisting) {
            const preview = button.closest('.image-preview');
            const index = Array.from(preview.parentNode.children).indexOf(preview);

            preview.remove();

            if (isExisting && imageId) {
                imagesToDelete.push(imageId);
            } else {
                // Calculate the correct index in newCarImages array
                const newImageIndex = index - existingImages.length + imagesToDelete.length;
                if (newImageIndex >= 0 && newImageIndex < newCarImages.length) {
                    newCarImages.splice(newImageIndex, 1);
                }
            }

            updateCarImageCount();
        }

        function removeDocument(type) {
            const container = document.getElementById(`existing-${type.toLowerCase()}-container`);
            const preview = container.querySelector('.document-preview');
            if (preview) {
                preview.remove();
            }

            if (type === 'OR') {
                newOrImage = null;
            } else if (type === 'CR') {
                newCrImage = null;
            }
        }

        function displayNewOrImage() {
            const container = document.getElementById('existing-or-container');
            container.innerHTML = '';

            if (newOrImage) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const preview = createDocumentPreview(e.target.result, 'OR');
                    container.appendChild(preview);
                };
                reader.readAsDataURL(newOrImage);
            }
        }

        function displayNewCrImage() {
            const container = document.getElementById('existing-cr-container');
            container.innerHTML = '';

            if (newCrImage) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const preview = createDocumentPreview(e.target.result, 'CR');
                    container.appendChild(preview);
                };
                reader.readAsDataURL(newCrImage);
            }
        }

        function updateCarImageCount() {
            const totalImages = existingImages.length + newCarImages.length - imagesToDelete.length;
            const countElement = document.getElementById('car-upload-count');
            if (countElement) {
                countElement.textContent = `${totalImages} images selected`;
            }
        }

        async function updateCar() {
            const form = document.getElementById('UpdateCarForm');
            const formData = new FormData(form);

            // Add new car images
            newCarImages.forEach((file, index) => {
                formData.append(`carImage_${index}`, file);
            });

            // Add new documents
            if (newOrImage) {
                formData.append('orImage', newOrImage);
            }
            if (newCrImage) {
                formData.append('crImage', newCrImage);
            }

            // Add images to delete
            if (imagesToDelete.length > 0) {
                formData.append('imagesToDelete', JSON.stringify(imagesToDelete));
            }

            const submitButton = form.querySelector('.submit');
            const originalText = submitButton.textContent;
            submitButton.textContent = 'Updating...';
            submitButton.classList.add('loading');
            submitButton.disabled = true;

            try {
                const response = await fetch('/php/update-car.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    showMessage('Car updated successfully!', 'success');
                    setTimeout(() => {
                        loadCarData(car_id);
                    }, 1000);
                } else {
                    showMessage(data.message || 'Failed to update car', 'error');
                }
            } catch (error) {
                console.error('Error updating car:', error);
                showMessage('Error updating car', 'error');
            } finally {
                submitButton.textContent = originalText;
                submitButton.classList.remove('loading');
                submitButton.disabled = false;
            }
        }

        function showMessage(message, type) {
            const messageDiv = document.getElementById('message');
            if (messageDiv) {
                messageDiv.textContent = message;
                messageDiv.className = `message ${type}`;
                messageDiv.style.display = 'block';

                setTimeout(() => {
                    messageDiv.style.display = 'none';
                }, 5000);
            }
        }

        // Initialize the page
        init();

        // Clean up function for SPA navigation
        window.cleanupUpdateCarPage = function () {
            window.updateCarPageInitialized = false;
            cleanupEventListeners();
        };
    }

    // Initialize when the script loads
    initUpdateCarPage();

    // Global function for back button (if needed)
    function goBack() {
        window.history.back();
    }
})();
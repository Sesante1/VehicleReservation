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

        function setupEventListeners() {
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
                carAddBtn.addEventListener('click', function (e) {
                    e.preventDefault();
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
                orAddBtn.addEventListener('click', function (e) {
                    e.preventDefault();
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
                crAddBtn.addEventListener('click', function (e) {
                    e.preventDefault();
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

            if (updateForm) {
                updateForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    console.log('Form submission triggered');
                    
                    if (validateForm()) {
                        updateCar();
                    }
                });
            }
        }

        function cleanupEventListeners() {

            const carFileInput = document.getElementById('car-file-input');
            const orFileInput = document.getElementById('or-file-input');
            const crFileInput = document.getElementById('cr-file-input');

            if (carFileInput) carFileInput.value = '';
            if (orFileInput) orFileInput.value = '';
            if (crFileInput) crFileInput.value = '';

        }

        function validateForm() {
            const requiredFields = ['make', 'model', 'year', 'car_type', 'description', 'daily_rate', 'location', 'transmission', 'seats'];
            let isValid = true;
            let firstErrorField = null;

            requiredFields.forEach(fieldName => {
                const field = document.getElementById(fieldName);
                if (field && (!field.value || field.value.trim() === '')) {
                    isValid = false;
                    field.style.borderColor = '#ff6b6b';
                    if (!firstErrorField) {
                        firstErrorField = field;
                    }
                } else if (field) {
                    field.style.borderColor = '';
                }
            });

            const totalImages = existingImages.length + newCarImages.length - imagesToDelete.length;
            if (totalImages < 3) {
                showMessage('Please ensure you have at least 3 car images', 'error');
                isValid = false;
            }

            if (!isValid) {
                if (firstErrorField) {
                    firstErrorField.focus();
                }
                showMessage('Please fill in all required fields', 'error');
            }

            return isValid;
        }

        async function loadCarData(carId) {
            try {
                console.log('Loading car data for ID:', carId);
                const response = await fetch(`/php/get-car-details.php?id=${carId}`);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                console.log('Car data received:', data);

                if (data.success) {
                    populateForm(data.car);
                    loadExistingImages(data.car.images);
                    loadExistingDocuments(data.car.documents);
                } else {
                    showMessage(data.message || 'Failed to load car data', 'error');
                }
            } catch (error) {
                console.error('Error loading car data:', error);
                showMessage('Error loading car data: ' + error.message, 'error');
            }
        }

        function populateForm(car) {
            console.log('Populating form with:', car);

            const fields = [
                'make', 'model', 'year', 'car_type', 'description',
                'daily_rate', 'location', 'transmission', 'seats',
                'available_from', 'available_until'
            ];

            fields.forEach(field => {
                const element = document.getElementById(field);
                if (element && car[field] !== undefined && car[field] !== null) {
                    element.value = car[field];
                }
            });

            if (car.features) {
                let features;
                try {
                    features = typeof car.features === 'string' ? JSON.parse(car.features) : car.features;

                    if (Array.isArray(features)) {
                        features.forEach(feature => {
                            const checkbox = document.querySelector(`input[name="${feature}"]`);
                            if (checkbox) {
                                checkbox.checked = true;
                            }
                        });
                    }
                } catch (e) {
                    console.error('Error parsing features:', e);
                }
            }
        }

        function loadExistingImages(images) {
            existingImages = images || [];
            const gallery = document.getElementById('car-image-gallery');
            const addButton = gallery.querySelector('.add-image-container');

            const existingPreviews = gallery.querySelectorAll('.image-preview');
            existingPreviews.forEach(preview => preview.remove());

            existingImages.forEach(image => {
                const imagePreview = createImagePreview(`/php/car-images/${car_id}/${image.image_path}`, image.image_id || image.id, true);
                gallery.insertBefore(imagePreview, addButton);
            });

            updateCarImageCount();
        }

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

            document.getElementById('car-file-input').value = '';
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

            div.querySelector('.remove-btn').addEventListener('click', function (e) {
                e.preventDefault();
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

            div.querySelector('.remove-btn').addEventListener('click', function (e) {
                e.preventDefault();
                removeDocument(type);
            });

            return div;
        }

        function removeCarImage(button, imageId, isExisting) {
            const preview = button.closest('.image-preview');
            const allPreviews = Array.from(preview.parentNode.querySelectorAll('.image-preview'));
            const index = allPreviews.indexOf(preview);

            preview.remove();

            if (isExisting && imageId) {
                imagesToDelete.push(imageId);
            } else {
                const existingCount = existingImages.length - imagesToDelete.length;
                const newImageIndex = index - existingCount;
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
            console.log('Starting car update process...');
            const form = document.getElementById('UpdateCarForm');
            const formData = new FormData(form);

            console.log('Form data being sent:');
            for (let [key, value] of formData.entries()) {
                console.log(key, value);
            }

            newCarImages.forEach((file, index) => {
                formData.append(`carImage_${index}`, file);
                console.log(`Added carImage_${index}:`, file.name);
            });

            if (newOrImage) {
                formData.append('orImage', newOrImage);
                console.log('Added OR image:', newOrImage.name);
            }
            if (newCrImage) {
                formData.append('crImage', newCrImage);
                console.log('Added CR image:', newCrImage.name);
            }

            if (imagesToDelete.length > 0) {
                formData.append('imagesToDelete', JSON.stringify(imagesToDelete));
                console.log('Images to delete:', imagesToDelete);
            }

            const submitButton = form.querySelector('.submit');
            const originalText = submitButton.textContent;
            submitButton.textContent = 'Updating...';
            submitButton.classList.add('loading');
            submitButton.disabled = true;

            try {
                console.log('Sending request to /php/update-car.php');
                const response = await fetch('/php/update-car.php', {
                    method: 'POST',
                    body: formData
                });

                console.log('Response status:', response.status);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const responseText = await response.text();
                console.log('Raw response:', responseText);

                let data;
                try {
                    data = JSON.parse(responseText);
                } catch (e) {
                    console.error('Failed to parse JSON response:', e);
                    throw new Error('Invalid JSON response from server');
                }

                console.log('Parsed response data:', data);

                if (data.success) {
                    showMessage('Car updated successfully!', 'success');

                    newCarImages = [];
                    imagesToDelete = [];
                    newOrImage = null;
                    newCrImage = null;
                    
                    setTimeout(() => {
                        loadCarData(car_id);
                        location.href = "/Cars";
                    }, 1000);
                } else {
                    showMessage(data.message || 'Failed to update car', 'error');
                }
            } catch (error) {
                console.error('Error updating car:', error);
                showMessage('Error updating car: ' + error.message, 'error');
            } finally {
                submitButton.textContent = originalText;
                submitButton.classList.remove('loading');
                submitButton.disabled = false;
            }
        }

        function showMessage(message, type) {
            console.log('Showing message:', message, type);

            let messageDiv = document.getElementById('message');
            if (!messageDiv) {
                messageDiv = document.createElement('div');
                messageDiv.id = 'message';
                messageDiv.style.cssText = `
                    padding: 10px;
                    margin: 10px 0;
                    border-radius: 5px;
                    font-weight: bold;
                    text-align: center;
                `;

                const form = document.getElementById('UpdateCarForm');
                if (form) {
                    form.insertBefore(messageDiv, form.firstChild);
                }
            }

            messageDiv.textContent = message;
            messageDiv.className = `message ${type}`;
            
            if (type === 'success') {
                messageDiv.style.backgroundColor = '#d4edda';
                messageDiv.style.color = '#155724';
                messageDiv.style.border = '1px solid #c3e6cb';
            } else if (type === 'error') {
                messageDiv.style.backgroundColor = '#f8d7da';
                messageDiv.style.color = '#721c24';
                messageDiv.style.border = '1px solid #f5c6cb';
            }
            
            messageDiv.style.display = 'block';

            setTimeout(() => {
                messageDiv.style.display = 'none';
            }, 5000);
        }

        init();

        window.cleanupUpdateCarPage = function () {
            window.updateCarPageInitialized = false;
            cleanupEventListeners();
        };
    }

    initUpdateCarPage();

    window.goBack = function() {
        window.history.back();
    };
})();
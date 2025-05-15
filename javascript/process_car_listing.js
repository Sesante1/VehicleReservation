(function () {
    function initAddCarForm() {
        const formId = 'AddCarForm';
        const addCarForm = document.getElementById(formId);

        if (!addCarForm) return; // Exit if form not on this page

        const handleSubmit = function (e) {
            e.preventDefault();

            const formData = new FormData(addCarForm);
            const imageData = window.carImageData || [];

            imageData.forEach((item, index) => {
                if (item !== null) {
                    formData.append(`carImage${index}`, item.file);
                }
            });

            const validImages = imageData.filter(item => item !== null);

            if (validImages.length < 3) {
                alert('Please upload at least 3 images of your car.');
                return;
            }

            fetch('php/process_car_listing.php', {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    if (!response.ok) throw new Error('Network error');
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        alert('Your car has been listed successfully!');
                        window.location.href = '/listCar';
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while saving your listing. Please try again.');
                });
        };

        addCarForm.addEventListener('submit', handleSubmit);

        // Return cleanup
        return function cleanup() {
            addCarForm.removeEventListener('submit', handleSubmit);
            console.log('AddCarForm unmounted and cleaned up');
        };
    }

    // Hook into SPA router or page switch logic
    // Run init manually when you load the AddCar page/component
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            window.addCarFormCleanup = initAddCarForm();
        });
    } else {
        window.addCarFormCleanup = initAddCarForm();
    }

    // Example cleanup trigger (you should call this on route change if needed)
    window.addEventListener('beforeunload', function () {
        if (typeof window.addCarFormCleanup === 'function') {
            window.addCarFormCleanup();
        }
    });
})();

(function () {
    // Wait for DOM content to be loaded
    function initCarUploader() {
        const moduleId = 'car-uploader';

        // Check if the module exists in the DOM
        if (!document.getElementById(moduleId)) {
            return; // Exit if the module doesn't exist in the current route
        }

        const gallery = document.getElementById('car-image-gallery');
        const addImageBtn = document.getElementById('car-add-image-btn');
        const fileInput = document.getElementById('car-file-input');
        const uploadCount = document.getElementById('car-upload-count');

        let imageCount = 0;
        const minRequiredImages = 3;

        // Use a global variable to store image data
        window.carImageData = [];

        // Handle add image button click
        function handleAddImageClick() {
            fileInput.click();
        }

        // Handle file selection
        function handleFileChange(e) {
            const files = e.target.files;

            for (let i = 0; i < files.length; i++) {
                const file = files[i];

                // Only process image files
                if (!file.type.match('image.*')) continue;

                const reader = new FileReader();

                reader.onload = function (e) {
                    // Create image container
                    const container = document.createElement('div');
                    container.className = 'image-container';

                    // Create image element
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    container.appendChild(img);

                    // Store file data for later submission
                    const fileIndex = window.carImageData.length;
                    window.carImageData.push({
                        file: file,
                        previewUrl: e.target.result
                    });

                    // Create delete button
                    const deleteBtn = document.createElement('div');
                    deleteBtn.className = 'delete-btn';
                    deleteBtn.innerHTML = '×';
                    deleteBtn.dataset.index = fileIndex;
                    deleteBtn.addEventListener('click', function () {
                        container.remove();
                        // Mark as deleted rather than splicing to maintain indexes
                        window.carImageData[fileIndex] = null;
                        imageCount--;
                        updateImageCount();
                    });
                    container.appendChild(deleteBtn);

                    // Insert before the add button
                    gallery.insertBefore(container, addImageBtn);
                    imageCount++;
                    updateImageCount();
                };

                // Read the image file as a data URL
                reader.readAsDataURL(file);
            }

            // Reset file input
            fileInput.value = '';
        }

        function updateImageCount() {
            uploadCount.textContent = `${imageCount} of ${minRequiredImages} images selected`;
        }

        // Add event listeners
        addImageBtn.addEventListener('click', handleAddImageClick);
        fileInput.addEventListener('change', handleFileChange);

        // Store the cleanup function to remove event listeners when component is unmounted
        return function cleanup() {
            addImageBtn.removeEventListener('click', handleAddImageClick);
            fileInput.removeEventListener('change', handleFileChange);
            console.log('Car uploader component unmounted and cleaned up');
        };
    }

    // Initialize when DOM is loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCarUploader);
    } else {
        // DOM already loaded, initialize immediately
        const cleanup = initCarUploader();

        // For SPA route changes - example of how to clean up when navigating away
        window.addEventListener('beforeunload', function () {
            if (typeof cleanup === 'function') {
                cleanup();
            }
        });
    }
})();
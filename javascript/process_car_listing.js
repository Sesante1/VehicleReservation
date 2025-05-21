// (function () {
//     function initAddCarForm() {
//         const formId = 'AddCarForm';
//         const addCarForm = document.getElementById(formId);

//         if (!addCarForm) return;

//         const handleSubmit = function (e) {
//             e.preventDefault();

//             const formData = new FormData(addCarForm);
//             const imageData = window.carImageData || [];

//             imageData.forEach((item, index) => {
//                 if (item !== null) {
//                     formData.append(`carImage${index}`, item.file);
//                 }
//             });

//             const validImages = imageData.filter(item => item !== null);

//             if (validImages.length < 3) {
//                 alert('Please upload at least 3 images of your car.');
//                 return;
//             }

//             fetch('php/process_car_listing.php', {
//                 method: 'POST',
//                 body: formData
//             })
//                 .then(response => {
//                     if (!response.ok) throw new Error('Network error');
//                     return response.json();
//                 })
//                 .then(data => {
//                     if (data.success) {
//                         alert('Your car has been listed successfully!');
//                         window.location.href = '/listCar';
//                     } else {
//                         alert('Error: ' + data.message);
//                     }
//                 })
//                 .catch(error => {
//                     console.error('Error:', error);
//                     alert('An error occurred while saving your listing. Please try again.');
//                 });
//         };

//         addCarForm.addEventListener('submit', handleSubmit);

//         // Return cleanup
//         return function cleanup() {
//             addCarForm.removeEventListener('submit', handleSubmit);
//             console.log('AddCarForm unmounted and cleaned up');
//         };
//     }

//     // Hook into SPA router or page switch logic
//     if (document.readyState === 'loading') {
//         document.addEventListener('DOMContentLoaded', () => {
//             window.addCarFormCleanup = initAddCarForm();
//         });
//     } else {
//         window.addCarFormCleanup = initAddCarForm();
//     }

//     window.addEventListener('beforeunload', function () {
//         if (typeof window.addCarFormCleanup === 'function') {
//             window.addCarFormCleanup();
//         }
//     });
// })();

// (function () {
//     function initCarUploader() {
//         const moduleId = 'car-uploader';

//         // Check if the module exists in the DOM
//         if (!document.getElementById(moduleId)) {
//             return; 
//         }

//         const gallery = document.getElementById('car-image-gallery');
//         const addImageBtn = document.getElementById('car-add-image-btn');
//         const fileInput = document.getElementById('car-file-input');
//         const uploadCount = document.getElementById('car-upload-count');

//         let imageCount = 0;
//         const minRequiredImages = 3;

//         window.carImageData = [];

//         function handleAddImageClick() {
//             fileInput.click();
//         }

//         function handleFileChange(e) {
//             const files = e.target.files;

//             for (let i = 0; i < files.length; i++) {
//                 const file = files[i];

//                 if (!file.type.match('image.*')) continue;

//                 const reader = new FileReader();

//                 reader.onload = function (e) {
//                     const container = document.createElement('div');
//                     container.className = 'image-container';

//                     const img = document.createElement('img');
//                     img.src = e.target.result;
//                     container.appendChild(img);

//                     const fileIndex = window.carImageData.length;
//                     window.carImageData.push({
//                         file: file,
//                         previewUrl: e.target.result
//                     });

//                     const deleteBtn = document.createElement('div');
//                     deleteBtn.className = 'delete-btn';
//                     deleteBtn.innerHTML = '×';
//                     deleteBtn.dataset.index = fileIndex;
//                     deleteBtn.addEventListener('click', function () {
//                         container.remove();
    
//                         window.carImageData[fileIndex] = null;
//                         imageCount--;
//                         updateImageCount();
//                     });
//                     container.appendChild(deleteBtn);

//                     gallery.insertBefore(container, addImageBtn);
//                     imageCount++;
//                     updateImageCount();
//                 };

//                 reader.readAsDataURL(file);
//             }

//             fileInput.value = '';
//         }

//         function updateImageCount() {
//             uploadCount.textContent = `${imageCount} of ${minRequiredImages} images selected`;
//         }

//         addImageBtn.addEventListener('click', handleAddImageClick);
//         fileInput.addEventListener('change', handleFileChange);

//         return function cleanup() {
//             addImageBtn.removeEventListener('click', handleAddImageClick);
//             fileInput.removeEventListener('change', handleFileChange);
//             console.log('Car uploader component unmounted and cleaned up');
//         };
//     }

//     if (document.readyState === 'loading') {
//         document.addEventListener('DOMContentLoaded', initCarUploader);
//     } else {
//         const cleanup = initCarUploader();

//         // Cleanup on page unload
//         window.addEventListener('beforeunload', function () {
//             if (typeof cleanup === 'function') {
//                 cleanup();
//             }
//         });
//     }
// })();
(function () {
    function initAddCarForm() {
        const formId = 'AddCarForm';
        const addCarForm = document.getElementById(formId);

        if (!addCarForm) return;

        const handleSubmit = function (e) {
            e.preventDefault();

            const formData = new FormData(addCarForm);
            const imageData = window.carImageData || [];
            const orImageData = window.orImageData || null;
            const crImageData = window.crImageData || null;

            imageData.forEach((item, index) => {
                if (item !== null) {
                    formData.append(`carImage${index}`, item.file);
                }
            });

            if (orImageData) {
                formData.append('orImage', orImageData.file);
            }

            if (crImageData) {
                formData.append('crImage', crImageData.file);
            }

            const validImages = imageData.filter(item => item !== null);

            if (validImages.length < 3) {
                alert('Please upload at least 3 images of your car.');
                return;
            }

            if (!orImageData) {
                alert('Please upload the Official Receipt (OR) image.');
                return;
            }

            if (!crImageData) {
                alert('Please upload the Certificate of Registration (CR) image.');
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

        return function cleanup() {
            addCarForm.removeEventListener('submit', handleSubmit);
            console.log('AddCarForm unmounted and cleaned up');
        };
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            window.addCarFormCleanup = initAddCarForm();
        });
    } else {
        window.addCarFormCleanup = initAddCarForm();
    }

    window.addEventListener('beforeunload', function () {
        if (typeof window.addCarFormCleanup === 'function') {
            window.addCarFormCleanup();
        }
    });
})();

(function () {
    function initCarUploader() {
        const moduleId = 'car-uploader';

        // Check if the module exists in the DOM
        if (!document.getElementById(moduleId)) {
            return; 
        }

        const gallery = document.getElementById('car-image-gallery');
        const addImageBtn = document.getElementById('car-add-image-btn');
        const fileInput = document.getElementById('car-file-input');
        const uploadCount = document.getElementById('car-upload-count');

        let imageCount = 0;
        const minRequiredImages = 3;

        window.carImageData = [];

        function handleAddImageClick() {
            fileInput.click();
        }

        function handleFileChange(e) {
            const files = e.target.files;

            for (let i = 0; i < files.length; i++) {
                const file = files[i];

                if (!file.type.match('image.*')) continue;

                const reader = new FileReader();

                reader.onload = function (e) {
                    const container = document.createElement('div');
                    container.className = 'image-container';

                    const img = document.createElement('img');
                    img.src = e.target.result;
                    container.appendChild(img);

                    const fileIndex = window.carImageData.length;
                    window.carImageData.push({
                        file: file,
                        previewUrl: e.target.result
                    });

                    const deleteBtn = document.createElement('div');
                    deleteBtn.className = 'delete-btn';
                    deleteBtn.innerHTML = '×';
                    deleteBtn.dataset.index = fileIndex;
                    deleteBtn.addEventListener('click', function () {
                        container.remove();
    
                        window.carImageData[fileIndex] = null;
                        imageCount--;
                        updateImageCount();
                    });
                    container.appendChild(deleteBtn);

                    gallery.insertBefore(container, addImageBtn);
                    imageCount++;
                    updateImageCount();
                };

                reader.readAsDataURL(file);
            }

            fileInput.value = '';
        }

        function updateImageCount() {
            uploadCount.textContent = `${imageCount} of ${minRequiredImages} images selected`;
        }

        addImageBtn.addEventListener('click', handleAddImageClick);
        fileInput.addEventListener('change', handleFileChange);

        return function cleanup() {
            addImageBtn.removeEventListener('click', handleAddImageClick);
            fileInput.removeEventListener('change', handleFileChange);
            console.log('Car uploader component unmounted and cleaned up');
        };
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCarUploader);
    } else {
        const cleanup = initCarUploader();

        // Cleanup on page unload
        window.addEventListener('beforeunload', function () {
            if (typeof cleanup === 'function') {
                cleanup();
            }
        });
    }
})();

// OR (Official Receipt) Uploader
(function () {
    function initORUploader() {
        const moduleId = 'or-uploader';

        if (!document.getElementById(moduleId)) {
            return; 
        }

        const gallery = document.getElementById('or-image-gallery');
        const addImageBtn = document.getElementById('or-add-image-btn');
        const fileInput = document.getElementById('or-file-input');
        const uploadCount = document.getElementById('or-upload-count');

        window.orImageData = null;

        function handleAddImageClick() {
            fileInput.click();
        }

        function handleFileChange(e) {
            const file = e.target.files[0];

            if (!file || !file.type.match('image.*')) return;

            const existing = gallery.querySelector('.image-container');
            if (existing) {
                existing.remove();
            }

            const reader = new FileReader();

            reader.onload = function (e) {
                const container = document.createElement('div');
                container.className = 'image-container';

                const img = document.createElement('img');
                img.src = e.target.result;
                container.appendChild(img);

                window.orImageData = {
                    file: file,
                    previewUrl: e.target.result
                };

                const deleteBtn = document.createElement('div');
                deleteBtn.className = 'delete-btn';
                deleteBtn.innerHTML = '×';
                deleteBtn.addEventListener('click', function () {
                    container.remove();
                    window.orImageData = null;
                    updateImageCount();
                });
                container.appendChild(deleteBtn);

                gallery.insertBefore(container, addImageBtn);
                updateImageCount();
            };

            reader.readAsDataURL(file);
            fileInput.value = '';
        }

        function updateImageCount() {
            const count = window.orImageData ? 1 : 0;
            uploadCount.textContent = `${count} of 1 image selected`;
        }

        addImageBtn.addEventListener('click', handleAddImageClick);
        fileInput.addEventListener('change', handleFileChange);

        return function cleanup() {
            addImageBtn.removeEventListener('click', handleAddImageClick);
            fileInput.removeEventListener('change', handleFileChange);
            console.log('OR uploader component unmounted and cleaned up');
        };
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initORUploader);
    } else {
        const cleanup = initORUploader();

        window.addEventListener('beforeunload', function () {
            if (typeof cleanup === 'function') {
                cleanup();
            }
        });
    }
})();

// CR (Certificate of Registration) Uploader
(function () {
    function initCRUploader() {
        const moduleId = 'cr-uploader';

        if (!document.getElementById(moduleId)) {
            return; 
        }

        const gallery = document.getElementById('cr-image-gallery');
        const addImageBtn = document.getElementById('cr-add-image-btn');
        const fileInput = document.getElementById('cr-file-input');
        const uploadCount = document.getElementById('cr-upload-count');

        window.crImageData = null;

        function handleAddImageClick() {
            fileInput.click();
        }

        function handleFileChange(e) {
            const file = e.target.files[0];

            if (!file || !file.type.match('image.*')) return;

            const existing = gallery.querySelector('.image-container');
            if (existing) {
                existing.remove();
            }

            const reader = new FileReader();

            reader.onload = function (e) {
                const container = document.createElement('div');
                container.className = 'image-container';

                const img = document.createElement('img');
                img.src = e.target.result;
                container.appendChild(img);

                window.crImageData = {
                    file: file,
                    previewUrl: e.target.result
                };

                const deleteBtn = document.createElement('div');
                deleteBtn.className = 'delete-btn';
                deleteBtn.innerHTML = '×';
                deleteBtn.addEventListener('click', function () {
                    container.remove();
                    window.crImageData = null;
                    updateImageCount();
                });
                container.appendChild(deleteBtn);

                gallery.insertBefore(container, addImageBtn);
                updateImageCount();
            };

            reader.readAsDataURL(file);
            fileInput.value = '';
        }

        function updateImageCount() {
            const count = window.crImageData ? 1 : 0;
            uploadCount.textContent = `${count} of 1 image selected`;
        }

        addImageBtn.addEventListener('click', handleAddImageClick);
        fileInput.addEventListener('change', handleFileChange);

        return function cleanup() {
            addImageBtn.removeEventListener('click', handleAddImageClick);
            fileInput.removeEventListener('change', handleFileChange);
            console.log('CR uploader component unmounted and cleaned up');
        };
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCRUploader);
    } else {
        const cleanup = initCRUploader();

        window.addEventListener('beforeunload', function () {
            if (typeof cleanup === 'function') {
                cleanup();
            }
        });
    }
})();
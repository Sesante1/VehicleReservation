// window.carDailyRate = <?= json_encode($car['daily_rate']) ?>;

// (function() {
//     function initPriceCalculator() {
//         const moduleId = 'price-calculation';

//         if (!document.getElementById(moduleId)) return;

//         const pickupDateInput = document.getElementById('pickup_date');
//         const returnDateInput = document.getElementById('return_date');
//         const totalContainer = document.getElementById('total-container');
//         const daysCountElement = document.getElementById('days-count');
//         const subtotalElement = document.getElementById('subtotal');
//         const totalAmountElement = document.getElementById('total-amount');
//         const dailyRate = window.carDailyRate || 0;

//         function updatePriceCalculation() {
//             if (pickupDateInput.value && returnDateInput.value) {
//                 const pickupDate = new Date(pickupDateInput.value);
//                 const returnDate = new Date(returnDateInput.value);

//                 if (returnDate > pickupDate) {
//                     const diffTime = Math.abs(returnDate - pickupDate);
//                     const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

//                     daysCountElement.textContent = diffDays;
//                     const subtotal = diffDays * dailyRate;

//                     document.getElementById('booking-form').dataset.totalPrice = subtotal;

//                     subtotalElement.textContent = '₱' + subtotal.toLocaleString();
//                     totalAmountElement.textContent = '₱' + subtotal.toLocaleString();
//                     totalContainer.style.display = 'block';
//                 } else {
//                     totalContainer.style.display = 'none';
//                 }
//             } else {
//                 totalContainer.style.display = 'none';
//             }
//         }

//         pickupDateInput.addEventListener('change', updatePriceCalculation);
//         returnDateInput.addEventListener('change', updatePriceCalculation);
//         updatePriceCalculation();

//         return function cleanup() {
//             pickupDateInput.removeEventListener('change', updatePriceCalculation);
//             returnDateInput.removeEventListener('change', updatePriceCalculation);
//         };
//     }

//     function initBookingForm() {
//         const bookingForm = document.querySelector('.booking-panel form');
//         if (!bookingForm) return;

//         bookingForm.id = 'booking-form';

//         bookingForm.addEventListener('submit', function(event) {
//             event.preventDefault();

//             const pickupDateInput = document.getElementById('pickup_date');
//             const returnDateInput = document.getElementById('return_date');

//             if (!pickupDateInput.value || !returnDateInput.value) {
//                 showBookingMessage('Please select both pickup and return dates', 'error');
//                 return;
//             }

//             const pickupDate = new Date(pickupDateInput.value);
//             const returnDate = new Date(returnDateInput.value);

//             if (returnDate < pickupDate) {
//                 showBookingMessage('Return date must be after pickup date', 'error');
//                 return;
//             }

//             const totalPrice = parseFloat(bookingForm.dataset.totalPrice || 0);

//             if (totalPrice <= 0) {
//                 showBookingMessage('Please select valid booking dates to calculate price', 'error');
//                 return;
//             }

//             // Show loading state
//             const submitButton = bookingForm.querySelector('button[type="submit"]');
//             const originalButtonText = submitButton.textContent;
//             submitButton.textContent = 'Processing...';
//             submitButton.disabled = true;

//             // Prepare form data
//             const formData = new FormData();
//             formData.append('car_id', document.querySelector('input[name="car_id"]').value);
//             formData.append('pickup_date', pickupDateInput.value);
//             formData.append('return_date', returnDateInput.value);
//             formData.append('total_price', totalPrice);

//             // Debug what's being sent
//             console.log('Sending booking data:', {
//                 car_id: document.querySelector('input[name="car_id"]').value,
//                 pickup_date: pickupDateInput.value,
//                 return_date: returnDateInput.value,
//                 total_price: totalPrice
//             });

//             fetch('/php/book-car.php', {
//                     method: 'POST',
//                     body: formData
//                 })
//                 .then(response => response.json())
//                 .then(data => {
//                     console.log('Server response:', data);

//                     if (data.success) {
//                         showBookingMessage(data.message, 'success');

//                         pickupDateInput.value = '';
//                         returnDateInput.value = '';

//                         document.getElementById('total-container').style.display = 'none';

//                         pickupDateInput.disabled = true;
//                         returnDateInput.disabled = true;
//                         submitButton.textContent = 'Booked!';
//                         submitButton.disabled = true;

//                         setTimeout(() => {
//                             window.location.href = '/booking-confirmation?id=' + data.booking_id;
//                         }, 2000);
//                     } else {
//                         showBookingMessage(data.message, 'error');
//                         submitButton.textContent = originalButtonText;
//                         submitButton.disabled = false;
//                     }
//                 })
//                 .catch(error => {
//                     console.error('Error:', error);
//                     showBookingMessage('An error occurred. Please try again.', 'error');
//                     submitButton.textContent = originalButtonText;
//                     submitButton.disabled = false;
//                 });
//         });
//     }

//     function showBookingMessage(message, type) {
//         // Check if message container exists, create if not
//         const bookingForm = document.getElementById('booking-form');
//         let messageContainer = document.getElementById('booking-message');

//         if (!messageContainer) {
//             messageContainer = document.createElement('div');
//             messageContainer.id = 'booking-message';
//             bookingForm.insertBefore(messageContainer, bookingForm.firstChild);
//         }

//         messageContainer.className = type === 'success' ? 'success-message' : 'error-message';
//         messageContainer.textContent = message;

//         setTimeout(() => {
//             messageContainer.style.opacity = '0';
//             setTimeout(() => {
//                 if (messageContainer.parentNode) {
//                     messageContainer.parentNode.removeChild(messageContainer);
//                 }
//             }, 500);
//         }, 5000);
//     }

//     if (document.readyState === 'loading') {
//         document.addEventListener('DOMContentLoaded', () => {
//             initPriceCalculator();
//             initBookingForm();
//         });
//     } else {
//         initPriceCalculator();
//         initBookingForm();
//     }
// })();
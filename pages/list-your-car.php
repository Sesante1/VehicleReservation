<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="style/general.css">
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>

<body>
    <form id="AddCarForm" class="add-form" method="POST" enctype="multipart/form-data">
        <h3>Basic Information</h3>
        <div class="input-container-grid add-container">
            <div class="input">
                <label for="make">Make</label>
                <input type="text" id="make" name="make" placeholder="e.g. Toyota" required>
            </div>
            <div class="input">
                <label for="model">Model</label>
                <input type="text" id="model" name="model" placeholder="e.g. Camry" required>
            </div>
            <div class="input">
                <label for="year">Year</label>
                <input type="number" id="year" name="year" placeholder="e.g. 2021" required>
            </div>
            <div class="input">
                <label for="car_type">Car Type</label>
                <input type="text" id="car_type" name="car_type" placeholder="e.g. SUV, Electric, Truck" required>
            </div>
        </div>
        <div class="listing-title add-container">
            <div class="input">
                <label for="description">Listing Title</label>
                <textarea id="description" name="description" placeholder="Describe your car, its condition, special features, etc." required></textarea>
            </div>
        </div>
        <div class="flex-column add-container">
            <h4>Pricing & Location</h4>
            <div class="input-container-grid">
                <div class="input">
                    <label for="daily_rate">Daily Rate (₱)</label>
                    <input type="number" id="daily_rate" name="daily_rate" placeholder="e.g. 50" required>
                </div>
                <div class="input">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" placeholder="e.g. San Francisco, CA" required>
                </div>
            </div>
        </div>
        <div class="flex-column add-container">
            <h4>Specifications</h4>
            <div class="input-container-grid">
                <div class="input">
                    <label>Transmission</label>
                    <select name="transmission" id="transmission" required>
                        <option value="Manual">Manual</option>
                        <option value="Automatic">Automatic</option>
                    </select>
                </div>
                <div class="input">
                    <label>Number of Seats</label>
                    <select name="seats" id="seats" required>
                        <option value="2">2</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="7">7</option>
                        <option value="8+">8+</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="flex-column add-container">
            <h4>Features</h4>
            <div class="flex">
                <div>
                    <input type="checkbox" id="air_condition" name="air-condition" value="1">
                    <label for="air_condition"> &nbsp; Air Conditioning </label><br>
                    <input type="checkbox" id="navigation_system" name="navigation-system" value="1">
                    <label for="navigation_system"> &nbsp; Navigation System </label><br>
                    <input type="checkbox" id="heated_seats" name="heated-seats" value="1">
                    <label for="heated_seats"> &nbsp; Heated Seats </label><br>
                    <input type="checkbox" id="apple_carplay" name="apple-carplay" value="1">
                    <label for="apple_carplay"> &nbsp; Apple CarPlay </label><br>
                </div>
                <div>
                    <input type="checkbox" id="bluetooth" name="bluetooth" value="1">
                    <label for="bluetooth"> &nbsp; Bluetooth</label><br>
                    <input type="checkbox" id="leather_seats" name="leather-seats" value="1">
                    <label for="leather_seats"> &nbsp; Leather Seats</label><br>
                    <input type="checkbox" id="camera" name="camera" value="1">
                    <label for="camera"> &nbsp; Backup Camera</label><br>
                    <input type="checkbox" id="android" name="android" value="1">
                    <label for="android"> &nbsp; Android Auto</label><br>
                </div>
                <div>
                    <input type="checkbox" id="cruise_control" name="cruise-control" value="1">
                    <label for="cruise_control"> &nbsp; Cruise Control</label><br>
                    <input type="checkbox" id="sunroof" name="sunroof" value="1">
                    <label for="sunroof"> &nbsp; Sunroof</label><br>
                    <input type="checkbox" id="keyless" name="keyless" value="1">
                    <label for="keyless"> &nbsp; Keyless Entry</label><br>
                    <input type="checkbox" id="sound_system" name="sound-system" value="1">
                    <label for="sound_system"> &nbsp; Premium Sound System</label><br>
                </div>
            </div>
        </div>
        <div class="flex-column add-container">
            <h4>Availability</h4>
            <div class="input-container-grid">
                <div class="input">
                    <label for="available_from">Available From</label>
                    <input type="date" id="available_from" name="available_from" required>
                </div>
                <div class="input">
                    <label for="available_until">Available Until</label>
                    <input type="date" id="available_until" name="available_until">
                    <span>Leave empty if there's no end date</span>
                </div>
            </div>
        </div>

        <div class="car-uploader add-container" id="car-uploader">
            <h4>Car Images</h4>
            <p>Upload at least 3 high-quality images of your car (exterior, interior, etc.)</p>

            <div class="image-gallery" id="car-image-gallery">
                <div class="add-image-container" id="car-add-image-btn">
                    <div class="add-icon">+</div>
                    <div class="add-text">Add Image</div>
                </div>
            </div>

            <input type="file" id="car-file-input" class="file-input" accept="image/*" multiple>

            <div class="upload-count" id="car-upload-count">0 of 3 images selected</div>
            <div class="tips">
                <i class="fa-solid fa-circle-exclamation"></i>
                <h5>Tips for great car photos:</h5>
                <ul>
                    <li>Take photos in good lighting (daylight is best)</li>
                    <li>Include exterior (front, back, sides) and interior views</li>
                    <li>Make sure the car is clean and the background is not cluttered</li>
                    <li>Highlight any special features or unique aspects</li>
                </ul>
            </div>
        </div>

        <button type="submit" class="submit">List Your Car</button>
    </form>
    <script src="javascript/process_car_listing.js"></script>
</body>

</html>
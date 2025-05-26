<?php
session_start();
include_once("libs/dbfunctions.php");
$dbobject = new dbobject();

$email = '';
$is_valid = false;
$error_message = '';

if (!isset($_SESSION['username_sess'])) {
    $error_message = "Invalid request. Session expired.";
    header("Location: sign_in.php?error=" . urlencode("Please login first to complete your registration."));
    exit;
} else {
    
    $username = $_SESSION['username_sess'];
    $is_valid = true;

    //Get merchant_id for the logged in user
    $sql = "SELECT merchant_id FROM userdata WHERE username = '$username' LIMIT 1";
    $result = $dbobject->db_query($sql);
    $merchant_id = isset($result[0]['merchant_id']) ? $result[0]['merchant_id'] : null;
    

    $sql = "SELECT DISTINCT(State) as state,stateid FROM lga order by State";
    $states = $dbobject->db_query($sql);

    if (isset($_POST['fetch_lga']) && isset($_POST['state_id'])) {
        $state_id = $_POST['state_id'];
        // Fetch LGAs based on the selected state
        $query = "SELECT Lga, stateid as id FROM lga WHERE stateid = '$state_id' ORDER BY lga";
        
        $lgas = $dbobject->db_query($query);
        // Return the result as JSON
        $lgas1 = json_encode($lgas);
        echo $lgas1;
        exit;
    } 
    
    header("Cache-Control: no-cache;no-store, must-revalidate");
    header_remove("X-Powered-By");
    header_remove('X-Frame-Options: SAMEORIGIN');

    $crossorigin = 'anonymous';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Complete Registration | Inventory System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/jquery.blockUI.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>

<body class="bg-gradient-to-br from-cyan-50 to-cyan-100 min-h-screen flex items-center justify-center">

    <div class="w-full max-w-3xl bg-white p-8 rounded-xl shadow-xl">
        <h2 class="text-2xl font-bold text-center text-gray-700 mb-4">You are almost done</h2>
        <p class="text-center text-sm text-gray-500 mb-4">Fill in your remaining information to complete registration.
        </p>

        <!-- Progress Bar -->
        <div class="w-full bg-gray-200 h-2 rounded-full mb-6">
            <div id="progressBar" class="bg-cyan-600 h-2 rounded-full transition-all duration-300 ease-in-out"
                style="width: 33%;"></div>
        </div>

        <form id="multiStepForm" autocomplete="off" enctype="multipart/form-data">
            <!-- Step 1 - Personal Information -->
            <div class="form-step active grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <input type="hidden" name="merchant_id" value="<?php echo $merchant_id; ?>" />
                    <input type="hidden" name="username" value="<?php echo $_SESSION['username_sess']; ?>" />
                    <input type="hidden" name="op" value="Users.complete_registration" />
                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                    <input type="text" placeholder="First Name" required
                        class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-cyan-400 focus:outline-none"
                        id="merchant_first_name" name="merchant_first_name" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                    <input type="text" placeholder="Last Name" required
                        class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-cyan-400 focus:outline-none"
                        id="merchant_last_name" name="merchant_last_name" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" placeholder="Email" required
                        class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-cyan-400 focus:outline-none"
                        id="merchant_email" name="merchant_email" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="tel" placeholder="Phone Number" required
                        class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-cyan-400 focus:outline-none"
                        id="merchant_phone" name="merchant_phone" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                    <input type="date" required
                        class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-cyan-400 focus:outline-none"
                        id="merchant_dob" name="merchant_dob" max="" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <input type="text" placeholder="Address" required class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-cyan-400
                        focus:outline-none" id="merchant_address" name="merchant_address" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">State of Origin</label>
                    <select onchange="fetchLgas(this.value)" required
                        class="form-control w-full border border-gray-300 rounded-lg p-2 select2"
                        name="merchant_state" id="merchant_state">
                        <option value="">::SELECT STATE::</option>
                        <?php
                            foreach ($states as $row) {
                                echo "<option value='".$row['stateid']."'>".$row['state']."</option>";
                            }
                        ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">LGA</label>
                    <select class="form-control w-full border border-gray-300 rounded-lg p-2 select2"
                        name="merchant_lga" id="merchant_lga" required>
                        <option value="">::SELECT LGA::</option>
                    </select>
                </div>

                <div class="col-span-2">
                    <button type="button" class="w-full bg-cyan-600 hover:bg-cyan-700 text-white py-2 rounded-lg mt-4"
                        onclick="nextStep()">Next</button>
                </div>
            </div>

            <!-- Step 2 - Business Information -->
            <div class="form-step hidden grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Business Name</label>
                    <input type="text" placeholder="Business Name" required
                        class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-cyan-400 focus:outline-none"
                        id="merchant_business_name" name="merchant_business_name" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Business Description</label>
                    <input type="text" placeholder="Business Description" required
                        class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-cyan-400 focus:outline-none"
                        id="merchant_business_description" name="merchant_business_description" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Business Email</label>
                    <input type="email" placeholder="Business Email" required
                        class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-cyan-400 focus:outline-none"
                        id="merchant_support_email" name="merchant_support_email" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Business Phone</label>
                    <input type="tel" placeholder="Business Phone" required
                        class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-cyan-400 focus:outline-none"
                        id="merchant_business_phone" name="merchant_business_phone" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload Business Logo</label>
                    <div class="relative w-32 h-32 flex items-center justify-center border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 cursor-pointer hover:border-cyan-400 transition group" onclick="document.getElementById('merchant_logo').click()">
                        <img id="logoPreview" src="#" alt="Logo Preview" class="hidden absolute inset-0 w-full h-full object-contain rounded-lg" />
                        <span id="logoPlaceholder" class="text-gray-400 flex flex-col items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7M16 3H8a2 2 0 00-2 2v0a2 2 0 002 2h8a2 2 0 002-2v0a2 2 0 00-2-2z" />
                            </svg>
                            <span class="text-xs">Click to upload</span>
                        </span>
                        <button type="button" id="removeLogoBtn" class="hidden absolute top-1 right-1 bg-white rounded-full p-1 shadow hover:bg-gray-100" onclick="removeImage(event, 'merchant_logo', 'logoPreview', 'logoPlaceholder', 'removeLogoBtn')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <input type="file" required class="hidden" id="merchant_logo" name="merchant_logo" accept="image/*" onchange="previewImage(this, 'logoPreview', 'logoPlaceholder', 'removeLogoBtn')" />
                </div>
                <div class="col-span-2 flex justify-between mt-4">
                    <button type="button"
                        class="text-gray-700 border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-100"
                        onclick="prevStep()">Previous</button>
                    <button type="button" class="bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 rounded-lg"
                        onclick="nextStep()">Next</button>
                </div>
            </div>

            <!-- Step 3 - Business Documentation -->
            <div class="form-step hidden grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">CAC Number</label>
                    <input type="text" placeholder="CAC Number" required
                        class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-cyan-400 focus:outline-none"
                        id="cac_number" name="cac_number" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload CAC Document</label>
                    <div class="relative w-32 h-32 flex items-center justify-center border-2 border-dashed border-gray-300 rounded-lg bg-gray-50 cursor-pointer hover:border-cyan-400 transition group" onclick="document.getElementById('cac_document').click()">
                        <img id="cacPreview" src="#" alt="CAC Preview" class="hidden absolute inset-0 w-full h-full object-contain rounded-lg" />
                        <span id="cacPlaceholder" class="text-gray-400 flex flex-col items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 014-4h2a4 4 0 014 4v2M7 7a4 4 0 018 0v4a4 4 0 01-8 0V7z" />
                            </svg>
                            <span class="text-xs">Click to upload</span>
                        </span>
                        <span id="cacFileName" class="hidden absolute bottom-1 left-1 right-1 text-xs text-gray-500 bg-white bg-opacity-80 rounded px-1 py-0.5"></span>
                        <button type="button" id="removeCacBtn" class="hidden absolute top-1 right-1 bg-white rounded-full p-1 shadow hover:bg-gray-100" onclick="removeImage(event, 'cac_document', 'cacPreview', 'cacPlaceholder', 'removeCacBtn')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <input type="file" required class="hidden" id="cac_document" name="cac_document" accept=".pdf,.jpg,.jpeg,.png" onchange="previewImage(this, 'cacPreview', 'cacPlaceholder', 'removeCacBtn', 'cac')" />
                </div>
                <div class="col-span-2 flex justify-between mt-4">
                    <button type="button"
                        class="text-gray-700 border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-100"
                        onclick="prevStep()">Previous</button>
                    <button type="submit" id="submitBtn"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">Complete
                        Registration</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        const steps = document.querySelectorAll(".form-step");
        const progressBar = document.getElementById("progressBar");
        let currentStep = 0;

        function showStep(index) {
            steps.forEach((step, i) => {
                step.classList.toggle("hidden", i !== index);
                step.classList.toggle("active", i === index);
            });
            const percent = ((index + 1) / steps.length) * 100;
            progressBar.style.width = percent + "%";
        }

        function validateCurrentStep() {
            const currentForm = steps[currentStep];
            const requiredFields = currentForm.querySelectorAll("input[required], select[required]");
            let valid = true;

            requiredFields.forEach(field => {
                if (!field.value || (field.type === "file" && field.files.length === 0)) {
                    field.classList.add("border-red-500");
                    valid = false;
                } else {
                    field.classList.remove("border-red-500");
                }
            });
            return valid;
        }

        function nextStep() {
            if (!validateCurrentStep()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Required Fields',
                    text: 'Please fill all required fields before proceeding.'
                });
                return;
            }
            if (currentStep < steps.length - 1) {
                currentStep++;
                showStep(currentStep);
            }
        }

        function prevStep() {
            if (currentStep > 0) {
                currentStep--;
                showStep(currentStep);
            }
        }

        function fetchLgas(stateId) {
            const lgaSelect = document.getElementById("merchant_lga");
            lgaSelect.innerHTML = '<option value="">Loading LGAs...</option>';

            fetch(window.location.pathname, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "fetch_lga=1&state_id=" + encodeURIComponent(stateId)
                })
                .then(response => response.json())
                .then(data => {
                    let options = '<option value="">::SELECT LGA::</option>';
                    data.forEach(lga => {
                        options += `<option value="${lga.id}">${lga.Lga}</option>`;
                    });
                    lgaSelect.innerHTML = options;
                    // Re-initialize Select2 after updating options
                    $('#merchant_lga').select2({
                        width: '100%',
                        placeholder: 'Select an option',
                        allowClear: true
                    });
                })
                .catch(error => {
                    console.error("Error fetching LGAs:", error);
                    lgaSelect.innerHTML = '<option value="">Error loading LGAs</option>';
                });
        }

        function previewImage(input, previewId, placeholderId, removeBtnId, type = 'logo') {
            const file = input.files[0];
            const preview = document.getElementById(previewId);
            const placeholder = document.getElementById(placeholderId);
            const removeBtn = document.getElementById(removeBtnId);
            let errorSpan = preview.parentElement.querySelector('.file-error');
            if (!errorSpan) {
                errorSpan = document.createElement('span');
                errorSpan.className = 'file-error text-xs text-red-500 absolute bottom-1 left-1 right-1 bg-white bg-opacity-80 rounded px-1 py-0.5';
                preview.parentElement.appendChild(errorSpan);
            }
            errorSpan.textContent = '';
            errorSpan.classList.add('hidden');

            // Validation rules
            let allowedTypes, maxSizeMB;
            if (type === 'logo') {
                allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                maxSizeMB = 5;
            } else if (type === 'cac') {
                allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
                maxSizeMB = 10;
            }

            if (!file) {
                preview.classList.add('hidden');
                placeholder.classList.remove('hidden');
                if (removeBtn) removeBtn.classList.add('hidden');
                if (type === 'cac') {
                    document.getElementById('cacFileName').classList.add('hidden');
                    document.getElementById('cacFileName').textContent = '';
                }
                return;
            }

            // Check file type
            if (!allowedTypes.includes(file.type)) {
                preview.classList.add('hidden');
                placeholder.classList.remove('hidden');
                if (removeBtn) removeBtn.classList.add('hidden');
                errorSpan.textContent = type === 'logo'
                    ? 'Invalid file type. Only JPG, JPEG, PNG, and GIF allowed.'
                    : 'Invalid file type. Only PDF, JPG, JPEG, and PNG allowed.';
                errorSpan.classList.remove('hidden');
                input.value = '';
                if (type === 'cac') {
                    document.getElementById('cacFileName').classList.add('hidden');
                    document.getElementById('cacFileName').textContent = '';
                }
                return;
            }

            // Check file size
            if (file.size > maxSizeMB * 1024 * 1024) {
                preview.classList.add('hidden');
                placeholder.classList.remove('hidden');
                if (removeBtn) removeBtn.classList.add('hidden');
                errorSpan.textContent = type === 'logo'
                    ? 'File too large. Max 5MB allowed.'
                    : 'File too large. Max 10MB allowed.';
                errorSpan.classList.remove('hidden');
                input.value = '';
                if (type === 'cac') {
                    document.getElementById('cacFileName').classList.add('hidden');
                    document.getElementById('cacFileName').textContent = '';
                }
                return;
            }

            // If CAC and PDF, show filename
            if (type === 'cac' && file.type === 'application/pdf') {
                preview.classList.add('hidden');
                placeholder.classList.add('hidden');
                if (removeBtn) removeBtn.classList.remove('hidden');
                const fileNameSpan = document.getElementById('cacFileName');
                fileNameSpan.textContent = file.name;
                fileNameSpan.classList.remove('hidden');
                errorSpan.classList.add('hidden');
                return;
            }

            // If image, show preview
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    placeholder.classList.add('hidden');
                    if (removeBtn) removeBtn.classList.remove('hidden');
                    errorSpan.classList.add('hidden');
                    if (type === 'cac') {
                        document.getElementById('cacFileName').classList.add('hidden');
                        document.getElementById('cacFileName').textContent = '';
                    }
                }
                reader.readAsDataURL(file);
            }
        }

        function removeImage(event, inputId, previewId, placeholderId, removeBtnId) {
            event.stopPropagation();
            const input = document.getElementById(inputId);
            const preview = document.getElementById(previewId);
            const placeholder = document.getElementById(placeholderId);
            const removeBtn = document.getElementById(removeBtnId);
            let errorSpan = preview.parentElement.querySelector('.file-error');
            if (errorSpan) {
                errorSpan.textContent = '';
                errorSpan.classList.add('hidden');
            }

            input.value = '';
            preview.src = "#";
            preview.classList.add('hidden');
            placeholder.classList.remove('hidden');
            if (removeBtn) removeBtn.classList.add('hidden');
            if (inputId === 'cac_document') {
                document.getElementById('cacFileName').classList.add('hidden');
                document.getElementById('cacFileName').textContent = '';
            }
        }

        // Set max date for DOB to ensure user is at least 18 years old
        document.addEventListener("DOMContentLoaded", function() {
            const dobInput = document.getElementById('merchant_dob');
            if (dobInput) {
                const today = new Date();
                today.setFullYear(today.getFullYear() - 18);
                const yyyy = today.getFullYear();
                const mm = String(today.getMonth() + 1).padStart(2, '0');
                const dd = String(today.getDate()).padStart(2, '0');
                dobInput.max = `${yyyy}-${mm}-${dd}`;
            }

            // Initialize Select2 for all .select2 elements
            $('.select2').select2({
                width: '100%',
                placeholder: 'Select an option',
                allowClear: true
            });
        });

        document.getElementById("multiStepForm").addEventListener("submit", function (e) {
            e.preventDefault();

            if (!validateCurrentStep()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Required Fields',
                    text: 'Please fill all required fields before submitting.'
                });
                return;
            }

            const formData = new FormData(this);
            const submitBtn = document.getElementById('submitBtn');

            $.blockUI({
                message: "Processing registration... Please wait...",
            });

            $.ajax({
                type: "post",
                url: "utilities_default.php",
                data: formData,
                processData: false,
                contentType: false,
                dataType: "json",
                success: function (response) {
                    $.unblockUI();

                    if (response.response_code == 0) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Registration Completed!',
                            text: response.response_message,
                            timer: 3000,
                            showConfirmButton: false
                        });

                        setTimeout(() => {
                            window.location = response.data.redirect || 'home.php';
                        }, 3000);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Registration Failed',
                            text: response.response_message
                        });
                    }
                },
                error: function (xhr, status, error) {
                    $.unblockUI();
                    console.error("Error:", error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: "Unable to process request at the moment! Please try again"
                    });
                }
            });
        });
    </script>

</body>

</html>
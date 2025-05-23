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

    //Get default role for registration
    $sql = "SELECT merchant_id FROM userdata WHERE username = '$username' LIMIT 1";
    $result = $dbobject->db_query($sql);
    $merchant_id = isset($result[0]['merchant_id']) ? $result[0]['merchant_id'] : null;


    $sql = "SELECT DISTINCT(State) as state,stateid FROM lga order by State";
    $states = $dbobject->db_query($sql);

    if (isset($_POST['fetch_lga']) && isset($_POST['state_id'])) {
    $state_id = $_POST['state_id'];
    $query = "SELECT lga, id FROM lga WHERE stateid = '$state_id' ORDER BY lga";
    $lgas = $dbobject->db_query($query);
    echo json_encode($lgas);
    exit;
    } 
    // Important: prevents the rest of the page from loading }
   
    //Check if user is already logged in

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

        <form id="multiStepForm" autocomplete="off">
            <!-- Step 1 -->
            <div class="form-step active grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">First Name</label>
                    <input type="text" placeholder="First Name" required
                        class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-cyan-400 focus:outline-none" id="merchant_first_name" name="merchant_first_name" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Name</label>
                    <input type="text" placeholder="Last Name" required
                        class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-cyan-400 focus:outline-none" 
                        id="merchant_last_name" name="merchant_last_name"/>
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
                        id="merchant_phone" name="merchant_phone"/>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                    <input type="date" required
                        class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-cyan-400 focus:outline-none" 
                        id="merchant_dob" name="merchant_dob"/>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <input type="text" placeholder="Address" required
                        class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-cyan-400
                        focus:outline-none" id="merchant_address" name="merchant_address"/>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">State of Origin</label>
                    <select onchange="fetchLgas(this.value)"
                        class="form-control w-full border border-gray-300 rounded-lg p-2" name="merchant_state"
                        id="merchant_state">
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
                        <select class="form-control w-full border border-gray-300 rounded-lg p-2" name="merchant_lga"
                            id="merchant_lga">
                            <option value="">::SELECT LGA::</option>
                        </select>
                    </div>
               
                <div class="col-span-2">
                    <button type="button" class="w-full bg-cyan-600 hover:bg-cyan-700 text-white py-2 rounded-lg mt-4"
                        onclick="nextStep()">Next</button>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="form-step hidden grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Business Name</label>
                    <input type="text" placeholder="Business Name" required
                        class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-cyan-400 focus:outline-none"
                        id="merchant_business_name" name="merchant_business_name"
                        />
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload Business Logo</label>
                    <input type="file" required class="w-full border border-gray-300 rounded-lg p-2 bg-white" 
                    id="merchant_logo" name="merchant_logo"/>
                </div>
                <div class="col-span-2 flex justify-between mt-4">
                    <button type="button"
                        class="text-gray-700 border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-100"
                        onclick="prevStep()">Previous</button>
                    <button type="button" class="bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 rounded-lg"
                        onclick="nextStep()">Next</button>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="form-step hidden grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">CAC Number</label>
                    <input type="text" placeholder="CAC Number" required
                        class="w-full border border-gray-300 rounded-lg p-2 focus:ring-2 focus:ring-cyan-400 focus:outline-none"
                        id="cac_number" name="cac_number" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Upload CAC Document</label>
                    <input type="file" required class="w-full border border-gray-300 rounded-lg p-2 bg-white" 
                    id="cac_document" name="cac_document"/>
                </div>
                <div class="col-span-2 flex justify-between mt-4">
                    <button type="button"
                        class="text-gray-700 border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-100"
                        onclick="prevStep()">Previous</button>
                    <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg">Submit</button>
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

        function nextStep() {
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
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "fetch_lga=1&state_id=" + encodeURIComponent(stateId)
        })
        .then(response => response.json())
        .then(data => {
        let options = '<option value="">::SELECT LGA::</option>';
        data.forEach(lga => {
        options += `<option value="${lga.id}">${lga.lga}</option>`;
        });
        lgaSelect.innerHTML = options;
        })
        .catch(error => {
        console.error("Error fetching LGAs:", error);
        lgaSelect.innerHTML = '<option value="">Error loading LGAs</option>';
        });
        }
        document.getElementById("multiStepForm").addEventListener("submit", function (e) {
            e.preventDefault();
            alert("Registration complete!");
        });
    </script>

</body>

</html>

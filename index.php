<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phone Number Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <script>
        function toggleInputSelection() {
            var inputType = document.querySelector('input[name="inputType"]:checked').value;
            if (inputType === 'phone') {
                document.getElementById('phoneInput').style.display = 'block';
                document.getElementById('fileInput').style.display = 'none';
                document.getElementById('emailFileInput').style.display = 'none';
            } else if (inputType === 'file') {
                document.getElementById('phoneInput').style.display = 'none';
                document.getElementById('fileInput').style.display = 'block';
                document.getElementById('emailFileInput').style.display = 'none';
            } else if (inputType === 'email') {
                document.getElementById('phoneInput').style.display = 'none';
                document.getElementById('fileInput').style.display = 'none';
                document.getElementById('emailFileInput').style.display = 'block';
            }
        }

        function showAlert(message, type) {
            var alertBox = document.createElement('div');
            alertBox.className = 'alert alert-' + type;
            alertBox.textContent = message;
            document.getElementById('alertContainer').appendChild(alertBox);
        }

        window.onload = function () {
            var urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('error')) {
                showAlert(urlParams.get('error'), 'danger');
            } else if (urlParams.has('success')) {
                showAlert(urlParams.get('success'), 'success');
            }
        }

        function selectCategory(category) {
            document.getElementById('category').value = category;
            var buttons = document.querySelectorAll('.btn-group .btn');
            buttons.forEach(function (button) {
                button.classList.remove('active');
            });
            document.querySelector('[onclick="selectCategory(\'' + category + '\')"]').classList.add('active');
        }

        function validatePhoneNumber() {
            var inputType = document.querySelector('input[name="inputType"]:checked').value;
            if (inputType === 'phone') {
                var phoneNumber = document.getElementById('phoneNumber').value;
                if (!phoneNumber.match(/^[7869354]\d{8}$/)) {
                    showAlert('Phone number must start with 7, 8, 6, 9, 3, 5, or 4 and be 9 digits long after "+8801".', 'danger');
                    return false;
                }
                document.getElementById('phoneNumber').value = '+8801' + phoneNumber;
            }
            return true;
        }

        function selectContactType(contactType) {
            document.getElementById('contactType').value = contactType;
            var buttons = document.querySelectorAll('.contact-type-group .btn');
            buttons.forEach(function (button) {
                button.classList.remove('active');
            });
            document.querySelector('[onclick="selectContactType(\'' + contactType + '\')"]').classList.add('active');
        }
    </script>
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center">
        <div class="row">
            <div class="col-md-12 box">
                <h3 class="text-center mb-4">Phone Number Manager</h3>
                <div id="alertContainer"></div>

                <form method="POST" action="process.php" class="mb-4" enctype="multipart/form-data"
                    onsubmit="return validatePhoneNumber()">
                    <div class="mb-3">
                        <label>Select Input Type</label><br>
                        <div class="btn-group" role="group" aria-label="Input Type">
                            <input type="radio" class="btn-check" id="phone" name="inputType" value="phone"
                                onclick="toggleInputSelection()" required>
                            <label class="btn btn-outline-primary" for="phone">Phone Number</label>

                            <input type="radio" class="btn-check" id="file" name="inputType" value="file"
                                onclick="toggleInputSelection()">
                            <label class="btn btn-outline-primary" for="file">File Upload</label>

                            <input type="radio" class="btn-check" id="email" name="inputType" value="email"
                                onclick="toggleInputSelection()">
                            <label class="btn btn-outline-primary" for="email">Email</label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="h5">Select Category</label><br>
                        <div class="row">
                            <div class="col-12 col-md-4 mb-2 mb-md-0">
                                <button type="button" class="btn btn-outline-primary w-100 text-truncate"
                                    onclick="selectCategory('matchmaker')">Matchmaker</button>
                            </div>
                            <div class="col-12 col-md-4 mb-2 mb-md-0">
                                <button type="button" class="btn btn-outline-primary w-100 text-truncate"
                                    onclick="selectCategory('candidate')">Candidate</button>
                            </div>
                            <div class="col-12 col-md-4">
                                <button type="button" class="btn btn-outline-primary w-100 text-truncate"
                                    onclick="selectCategory('guardian')">Guardian</button>
                            </div>
                        </div>
                        <input type="hidden" id="category" name="category" required>
                    </div>

                    <div class="mb-3">
                        <label class="h5">Select Contact Type</label><br>
                        <div class="row contact-type-group">
                            <div class="col-12 col-md-4 mb-2 mb-md-0">
                                <button type="button" class="btn btn-outline-primary w-100 text-truncate"
                                    onclick="selectContactType('bulk')">Bulk</button>
                            </div>
                            <div class="col-12 col-md-4 mb-2 mb-md-0">
                                <button type="button" class="btn btn-outline-primary w-100 text-truncate"
                                    onclick="selectContactType('whatsapp')">WhatsApp</button>
                            </div>
                            <div class="col-12 col-md-4 mb-2 mb-md-0">
                                <button type="button" class="btn btn-outline-primary w-100 text-truncate"
                                    onclick="selectContactType('email')">Email</button>
                            </div>
                        </div>
                        <input type="hidden" id="contactType" name="contactType" required>
                    </div>

                    <div class="mb-3" id="phoneInput" style="display: none;">
                        <label for="phoneNumber">Enter Phone Number</label>
                        <div class="input-group">
                            <span class="input-group-text">+8801</span>
                            <input type="text" class="form-control" id="phoneNumber" name="phoneNumber"
                                placeholder="Enter Phone Number" maxlength="9" />
                        </div>
                    </div>

                    <div class="mb-3" id="fileInput" style="display: none;">
                        <label for="fileInput">Upload File</label>
                        <input type="file" class="form-control" id="fileInput" name="fileInput" accept=".txt" />
                    </div>

                    <div class="mb-3" id="emailFileInput" style="display: none;">
                        <label for="emailFileInput">Upload Email File</label>
                        <input type="file" class="form-control" id="emailFileInput" name="emailFileInput"
                            accept=".txt" />
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mt-3">Add Contact</button>
                </form>
            </div>
        </div>

        <div class="col-md-12 box">
            <div class="row text-center mb-4">
                <?php include 'fetch_data.php'; ?>
                <div class="col-md-4">
                    <h4>Matchmaker</h4>
                    <button class="btn btn-success mb-2" onclick="downloadFile('matchmaker', 'bulk')">
                        Bulk List Download <span
                            class="badge bg-danger"><?php echo $counts['matchmaker_bulk']; ?></span>
                    </button>
                    <button class="btn btn-success" onclick="downloadFile('matchmaker', 'email')">
                        Email List Download <span
                            class="badge bg-danger"><?php echo $counts['matchmaker_email']; ?></span>
                    </button>
                    <button class="btn btn-success" onclick="downloadFile('matchmaker', 'whatsapp')">
                        WhatsApp List Download <span
                            class="badge bg-danger"><?php echo $counts['matchmaker_whatsapp']; ?></span>
                    </button>

                </div>
                <div class="col-md-4">
                    <h4>Candidate</h4>
                    <button class="btn btn-success mb-2" onclick="downloadFile('candidate', 'bulk')">
                        Bulk List Download <span class="badge bg-danger"><?php echo $counts['candidate_bulk']; ?></span>
                    </button>
                    <button class="btn btn-success" onclick="downloadFile('candidate', 'email')">
                        Email List Download <span
                            class="badge bg-danger"><?php echo $counts['candidate_email']; ?></span>
                    </button>
                    <button class="btn btn-success" onclick="downloadFile('candidate', 'whatsapp')">
                        WhatsApp List Download <span
                            class="badge bg-danger"><?php echo $counts['candidate_whatsapp']; ?></span>
                    </button>

                </div>
                <div class="col-md-4">
                    <h4>Guardian</h4>
                    <button class="btn btn-success mb-2" onclick="downloadFile('guardian', 'bulk')">
                        Bulk List Download <span class="badge bg-danger"><?php echo $counts['guardian_bulk']; ?></span>
                    </button>
                    <button class="btn btn-success" onclick="downloadFile('guardian', 'email')">
                        Email List Download <span
                            class="badge bg-danger"><?php echo $counts['guardian_email']; ?></span>
                    </button>
                    <button class="btn btn-success" onclick="downloadFile('guardian', 'whatsapp')">
                        WhatsApp List Download <span
                            class="badge bg-danger"><?php echo $counts['guardian_whatsapp']; ?></span>
                    </button>
                </div>
            </div>
        </div>

    </div>
    </div>

    <script>
        function downloadFile(category, content) {
            window.location.href = `download.php?category=${category}&content=${content}`;
        }
    </script>
</body>

</html>
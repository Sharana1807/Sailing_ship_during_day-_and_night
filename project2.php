<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate QR code for lab components</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        // Load departments on page load
        $.get('index.php?action=get_departments', function(data) {
            $('#departments').html(data);
        });

        // Load labs based on selected department
        $('#departments').on('change', function() {
            var department_id = $(this).val();
            $.get('index.php?action=get_labs&department_id=' + department_id, function(data) {
                $('#labs').html(data);
                $('#systems').html('<option value="">Select System</option>'); // Reset systems
                $('#components').html('<option value="">Select Component</option>'); // Reset components
                $('#qr_code').html(''); // Clear QR code
            });
        });

        // Load systems based on selected lab
        $('#labs').on('change', function() {
            var lab_id = $(this).val();
            $.get('index.php?action=get_systems&lab_id=' + lab_id, function(data) {
                $('#systems').html(data);
                $('#components').html('<option value="">Select Component</option>'); // Reset components
                $('#qr_code').html(''); // Clear QR code
            });
        });

        // Load components based on selected system
        $('#systems').on('change', function() {
            var system_id = $(this).val();
            $.get('index.php?action=get_components&system_id=' + system_id, function(data) {
                $('#components').html(data);
                $('#qr_code').html(''); // Clear QR code
            });
        });

        // Generate QR code based on selected component
        $('#components').on('change', function() {
            var component_id = $(this).val();
            $.get('index.php?action=generate_qr&component_id=' + component_id, function(data) {
                $('#qr_code').html(data);
            });
        });
    });
    </script>
    <style>
    nav {
        background-color: lightblue;
        padding: 15px;
    }
    nav a {
        color: #fff;
        text-decoration: none;
        padding: 10px 20px;
        margin: 0px 10px;
        border-radius: 5px;
        background-color: #555;
        display: inline-block;
    }
    nav a:hover {
        background-color: blue;
    }
    </style>
</head>
<body>
    <center>
        <h1 style="background-color:#161B7F;color:yellow">Generate QR code for lab components</h1>
        <nav>
            <b><h1>Select Department</h1></b>
            <select id="departments"></select>
            <b><h1>Select Lab</h1></b>
            <select id="labs"></select>
            <b><h1>Select System</h1></b>
            <select id="systems"></select>
            <b><h1>Select Component</h1></b>
            <select id="components"></select>
            <br><br>
            <div id="qr_code"></div>
        </nav>
    </center>
</body>
</html>

<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'lab_objects');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action == 'get_departments') {
    $sql = "SELECT id, name FROM departments";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        echo '<option value="">Select Department</option>';
        while($row = $result->fetch_assoc()) {
            echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
        }
    } else {
        echo '<option value="">No Departments</option>';
    }
} elseif ($action == 'get_labs') {
    $department_id = $_GET['department_id'];
    $stmt = $conn->prepare("SELECT id, name FROM labs WHERE department_id = ?");
    $stmt->bind_param("i", $department_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo '<option value="">Select Lab</option>';
        while($row = $result->fetch_assoc()) {
            echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
        }
    } else {
        echo '<option value="">No Labs</option>';
    }
    $stmt->close();
} elseif ($action == 'get_systems') {
    $lab_id = $_GET['lab_id'];
    $stmt = $conn->prepare("SELECT id, name FROM systems WHERE lab_id = ?");
    $stmt->bind_param("i", $lab_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo '<option value="">Select System</option>';
        while($row = $result->fetch_assoc()) {
            echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
        }
    } else {
        echo '<option value="">No Systems</option>';
    }
    $stmt->close();
} elseif ($action == 'get_components') {
    $system_id = $_GET['system_id'];
    $stmt = $conn->prepare("SELECT id, name FROM components WHERE system_id = ?");
    $stmt->bind_param("i", $system_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo '<option value="">Select Component</option>';
        while($row = $result->fetch_assoc()) {
            echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
        }
    } else {
        echo '<option value="">No Components</option>';
    }
    $stmt->close();
} elseif ($action == 'generate_qr') {
    $component_id = $_GET['component_id'];
    $stmt = $conn->prepare("
        SELECT 
            components.name AS component_name, 
            systems.name AS system_name, 
            labs.name AS lab_name, 
            departments.name AS department_name 
        FROM components 
        JOIN systems ON components.system_id = systems.id 
        JOIN labs ON systems.lab_id = labs.id 
        JOIN departments ON labs.department_id = departments.id 
        WHERE components.id = ?
    ");
    $stmt->bind_param("i", $component_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $details = $result->fetch_assoc();
    $stmt->close();
    
    if ($details) {
        $info = "Department: " . $details['department_name'] . "\n" .
                "Lab: " . $details['lab_name'] . "\n" .
                "System: " . $details['system_name'] . "\n" .
                "Component: " . $details['component_name'];
        
        $encoded_info = urlencode($info);
        $qr_code_url = "https://api.qrserver.com/v1/create-qr-code/?data={$encoded_info}&size=150x150";
        
        // Save the QR code image locally
        $image_path = "qrcode_{$component_id}.png";
        file_put_contents($image_path, file_get_contents($qr_code_url));

        // Update the component's QR code path in the database
        $update_stmt = $conn->prepare("UPDATE components SET qr_code_path = ? WHERE id = ?");
        $update_stmt->bind_param("si", $image_path, $component_id);
        $update_stmt->execute();
        $update_stmt->close();

        // Display the QR code image, download link, and component information
        echo "<img src='$image_path' alt='QR Code' />";
        echo "<br><h2><a href='$image_path' download>Download QR Code</a></h2>";
        echo "<p><strong>Component Information:</strong></p>";
        echo "<p>Department: " . $details['department_name'] . "</p>";
        echo "<p>Lab: " . $details['lab_name'] . "</p>";
        echo "<p>System: " . $details['system_name'] . "</p>";
        echo "<p>Component: " . $details['component_name'] . "</p>";
    } else {
        echo "Component not found.";
    }
}

$conn->close();
?>
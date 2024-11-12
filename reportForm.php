<?php
if (isset($_GET['post_id'])) {
    $post_id = $_GET['post_id'];
} else {
    die("No post selected.");
}
?>
<div class="container">

    <div id="reportFormContainer">
        <h2 class="form-title">Report Post</h2>
        <form id="reportForm" action="submit_report.php" method="POST">
            <div class="mb-3">
                <label for="reportTitle" class="form-label">Reason for Report</label>
                <select class="form-select" id="reportTitle" name="report_title" required>
                    <option value="" disabled selected>Select a reason</option>
                    <option value="Spam">Spam</option>
                    <option value="Inappropriate Content">Inappropriate Content</option>
                    <option value="Harassment">Harassment</option>
                </select> 
            </div> <br>
            <div class="mb-3">
                <label for="reportDescription" class="form-label">Additional Details</label>
                <textarea class="form-control" id="reportDescription" name="report_description" rows="4" placeholder="Provide more information (optional)"></textarea>
            </div> <br>
            <!-- Hidden input to pass the post ID -->
            <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
            <button type="submit" class="btn btn-danger btn-block" onclick="goBack()" style="background-color:black">Back</button>
            <button type="submit" class="btn btn-danger btn-block">Submit Report</button>
        </form>
    </div>
</div>

<script>
    function goBack() {
        window.history.back();
    }
</script>

<style>
    /* พื้นหลังแบบ Gradient */
body {
    background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
    min-height: 100vh;
    margin: 0;
    font-family: 'Poppins', sans-serif;
}

/* คอนเทนเนอร์หลัก */
.container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    padding: 2rem;
}

/* การ์ดฟอร์ม */
#reportFormContainer {
    background: rgba(255, 255, 255, 0.95);
    padding: 3rem;
    border-radius: 20px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1), 
                0 5px 15px rgba(0, 0, 0, 0.05);
    max-width: 500px;
    width: 100%;
    backdrop-filter: blur(10px);
    transform: translateY(0);
    transition: all 0.3s ease;
}

#reportFormContainer:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15), 
                0 8px 20px rgba(0, 0, 0, 0.1);
}

/* หัวข้อฟอร์ม */
.form-title {
    color: #C85C5C;
    font-size: 2rem;
    font-weight: 600;
    text-align: center;
    margin-bottom: 2rem;
    position: relative;
    padding-bottom: 1rem;
}

.form-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 60px;
    height: 3px;
    background: linear-gradient(to right, #FBD148, #C85C5C);
    border-radius: 3px;
}

/* ป้ายกำกับ */
.form-label {
    color: #2b2d42;
    font-weight: 600;
    font-size: 1rem;
    margin-bottom: 0.8rem;
    display: block;
}

/* Select Field */
.form-select {
    width: 100%;
    padding: 1rem;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    background-color: #fff;
    color: #2b2d42;
    font-size: 1rem;
    transition: all 0.3s ease;
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24'%3E%3Cpath fill='%23C85C5C' d='M7 10l5 5 5-5z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1rem center;
}

.form-select:focus {
    border-color: #C85C5C;
    box-shadow: 0 0 0 0.2rem rgba(200, 92, 92, 0.25);
    outline: none;
}

/* Textarea */
.form-control {
    width: 100%;
    padding: 1rem;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    background-color: #fff;
    color: #2b2d42;
    font-size: 1rem;
    transition: all 0.3s ease;
    resize: vertical;
    min-height: 120px;
}

.form-control:focus {
    border-color: #C85C5C;
    box-shadow: 0 0 0 0.2rem rgba(200, 92, 92, 0.25);
    outline: none;
}

/* ปุ่มกด */
.btn-container {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.btn {
    flex: 1;
    padding: 1rem 2rem;
    border: none;
    border-radius: 25px;
    font-size: 1rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.3s ease;
    cursor: pointer;
}

.btn-danger {
    background: #C85C5C;
    color: white;
}

.btn-back {
    background: #2b2d42;
    color: white;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.btn:active {
    transform: translateY(0);
}

/* Animation สำหรับข้อความแจ้งเตือน */
.alert {
    padding: 1rem;
    border-radius: 12px;
    margin-bottom: 1.5rem;
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from {
        transform: translateY(-10px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

/* Placeholder สไตล์ */
::placeholder {
    color: #adb5bd;
    opacity: 1;
}

/* Responsive Design */
@media (max-width: 768px) {
    .container {
        padding: 1rem;
    }

    #reportFormContainer {
        padding: 2rem;
    }

    .form-title {
        font-size: 1.75rem;
    }

    .btn-container {
        flex-direction: column;
    }

    .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
}

/* Loading Effect */
.loading {
    position: relative;
    overflow: hidden;
}

.loading::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        90deg,
        transparent,
        rgba(255, 255, 255, 0.2),
        transparent
    );
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% {
        transform: translateX(-100%);
    }
    100% {
        transform: translateX(100%);
    }
}
</style>

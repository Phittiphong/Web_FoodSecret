<?php
session_start();
include 'connect.php'; // Ensure this includes your database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to delete a post.";
    exit;
}

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID

// Check if a post ID is passed in the URL
if (isset($_GET['post_id'])) {
    $post_id = intval($_GET['post_id']); // Ensure it's an integer

    // Debugging: Check if the post ID is being received correctly
    error_log("Post ID to delete: " . $post_id);

    // Check if the post exists and belongs to the logged-in user
    $stmt = $conn->prepare("SELECT user_id FROM posts WHERE id = ?");
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        echo "Failed to prepare SQL statement.";
        exit;
    }
    
    $stmt->bind_param('i', $post_id);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($post_owner_id);
        $stmt->fetch();

        // Verify if the logged-in user is the owner of the post
        if ($post_owner_id === $user_id) {
            // Prepare a statement to delete the post
            $delete_stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
            if (!$delete_stmt) {
                error_log("Delete statement prepare failed: " . $conn->error);
                echo "Failed to prepare delete SQL statement.";
                exit;
            }
            
            $delete_stmt->bind_param('i', $post_id);

            if ($delete_stmt->execute()) {
                // Post deleted successfully, set session message
                $_SESSION['delete_message'] = 'success';
                header("Location: Home.php?delete_success=true");
                exit;
            }

            $delete_stmt->close();
        } else {
            // If the logged-in user is not the owner
            echo "You are not authorized to delete this post.";
            error_log("User with ID " . $user_id . " tried to delete post owned by user with ID " . $post_owner_id);
        }
    } else {
        // Post does not exist
        echo "Post not found.";
        error_log("Post with ID " . $post_id . " not found.");
    }

    $stmt->close();
} if (!isset($_GET['post_id'])) {
    echo "ไม่พบรหัสโพสต์ที่ต้องการลบ";
    error_log("No post ID provided.");
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ลบโพสต์</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Modal Overlay */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Confirm Modal */
        .confirm-modal {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            text-align: center;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .confirm-modal h3 {
            margin: 0 0 1rem 0;
            color: #333;
        }

        .modal-buttons {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .btn {
            padding: 0.5rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
        }

        .btn-confirm {
            background: #dc3545;
            color: white;
        }

        .btn-cancel {
            background: #6c757d;
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        /* Loading Animation */
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.9);
            z-index: 2000;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .loading-spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        .loading-text {
            margin-top: 1rem;
            color: #333;
            font-size: 1.1rem;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

<!-- Confirm Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmModalLabel">ยืนยันการลบโพสต์</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>คุณแน่ใจหรือไม่ที่จะลบโพสต์นี้? การดำเนินการนี้ไม่สามารถย้อนกลับได้</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">ลบโพสต์</button>
            </div>
        </div>
    </div>
</div>

<style>
/* Modal styles */
.modal-content {
    border: none;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.modal-header {
    background-color: #f8d7da;
    border-bottom: none;
    border-radius: 15px 15px 0 0;
}

.modal-footer {
    border-top: none;
    padding: 1rem;
}

/* Loading overlay styles */
.loading-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.9);
    z-index: 9999;
    justify-content: center;
    align-items: center;
    flex-direction: column;
}

.loading-spinner {
    width: 50px;
    height: 50px;
    border: 5px solid #f3f3f3;
    border-top: 5px solid #C85C5C;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

.loading-text {
    margin-top: 1rem;
    color: #333;
    font-size: 1.1rem;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Button styles */
.modal-footer .btn {
    padding: 0.5rem 1.5rem;
    font-weight: 500;
    border-radius: 25px;
    transition: all 0.3s ease;
}

.modal-footer .btn-secondary {
    background-color: #6c757d;
    color: white;
}

.modal-footer .btn-danger {
    background-color: #dc3545;
}

.modal-footer .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}
</style>

<script>
let postIdToDelete = null;

function showDeleteConfirmation(postId) {
    postIdToDelete = postId;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
    deleteModal.show();
}

// Add event listener for confirmation button
document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (postIdToDelete) {
        // Hide the confirmation modal
        const deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteConfirmModal'));
        deleteModal.hide();
        
        // Show loading overlay
        document.getElementById('loadingOverlay').style.display = 'flex';
        
        // Redirect to delete_post.php
        window.location.href = `delete_post.php?post_id=${postIdToDelete}`;
    }
});

// Show success message if deletion was successful
window.onload = function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('delete_success') === 'true') {
        alert('โพสต์ถูกลบเรียบร้อยแล้ว');
    }
}
</script>

<?php
// Your existing PHP code here
?>

</body>
</html>

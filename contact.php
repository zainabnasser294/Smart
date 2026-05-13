<?php 
include 'includes/header.php'; 
include 'config/db.php';

$message_sent = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$name, $email, $subject, $message])) {
        $message_sent = true;
    }
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <h2 class="fw-bold text-primary mb-4"><i class="fas fa-envelope-open-text me-2"></i>Contact Us</h2>
                
                <?php if ($message_sent): ?>
                    <div class="alert alert-success">Your inquiry has been sent successfully! Our team will review it.</div>
                <?php endif; ?>

                <p class="text-muted">If you have any inquiries regarding the smart dashboard or system integration, please reach out below.</p>
                
                <form action="contact.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" required placeholder="Enter your name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" required placeholder="name@example.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" name="subject" class="form-control" placeholder="e.g., Device Integration">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea name="message" class="form-control" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill">Send Message</button>
                </form>

                <hr class="my-4">
                <p class="mb-1"><strong>Developed by:</strong> Zainab Nasser ALhdidi</p>
                <small class="text-muted">Project: Smart Network Monitoring System (NBI)</small>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
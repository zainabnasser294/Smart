<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-lg border-0 bg-dark text-white rounded-4 overflow-hidden">
            <div class="card-header bg-primary text-black fw-bold d-flex justify-content-between align-items-center p-4">
                <div class="d-flex align-items-center">
                    <div class="bg-black bg-opacity-20 p-2 rounded-circle me-3"><i class="fas fa-robot fa-lg"></i></div>
                    <div>
                        <h5 class="mb-0 fw-bold">NBI Smart Assistant</h5>
                        <small class="opacity-75">AI-Powered Network Intelligence</small>
                    </div>
                </div>
                <span class="badge bg-black bg-opacity-50 text-success">● Systems Active</span>
            </div>
            
            <div class="card-body bg-black bg-opacity-40 p-4" id="chat-window" style="height: 500px; overflow-y: auto;">
                <div class="message-bot mb-4">
                    <div class="d-flex align-items-start">
                        <div class="bg-primary text-black p-3 rounded-4 shadow-sm" style="max-width: 80%;">
                            <p class="mb-0">Greetings! I am the Network Behavior Intelligence assistant. I can help you analyze device metrics, identify security threats, or generate system audits. How can I assist you today?</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card-footer bg-dark border-0 p-4">
                <form id="chat-form" class="input-group">
                    <input type="text" id="user-input" class="form-control bg-secondary text-white border-0 py-3 ps-4 rounded-start-pill" placeholder="Ask me about network status, threats, or metrics...">
                    <button class="btn btn-primary px-4 rounded-end-pill" type="submit"><i class="fas fa-paper-plane"></i></button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const chatForm = document.getElementById('chat-form');
    const chatWindow = document.getElementById('chat-window');
    const userInput = document.getElementById('user-input');

    function appendMessage(text, side) {
        const msgDiv = document.createElement('div');
        msgDiv.className = side === 'user' ? 'message-user mb-4 text-end' : 'message-bot mb-4';
        
        const bgColor = side === 'user' ? 'bg-secondary text-white' : 'bg-primary text-black';
        const align = side === 'user' ? 'justify-content-end' : 'justify-content-start';
        
        msgDiv.innerHTML = `
            <div class="d-flex ${align}">
                <div class="${bgColor} p-3 rounded-4 shadow-sm" style="max-width: 80%;">
                    <p class="mb-0">${text}</p>
                </div>
            </div>
        `;
        chatWindow.appendChild(msgDiv);
        chatWindow.scrollTop = chatWindow.scrollHeight;
    }

    chatForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const text = userInput.value.trim();
        if(!text) return;

        appendMessage(text, 'user');
        userInput.value = '';
        
        setTimeout(() => {
            let response = "I'm currently indexing the global telemetry data... All systems appear to be operating within normal parameters.";
            
            if(text.toLowerCase().includes('status')) {
                response = "System Check Complete: 5 nodes are online, 2 nodes are offline. Global CPU load is at 14%.";
            } else if(text.toLowerCase().includes('threat') || text.toLowerCase().includes('alert')) {
                response = "I have identified 3 security anomalies in the last 24 hours. The most critical is a High CPU usage spike on Node-X. You should review the Intelligence Logs.";
            } else if(text.toLowerCase().includes('report')) {
                response = "You can generate detailed behavioral reports for any device in the 'View Master' or 'Reports' section.";
            }

            appendMessage(response, 'bot');
        }, 1200);
    });
</script>

<style>
    #chat-window::-webkit-scrollbar { width: 6px; }
    #chat-window::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
    .message-bot .rounded-4 { border-bottom-left-radius: 2px !important; }
    .message-user .rounded-4 { border-bottom-right-radius: 2px !important; }
</style>

<?php include 'includes/footer.php'; ?>

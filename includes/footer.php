</div> </div> 

<footer class="smart-footer py-4 mt-auto">
    <div class="container-fluid px-5">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <span class="footer-text">
                    <strong>SmartMonitor AI System</strong> &copy; 2026. All Rights Reserved.
                </span>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <span class="footer-text">
                    <i class="fas fa-graduation-cap me-1"></i> 
                    Student: <strong>Zainab Nasser ALhdidi</strong> | ID: 2110072
                </span>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>

<div id="chat-widget" class="smart-chat-widget">
    
    <button onclick="toggleChat()" class="chat-toggle-btn">
        <i class="fas fa-robot"></i>
        <span>AI Assistant</span>
    </button>
    
    <div id="chat-box" class="smart-chat-box">
        <div class="chat-header">
            <div class="d-flex align-items-center">
                <div class="online-dot"></div>
                <span>SmartMonitor Support</span>
            </div>
            <button onclick="toggleChat()" class="close-chat">&times;</button>
        </div>

        <div id="messages" class="chat-messages">
            <div class="bot-msg">
                Hello! How can I assist you today?
            </div>
        </div>

        <div class="chat-input-area">
            <input type="text" id="userInput" placeholder="Ask a question..." autocomplete="off">
            <button onclick="sendMessage()" class="send-btn">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>
</div>

<style>
    /* تنسيق الفوتر الجديد */
    .smart-footer {
        background: #0b1120;
        border-top: 1px solid rgba(255, 255, 255, 0.05);
    }
    .footer-text {
        color: #94a3b8;
        font-size: 0.85rem;
    }

    /* تنسيق الشات ويدجت */
    .smart-chat-widget {
        position: fixed;
        bottom: 25px;
        right: 25px;
        z-index: 9999;
        font-family: 'Inter', sans-serif;
    }

    .chat-toggle-btn {
        padding: 12px 25px;
        border-radius: 50px;
        background: linear-gradient(135deg, #38bdf8, #1e3a8a);
        color: white;
        border: none;
        cursor: pointer;
        box-shadow: 0 10px 25px rgba(56, 189, 248, 0.3);
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: 0.3s;
    }
    .chat-toggle-btn:hover { transform: scale(1.05); box-shadow: 0 15px 30px rgba(56, 189, 248, 0.5); }

    .smart-chat-box {
        display: none;
        width: 350px;
        height: 480px;
        background: #1e293b; /* داكن متناسق مع الموقع */
        border-radius: 20px;
        position: absolute;
        bottom: 75px;
        right: 0;
        flex-direction: column;
        box-shadow: 0 15px 40px rgba(0,0,0,0.4);
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .chat-header {
        background: #0f172a;
        color: white;
        padding: 15px 20px;
        font-weight: bold;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .online-dot {
        width: 10px; height: 10px;
        background: #22c55e;
        border-radius: 50%;
        margin-right: 10px;
        box-shadow: 0 0 10px #22c55e;
    }

    .chat-messages {
        flex: 1; padding: 20px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 12px;
        background: #111d35;
    }

    .bot-msg {
        background: #1e293b;
        color: #e2e8f0;
        padding: 10px 15px;
        border-radius: 15px 15px 15px 2px;
        align-self: flex-start;
        max-width: 85%;
        font-size: 14px;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .user-msg {
        background: #38bdf8;
        color: #000;
        padding: 10px 15px;
        border-radius: 15px 15px 2px 15px;
        align-self: flex-end;
        max-width: 85%;
        font-size: 14px;
        font-weight: 500;
    }

    .chat-input-area {
        padding: 15px;
        background: #0f172a;
        display: flex;
        gap: 10px;
    }

    .chat-input-area input {
        flex: 1;
        background: #1e293b;
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 10px 15px;
        border-radius: 10px;
        color: white;
        outline: none;
        font-size: 14px;
    }

    .send-btn {
        background: #38bdf8;
        color: #000;
        border: none;
        width: 40px; height: 40px;
        border-radius: 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .close-chat { background: none; border: none; color: #94a3b8; font-size: 24px; cursor: pointer; }
</style>

<script>
function toggleChat() {
    const box = document.getElementById('chat-box');
    box.style.display = box.style.display === 'none' ? 'flex' : 'none';
}

async function sendMessage() {
    const input = document.getElementById('userInput');
    const msg = input.value.trim();
    if (!msg) return;

    const messagesDiv = document.getElementById('messages');
    
    // رسالة المستخدم
    messagesDiv.innerHTML += `<div class="user-msg">${msg}</div>`;
    
    input.value = '';
    messagesDiv.scrollTop = messagesDiv.scrollHeight;

    const n8n_url = "http://localhost:5678/webhook/f8374bc0-7992-45de-a51e-523debf6c284";

    try {
        const loadingId = "loading-" + Date.now();
        messagesDiv.innerHTML += `
            <div id="${loadingId}" class="bot-msg" style="opacity: 0.7;">
                <i class="fas fa-spinner fa-spin me-2"></i> Thinking...
            </div>`;
        messagesDiv.scrollTop = messagesDiv.scrollHeight;

        const response = await fetch(n8n_url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                chatInput: msg,
                user_email: "<?php echo $_SESSION['email'] ?? 'guest@smartmonitor.ai'; ?>" 
            })
        });
        
        if(response.ok) {
            const data = await response.json();
            const aiResponse = data.output || "Request processed. Check your metrics.";
            const loadingElement = document.getElementById(loadingId);
            loadingElement.innerHTML = aiResponse;
            loadingElement.style.opacity = "1";
        } else {
            throw new Error();
        }
    } catch (e) {
        messagesDiv.innerHTML += `<div style="color: #ef4444; font-size: 12px; text-align: center;">Connection error.</div>`;
    }
    messagesDiv.scrollTop = messagesDiv.scrollHeight;
}

document.getElementById('userInput').addEventListener('keypress', function (e) {
    if (e.key === 'Enter') sendMessage();
});
</script>

</body>
</html>
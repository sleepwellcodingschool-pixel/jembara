// Live Chat Widget JavaScript for JEMBARA RISET DAN MEDIA

class ChatWidget {
    constructor() {
        this.chatToggle = document.getElementById('chat-toggle');
        this.chatWindow = document.getElementById('chat-window');
        this.chatClose = document.getElementById('chat-close');
        this.chatForm = document.getElementById('chat-form');
        this.chatInput = document.getElementById('chat-input');
        this.chatMessages = document.getElementById('chat-messages');
        this.chatInfoForm = document.getElementById('chat-info-form');
        this.chatName = document.getElementById('chat-name');
        this.chatEmail = document.getElementById('chat-email');
        
        this.sessionId = null;
        this.isSessionStarted = false;
        this.lastMessageId = 0;
        this.pollInterval = null;
        this.isTyping = false;
        
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.loadSessionFromStorage();
    }
    
    bindEvents() {
        // Toggle chat window
        this.chatToggle.addEventListener('click', () => {
            this.toggleChat();
        });
        
        // Close chat window
        this.chatClose.addEventListener('click', () => {
            this.closeChat();
        });
        
        // Handle form submission
        this.chatForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.sendMessage();
        });
        
        // Handle enter key in input
        this.chatInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });
        
        // Show typing indicator
        this.chatInput.addEventListener('input', () => {
            this.handleTyping();
        });
        
        // Close chat when clicking outside
        document.addEventListener('click', (e) => {
            if (!this.chatWindow.contains(e.target) && !this.chatToggle.contains(e.target)) {
                if (!this.chatWindow.classList.contains('hidden')) {
                    // Don't auto-close if user is actively chatting
                    if (!this.isTyping && this.chatInput.value.trim() === '') {
                        this.closeChat();
                    }
                }
            }
        });
    }
    
    toggleChat() {
        if (this.chatWindow.classList.contains('hidden')) {
            this.openChat();
        } else {
            this.closeChat();
        }
    }
    
    openChat() {
        this.chatWindow.classList.remove('hidden');
        this.chatToggle.classList.add('bg-secondary');
        
        if (!this.isSessionStarted) {
            this.showContactForm();
        } else {
            this.chatInput.focus();
            this.startPolling();
        }
    }
    
    closeChat() {
        this.chatWindow.classList.add('hidden');
        this.chatToggle.classList.remove('bg-secondary');
        this.stopPolling();
    }
    
    showContactForm() {
        this.chatInfoForm.style.display = 'block';
        this.chatInput.disabled = true;
        this.chatInput.placeholder = 'Masukkan nama dan email terlebih dahulu...';
        
        // Focus on name input
        this.chatName.focus();
        
        // Add event listener for contact form
        const startChatBtn = document.createElement('button');
        startChatBtn.type = 'button';
        startChatBtn.className = 'w-full bg-primary text-white px-3 py-2 rounded-lg mt-2 text-sm font-medium hover:bg-secondary transition-colors';
        startChatBtn.textContent = 'Mulai Chat';
        
        if (!this.chatInfoForm.querySelector('button')) {
            this.chatInfoForm.appendChild(startChatBtn);
        }
        
        startChatBtn.addEventListener('click', () => {
            this.startChatSession();
        });
    }
    
    async startChatSession() {
        const name = this.chatName.value.trim();
        const email = this.chatEmail.value.trim();
        
        if (!name || !email) {
            this.showNotification('Nama dan email wajib diisi', 'error');
            return;
        }
        
        if (!this.isValidEmail(email)) {
            this.showNotification('Format email tidak valid', 'error');
            return;
        }
        
        try {
            const response = await fetch('/api/chat.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    action: 'start_session',
                    name: name,
                    email: email
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.sessionId = data.data.session_id;
                this.isSessionStarted = true;
                
                // Save session to localStorage
                localStorage.setItem('chat_session_id', this.sessionId);
                localStorage.setItem('chat_name', name);
                localStorage.setItem('chat_email', email);
                
                // Hide contact form
                this.chatInfoForm.style.display = 'none';
                
                // Enable chat input
                this.chatInput.disabled = false;
                this.chatInput.placeholder = 'Ketik pesan...';
                this.chatInput.focus();
                
                // Add welcome message
                this.addMessage({
                    sender_type: 'admin',
                    sender_name: 'Tim Support',
                    message: `Halo ${name}! Terima kasih telah menghubungi JEMBARA RISET DAN MEDIA. Ada yang bisa kami bantu?`,
                    created_at: new Date().toISOString()
                });
                
                // Start polling for new messages
                this.startPolling();
                
            } else {
                this.showNotification(data.message, 'error');
            }
        } catch (error) {
            console.error('Chat session error:', error);
            this.showNotification('Gagal memulai sesi chat. Silakan coba lagi.', 'error');
        }
    }
    
    async sendMessage() {
        const message = this.chatInput.value.trim();
        
        if (!message || !this.sessionId) {
            return;
        }
        
        // Disable input temporarily
        this.chatInput.disabled = true;
        
        try {
            // Add message to UI immediately (optimistic update)
            this.addMessage({
                sender_type: 'visitor',
                sender_name: localStorage.getItem('chat_name'),
                message: message,
                created_at: new Date().toISOString()
            });
            
            // Clear input
            this.chatInput.value = '';
            
            // Send to server
            const response = await fetch('/api/chat.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    action: 'send_message',
                    session_id: this.sessionId,
                    sender_type: 'visitor',
                    sender_name: localStorage.getItem('chat_name'),
                    sender_email: localStorage.getItem('chat_email'),
                    message: message
                })
            });
            
            const data = await response.json();
            
            if (!data.success) {
                this.showNotification(data.message, 'error');
                // Remove the optimistically added message
                const messages = this.chatMessages.querySelectorAll('.chat-message');
                const lastMessage = messages[messages.length - 1];
                if (lastMessage && lastMessage.dataset.sender === 'visitor') {
                    lastMessage.remove();
                }
            }
            
        } catch (error) {
            console.error('Send message error:', error);
            this.showNotification('Gagal mengirim pesan. Silakan coba lagi.', 'error');
        } finally {
            this.chatInput.disabled = false;
            this.chatInput.focus();
        }
    }
    
    addMessage(messageData) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `chat-message ${messageData.sender_type} p-3 rounded-lg mb-3`;
        messageDiv.dataset.sender = messageData.sender_type;
        messageDiv.dataset.messageId = messageData.id || 0;
        
        const time = new Date(messageData.created_at).toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit'
        });
        
        messageDiv.innerHTML = `
            <div class="text-sm mb-1 opacity-75">
                ${messageData.sender_name} • ${time}
            </div>
            <div class="message-text">
                ${this.escapeHtml(messageData.message)}
            </div>
        `;
        
        // Clear initial message if it exists
        const initialMessage = this.chatMessages.querySelector('.text-center');
        if (initialMessage) {
            initialMessage.remove();
        }
        
        this.chatMessages.appendChild(messageDiv);
        this.scrollToBottom();
    }
    
    scrollToBottom() {
        this.chatMessages.scrollTop = this.chatMessages.scrollHeight;
    }
    
    startPolling() {
        if (this.pollInterval) {
            clearInterval(this.pollInterval);
        }
        
        this.pollInterval = setInterval(() => {
            this.pollForNewMessages();
        }, 3000); // Poll every 3 seconds
    }
    
    stopPolling() {
        if (this.pollInterval) {
            clearInterval(this.pollInterval);
            this.pollInterval = null;
        }
    }
    
    async pollForNewMessages() {
        if (!this.sessionId) return;
        
        try {
            const response = await fetch(`/api/chat.php?action=get_messages&session_id=${this.sessionId}&last_message_id=${this.lastMessageId}`);
            const data = await response.json();
            
            if (data.success && data.data.length > 0) {
                data.data.forEach(message => {
                    if (message.sender_type === 'admin') {
                        this.addMessage(message);
                    }
                    this.lastMessageId = Math.max(this.lastMessageId, parseInt(message.id));
                });
            }
        } catch (error) {
            console.error('Poll messages error:', error);
        }
    }
    
    loadSessionFromStorage() {
        const sessionId = localStorage.getItem('chat_session_id');
        const name = localStorage.getItem('chat_name');
        const email = localStorage.getItem('chat_email');
        
        if (sessionId && name && email) {
            this.sessionId = sessionId;
            this.isSessionStarted = true;
            this.chatName.value = name;
            this.chatEmail.value = email;
        }
    }
    
    handleTyping() {
        this.isTyping = true;
        clearTimeout(this.typingTimeout);
        
        this.typingTimeout = setTimeout(() => {
            this.isTyping = false;
        }, 2000);
    }
    
    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transform translate-x-full transition-transform duration-300`;
        
        // Set notification style based on type
        switch (type) {
            case 'success':
                notification.classList.add('bg-green-500', 'text-white');
                break;
            case 'error':
                notification.classList.add('bg-red-500', 'text-white');
                break;
            case 'warning':
                notification.classList.add('bg-yellow-500', 'text-gray-900');
                break;
            default:
                notification.classList.add('bg-blue-500', 'text-white');
        }
        
        notification.innerHTML = `
            <div class="flex items-center">
                <div class="flex-1">
                    <p class="font-medium">${message}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 hover:opacity-75">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 300);
        }, 5000);
    }
    
    destroy() {
        this.stopPolling();
        // Close session if needed
        if (this.sessionId && this.isSessionStarted) {
            fetch('/api/chat.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams({
                    action: 'close_session',
                    session_id: this.sessionId
                })
            });
        }
    }
}

// Initialize chat widget when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('chat-widget')) {
        const chatWidget = new ChatWidget();
        
        // Cleanup on page unload
        window.addEventListener('beforeunload', () => {
            chatWidget.destroy();
        });
    }
});

// Export for global use
window.ChatWidget = ChatWidget;
